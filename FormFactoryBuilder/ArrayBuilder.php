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

/**
 * ArrayBuilder class definition
 *
 * @package
 * @subpackage
 * @author Tomáš Lembacher <tomas.lembacher@seznam.cz>
 * @license
 */
class ArrayBuilder implements IFormBuilder
{

	/**
	 * @var GeneratorFactory
	 */
	private $generatorFactory;

	public function __construct($generatorFactory)
	{
		$this->generatorFactory = $generatorFactory;
	}

	public function getDependencies()
	{
		return array();
	}

	protected function getGeneratorFactory()
	{
		return $this->generatorFactory;
	}

	/**
	 * @return VariableNamingContainer
	 */
	protected function createNamingContainer()
	{
		return $this->getGeneratorFactory()->createNamingContainer();
	}

	protected function createFormFactoryGenerator($className, array $config, VariableNamingContainer $namingContainer)
	{
		return $this->getGeneratorFactory()->createFormFactoryGenerator($className, $config, $namingContainer);
	}

	/**
	 * @param string $className
	 * @param mixed $config
	 *
	 * @return string
	 */
	public function create($className, $config)
	{
		if (!is_array($config)) {
			throw new \InvalidArgumentException('Configuration has to be array, ' . gettype($config) . ' given.');
		}
		$namingStorage = $this->createNamingContainer();
		$formFactoryGenerator = $this->createFormFactoryGenerator($className, $config, $namingStorage);

		$builder = $formFactoryGenerator->generate();

		return (string) $builder;
	}
}
 