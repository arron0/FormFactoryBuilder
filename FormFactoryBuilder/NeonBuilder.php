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

use Nette\Neon\Decoder;
use Nette\Neon\Entity;

/**
 * NeonBuilder class definition
 *
 * @package
 * @subpackage
 * @author Tomáš Lembacher <tomas.lembacher@seznam.cz>
 * @license
 */
class NeonBuilder implements IFormBuilder
{

	/**
	 * @var IFormBuilder
	 */
	private $builder;

	/**
	 * @var Decoder
	 */
	private $neonDecoder;

	private $dependencies = array();

	/**
	 * @param IFormBuilder $builder
	 */
	public function __construct(IFormBuilder $builder, Decoder $neonDecoder)
	{
		$this->builder = $builder;
		$this->neonDecoder = $neonDecoder;
	}

	public function getDependencies()
	{
		return $this->dependencies;
	}

	/**
	 * @return IFormBuilder
	 */
	protected function getBuilder()
	{
		return $this->builder;
	}

	protected function neonToArray($neon)
	{
		return (array) $this->neonDecoder->decode($neon);
	}

	/**
	 * @param string $className
	 * @param mixed $config
	 *
	 * @return string
	 */
	public function create($className, $config)
	{
		$config = realpath((string) $config);

		if (!file_exists($config)) {
			throw new \InvalidArgumentException("Config file $config does not exist.");
		}

		$this->dependencies[] = $config;

		$arrayConfig = $this->neonToArray(file_get_contents($config));
		$arrayConfig = $this->changeNeonEntitiesToArrays($arrayConfig);

		return $this->getBuilder()->create($className, $arrayConfig);
	}

	protected function changeNeonEntitiesToArrays($array)
	{
		array_walk_recursive(
				$array, function (&$item, $key) {
					if ($item instanceof Entity) {
						$arrayItem = array();
						$arrayItem['type'] = $item->value;
						foreach ($item->attributes as $key => $value) {
							$arrayItem[$key] = $value;
						}
						$item = $arrayItem;
					}
				}
		);
		return $array;
	}
}
 