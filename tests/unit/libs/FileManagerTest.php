<?php

/**
 * Class FileManagerTEst
 */
class FileManagerTEst extends PHPUnit_Framework_TestCase {

	/**
	 * @return FileManager
	 */
	private function getFileManager() {
		$beforeConfig = Config::get(Config::BASE_PATH);
		Config::set(Config::BASE_PATH, PHPUNIT_TEST_DIR_FIXTURES);
		$fileManager = FileManager::get();
		Config::set(Config::BASE_PATH, $beforeConfig);

		return $fileManager;
	}

	/**
	 *
	 */
	public function testFixturePathsExists() {
		$ds = DIRECTORY_SEPARATOR;
		$directories = array(
			'cache',
			'task',
			'report',
			'images'
		);

		foreach ($directories as $directoryName) {
			$fullPathToDir = PHPUNIT_TEST_DIR_FIXTURES . $directoryName;
			$this->assertTrue(file_exists($fullPathToDir));
			$this->assertTrue(is_dir($fullPathToDir));
		}
	}

	/**
	 *
	 */
	public function testPathsAndProperties() {
		$fileManager = $this->getFileManager();
		$ds = DIRECTORY_SEPARATOR;

		$this->assertSame(PHPUNIT_TEST_DIR_FIXTURES . 'cache' . $ds, $fileManager->getDirCache());
		$this->assertSame(PHPUNIT_TEST_DIR_FIXTURES . 'task' . $ds, $fileManager->getDirTasks());
		$this->assertSame(PHPUNIT_TEST_DIR_FIXTURES . 'report' . $ds, $fileManager->getDirReports());
		$this->assertSame(PHPUNIT_TEST_DIR_FIXTURES . 'images' . $ds, $fileManager->getDirImage());
		$this->assertTrue($fileManager->getInstance() instanceof FileManager);
	}

	/**
	 * @dataProvider testDataFixtures
	 */
	public function testSaveStringTypes($saveValue, $expectedString) {
		$fileName = 'UnitTest';
		$fileManager = $this->getFileManager();
		$ds = DIRECTORY_SEPARATOR;
		$cachePath = PHPUNIT_TEST_DIR_FIXTURES . 'cache' . $ds . 'UnitTest';
		$fileManager->storeCacheData($fileName, $saveValue);
		$savedFileContent = file_get_contents($cachePath);
		$this->assertSame($expectedString, $savedFileContent);
		if ($saveValue instanceof TestMiniClass) {
			$this->assertEquals($saveValue, $saveValue, $fileManager->loadCacheData($fileName));
		}
		else {
			$this->assertSame($saveValue, $fileManager->loadCacheData($fileName));
		}
		unlink($cachePath);
	}

	/**
	 * @return array
	 */
	public static function testDataFixtures() {
		return array(
			array(
				'testValue',
				'{"isString":true,"value":"testValue"}'
			),
			array(
				1,
				'1'
			),
			array(
				new TestMiniClass(),
				'{"isObject":true,"value":"O:13:\"TestMiniClass\":1:{s:19:\"\u0000TestMiniClass\u0000test\";i:1;}"}'
			),
			array(
				array(
					1,
					2 => array(
						'1',
						2
					),
					'1'
				),
				'{"0":1,"2":["1",2],"3":"1"}'
			),
		);
	}

	/**
	 *
	 */
	public function testLockCommands() {
		$fileManager = $this->getFileManager();
		$fileManager->unlockCommands();
		$fileManager->lockActionsForCommands(array(
			'test',
			'test2'
		));

		$this->assertTrue($fileManager->isLockedAndCurrentCommandIsDisallowed('x'));
		$this->assertTrue($fileManager->isLockedAndCurrentCommandIsDisallowed('abc'));
		$this->assertTrue($fileManager->isLockedAndCurrentCommandIsDisallowed('direct'));

		$this->assertFalse($fileManager->isLockedAndCurrentCommandIsDisallowed('test'));
		$this->assertFalse($fileManager->isLockedAndCurrentCommandIsDisallowed('test2'));

		$fileManager->unlockCommands();

		$this->assertFalse($fileManager->isLockedAndCurrentCommandIsDisallowed('x'));
		$this->assertFalse($fileManager->isLockedAndCurrentCommandIsDisallowed('abc'));
		$this->assertFalse($fileManager->isLockedAndCurrentCommandIsDisallowed('direct'));

		$this->assertFalse($fileManager->isLockedAndCurrentCommandIsDisallowed('test'));
		$this->assertFalse($fileManager->isLockedAndCurrentCommandIsDisallowed('test2'));
	}

	/**
	 *
	 */
	public function testStoreWorkNameFailed() {
		$this->setExpectedException('Command_Exception', 'Missing work name!');
		$fileManager = $this->getFileManager();
		$workContainer = new Work_Container();
		$workContainer->startWorkTime();
		$workContainer->setLastWorkTimeBegin(time()+50);
		$fileManager->storeWork($workContainer);
	}

	/**
	 *
	 */
	public function testStoreWork() {
		$workName = 'phpunit test';
		$fileManager = $this->getFileManager();
		$workContainer = new Work_Container();
		$workContainer->setLabel($workName);
		$workContainer->setStarted(1392591600);
		$workContainer->setLastWorkTimeBegin(1392591600);
		$workContainer->setLastWorkTimeBegin(1392591600+50);
		$fileManager->storeWork($workContainer);
		unset($workContainer);

		$workContainer = $fileManager->getWorkContainerByWorkNameFromToday($workName);
		$this->assertSame($workName, $workContainer->getLabel());
		unlink(PHPUNIT_TEST_DIR_FIXTURES.'task'.DIRECTORY_SEPARATOR.'1392591600_phpunittest.dat');

		$this->assertNull($fileManager->getWorkContainerByWorkNameFromToday('test'));
	}

	/**
	 *
	 */
	public function testSaveReport() {
		$this->markTestIncomplete();
	}

	/**
	 *
	 */
	public function testGetImage() {
		$this->markTestIncomplete();
	}
}

/**
 * Class TestMiniClass
 */
class TestMiniClass {
	/**
	 * @var int
	 */
	private $test = 1;

	/**
	 * @return int
	 */
	public function get() {
		return $this->test;
	}
}
