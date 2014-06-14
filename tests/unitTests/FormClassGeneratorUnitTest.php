<?php
/**
 * Requires PHP Version 5.3 (min)
 *
 * @package Cms
 * @subpackage Tests
 * @author Tomáš Lembacher <tomas.lembacher@seznam.cz>
 * @license
 */
namespace Cms\Tests;

use Arron\FormBuilder\FormClassGenerator;
use Arron\TestIt\TestCase;
use Nette\PhpGenerator\Method;

/**
 * FormClassGeneratorUnitTest class definition
 *
 * @package Cms
 * @subpackage Tests
 * @author Tomáš Lembacher <tomas.lembacher@seznam.cz>
 * @license
 */
class FormClassGeneratorUnitTest extends TestCase
{
	/**
	 * @return NULL
	 */
	protected function createTestObject()
	{
		return NULL;
	}

	protected function createTestObjectWithParams(array $config)
	{
		$this->setTestObject(
				new FormClassGenerator(
						$config,
						$this->getMockedClass('\Arron\FormBuilder\VariableNamingContainer', 'namingContainer'),
						$this->getMockedClass('\Arron\FormBuilder\GeneratorFactory', 'generatorFactory')
				)
		);
	}

	public function testGenerate()
	{
		$config = array(
				'class' => "registerMe",
				'type' => '\Nette\Forms\Form',
				'method' => 'param1',
				'parameters' => array(
						0 => 'param1',
						'param3' => "default value",
				),
				'groups' => array(
						"Osobní údaje" => array(
								0 => "name",
								1 => "surname",
								2 => "email",
						),
						'Heslo' => array(
								0 => "password",
								1 => "password_repeat",
						)
				)
		);

		$expectedResult = 'function() {
	$formb3604ff8a9db2e7798eb7872ae8d2890 = new \Nette\Forms\Form();
	$formb3604ff8a9db2e7798eb7872ae8d2890->setMethod($param1);
	$formb3604ff8a9db2e7798eb7872ae8d2890->getElementPrototype()->setClass(\'registerMe\');
	$formb3604ff8a9db2e7798eb7872ae8d2890->addProtection(\'We are sorry, but form times up. Please send it one more time.\');
	$group0dae1b844c2f5c1ceb75b9caabf66a65 = $formb3604ff8a9db2e7798eb7872ae8d2890->addGroup(\'Osobní údaje\', FALSE);
	$group3ea4db050dfd4daa3a93e9434c468776 = $formb3604ff8a9db2e7798eb7872ae8d2890->addGroup(\'Heslo\', FALSE);
	$noGroup = NULL;
	$container0c63ef9225db6aad41ef9a7c11833042 = $formb3604ff8a9db2e7798eb7872ae8d2890->addContainer(\'container1\');
	$formb3604ff8a9db2e7798eb7872ae8d2890->setCurrentGroup($group0dae1b844c2f5c1ceb75b9caabf66a65);
	$formb3604ff8a9db2e7798eb7872ae8d2890->setCurrentGroup($noGroup);
	return $formb3604ff8a9db2e7798eb7872ae8d2890;
}';
		$this->createTestObjectWithParams($config);
		$namingContainerMock = $this->getMockedClass('\Arron\FormBuilder\VariableNamingContainer', 'namingContainer');
		$fieldOneGeneratorMock = $this->getMockedClass('\Arron\FormBuilder\FormFieldGenerator', 'fieldOneGenerator');
		$fieldTwoGeneratorMock = $this->getMockedClass('\Arron\FormBuilder\FormFieldGenerator', 'fieldTwoGenerator');
		$builder = new Method();

		$this->expectDependencyCall('fieldOneGenerator', 'getContainer', array(), NULL);
		$this->expectDependencyCall('fieldTwoGenerator', 'getContainer', array(), 'container1');

		$this->expectDependencyCall('namingContainer', 'isParameter', array('\Nette\Forms\Form'), FALSE);
		$this->expectDependencyCall('namingContainer', 'isParameter', array('param1'), TRUE);
		$this->expectDependencyCall('namingContainer', 'getParameterVariable', array('param1'), 'param1');
		$this->expectDependencyCall('namingContainer', 'isParameter', array('registerMe'), FALSE);

		$this->expectDependencyCall('namingContainer', 'addContainer', array('container1', 'container0c63ef9225db6aad41ef9a7c11833042'));

		$this->expectDependencyCall('fieldOneGenerator', 'getName', array(), 'name');
		$this->expectDependencyCall('fieldOneGenerator', 'getContainer', array(), NULL);
		$this->expectDependencyCall('namingContainer', 'getContainerVariable', array(NULL), new \InvalidArgumentException());
		$this->expectDependencyCall('fieldOneGenerator', 'generateFieldCreation', array($builder, 'formb3604ff8a9db2e7798eb7872ae8d2890'));

		$this->expectDependencyCall('fieldTwoGenerator', 'getName', array(), 'fieldTwo');
		$this->expectDependencyCall('fieldTwoGenerator', 'getContainer', array(), 'container1');
		$this->expectDependencyCall('namingContainer', 'getContainerVariable', array('container1'), 'container1Variable');
		$this->expectDependencyCall('fieldTwoGenerator', 'generateFieldCreation', array($builder, 'container1Variable'));

		$this->expectDependencyCall('fieldOneGenerator', 'generateFieldInitialization', array($builder));
		$this->expectDependencyCall('fieldTwoGenerator', 'generateFieldInitialization', array($builder));

		$this->expectDependencyCall('fieldOneGenerator', 'generateFieldRestraint', array($builder));
		$this->expectDependencyCall('fieldTwoGenerator', 'generateFieldRestraint', array($builder));

		$this->getTestObject()->addField($fieldOneGeneratorMock);
		$this->getTestObject()->addField($fieldTwoGeneratorMock);
		$this->getTestObject()->generate($builder);

		$this->assertEquals($expectedResult, (string) $builder);
	}

	public function testGenerateNoTypeSetInConfig()
	{
		$config = array(
				'class' => "registerMe",
				'method' => 'param1',
				'parameters' => array(
						0 => 'param1',
						'param3' => "default value",
				),
				'groups' => array(
						"Osobní údaje" => array(
								0 => "name",
								1 => "surname",
								2 => "email",
						),
						'Heslo' => array(
								0 => "password",
								1 => "password_repeat",
						)
				)
		);

		$this->createTestObjectWithParams($config);
		$builder = new Method();

		$this->setExpectedException('\InvalidArgumentException');

		$this->getTestObject()->generate($builder);
	}
}