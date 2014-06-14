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

use Arron\FormBuilder\NeonBuilder;
use Arron\TestIt\TestCase;
use Nette\Neon\Entity;

/**
 * NeonBuilderUnitTest class definition
 *
 * @package Cms
 * @subpackage Tests
 * @author Tomáš Lembacher <tomas.lembacher@seznam.cz>
 * @license
 */

class NeonBuilderUnitTest extends TestCase
{
	protected function setUp()
	{
		parent::setUp();

		$this->mockGlobalFunction('realpath', 'Arron\FormBuilder');
		$this->mockGlobalFunction('file_exists', 'Arron\FormBuilder');
		$this->mockGlobalFunction('file_get_contents', 'Arron\FormBuilder');
	}

	/**
	 * @return object
	 */
	protected function createTestObject()
	{
		return new NeonBuilder($this->getMockedClass('\Arron\FormBuilder\IFormBuilder', 'formBuilder'), $this->getMockedClass('\Nette\Neon\Decoder', 'neonDecoder'));
	}

	public function testCreate()
	{
		$className = 'TestClassName';
		$configFile = 'testConfig.neon';
		$configFileRealPath = 'foo/testConfig.neon';
		$neonSource = 'some neon source file';

		$decodedNeon = array(
				"container3-textInputName" => array(
						'type' => "text",
						'label' => "some label",
						'value' => "param3",
						'conditions' => array(
								0 => array(
										'operation' => "max_length",
										'value' => 5,
										'rules' => array(
											0 => new Entity('someValue', array('art1' => 'val1', 'atr2' => 'val2')),
											1 => new Entity('someValue2', array('art3' => 'val3', 'atr4' => 'val4')),
										),
								),
								1 => array(
										'operation' => "pattern",
										'value' => ".*[0-9].*",
										'rules' => array(
												0 => new Entity('someValue3', array('art5' => 'val5', 'atr6' => 'val6')),
												1 => new Entity('someValue4', array('art7' => 'val7', 'atr8' => 'val8')),
										),
								),
						),
				),
		);

		$fixedDecodedNeon = array(
				"container3-textInputName" => array(
						'type' => "text",
						'label' => "some label",
						'value' => "param3",
						'conditions' => array(
								0 => array(
										'operation' => "max_length",
										'value' => 5,
										'rules' => array(
												array(
														'type' => 'someValue',
														'art1' => 'val1',
														'atr2' => 'val2'
												),
												array(
														'type' => 'someValue2',
														'art3' => 'val3',
														'atr4' => 'val4'
												),
										),
								),
								1 => array(
										'operation' => "pattern",
										'value' => ".*[0-9].*",
										'rules' => array(
												array(
														'type' => 'someValue3',
														'art5' => 'val5',
														'atr6' => 'val6'
												),
												array(
														'type' => 'someValue4',
														'art7' => 'val7',
														'atr8' => 'val8'
												),
										),
								),
						),
				),
		);


		$createdSourceCode = 'some generated source code';

		$this->expectDependencyCall('global', 'realpath', array($configFile), $configFileRealPath);
		$this->expectDependencyCall('global', 'file_exists', array($configFileRealPath), TRUE);
		$this->expectDependencyCall('global', 'file_get_contents', array($configFileRealPath), $neonSource);
		$this->expectDependencyCall('neonDecoder', 'decode', array($neonSource), $decodedNeon);
		$this->expectDependencyCall('formBuilder', 'create', array($className, $fixedDecodedNeon), $createdSourceCode);

		$returnedResult = $this->getTestObject()->create($className, $configFile);

		$this->assertSame($createdSourceCode, $returnedResult);
		$this->assertEquals(array($configFileRealPath), $this->getPropertyFromTestSubject('dependencies'));
	}

	public function testCreateNonExistingConfigFile()
	{
		$className = 'TestClassName';
		$configFile = 'testConfig.neon';

		$this->expectDependencyCall('global', 'realpath', array($configFile), $configFile);
		$this->expectDependencyCall('global', 'file_exists', array($configFile), FALSE);

		$this->setExpectedException('\InvalidArgumentException');

		$this->getTestObject()->create($className, $configFile);
	}

	public function testGetDependencies()
	{
		$dependencies = array('someFile');
		$this->setPropertyInTestSubject('dependencies', $dependencies);

		$returnedResult = $this->getTestObject()->getDependencies();

		$this->assertSame($dependencies, $returnedResult);
	}
}