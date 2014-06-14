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
 * FieldConditionGenerator class definition
 *
 * @package
 * @subpackage
 * @author Tomáš Lembacher <tomas.lembacher@seznam.cz>
 * @license
 */
class FieldConditionGenerator extends GeneratorBase
{

	/**
	 * @var string
	 */
	private $variable;

	/**
	 * @return string
	 */
	protected function getConditionVariableName()
	{
		if (is_null($this->variable)) {
			$this->variable = $this->generateVariableName('condition');
		}
		return $this->variable;
	}

	/**
	 * @param Method $builder
	 * @param string $conditionBaseVariable
	 */
	public function generate(Method $builder, $conditionBaseVariable)
	{
		$conditionVariable = $this->getConditionVariableName();
		$this->generateBaseCondition($builder, $conditionVariable, $conditionBaseVariable);
		$this->generateSubConditions($builder);
		$this->generateRules($builder);
		$this->generateEndCondition($builder);
	}

	/**
	 * @param Method $builder
	 * @param string $conditionVariableName
	 * @param string $conditionBaseVariable
	 */
	protected function generateBaseCondition(Method $builder, $conditionVariableName, $conditionBaseVariable)
	{
		if ($this->isConditionOn()) {
			$pattern = '$? = $?->addConditionOn($?, \Nette\Forms\Form::?, ?);';
			$values = array($conditionVariableName, $conditionBaseVariable, $this->namingStorage->getFieldVariable($this->getConditionOnField()), $this->getOperationName(), $this->getValue());
		} else {
			$pattern = '$? = $?->addCondition(\Nette\Forms\Form::?, ?);';
			$values = array($conditionVariableName, $conditionBaseVariable, $this->getOperationName(), $this->getValue());
		}

		$builder->addBody($pattern, $values);
	}

	/**
	 * @param Method $builder
	 */
	protected function generateSubConditions(Method $builder)
	{
		$subConditions = $this->getConfig('conditions', array());

		foreach ($subConditions as $condition) {
			$generator = $this->createConditionGenerator($condition);
			$generator->generate($builder, $this->getConditionVariableName());
		}
	}

	/**
	 * @param Method $builder
	 */
	protected function generateRules(Method $builder)
	{
		$rules = $this->getConfig('rules', array());
		foreach ($rules as $rule) {
			$ruleGenerator = $this->createRuleGenerator($rule);
			$ruleGenerator->generate($builder, $this->getConditionVariableName());
		}
	}

	/**
	 * @param Method $builder
	 */
	protected function generateEndCondition(Method $builder)
	{
		$builder->addBody('$?->endCondition();', array($this->getConditionVariableName()));
	}

	/**
	 * @return string
	 *
	 * @throws \LogicException
	 */
	protected function getOperationName()
	{
		$operation = $this->getConfig('operation');

		if (is_null($operation)) {
			throw new \LogicException ('In condition field \'operation\' has to be set.');
		}

		return strtoupper($operation);
	}

	/**
	 * @return bool
	 */
	protected function isConditionOn()
	{
		return !is_null($this->getConfig('field'));
	}

	/**
	 * @return mixed|NULL
	 */
	protected function getConditionOnField()
	{
		return $this->getConfig('field');
	}

	/**
	 * @return mixed|NULL
	 */
	protected function getValue()
	{
		return $this->getConfig('value');
	}
}
 