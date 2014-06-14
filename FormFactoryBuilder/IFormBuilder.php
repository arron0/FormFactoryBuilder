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

/**
 * IFormBuilder interface definition
 *
 * @package
 * @subpackage
 * @author Tomáš Lembacher <tomas.lembacher@seznam.cz>
 * @license
 */
interface IFormBuilder
{
	/**
	 * @param string $className
	 * @param mixed $config
	 *
	 * @throws \InvalidArgumentException
	 *
	 * @return string
	 */
	public function create($className, $config);

	/**
	 * @return array
	 */
	public function getDependencies();
} 