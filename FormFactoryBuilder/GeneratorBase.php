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

use Nette\PhpGenerator\PhpLiteral;

/**
 * GeneratorBase class definition
 *
 * @package
 * @subpackage
 * @author Tomáš Lembacher <tomas.lembacher@seznam.cz>
 * @license
 */
class GeneratorBase
{

	/**
	 * @var array
	 */
	protected $config = array();

	/**
	 * @var VariableNamingContainer
	 */
	protected $namingStorage;

	/**
	 * @var GeneratorFactory
	 */
	private $generatorFactory;

	public function __construct(array $config, $namingContainer, $generatorFactory)
	{
		$this->config = $config;
		$this->namingStorage = $namingContainer;
		$this->generatorFactory = $generatorFactory;
	}

	protected function getUniqueId()
	{
		return md5(serialize($this->config));
	}

	/**
	 * @param string $prefix
	 *
	 * @return string
	 */
	protected function generateVariableName($prefix = '')
	{
		return $prefix . $this->getUniqueId();
	}

	protected function getNamingContainer()
	{
		return $this->namingStorage;
	}

	protected function getGeneratorFactory()
	{
		return $this->generatorFactory;
	}

	/**
	 * @param string $key
	 * @param mixed $defaultValue
	 *
	 * @return mixed|NULL
	 */
	public function getConfig($key, $defaultValue = NULL)
	{
		if (isset($this->config[$key])) {
			$value = $this->config[$key];
			if (is_string($value) && $this->getNamingContainer()->isParameter($value)) {
				return new PhpLiteral('$' . $this->getNamingContainer()->getParameterVariable($value));
			}

			return $value;
		}
		return $defaultValue;
	}

	/**
	 * @return array
	 */
	protected function getConfigArray()
	{
		return $this->config;
	}

	protected function getFieldVariable($name)
	{
		return $this->getNamingContainer()->getFieldVariable($name);
	}

	protected function isFieldName($name)
	{
		return $this->getNamingContainer()->isFieldName($name);
	}

	protected function addContainerVariable($name, $variable)
	{
		$this->getNamingContainer()->addContainer($name, $variable);
	}

	protected function addFieldVariable($name, $variable)
	{
		$this->getNamingContainer()->addField($name, $variable);
	}

	protected function createRuleGenerator($rule)
	{
		return $this->getGeneratorFactory()->createRuleGenerator($rule, $this->getNamingContainer());
	}

	protected function createConditionGenerator($condition)
	{
		return $this->getGeneratorFactory()->createConditionGenerator($condition, $this->getNamingContainer());
	}
}
 