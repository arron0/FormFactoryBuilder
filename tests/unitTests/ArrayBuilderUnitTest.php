<?php
/**
 * Requires PHP Version 5.3 (min)
 *
 * @package Cms
 * @subpackage Tests
 * @author Tomáš Lembacher <tomas.lembacher@seznam.cz>
 * @license
 */
namespace Cms\Tests;

use Arron\FormBuilder\ArrayBuilder;
use Arron\TestIt\TestCase;

/**
 * ArrayBuilderUnitTest class definition
 *
 * @package Cms
 * @subpackage Tests
 * @author Tomáš Lembacher <tomas.lembacher@seznam.cz>
 * @license
 */
class ArrayBuilderUnitTest extends TestCase
{
	/**
	 * @return object
	 */
	protected function createTestObject()
	{
		return new ArrayBuilder($this->getMockedClass('\Arron\FormBuilder\GeneratorFactory', 'generatorFactory'));
	}

	public function testCreate()
	{
		$className = 'someClassName';
		$config = array('data');

		$expectedResult = 'sourceCode';
		$mockedNamingContainer = $this->getMockedClass('\Arron\FormBuilder\VariableNamingContainer', 'namingContainer');
		$mockedFormFactoryGenerator = $this->getMockedClass('\Arron\FormBuilder\FormFactoryGenerator', 'formFactoryGenerator');

		$this->expectDependencyCall('generatorFactory', 'createNamingContainer', array(), $mockedNamingContainer);
		$this->expectDependencyCall('generatorFactory', 'createFormFactoryGenerator', array($className, $config, $mockedNamingContainer), $mockedFormFactoryGenerator);
		$this->expectDependencyCall('formFactoryGenerator', 'generate', array(), $expectedResult);

		$returnedResult = $this->getTestObject()->create($className, $config);

		$this->assertSame($expectedResult, $returnedResult);
	}

	public function testCreateConfigNotArray()
	{
		$className = 'anyName';
		$config = 'some config string';

		$this->setExpectedException('\InvalidArgumentException');

		$this->getTestObject()->create($className, $config);
	}

	public function testGetDependecies()
	{
		$returnedResult = $this->getTestObject()->getDependencies();

		$this->assertEquals(array(), $returnedResult);
	}
}