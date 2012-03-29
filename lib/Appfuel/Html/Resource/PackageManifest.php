<?php
/**
 * Appfuel
 * PHP 5.3+ object oriented MVC framework supporting domain driven design. 
 *
 * @package     Appfuel
 * @author      Robert Scott-Buccleuch <rsb.code@gmail.com.com>
 * @copyright   2009-2010 Robert Scott-Buccleuch <rsb.code@gmail.com>
 * @license		http://www.apache.org/licenses/LICENSE-2.0
 */
namespace Appfuel\Html\Resource;

use InvalidArgumentException;

/**
 * A value object used to describe the manifest.json in the package directory
 */
class PackageManifest implements PackageManifestInterface
{
	/**
	 * @var string
	 */
	protected $name = null;

	/**
	 * @var string
	 */
	protected $desc = null;

	/**
	 * Relative path from the package dir to the package files
	 * @var string
	 */
	protected $srcDir = 'src';

	/**
	 * @var FileStackInterface
	 */
	protected $srcFiles = null; 

	/**
	 * @var array
	 */
	protected $srcDepends = array();

	/**
	 * Name of the file used when building js or css files
	 * @var string
	 */
	protected $srcBuildFile = null;

	/**
	 * Relative path from the package dir to the test files
	 * @var string 
	 */
	protected $testDir = 'test';
	
	/**
	 * @var FileStackInterface
	 */
	protected $testFiles = null;

	/**
	 * @var array
	 */
	protected $testDepends = null;

	/**
	 * @param	array $data	
	 * @return	PackageManifest
	 */
	public function __construct(array $data)
	{
		if (! isset($data['name'])) {
			$err = 'package name not found an must exist';
			throw new InvalidArgumentException($err);
		}
		$this->setPackageName($data['name']);

		if (isset($data['desc'])) {
			$this->setPackageDescription($data['desc']);
		}
	
		if (! isset($data['src']) || ! is_array($data['src'])) {
			$err = 'every package must define a source: no src key found';
			throw new InvalidArgumentException($err);
		}
		$this->initSource($data['src']);

		if (isset($data['test']) && is_array($data['test'])) {
			$this->initTest($data['test']);
		}
	}

	/**
	 * @return	string
	 */
	public function getPackageName()
	{
		return $this->name;
	}

	/**
	 * @return	string
	 */
	public function getPackageDescription()
	{
		return $this->desc;
	}

	/**
	 * @return	string
	 */
	public function getSourceDirectory()
	{
		return $this->srcDir;
	}

	/**
	 * @return string
	 */
	public function getSourceBuildFile()
	{
		return $this->srcBuildFile;	
	}

	/**
	 * @return	array
	 */
	public function getSourceTypes()
	{
		return $this->getSourceFileStack()
					->getTypes();
	}

	/**
	 * @return	string
	 */
	public function getAllSourceFiles()
	{
		return $this->getSourceFileStack()
					->getAll();
	}

	/**
	 * @params	string $type 
	 * @return	array|false
	 */
	public function getSourceFiles($type)
	{
		return $this->getSourceFileStack()
					->get($type);
	}

	/**
	 * @return	array
	 */
	public function getSourceDependencies()
	{
		return $this->srcDepends;
	}

	/**
	 * @return	string
	 */
	public function getTestBuildFile()
	{
		return $this->testBuildFile;
	}

	/**
	 * @return	string
	 */
	public function getTestDirectory()
	{
		return $this->testDir;
	}

	/**
	 * @return	array
	 */
	public function getTestFileTypes()
	{
		return $this->getTestFileStack()
					->getTypes();
	}

	/**
	 * @return	array
	 */
	public function getAllTestFiles()
	{
		return $this->getTestFileStack()
					->getAll();
	}

	/**
	 * @param	string	$type
	 * @return	array|false
	 */
	public function getTestFiles($type)
	{
		return $this->getTestFileStack()
					->get($type);
	}

	/**
	 * @return	array
	 */
	public function getTestDependencies()
	{
		return $this->testDepends;
	}

	/**
	 * @param	array	$src
	 * @return	null
	 */
	protected function initSource(array $src)
	{
		if (isset($src['dir'])) {
			$this->setSourceDirectory($src['dir']);
		}	

		if (isset($src['build-file'])) {
			$buildFile = $src['build-file'];
		}
		else {
			$buildFile = $this->getPackageName();
		}
		$this->setSourceBuildFile($buildFile);

		if (! isset($src['files'])) {
			$err = 'every package must define its source files: none found';
			throw new InvalidArgumentException($err);
		}
		$this->setSourceFileStack($src['files']);

		if (isset($src['depends']) && is_array($src['depends'])) {
			$this->setSourceDependencies($src['depends']);
		}
	}

