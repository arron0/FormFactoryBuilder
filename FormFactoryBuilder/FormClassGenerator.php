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

use Nette\PhpGenerator\ClassType;
use Nette\PhpGenerator\Helpers;
use Nette\PhpGenerator\Method;
use Nette\PhpGenerator\PhpLiteral;

/**
 * FormClassGenerator class definition
 *
 * @package
 * @subpackage
 * @author Tomáš Lembacher <tomas.lembacher@seznam.cz>
 * @license
 */
class FormClassGenerator extends GeneratorBase
{

	/**
	 * @var string
	 */
	private $formVariable;

	/**
	 * @var array
	 */
	protected $groups = array();

	/**
	 * @var array
	 */
	protected $containers = array();

	/**
	 * @var array
	 */
	protected $fields = array();

	/**
	 * @return string
	 */
	protected function getFormVariable()
	{
		if (!$this->formVariable) {
			$this->formVariable = $this->generateVariableName('form');
		}
		return $this->formVariable;
	}

	/**
	 * @param FormFieldGenerator $generator
	 */
	public function addField(FormFieldGenerator $generator)
	{
		$this->addContainer($generator->getContainer());
		$this->fields[] = $generator;
	}

	/**
	 * @return array
	 */
	protected function getFields()
	{
		return $this->fields;
	}

	/**
	 * @param string $name
	 */
	protected function addContainer($name)
	{
		if (is_null($name)) {
			return;
		}
		if (isset($this->containers[$name])) {
			return;
		}
		$this->containers[$name] = TRUE;
	}

	/**
	 * @return array
	 */
	protected function getContainers()
	{
		return array_keys($this->containers);
	}

	/**
	 * @param Method $method
	 */
	protected function generateNewForm(Method $method)
	{
		$formType = $this->getConfig('type');
		if (is_null($formType)) {
			throw new \InvalidArgumentException("Form has to have 'type' specifyed.");
		}
		$method->addBody('$? = new ?();', array($this->getFormVariable(), new PhpLiteral($formType)));
	}

	protected function generateFormParameters(Method $method)
	{
		if (!is_null($formMethod = $this->getConfig('method'))) {
			$method->addBody('$?->setMethod(?);', array($this->getFormVariable(), $formMethod));
		}

		if (!is_null($class = $this->getConfig('class'))) {
			$method->addBody('$?->getElementPrototype()->setClass(?);', array($this->getFormVariable(), $class));
		}

		if ($this->getConfig('protection', TRUE)) {
			$protectionMsg = $this->getConfig('protectionMsg', "We are sorry, but form times up. Please send it one more time.");
			$method->addBody('$?->addProtection(?);', array($this->getFormVariable(), $protectionMsg));
		}
	}

	/**
	 * @param Method $builder
	 */
	protected function generateGroups(Method $builder)
	{
		$groups = $this->getConfig('groups');
		$groups = is_null($groups) ? array() : $groups;

		foreach ($groups as $groupName => $groupFields) {
			$groupVariable = 'group' . md5($groupName);
			$builder->addBody('$? = $?->addGroup(?, FALSE);', array($groupVariable, $this->getFormVariable(), $groupName));
			$this->groups[$groupVariable] = $groupFields;
		}
		$builder->addBody('$noGroup = NULL;');
	}

	/**
	 * @param Method $builder
	 */
	protected function generateContainers(Method $builder)
	{
		$containers = $this->getContainers();

		foreach ($containers as $containerName) {
			$containerVariable = 'container' . md5($containerName);
			$builder->addBody('$? = $?->addContainer(?);', array($containerVariable, $this->getFormVariable(), $containerName));
			$this->addContainerVariable($containerName, $containerVariable);
		}
	}

	/**
	 * @param $fieldName
	 *
	 * @return string
	 */
	protected function getFieldGroupVariable($fieldName)
	{
		foreach ($this->groups as $variable => $fields) {
			if (in_array($fieldName, $fields)) {
				return $variable;
			}
		}
		return 'noGroup';
	}

	/**
	 * @param $containerName
	 *
	 * @return string
	 */
	protected function getContainerVariable($containerName)
	{
		try {
			return $this->getNamingContainer()->getContainerVariable($containerName);
		} catch (\InvalidArgumentException $e) {
			return $this->getFormVariable();
		}
	}

	/**
	 * @param Method $builder
	 */
	public function generate(Method $builder)
	{
		$currentGroup = NULL;

		$this->generateNewForm($builder);
		$this->generateFormParameters($builder);
		$this->generateGroups($builder);
		$this->generateContainers($builder);

		$fieldsGenerators = $this->getFields();

		foreach ($fieldsGenerators as $fieldGenerator) {
			$fieldGroup = $this->getFieldGroupVariable($fieldGenerator->getName());
			if ($fieldGroup != $currentGroup) {
				$builder->addBody('$?->setCurrentGroup($?);', array($this->getFormVariable(), $fieldGroup));
				$currentGroup = $fieldGroup;
			}
			$fieldContainer = $this->getContainerVariable($fieldGenerator->getContainer());
			$fieldGenerator->generateFieldCreation($builder, $fieldContainer);
		}
		foreach ($fieldsGenerators as $fieldGenerator) {
			$fieldGenerator->generateFieldInitialization($builder);
		}
		foreach ($fieldsGenerators as $fieldGenerator) {
			$fieldGenerator->generateFieldRestraint($builder);
		}
		$builder->addBody('return $?;', array($this->getFormVariable()));
	}
}
 