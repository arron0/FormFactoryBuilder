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
 * CachedLoader class definition
 *
 * @package
 * @subpackage
 * @author Tomáš Lembacher <tomas.lembacher@seznam.cz>
 * @license
 */
class CachedLoader
{

	/**
	 * @var IFormBuilder
	 */
	private $builder;

	/**
	 * @var SourceCodeFileCache
	 */
	private $sourceCodeCache;

	function __construct(IFormBuilder $builder, SourceCodeFileCache $cache)
	{
		$this->builder = $builder;
		$this->sourceCodeCache = $cache;
	}

	/**
	 * @return SourceCodeFileCache
	 */
	public function getSourceCodeCache()
	{
		return $this->sourceCodeCache;
	}

	/**
	 * @return IFormBuilder
	 */
	public function getBuilder()
	{
		return $this->builder;
	}

	/**
	 * @param mixed $config
	 *
	 * @return string
	 */
	protected function createClassName($config)
	{
		return 'formBuilder' . md5(serialize($config));
	}

	/**
	 * @param mixed $config
	 */
	public function load($config)
	{
		$className = $this->createClassName($config);
		if (!$this->getSourceCodeCache()->load($className)) {
			$sourceCode = $this->getBuilder()->create($className, $config);
			$dependencies = $this->getBuilder()->getDependencies();
			$this->getSourceCodeCache()->writeAndLoad($className, $sourceCode, $dependencies);
		}
		return new $className();
	}
}
 