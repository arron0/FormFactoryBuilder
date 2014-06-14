<?php
/**
 * Requires PHP Version 5.3 (min)
 *
 * @package Cms
 * @subpackage Tests
 * @author Tomáš Lembacher <tomas.lembacher@seznam.cz>
 * @license
 */
namespace Cms\Tests {

	use Arron\FormBuilder\CachedLoader;
	use Arron\TestIt\TestCase;

	/**
	 * CachedLoaderUnitTest class definition
	 *
	 * @package Cms
	 * @subpackage Tests
	 * @author Tomáš Lembacher <tomas.lembacher@seznam.cz>
	 * @license
	 */
	class CachedLoaderUnitTest extends TestCase
	{
		/**
		 * @return object
		 */
		protected function createTestObject()
		{
			return new CachedLoader($this->getMockedClass('\Arron\FormBuilder\IFormBuilder', 'formBuilder'), $this->getMockedClass('\Arron\FormBuilder\SourceCodeFileCache', 'cache'));
		}

		public function testLoadSuccesful()
		{
			$this->expectDependencyCall('cache', 'load', array('formBuilder40cd750bba9870f18aada2478b24840a'), TRUE);

			$returnedResult = $this->getTestObject()->load(array());

			$this->assertInstanceOf('\formBuilder40cd750bba9870f18aada2478b24840a', $returnedResult);
		}

		public function testLoadGenerateNew()
		{
			$sourceCode = 'class formBuilderd4745e82aecb406b29b31e88c9ff0e4a {}';
			$config = array(1);
			$dependencies = array('fileName');

			$this->expectDependencyCall('cache', 'load', array('formBuilderd4745e82aecb406b29b31e88c9ff0e4a'), FALSE);
			$this->expectDependencyCall('formBuilder', 'create', array('formBuilderd4745e82aecb406b29b31e88c9ff0e4a', array(1)), $sourceCode);
			$this->expectDependencyCall('formBuilder', 'getDependencies', array(), $dependencies);
			$this->expectDependencyCall('cache', 'writeAndLoad', array('formBuilderd4745e82aecb406b29b31e88c9ff0e4a', $sourceCode, $dependencies));

			$returnedResult = $this->getTestObject()->load(array(1));
		}
	}
}

namespace { //it is declared as global
	class formBuilder40cd750bba9870f18aada2478b24840a
	{

	}

	class formBuilderd4745e82aecb406b29b31e88c9ff0e4a
	{

	}
}