	/**
	 * @param	array	$test
	 * @return test
	 */
	protected function initTest(array $test)
	{
		if (isset($test['dir'])) {
			$this->setTestDirectory($test['dir']);
		}

		if (isset($test['build-file'])) {
			$buildFile = $test['build-file'];
		}
		else {
			$buildFile = $this->getPackageName() . '-test';
		}
		$this->setTestBuildFile($buildFile);

		if (! isset($test['files'])) {
			$err = 'when defined tests must have files: none found';
			throw new InvalidArgumentException($err);
		}
		$this->setTestFileStack($test['files']);


		if (isset($test['depends']) && is_array($test['depends'])) {
			$this->setTestDependencies($test['depends']);
		}
	}

	/**
	 * @param	string	$name
	 * @return	null
	 */
	protected function setPackageName($name)
	{
		if (! is_string($name) || empty($name)) {
			$err = 'package name must be a none empty string';
			throw new InvalidArgumentException($err);
		}
		$this->name = $name;
	}

	/**
	 * @param	string	$name
	 * @return	null
	 */
	protected function setPackageDescription($desc)
	{
		if (! is_string($desc)) {
			$err = 'package description must be a string';
			throw new InvalidArgumentException($err);
		}

		$this->desc = $desc;
	}

	/**
	 * @param	string	$name
	 * @return	null
	 */
	protected function setSourceBuildFile($name)
	{
		if (! is_string($name) || empty($name)) {
			$err = 'build file must be a non empty string';
			throw new InvalidArgumentException($err);
		}

		$this->srcBuildFile = $name;
	}

	/**
	 * @return	FileStack
	 */
	protected function getSourceFileStack()
	{
		return $this->sourceFiles;
	}

	/**
	 * @param	array	$list
	 * @return	null
	 */	
	protected function setSourceFileStack($files)
	{
		if ($files instanceof FileStackInterface) {
			$this->sourceFiles = $files;
			return;
		}
		else if (! is_array($files)) {
			$err  = 'files must be an array or an object that implments ';
			$err .= 'Appfuel\Html\Resource\FileStackInterface';
			throw new InvalidArgumentException($err);
		}

		$list = $this->createFileStack($files);
		$this->sourceFiles = $list;
	}

	/**
	 * @param	string	$name
	 * @return	null
	 */
	protected function setSourceDirectory($dir)
	{
		if (! is_string($dir)) {
			$err = 'package source directory must be a string';
			throw new InvalidArgumentException($err);
		}
		$this->srcDir = $dir;
	}

	/**
	 * @param	array	$list
	 * @return	null
	 */
	protected function setSourceDependencies(array $list)
	{
		foreach ($list as $vendor => $packages) {
			if (! is_string($vendor) || empty($vendor)) {
				$err = 'vendor name must be a non empty string';
				throw new InvalidArgumentException($err);
			}
		}

		$this->srcDepends = $list;
	}

	/**
	 * @param	string	$name
	 * @return	null
	 */
	protected function setTestBuildFile($name)
	{
		if (! is_string($name) || empty($name)) {
			$err = 'test build file must be a non empty string';
			throw new InvalidArgumentException($err);
		}

		$this->testBuildFile = $name;
	}

	/**
	 * @param	string	$name
	 * @return	null
	 */
	protected function setTestDirectory($dir)
	{
		if (! is_string($dir)) {
			$err = 'package dir must be a string';
			throw new InvalidArgumentException($err);
		}
		$this->testDir = $dir;
	}
	
	protected  function getTestFileStack()
	{
		return $this->testFiles;
	}
	
	/**
	 * @param	array	$list
	 * @return	null
	 */	
	protected function setTestFileStack($files)
	{
		if ($files instanceof FileStackInterface) {
			$this->testFiles = $files;
			return;
		}
		else if (! is_array($files)) {
			$err  = 'tests must be an array or an object that implements ';
			$err .= 'Appfuel\Html\Resource\FileStackInterface';
			throw new InvalidArgumentException($err);
		}

		$this->testFiles = $this->createFileStack($files);
	}

	/**
	 * @param	array	$list
	 * @return	null
	 */
	protected function setTestDependencies(array $list)
	{
		foreach ($list as $vendor => $packages) {
			if (! is_string($vendor) || empty($vendor)) {
				$err = 'vendor name must be a non empty string';
				throw new InvalidArgumentException($err);
			}
		}

		$this->testDepends = $list;
	}

	/**
	 * @return	PackageFileList
	 */
	protected function createFileStack(array $files)
	{
		return new FileStack($files);
	}
}