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
 * VariableNamingContainer class definition
 *
 * @package
 * @subpackage
 * @author Tomáš Lembacher <tomas.lembacher@seznam.cz>
 * @license
 */
class VariableNamingContainer
{

	/**
	 * @var array
	 */
	private $containers = array();

	/**
	 * @var array
	 */
	private $fields = array();

	/**
	 * @var array
	 */
	private $parameters = array();

	/**
	 * @param string $name
	 * @param string $variableName
	 */
	public function addContainer($name, $variableName)
	{
		if (isset($this->containers[$name])) {
			return;
		}
		$this->containers[$name] = $variableName;
	}

	/**
	 * @param $name
	 *
	 * @return string
	 *
	 * @throws \InvalidArgumentException
	 */
	public function getContainerVariable($name)
	{
		if (isset($this->containers[$name])) {
			return $this->containers[$name];
		}
		throw new \InvalidArgumentException("Container '$name' does not exist.'");
	}

	public function addField($name, $variableName)
	{
		$this->fields[$name] = $variableName;
	}

	/**
	 * @param $name
	 *
	 * @return string
	 *
	 * @throws \InvalidArgumentException
	 */
	public function getFieldVariable($name)
	{
		if ($this->hasFieldVariable($name)) {
			return $this->fields[$name];
		}
		throw new \InvalidArgumentException("Field '$name' does not exist.'");
	}

	/**
	 * @param string $name
	 *
	 * @return bool
	 */
	public function hasFieldVariable($name)
	{
		return isset($this->fields[$name]);
	}

	public function isFieldName($name)
	{
		return $this->hasFieldVariable($name);
	}

	/**
	 * @param string $name
	 * @param string $variableName
	 */
	public function addParameter($name, $variableName)
	{
		if (isset($this->parameters[$name])) {
			return;
		}
		$this->parameters[$name] = $variableName;
	}

	/**
	 * @param string $name
	 *
	 * @return string
	 *
	 * @throws \InvalidArgumentException
	 */
	public function getParameterVariable($name)
	{
		if ($this->isParameter($name)) {
			return $this->parameters[$name];
		}
		throw new \InvalidArgumentException("Parameter '$name' does not exist.'");
	}

	/**
	 * @param string $name
	 *
	 * @return bool
	 */
	public function isParameter($name)
	{
		return isset($this->parameters[$name]);
	}
}
 