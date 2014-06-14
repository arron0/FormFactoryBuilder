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
 * SourceCodeFileCache class definition
 *
 * @package
 * @subpackage
 * @author Tomáš Lembacher <tomas.lembacher@seznam.cz>
 * @license
 */
class SourceCodeFileCache
{

	/**
	 * @var string
	 */
	private $tempDirectory;

	private $autoRebuild = TRUE;

	public function __construct($tempDirectory)
	{
		$this->tempDirectory = $tempDirectory;
	}

	protected function getTempDir()
	{
		if (!is_dir($this->tempDirectory)) {
			umask(0000);
			if (!mkdir($this->tempDirectory, 0777)) {
				throw new \RuntimeException('Can not create directory ' . $this->tempDirectory);
			}
		}
		return $this->tempDirectory;
	}

	protected function getCacheFileName($key)
	{
		return "{$this->getTempDir()}/$key.php";
	}

	protected function addHeader($sourceCode)
	{
		if (strpos($sourceCode, "<?php") !== 0) {
			$sourceCode = "<?php\n" . $sourceCode;
		}
		return $sourceCode;
	}

	protected function requireFile($file)
	{
		require_once $file;
	}

	public function writeAndLoad($key, $sourceCode, array $dependencies)
	{
		$sourceCode = $this->addHeader($sourceCode);
		$handle = fopen($file = $this->getCacheFileName($key), 'c+');
		if (!$handle) {
			throw new \RuntimeException("Unable to open or create file '$file'.");
		}
		flock($handle, LOCK_SH);
		ftruncate($handle, 0);
		flock($handle, LOCK_EX);
		$stat = fstat($handle);
		if (!$stat['size']) {
			if (fwrite($handle, $sourceCode, strlen($sourceCode)) !== strlen($sourceCode)) {
				ftruncate($handle, 0);
				throw new \RuntimeException("Unable to write file '$file'.");
			}

			$tmp = array();
			foreach ($dependencies as $f) {
				$tmp[$f] = @filemtime($f); // @ - stat may fail
			}
			file_put_contents($file . '.meta', serialize($tmp));
		}
		flock($handle, LOCK_SH);
		$this->requireFile($file);
	}

	public function load($key)
	{
		$handle = fopen($file = $this->getCacheFileName($key), 'c+');
		if (!$handle) {
			throw new \RuntimeException("Unable to open or create file '$file'.");
		}
		flock($handle, LOCK_SH);
		$stat = fstat($handle);
		if ($stat['size']) {
			if ($this->autoRebuild) {
				foreach ((array) @unserialize(file_get_contents($file . '.meta')) as $f => $time) { // @ - file may not exist
					if (@filemtime($f) !== $time) { // @ - stat may fail
						return FALSE;
					}
				}
			}
		} else {
			return FALSE;
		}

		$this->requireFile($file);
		return TRUE;
	}
}
 