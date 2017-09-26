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

use Arron\FormBuilder\FormFieldGenerator;
use Arron\TestIt\TestCase;
use Nette\PhpGenerator\Method;

/**
 * FormFieldGeneratorUnitTest class definition
 *
 * @package Cms
 * @subpackage Tests
 * @author Tomáš Lembacher <tomas.lembacher@seznam.cz>
 * @license
 */
class FormFieldGeneratorUnitTest extends TestCase
{

	protected $namingContainerMock;

	/**
	 * @return NULL
	 */
	protected function createTestObject()
	{
		return NULL;
	}

	protected function createTestObjectWithParams($name, array $config)
	{
		$this->setTestObject(
				new FormFieldGenerator(
						$name,
						$config,
						$this->namingContainerMock = $this->getMockedClass('\Arron\FormBuilder\VariableNamingContainer', 'namingContainer'),
						$this->getMockedClass('\Arron\FormBuilder\GeneratorFactory', 'generatorFactory')
				)
		);
	}

	/**
	 * @param string $fullName
	 * @param string $container
	 * @param string $name
	 *
	 * @dataProvider parsingContanersAndNamesDataProvider
	 */
	public function testParsingContainersAndNames($fullName, $container, $name)
	{
		$this->createTestObjectWithParams($fullName, array());

		$returnedFullName = $this->getTestObject()->getFullName();
		$returnedContainer = $this->getTestObject()->getContainer();
		$returnedName = $this->getTestObject()->getName();

		$this->assertEquals($fullName, $returnedFullName);
		$this->assertEquals($container, $returnedContainer);
		$this->assertEquals($name, $returnedName);
	}

	public function parsingContanersAndNamesDataProvider()
	{
		return array(
				array('testInputField', NULL, 'testInputField'),
				array('container1-testInputField', 'container1', 'testInputField'),
		);
	}

	/**
	 * @param string $name
	 * @param array $config
	 * @param string $expectedResult
	 * @param  \Exception|NULL $expectedResult
	 *
	 * @dataProvider generateFieldCreationDataProvider
	 */
	public function testGenerateFieldCreation($name, $config, $expectedResult, $expectedException)
	{
		$this->createTestObjectWithParams($name, $config);
		$builder = new Method('foo');
		$formPointer = 'form';

		/*if (isset($config['type'])) {
			$this->expectDependencyCall('namingContainer', 'isParameter', array($config['type']), FALSE);
		}*/

		foreach ($config as $configKey => $configValue) {
			$this->expectDependencyCall('namingContainer', 'isParameter', array($config[$configKey]), FALSE);
		}

		if (!is_null($expectedException)) {
			$this->expectException($expectedException);
		}

		$this->getTestObject()->generateFieldCreation($builder, $formPointer);

		$this->assertEquals($expectedResult, (string) $builder);
	}

	public function generateFieldCreationDataProvider()
	{
		return array(
				array('text', array('type' => 'text'), "function foo()\n{\n\t\$field07fd0b026fd47eb98cd87f88e5ea281d = \$form['text'] = new \\Nette\\Forms\\Controls\\TextInput;\n}", NULL),
				array('password', array('type' => 'password'), "function foo()\n{\n\t\$fielde6fdbaef571fec7e98a14dcb553cb5cb = \$form['password'] = new \\Nette\\Forms\\Controls\\TextInput;\n\t\$fielde6fdbaef571fec7e98a14dcb553cb5cb->setType('password');\n}", NULL),
				array('textarea', array('type' => 'textarea'), "function foo()\n{\n\t\$fieldc33f9f9e640148a523573a0a0662b5aa = \$form['textarea'] = new \\Nette\\Forms\\Controls\\TextArea;\n}", NULL),
				array('select', array('type' => 'select'), "function foo()\n{\n\t\$field91a748ca57c02db3d5ebf8106bbcb78a = \$form['select'] = new \\Nette\\Forms\\Controls\\SelectBox;\n}", NULL),
				array('radioList', array('type' => 'radioList'), "function foo()\n{\n\t\$field5b90a305b1f78d8a59b51c65337c7e67 = \$form['radioList'] = new \\Nette\\Forms\\Controls\\RadioList;\n}", NULL),
				array('multiSelect', array('type' => 'multiSelect'), "function foo()\n{\n\t\$field6cf402ae36a64f20051fd6bedb4909f2 = \$form['multiSelect'] = new \\Nette\\Forms\\Controls\\MultiSelectBox;\n}", NULL),
				array('hidden', array('type' => 'hidden'), "function foo()\n{\n\t\$field182b55df9fd7f58e7a4893d99a3b739c = \$form['hidden'] = new \\Nette\\Forms\\Controls\\HiddenField;\n}", NULL),
				array('checkboxList', array('type' => 'checkboxList'), "function foo()\n{\n\t\$fieldbc3351cd745df76da7489780b97a899d = \$form['checkboxList'] = new \\Nette\\Forms\\Controls\\CheckboxList;\n}", NULL),
				array('checkbox', array('type' => 'checkbox'), "function foo()\n{\n\t\$fieldec25d158fee0568cfb0ed776c212803c = \$form['checkbox'] = new \\Nette\\Forms\\Controls\\Checkbox;\n}", NULL),
				array('imageButton', array('type' => 'imageButton'), "function foo()\n{\n\t\$fieldd239afd06a853c037da7c66ac0f9c8bf = \$form['imageButton'] = new \\Nette\\Forms\\Controls\\ImageButton;\n}", NULL),
				array('submit', array('type' => 'submit'), "function foo()\n{\n\t\$fieldc03a5925f48fb348342b39e88e844553 = \$form['submit'] = new \\Nette\\Forms\\Controls\\SubmitButton;\n}", NULL),
				array('button', array('type' => 'button'), "function foo()\n{\n\t\$fieldeb6db471a6af85d718f7309eb4114b38 = \$form['button'] = new \\Nette\\Forms\\Controls\\Button;\n}", NULL),
				array('customClassControl', array('type' => 'customClass', 'class' => 'CustomClassControl'), "function foo()\n{\n\t\$fieldfa6fe6c7e4328a06d595536a87e23127 = \$form['customClassControl'] = new CustomClassControl;\n}", NULL),
				array('noTypeSpecified', array(), NULL, '\InvalidArgumentException'),
				array('notExistingTypeSpecified', array('type' => 'notExisting'), NULL, '\InvalidArgumentException'),

		);
	}

