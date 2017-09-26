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

use Arron\FormBuilder\VariableNamingContainer;
use Arron\TestIt\TestCase;

/**
 * VariableNamingContainerUnitTest class definition
 *
 * @package Cms
 * @subpackage Tests
 * @author Tomáš Lembacher <tomas.lembacher@seznam.cz>
 * @license
 */
class VariableNamingContainerUnitTest extends TestCase
{
	/**
	 * @return object
	 */
	protected function createTestObject()
	{
		return new VariableNamingContainer();
	}

	public function testAddContainer()
	{
		$name = 'anyName';
		$variableName = 'someVariableName';
		$expectedResult = array($name => $variableName);

		$this->getTestObject()->addContainer($name, $variableName);

		$this->assertEquals($expectedResult, $this->getPropertyFromTestSubject('containers'));
	}

	public function testAddContainerWithAlreadyAddedValue()
	{
		$name = 'anyName';
		$variableName = 'someDifferentValue';
		$expectedResult = array($name => 'someValue');

		$this->setPropertyInTestSubject('containers', array($name => 'someValue'));

		$this->getTestObject()->addContainer($name, $variableName);

		$this->assertEquals($expectedResult, $this->getPropertyFromTestSubject('containers'));
	}

	public function testGetContainerVariable()
	{
		$name = 'anyName';
		$variableName = 'someValue';
		$expectedResult = $variableName;

		$this->setPropertyInTestSubject('containers', array($name => $variableName));

		$returnedResult = $this->getTestObject()->getContainerVariable($name);

		$this->assertEquals($expectedResult, $returnedResult);
	}

	public function testGetContainerVariableNotExisting()
	{
		$this->expectException('\InvalidArgumentException');

		$this->getTestObject()->getContainerVariable('notExistingName');
	}

	public function testAddField()
	{
		$name = 'anyName';
		$variableName = 'someValue';
		$expectedResult = array($name => $variableName);

		$this->getTestObject()->addField($name, $variableName);

		$this->assertEquals($expectedResult, $this->getPropertyFromTestSubject('fields'));
	}

	public function testAddFieldRewrittenValue()
	{
		$name = 'anyName';
		$variableName = 'someOtherValue';
		$expectedResult = array($name => $variableName);

		$this->setPropertyInTestSubject('fields', array($name => 'someValue'));

		$this->getTestObject()->addField($name, $variableName);

		$this->assertEquals($expectedResult, $this->getPropertyFromTestSubject('fields'));
	}

	public function testGetFieldVariable()
	{
		$name = 'anyName';
		$variableName = 'someValue';
		$expectedResult = $variableName;

		$this->setPropertyInTestSubject('fields', array($name => $variableName));

		$returnedResult = $this->getTestObject()->getFieldVariable($name);

		$this->assertEquals($expectedResult, $returnedResult);
	}

	public function testGetFieldVariableNotExisting()
	{
		$this->expectException('\InvalidArgumentException');

		$this->getTestObject()->getFieldVariable('notExistingName');
	}

	public function testIsFieldVariableExisting()
	{
		$name = 'anyName';
		$variableName = 'someValue';
		$this->setPropertyInTestSubject('fields', array($name => $variableName));

		$this->assertTrue($this->getTestObject()->isFieldName($name));
	}

	public function testIsFieldVariableNotExisting()
	{
		$name = 'anyName';

		$this->assertFalse($this->getTestObject()->isFieldName($name));
	}

	public function testAddParameter()
	{
		$name = 'anyName';
		$variableName = 'someVariableName';
		$expectedResult = array($name => $variableName);

		$this->getTestObject()->addParameter($name, $variableName);

		$this->assertEquals($expectedResult, $this->getPropertyFromTestSubject('parameters'));
	}

	public function testAddParameterWithAlreadyAddedValue()
	{
		$name = 'anyName';
		$variableName = 'someDifferentValue';
		$expectedResult = array($name => 'someValue');

		$this->setPropertyInTestSubject('parameters', array($name => 'someValue'));

		$this->getTestObject()->addParameter($name, $variableName);

		$this->assertEquals($expectedResult, $this->getPropertyFromTestSubject('parameters'));
	}

	public function testGetParameterVariable()
	{
		$name = 'anyName';
		$variableName = 'someValue';
		$expectedResult = $variableName;

		$this->setPropertyInTestSubject('parameters', array($name => $variableName));

		$returnedResult = $this->getTestObject()->getParameterVariable($name);

		$this->assertEquals($expectedResult, $returnedResult);
	}

	public function testGetParameterVariableNotExisting()
	{
		$this->expectException('\InvalidArgumentException');

		$this->getTestObject()->getParameterVariable('notExistingName');
	}

	public function testIsParameterExisting()
	{
		$name = 'anyName';
		$variableName = 'someValue';
		$this->setPropertyInTestSubject('parameters', array($name => $variableName));

		$this->assertTrue($this->getTestObject()->isParameter($name));
	}

	public function testIsParameterNotExisting()
	{
		$name = 'anyName';

		$this->assertFalse($this->getTestObject()->isParameter($name));
	}
}