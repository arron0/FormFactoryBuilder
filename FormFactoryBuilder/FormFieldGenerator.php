<?php
/**
 * Requires PHP Version 5.3 (min)
 *
 * @package
 * @subpackage
 * @author Tomáš Lembacher <tomas.lembacher@seznam.cz>
 * @license
 */

namespace Arron\FormBuilder;

use Nette\PhpGenerator\Method;

/**
 * FormFieldGenerator class definition
 *
 * @package
 * @subpackage
 * @author Tomáš Lembacher <tomas.lembacher@seznam.cz>
 * @license
 */
class FormFieldGenerator extends GeneratorBase
{

	/**
	 * @var string
	 */
	protected $containerName;

	/**
	 * @var string
	 */
	protected $name;

	/**
	 * @var array
	 */
	public static $registeredWrappers = array(
			'text' => '\Nette\Forms\Controls\TextInput',
			'password' => '\Nette\Forms\Controls\TextInput',
			'textarea' => '\Nette\Forms\Controls\TextArea',
			'select' => '\Nette\Forms\Controls\SelectBox',
			'radioList' => '\Nette\Forms\Controls\RadioList',
			'multiSelect' => '\Nette\Forms\Controls\MultiSelectBox',
			'hidden' => '\Nette\Forms\Controls\HiddenField',
			'checkboxList' => '\Nette\Forms\Controls\CheckboxList',
			'checkbox' => '\Nette\Forms\Controls\Checkbox',
			'imageButton' => '\Nette\Forms\Controls\ImageButton',
			'submit' => '\Nette\Forms\Controls\SubmitButton',
			'button' => '\Nette\Forms\Controls\Button',
			'customClass' => '',
	);

	/**
	 * @param string $name
	 * @param array $config
	 * @param VariableNamingContainer $namingStorage
	 * @param GeneratorFactory $generatorFactory
	 */
	public function __construct($name, array $config, VariableNamingContainer $namingStorage, GeneratorFactory $generatorFactory)
	{
		parent::__construct($config, $namingStorage, $generatorFactory);
		list($this->containerName, $this->name) = $this->parseControlName($name);
	}

	/**
	 * @param $name
	 *
	 * @return array
	 */
	private function parseControlName($name)
	{
		if (strpos($name, '-') !== FALSE) {
			return explode('-', $name, 2);
		}
		return array(NULL, $name);
	}

	/**
	 * @return string
	 */
	public function getName()
	{
		return $this->name;
	}

	/**
	 * @return string
	 */
	public function getFullName()
	{
		$container = $this->getContainer();
		if (is_null($container)) {
			return $this->getName();
		}
		return $container . '-' . $this->getName();
	}

	/**
	 * @return string
	 */
	public function getVariableName()
	{
		return $this->generateVariableName('field');
	}

	/**
	 * @return string
	 */
	public function getContainer()
	{
		return $this->containerName;
	}

	/**
	 * @return string
	 */
	protected function getType()
	{
		$type = $this->getConfig('type');
		if (is_null($type)) {
			throw new \InvalidArgumentException("Field {$this->getFullName()} has to have 'type' defined.");
		}
		return $type;
	}

	/**
	 * @return string
	 */
	protected function getClass()
	{
		$type = $this->getConfig('class');
		if (is_null($type)) {
			throw new \InvalidArgumentException("Field {$this->getFullName()} has to have 'class' defined.");
		}
		return $type;
	}

	/**
	 * @param Method $builder
	 * @param string $formPointer
	 */
	public function generateFieldCreation(Method $builder, $formPointer)
	{
		$type = $this->getType();

		if (!isset(self::$registeredWrappers[$type])) {
			throw new \InvalidArgumentException("Form control '{$this->getName()}' has unknown type '$type'.'");
		}

		$inputClassName = self::$registeredWrappers[$type];
		if ($type === 'customClass'){
		    $inputClassName = $this->getClass();
        }
		$builder->addBody('$? = $?[?] = new ' . $inputClassName . ';', array($this->getVariableName(), $formPointer, $this->getName()));
		if ($type === 'password') {
			$builder->addBody("$?->setType('password');", array($this->getVariableName()));
		}
	}

	/**
	 * @param Method $builder
	 */
	public function generateFieldInitialization(Method $builder)
	{
		if (!is_null($label = $this->getConfig('label'))) {
			$builder->addBody('$?->caption = ?;', array($this->getVariableName(), $label));
		}
		if (!is_null($items = $this->getConfig('items'))) {
			$builder->addBody('$?->setItems(?);', array($this->getVariableName(), $items));
		}
		if (!is_null($selected = $this->getConfig('selected'))) {
			$builder->addBody('$?->setDefaultValue(?);', array($this->getVariableName(), $selected));
		}
		if (!is_null($prompt = $this->getConfig('prompt'))) {
			$builder->addBody('$?->setPrompt(?);', array($this->getVariableName(), $prompt));
		}
		if (!is_null($value = $this->getConfig('value'))) {
			$builder->addBody('$?->setDefaultValue(?);', array($this->getVariableName(), $value));
		}
		if (!is_null($emptyValue = $this->getConfig('emptyValue'))) {
			$builder->addBody('$?->setEmptyValue(?);', array($this->getVariableName(), $emptyValue));
		}
		if (!is_null($src = $this->getConfig('src'))) {
			$builder->addBody('$?->getControlPrototype()->src = ?;', array($this->getVariableName(), $src));
		}
		if (!is_null($alt = $this->getConfig('alt'))) {
			$builder->addBody('$?->getControlPrototype()->alt = ?;', array($this->getVariableName(), $alt));
		}
	}

	/**
	 * @param Method $builder
	 */
	public function generateFieldRestraint(Method $builder)
	{
		$conditions = $this->getConfig('conditions', array());
		$this->generateConditions($builder, $conditions);

		$rules = $this->getConfig('rules', array());
		$this->generateRules($builder, $rules);
		/*if (!is_null($required = $this->getConfig('required'))) {
			if ($required !== FALSE) {
				$msg = $required === TRUE ? NULL : $required;
				$builder->addBody('$?->addRule(\Nette\Forms\Form::REQUIRED, ?);', array($this->getVariableName(), $msg));
			}

		}*/
	}

	/**
	 * @param Method $builder
	 * @param array $conditions
	 */
	protected function generateConditions(Method $builder, $conditions)
	{
		foreach ($conditions as $condition) {
			$generator = $this->createConditionGenerator($condition);
			$generator->generate($builder, $this->getVariableName());
		}
	}

	/**
	 * @param Method $builder
	 * @param array $rules
	 */
	protected function generateRules(Method $builder, array $rules)
	{
		foreach ($rules as $rule) {
			$generator = $this->createRuleGenerator($rule);
			$generator->generate($builder, $this->getVariableName());
		}
	}
}
 