	public function testGenerateFieldInitialization()
	{
		$name = 'anyName';
		$config = array(
				'label' => 'someLabel',
				'items' => array('items'),
				'selected' => 'key01',
				'prompt' => 'Some promt text',
				'value' => 42,
				'emptyValue' => 'Add value',
				'src' => 'http://src',
				'alt' => 'alternative text',
				'description' => 'some description text',
				'attributes' => [
						'attr1' => 'value1',
						'attr2' => 'value2',
						],
				'validationScope' => false,
		);
		$builder = new Method('foo');
		$expectedResult = "function foo()\n{
	\$field768879750d6dff186474d4a9041c385d->caption = 'someLabel';
	\$field768879750d6dff186474d4a9041c385d->setItems(['items']);
	\$field768879750d6dff186474d4a9041c385d->setDefaultValue('key01');
	\$field768879750d6dff186474d4a9041c385d->setPrompt('Some promt text');
	\$field768879750d6dff186474d4a9041c385d->setDefaultValue(42);
	\$field768879750d6dff186474d4a9041c385d->setEmptyValue('Add value');
	\$field768879750d6dff186474d4a9041c385d->getControlPrototype()->src = 'http://src';
	\$field768879750d6dff186474d4a9041c385d->getControlPrototype()->alt = 'alternative text';
	\$field768879750d6dff186474d4a9041c385d->setOption(\"description\", 'some description text');
	\$field768879750d6dff186474d4a9041c385d->getControlPrototype()->addAttributes(['attr1' => 'value1', 'attr2' => 'value2']);
	\$field768879750d6dff186474d4a9041c385d->setValidationScope(false);
}";

		$this->createTestObjectWithParams($name, $config);

		$this->expectDependencyCall('namingContainer', 'isParameter', array('someLabel'), FALSE);
		$this->expectDependencyCall('namingContainer', 'isParameter', array('key01'), FALSE);
		$this->expectDependencyCall('namingContainer', 'isParameter', array('Some promt text'), FALSE);
		$this->expectDependencyCall('namingContainer', 'isParameter', array('Add value'), FALSE);
		$this->expectDependencyCall('namingContainer', 'isParameter', array('http://src'), FALSE);
		$this->expectDependencyCall('namingContainer', 'isParameter', array('alternative text'), FALSE);
		$this->expectDependencyCall('namingContainer', 'isParameter', array('some description text'), FALSE);

		$this->getTestObject()->generateFieldInitialization($builder);

		$this->assertEquals($expectedResult, (string) $builder);
	}

	public function testgenerateFieldRestraint()
	{
		$name = 'anyName';
		$config = array(
				'conditions' => array(
						array('condition1'),
						array('condition2'),
				),
				'rules' => array(
						array('rule1'),
						array('rule2'),
				),
		);
		$conditionGeneratorMock = $this->getMockedClass('\Arron\FormBuilder\FieldConditionGenerator', 'conditionGenerator');
		$ruleGeneratorMock = $this->getMockedClass('\Arron\FormBuilder\FieldRuleGenerator', 'ruleGenerator');
		$builder = new Method('foo');

		$this->createTestObjectWithParams($name, $config);
		$namingContainerMock = $this->namingContainerMock;

		$this->expectDependencyCall('generatorFactory', 'createConditionGenerator', array(array('condition1'), $namingContainerMock), $conditionGeneratorMock);
		$this->expectDependencyCall('conditionGenerator', 'generate', array($builder, 'fieldfae31f778844f6dab6a66e0b332c198f'));
		$this->expectDependencyCall('generatorFactory', 'createConditionGenerator', array(array('condition2'), $namingContainerMock), $conditionGeneratorMock);
		$this->expectDependencyCall('conditionGenerator', 'generate', array($builder, 'fieldfae31f778844f6dab6a66e0b332c198f'));

		$this->expectDependencyCall('generatorFactory', 'createRuleGenerator', array(array('rule1'), $namingContainerMock), $ruleGeneratorMock);
		$this->expectDependencyCall('ruleGenerator', 'generate', array($builder, 'fieldfae31f778844f6dab6a66e0b332c198f'));
		$this->expectDependencyCall('generatorFactory', 'createRuleGenerator', array(array('rule2'), $namingContainerMock), $ruleGeneratorMock);
		$this->expectDependencyCall('ruleGenerator', 'generate', array($builder, 'fieldfae31f778844f6dab6a66e0b332c198f'));

		$this->getTestObject()->generateFieldRestraint($builder);
	}
}