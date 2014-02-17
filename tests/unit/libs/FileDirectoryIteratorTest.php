<?php

/**
 * Class CalculateTest
 */
class FileDirectoryIteratorTest extends PHPUnit_Framework_TestCase {

	/**
	 * @param $directory
	 * @return FileDirectoryIterator
	 */
	private function getFileIterator($directory) {
		return new FileDirectoryIterator($directory);
	}

	/**
	 *
	 */
	public function testDirectoryFilesAll() {
		$directory = PHPUNIT_TEST_DIR_FIXTURES . 'iterator' . DIRECTORY_SEPARATOR;
		$iterator = $this->getFileIterator($directory);
		$iterator->setRecursive(false);

		$expected = array(
			'exclusion1.dat',
			'test.uno',
			'file2.dat',
			'exclusion2.dat',
			'file3.dat',
			'file1.dat',
		);

		$expectedArray = array();
		foreach ($expected as $file) {
			$fullPath = $directory . $file;
			$expectedArray[$fullPath] = $fullPath;
		}

		$this->assertSame($expectedArray, $iterator->getFiles());
	}

	/**
	 *
	 */
	public function testDirectoryFilesWithAllowedExtensionOnly() {
		$directory = PHPUNIT_TEST_DIR_FIXTURES . 'iterator' . DIRECTORY_SEPARATOR;
		$iterator = $this->getFileIterator($directory);
		$iterator->addAllowedExtension('dat');
		$iterator->setRecursive(false);

		$expected = array(
			'exclusion1.dat',
			'file2.dat',
			'exclusion2.dat',
			'file3.dat',
			'file1.dat',
		);

		$expectedArray = array();
		foreach ($expected as $file) {
			$fullPath = $directory . $file;
			$expectedArray[$fullPath] = $fullPath;
		}

		$this->assertSame($expectedArray, $iterator->getFiles());
	}

	/**
	 *
	 */
	public function testDirectoryFilesWithReExExclusion() {
		$directory = PHPUNIT_TEST_DIR_FIXTURES . 'iterator' . DIRECTORY_SEPARATOR;
		$iterator = $this->getFileIterator($directory);
		$iterator->addAllowedExtension('dat');
		$iterator->addExclusion('file.*');
		$iterator->setRecursive(true);

		$expected = array(
			'exclusion1.dat',
			'exclusion2.dat',
			'subdir/sub.dat',
			'subdir/subdir2/sub.dat',
		);

		$expectedArray = array();
		foreach ($expected as $file) {
			$fullPath = $directory . $file;
			$expectedArray[$fullPath] = $fullPath;
		}

		$this->assertSame($expectedArray, $iterator->getFiles());
		$this->assertSame($directory, $iterator->getDestination());
		$this->assertSame(true, $iterator->getRecursive());
		$iterator->setRecursive(false);
		$this->assertSame(false, $iterator->getRecursive());
	}
}
