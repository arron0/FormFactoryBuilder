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

use Nette\PhpGenerator\Helpers;
use Nette\PhpGenerator\Method;
use Nette\PhpGenerator\PhpLiteral;

/**
 * FieldRuleGenerator class definition
 *
 * @package
 * @subpackage
 * @author Tomáš Lembacher <tomas.lembacher@seznam.cz>
 * @license
 */
class FieldRuleGenerator extends GeneratorBase
{
	public function generate(Method $builder, $rulesBaseVariable)
	{
		$builder->addBody('$?->addRule(\Nette\Forms\Form::?, ?, ?);', array($rulesBaseVariable, $this->getOperationName(), $this->getMessage(), $this->getValue()));
	}

	protected function getOperationName()
	{
		return strtoupper($this->getConfig('type'));
	}

	protected function getMessage()
	{
		return $this->getConfig('msg');
	}

	/**
	 * @return mixed|PhpLiteral|NULL
	 */
	protected function getValue()
	{
		$value = $this->getConfig('value');
		if (is_array($value)) {
			$valuesObjects = array();
			foreach ($value as $separatedValue) {
				if ($this->namingStorage->isParameter($separatedValue)) {
					$valuesObjects[] = new PhpLiteral('$' . $this->namingStorage->getParameterVariable($separatedValue));
				} elseif ($this->isFieldName($separatedValue)) {
					$valuesObjects[] = new PhpLiteral('$' . $this->getFieldVariable($separatedValue));
				} else {
					$valuesObjects[] = $separatedValue;
				}
			}

			$pattern = array_reduce(
					$valuesObjects, function ($result, $item) {
						if ($result === '') {
							return '?';
						} else {
							return $result . ', ?';
						}
					}, ''
			);
			$values = array_reduce(
					$valuesObjects, function ($result, $item) {
						$result[] = $item;
						return $result;
					}, array()
			);

			$generatedValue = Helpers::formatArgs('array(' . $pattern . ')', $values);

			return new PhpLiteral($generatedValue);
		} else {
			if ($this->isFieldName($value)) {
				return new PhpLiteral('$' . $this->namingStorage->getFieldVariable($value));
			} else {
				return $value;
			}
		}
	}
}
 