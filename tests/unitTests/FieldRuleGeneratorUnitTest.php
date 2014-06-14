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

use Arron\FormBuilder\FieldRuleGenerator;
use Arron\TestIt\TestCase;
use Nette\PhpGenerator\Method;

/**
 * FieldRuleGeneratorUnitTest class definition
 *
 * @package Cms
 * @subpackage Tests
 * @author Tomáš Lembacher <tomas.lembacher@seznam.cz>
 * @license
 */
class FieldRuleGeneratorUnitTest extends TestCase
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
				new FieldRuleGenerator(
						$config,
						$this->getMockedClass('\Arron\FormBuilder\VariableNamingContainer', 'namingContainer'),
						$this->getMockedClass('\Arron\FormBuilder\GeneratorFactory', 'generatorFactory')
				)
		);
	}

	public function testSimpleRule()
	{
		$config = array(
				'type' => 'max_length',
				'msg' => 'someMsg',
				'value' => 5,
		);
		$builder = new Method();
		$expectedResult = 'function() {
	$ruleBaseVariable->addRule(\Nette\Forms\Form::MAX_LENGTH, \'someMsg\', 5);
}';

		$this->createTestObjectWithParams($config);

		$this->expectDependencyCall('namingContainer', 'isParameter', array('max_length'), FALSE);
		$this->expectDependencyCall('namingContainer', 'isParameter', array('someMsg'), FALSE);
		$this->expectDependencyCall('namingContainer', 'isFieldName', array(5), FALSE);

		$this->getTestObject()->generate($builder, 'ruleBaseVariable');

		$this->assertEquals($expectedResult, (string) $builder);
	}

	public function testSimpleRuleWithParameter()
	{
		$config = array(
				'type' => 'max_length',
				'msg' => 'someMsg',
				'value' => 'parameter01',
		);
		$builder = new Method();
		$expectedResult = 'function() {
	$ruleBaseVariable->addRule(\Nette\Forms\Form::MAX_LENGTH, \'someMsg\', $param01);
}';

		$this->createTestObjectWithParams($config);

		$this->expectDependencyCall('namingContainer', 'isParameter', array('max_length'), FALSE);
		$this->expectDependencyCall('namingContainer', 'isParameter', array('someMsg'), FALSE);
		$this->expectDependencyCall('namingContainer', 'isParameter', array('parameter01'), TRUE);
		$this->expectDependencyCall('namingContainer', 'getParameterVariable', array('parameter01'), 'param01');
		$this->expectDependencyCall('namingContainer', 'isFieldName', NULL, FALSE);

		$this->getTestObject()->generate($builder, 'ruleBaseVariable');

		$this->assertEquals($expectedResult, (string) $builder);
	}

	public function testSimpleRuleWithOtherFieldValue()
	{
		$config = array(
				'type' => 'max_length',
				'msg' => 'someMsg',
				'value' => 'field01',
		);
		$builder = new Method();
		$expectedResult = 'function() {
	$ruleBaseVariable->addRule(\Nette\Forms\Form::MAX_LENGTH, \'someMsg\', $fieldVariable01);
}';

		$this->createTestObjectWithParams($config);

		$this->expectDependencyCall('namingContainer', 'isParameter', array('max_length'), FALSE);
		$this->expectDependencyCall('namingContainer', 'isParameter', array('someMsg'), FALSE);
		$this->expectDependencyCall('namingContainer', 'isParameter', array('field01'), FALSE);
		$this->expectDependencyCall('namingContainer', 'isFieldName', array('field01'), TRUE);
		$this->expectDependencyCall('namingContainer', 'getFieldVariable', array('field01'), 'fieldVariable01');

		$this->getTestObject()->generate($builder, 'ruleBaseVariable');

		$this->assertEquals($expectedResult, (string) $builder);
	}

	public function testRuleWithArrayParameters()
	{
		$config = array(
				'type' => 'range',
				'msg' => 'someMsg',
				'value' => array(5, 'maxValue'),
		);
		$builder = new Method();
		$expectedResult = 'function() {
	$ruleBaseVariable->addRule(\Nette\Forms\Form::RANGE, \'someMsg\', array(5, $maxValue));
}';

		$this->createTestObjectWithParams($config);

		$this->expectDependencyCall('namingContainer', 'isParameter', array('range'), FALSE);
		$this->expectDependencyCall('namingContainer', 'isParameter', array('someMsg'), FALSE);
		$this->expectDependencyCall('namingContainer', 'isParameter', array(5), FALSE);
		$this->expectDependencyCall('namingContainer', 'isFieldName', array(5), FALSE);

		$this->expectDependencyCall('namingContainer', 'isParameter', array('maxValue'), TRUE);
		$this->expectDependencyCall('namingContainer', 'getParameterVariable', array('maxValue'), 'maxValue');

		$this->getTestObject()->generate($builder, 'ruleBaseVariable');

		$this->assertEquals($expectedResult, (string) $builder);
	}

	public function testRuleWithArrayParametersOneIsOtherField()
	{
		$config = array(
				'type' => 'range',
				'msg' => 'someMsg',
				'value' => array(5, 'maxValue'),
		);
		$builder = new Method();
		$expectedResult = 'function() {
	$ruleBaseVariable->addRule(\Nette\Forms\Form::RANGE, \'someMsg\', array(5, $maxValueField));
}';

		$this->createTestObjectWithParams($config);

		$this->expectDependencyCall('namingContainer', 'isParameter', array('range'), FALSE);
		$this->expectDependencyCall('namingContainer', 'isParameter', array('someMsg'), FALSE);
		$this->expectDependencyCall('namingContainer', 'isParameter', array(5), FALSE);
		$this->expectDependencyCall('namingContainer', 'isFieldName', array(5), FALSE);

		$this->expectDependencyCall('namingContainer', 'isParameter', array('maxValue'), FALSE);
		$this->expectDependencyCall('namingContainer', 'isFieldName', array('maxValue'), TRUE);
		$this->expectDependencyCall('namingContainer', 'getFieldVariable', array('maxValue'), 'maxValueField');

		$this->getTestObject()->generate($builder, 'ruleBaseVariable');

		$this->assertEquals($expectedResult, (string) $builder);
	}
}