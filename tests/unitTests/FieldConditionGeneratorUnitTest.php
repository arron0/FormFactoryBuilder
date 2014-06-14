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

use Arron\FormBuilder\FieldConditionGenerator;
use Arron\TestIt\TestCase;
use Nette\PhpGenerator\Method;

/**
 * FieldConditionGeneratorUnitTest class definition
 *
 * @package Cms
 * @subpackage Tests
 * @author Tomáš Lembacher <tomas.lembacher@seznam.cz>
 * @license
 */
class FieldConditionGeneratorUnitTest extends TestCase
{

	protected $namingContainerMock;

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
				new FieldConditionGenerator(
						$config,
						$this->namingContainerMock = $this->getMockedClass('\Arron\FormBuilder\VariableNamingContainer', 'namingContainer'),
						$this->getMockedClass('\Arron\FormBuilder\GeneratorFactory', 'generatorFactory')
				)
		);
	}

	public function testGenerateSimpleCondition()
	{
		$config = array(
				'operation' => 'yetAnotherOperation',
				'value' => '.*[0-9].*',
				'rules' => array(
						array(
								'type' => 'max_length',
								'msg' => 'someMsg',
								'value' => 5,
						),
				),
		);

		$ruleGeneratorMock = $this->getMockedClass('\Arron\FormBuilder\FieldRuleGenerator', 'ruleGenerator');

		$builder = new Method();
		$expectedResult = 'function() {
	$condition5d4ab6f09d469ece6e09b030876a0aa5 = $baseConditionVariable->addCondition(\Nette\Forms\Form::YETANOTHEROPERATION, \'.*[0-9].*\');
	$condition5d4ab6f09d469ece6e09b030876a0aa5->endCondition();
}';

		$this->createTestObjectWithParams($config);
		$namingContainerMock = $this->namingContainerMock;

		$this->expectDependencyCall('namingContainer', 'isParameter', array('yetAnotherOperation'), FALSE); // it is called each time, getConfig is called...
		$this->expectDependencyCall('namingContainer', 'isParameter', array('.*[0-9].*'), FALSE);

		$this->expectDependencyCall('generatorFactory', 'createRuleGenerator', array($config['rules'][0], $namingContainerMock), $ruleGeneratorMock);
		$this->expectDependencyCall('ruleGenerator', 'generate', array($builder, 'condition5d4ab6f09d469ece6e09b030876a0aa5'));

		$this->getTestObject()->generate($builder, 'baseConditionVariable');

		$this->assertEquals($expectedResult, (string) $builder);
	}

	public function testConditionOnFieldWithMoreRules()
	{
		$config = array(
				'field' => 'someField',
				'operation' => 'someOperation',
				'rules' => array(
						array(
								'type' => 'pattern',
								'msg' => 'someMsg',
								'value' => '.*[0-9].*',
						),
						array(
								'type' => 'equals',
								'msg' => 'someMsg',
								'value' => 'someOtherField',
						),
				)
		);

		$ruleGeneratorMock = $this->getMockedClass('\Arron\FormBuilder\FieldRuleGenerator', 'ruleGenerator');

		$builder = new Method();
		$expectedResult = 'function() {
	$condition8aa6b989b6485ec3b5b2e8aa51ba4e23 = $baseConditionVariable->addConditionOn($someFieldVariable, \Nette\Forms\Form::SOMEOPERATION, NULL);
	$condition8aa6b989b6485ec3b5b2e8aa51ba4e23->endCondition();
}';

		$this->createTestObjectWithParams($config);
		$namingContainerMock = $this->namingContainerMock;

		$this->expectDependencyCall('namingContainer', 'isParameter', array('someField'), FALSE); // it is called each time, getConfig is called...
		$this->expectDependencyCall('namingContainer', 'isParameter', array('someField'), FALSE);
		$this->expectDependencyCall('namingContainer', 'getFieldVariable', array('someField'), 'someFieldVariable');
		$this->expectDependencyCall('namingContainer', 'isParameter', array('someOperation'), FALSE);

		$this->expectDependencyCall('generatorFactory', 'createRuleGenerator', array($config['rules'][0], $namingContainerMock), $ruleGeneratorMock);
		$this->expectDependencyCall('ruleGenerator', 'generate', array($builder, 'condition8aa6b989b6485ec3b5b2e8aa51ba4e23'));

		$this->expectDependencyCall('generatorFactory', 'createRuleGenerator', array($config['rules'][1], $namingContainerMock), $ruleGeneratorMock);
		$this->expectDependencyCall('ruleGenerator', 'generate', array($builder, 'condition8aa6b989b6485ec3b5b2e8aa51ba4e23'));

		$this->getTestObject()->generate($builder, 'baseConditionVariable');

		$this->assertEquals($expectedResult, (string) $builder);
	}

	public function testGenerateWithSubConditions()
	{
		$config = array(
				'field' => 'someField',
				'operation' => 'someOperation',
				'conditions' => array(
						0 => array('dataForSubCondition01'),

						1 => array('dataForSubCondition02'),
				),
		);

		$conditionGeneratorMock = $this->getMockedClass('\Arron\FormBuilder\FieldConditionGenerator', 'conditionGenerator');

		$builder = new Method();
		$expectedResult = 'function() {
	$conditionfc3b9f82e43606f431b35bd3930e76af = $baseConditionVariable->addConditionOn($someFieldVariable, \Nette\Forms\Form::SOMEOPERATION, NULL);
	$conditionfc3b9f82e43606f431b35bd3930e76af->endCondition();
}';

		$this->createTestObjectWithParams($config);
		$namingContainerMock = $this->namingContainerMock;

		$this->expectDependencyCall('namingContainer', 'isParameter', array('someField'), FALSE); // it is called each time, getConfig is called...
		$this->expectDependencyCall('namingContainer', 'isParameter', array('someField'), FALSE);
		$this->expectDependencyCall('namingContainer', 'getFieldVariable', array('someField'), 'someFieldVariable');
		$this->expectDependencyCall('namingContainer', 'isParameter', array('someOperation'), FALSE);

		$this->expectDependencyCall('generatorFactory', 'createConditionGenerator', array($config['conditions'][0], $namingContainerMock), $conditionGeneratorMock);
		$this->expectDependencyCall('conditionGenerator', 'generate', array($builder, 'conditionfc3b9f82e43606f431b35bd3930e76af'));

		$this->expectDependencyCall('generatorFactory', 'createConditionGenerator', array($config['conditions'][1], $namingContainerMock), $conditionGeneratorMock);
		$this->expectDependencyCall('conditionGenerator', 'generate', array($builder, 'conditionfc3b9f82e43606f431b35bd3930e76af'));

		$this->getTestObject()->generate($builder, 'baseConditionVariable');

		$this->assertEquals($expectedResult, (string) $builder);
	}

	public function testGenerateNoOperationSetException()
	{
		$config = array(
				'value' => '.*[0-9].*',
				'rules' => array(
						array(
								'type' => 'max_length',
								'msg' => 'someMsg',
								'value' => 5,
						),
				),
		);

		$builder = new Method();
		$this->createTestObjectWithParams($config);

		$this->setExpectedException('\LogicException');

		$this->getTestObject()->generate($builder, 'baseConditionVariable');
	}
}