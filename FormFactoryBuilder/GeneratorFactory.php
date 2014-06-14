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

/**
 * GeneratorFactory class definition
 *
 * @package
 * @subpackage
 * @author Tomáš Lembacher <tomas.lembacher@seznam.cz>
 * @license
 */
class GeneratorFactory
{
	/**
	 * @param string $name
	 * @param array $config
	 * @param VariableNamingContainer $namingContainer
	 *
	 * @return FormFieldGenerator
	 */
	public function createFieldGenerator($name, array $config, VariableNamingContainer $namingContainer)
	{
		return new FormFieldGenerator($name, $config, $namingContainer, $this);
	}

	/**
	 * @param $rule
	 * @param VariableNamingContainer $namingContainer
	 *
	 * @return FieldRuleGenerator
	 */
	public function createRuleGenerator($rule, VariableNamingContainer $namingContainer)
	{
		return new FieldRuleGenerator($rule, $namingContainer, $this);
	}

	/**
	 * @param $condition
	 * @param VariableNamingContainer $namingContainer
	 *
	 * @return FieldConditionGenerator
	 */
	public function createConditionGenerator($condition, VariableNamingContainer $namingContainer)
	{
		return new FieldConditionGenerator($condition, $namingContainer, $this);
	}

	/**
	 * @param array $config
	 * @param VariableNamingContainer $namingContainer
	 *
	 * @return FormClassGenerator
	 */
	public function createClassGenerator(array $config, VariableNamingContainer $namingContainer)
	{
		return new FormClassGenerator($config, $namingContainer, $this);
	}

	/**
	 * @param string $className
	 * @param array $config
	 * @param VariableNamingContainer $namingContainer
	 *
	 * @return FormFactoryGenerator
	 */
	public function createFormFactoryGenerator($className, array $config, VariableNamingContainer $namingContainer)
	{
		return new FormFactoryGenerator($className, $config, $namingContainer, $this);
	}

	/**
	 * @return VariableNamingContainer
	 */
	public function createNamingContainer()
	{
		return new VariableNamingContainer();
	}
}
 