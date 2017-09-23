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

use Arron\FormBuilder\FormFactoryGenerator;
use Arron\TestIt\TestCase;

/**
 * FormFactoryGeneratorUnitTest class definition
 *
 * @package Cms
 * @subpackage Tests
 * @author Tomáš Lembacher <tomas.lembacher@seznam.cz>
 * @license
 */
class FormFactoryGeneratorUnitTest extends TestCase
{
	/**
	 * @return NULL
	 */
	protected function createTestObject()
	{
		return NULL;
	}

	protected function createTestObjectWithParams($className, array $config)
	{
		$this->setTestObject(
				new FormFactoryGenerator(
						$className,
						$config,
						$this->getMockedClass('\Arron\FormBuilder\VariableNamingContainer', 'namingContainer'),
						$this->getMockedClass('\Arron\FormBuilder\GeneratorFactory', 'generatorFactory')
				)
		);
	}

	/**
	 * @param string $className
	 * @param array $config
	 * @param string $expectedResult
	 */
	public function testGenerate()
	{
		$className = 'factory';
		$config = array(
				'form' => array(
						'class' => "registerMe",
						'type' => '\Nette\Forms\Form',
						'method' => "post",
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
				),
				'control1' => array('control 1 data'),
				'control2' => array('control 2 data'),
		);

		$expectedResult = 'class factory implements \Arron\FormBuilder\IFormFactory
{
	/**
	 * @return \Nette\Forms\Form
	 */
	public function create($param1, $param3 = \'default value\')
	{
	}
}
';
		$this->createTestObjectWithParams($className, $config);
		$namingContainerMock = $this->getMockedClass('\Arron\FormBuilder\VariableNamingContainer', 'namingContainer');
		$fieldOneGeneratorMock = $this->getMockedClass('\Arron\FormBuilder\FormFieldGenerator', 'fieldOneGenerator');
		$fieldTwoGeneratorMock = $this->getMockedClass('\Arron\FormBuilder\FormFieldGenerator', 'fieldTwoGenerator');

		$this->expectDependencyCall('namingContainer', 'addParameter', array('param1', 'param1'));
		$this->expectDependencyCall('namingContainer', 'addParameter', array('param3', 'param3'));
		$this->expectDependencyCall('generatorFactory', 'createClassGenerator', array($config['form'], $namingContainerMock), $this->getMockedClass('\Arron\FormBuilder\FormClassGenerator', 'formClassGenerator'));

		$this->expectDependencyCall('generatorFactory', 'createFieldGenerator', array('control1', $config['control1'], $namingContainerMock), $fieldOneGeneratorMock);
		$this->expectDependencyCall('fieldOneGenerator', 'getVariableName', array(), 'control1Variable');
		$this->expectDependencyCall('namingContainer', 'addField', array('control1', 'control1Variable'));
		$this->expectDependencyCall('formClassGenerator', 'addField', array($fieldOneGeneratorMock));

		$this->expectDependencyCall('generatorFactory', 'createFieldGenerator', array('control2', $config['control2'], $namingContainerMock), $fieldTwoGeneratorMock);
		$this->expectDependencyCall('fieldTwoGenerator', 'getVariableName', array(), 'control2Variable');
		$this->expectDependencyCall('namingContainer', 'addField', array('control2', 'control2Variable'));
		$this->expectDependencyCall('formClassGenerator', 'addField', array($fieldTwoGeneratorMock));

		$this->expectDependencyCall('formClassGenerator', 'generate', NULL, '');

		$returnedResult = $this->getTestObject()->generate();

		$this->assertEquals($expectedResult, (string)$returnedResult);
	}

	public function testGenerateWithNoFormConfig()
	{
		$this->createTestObjectWithParams('factoryClass', array());

		$this->setExpectedException('\LogicException');

		$this->getTestObject()->generate();
	}

}