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

use Nette\DI\CompilerExtension;
use Nette\DI\ContainerBuilder;

/**
 * FormBuilderExtension class definition
 *
 * @package
 * @subpackage
 * @author Tomáš Lembacher <tomas.lembacher@seznam.cz>
 * @license
 */
class FormBuilderExtension extends CompilerExtension
{

	protected $defaults = array(
			'tempDir' => '',
	);

	public function loadConfiguration()
	{
		$config = $this->getConfig($this->defaults);
		$builder = $this->getContainerBuilder();
		$this->compiler->parseServices($builder, $this->loadFromFile($this->getDefaultConfigFileName()));

		$this->setupSourceCodeCache($builder, $config);
	}

	protected function setupSourceCodeCache($builder, $config)
	{
		$builder->addDefinition($this->prefix('sourceCodeCache'))
		        ->setClass('\Arron\FormBuilder\SourceCodeFileCache', array($config['tempDir']));
	}

	protected function getDefaultConfigFileName()
	{
		return __DIR__ . '/formBuilder.neon';
	}
}
 