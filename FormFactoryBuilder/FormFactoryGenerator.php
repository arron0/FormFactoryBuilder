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
use Nette\PhpGenerator\Method;

/**
 * FormFactoryGenerator class definition
 *
 * @package
 * @subpackage
 * @author Tomáš Lembacher <tomas.lembacher@seznam.cz>
 * @license
 */
class FormFactoryGenerator extends GeneratorBase
{

	/**
	 * @var string
	 */
	protected $className;

	/**
	 * @param string $className
	 * @param array $config
	 * @param VariableNamingContainer $namingContainer
	 * @param GeneratorFactory $generatorFactory
	 */
	public function __construct($className, array $config, VariableNamingContainer $namingContainer, GeneratorFactory $generatorFactory)
	{
		parent::__construct($config, $namingContainer, $generatorFactory);
		$this->className = $className;
	}

	/**
	 * @return string
	 */
	public function getClassName()
	{
		return $this->className;
	}

	/**
	 * @return ClassType
	 */
	protected function generateFactoryClass()
	{
		$classBuilder = new ClassType();
		$classBuilder->setName($this->getClassName());
		return $classBuilder;
	}

	/**
	 * @param ClassType $classBuilder
	 *
	 * @return Method
	 */
	protected function createCreateMethod(ClassType $classBuilder)
	{
		$createMethod = $classBuilder->addMethod('create');
		$classBuilder->addImplement('\Arron\FormBuilder\IFormFactory');
		$this->generateDocBlock($createMethod);
		$this->addCreateParameters($createMethod);
		return $createMethod;
	}

	/**
	 * @param Method $method
	 */
	protected function generateDocBlock(Method $method)
	{
		$config = $this->getConfigArray();
		if(isset($config['form']['type'])) {
			$method->addComment("@return {$config['form']['type']}");
		}
	}

	/**
	 * @param Method $method
	 */
	protected function addCreateParameters(Method $method)
	{
		$config = $this->getConfigArray();
		if(isset($config['form']['parameters'])) {
			$parameters = $config['form']['parameters'];

			foreach ($parameters as $key => $value) {
				if (Helpers::isIdentifier($key)) {
					$method->addParameter($key, $value);
					$this->getNamingContainer()->addParameter($key, $key);
				} else {
					$method->addParameter($value);
					$this->getNamingContainer()->addParameter($value, $value);
				}
			}
		}

	}

	/**
	 * @param array $config
	 * @param VariableNamingContainer $namingContainer
	 *
	 * @return FormClassGenerator
	 */
	protected function createClassGenerator(array $config)
	{
		return $this->getGeneratorFactory()->createClassGenerator($config, $this->getNamingContainer());
	}

	/**
	 * @param string $name
	 * @param array $config
	 * @param VariableNamingContainer $namingContainer
	 *
	 * @return FormFieldGenerator
	 */
	protected function createFieldGenerator($name, array $config)
	{
		return $this->getGeneratorFactory()->createFieldGenerator($name, $config, $this->getNamingContainer());
	}

	/**
	 * @return ClassType
	 */
	public function generate()
	{
		$classBuilder = $this->generateFactoryClass();
		$constructor = $this->createCreateMethod($classBuilder);

		$config = $this->getConfigArray();

		if (!isset($config['form'])) {
			throw new \LogicException("There is no 'form' key in you config. There has to be configuration for form itself.");
		}
		$formGenerator = $this->createClassGenerator($config['form']);
		unset($config['form']);

		//create generators
		foreach ($config as $controlName => $controlConfig) {
			$fieldGenerator = $this->createFieldGenerator($controlName, $controlConfig);
			$this->addFieldVariable($controlName, $fieldGenerator->getVariableName());
			$formGenerator->addField($fieldGenerator);
		}
		$formGenerator->generate($constructor);
		return $classBuilder;
	}
}
 