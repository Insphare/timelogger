<?php

/**
 * Class FileServer
 *
 * @author Manuel Will <insphare@gmail.com>
 * @copyright Copyright (c) 2014, Manuel Will
 */
class FileManager {

	/**
	 * Directory naming for workings.
	 */
	const DIR_NAME_WORKING = 'working';

	/**
	 * Directory naming for ascii images.
	 */
	const DIR_NAME_IMAGES = 'images';

	/**
	 * Directory naming for caches.
	 */
	const DIR_NAME_CACHE = 'cache';

	/**
	 * Directory naming for reports.
	 */
	const DIR_NAME_REPORT = 'report';

	/**
	 * @var null|string
	 */
	private $dirReports = null;

	/**
	 * @var null|string
	 */
	private $dirWorkings = null;

	/**
	 * @var null|string
	 */
	private $dirCache = null;

	/**
	 * @var null
	 */
	private $dirImage = null;

	/**
	 * @var null|string
	 */
	private $lockedForCommands = null;

	/**
	 * @var FileManager
	 */
	private static $instance;

	/**
	 *
	 */
	protected function __construct() {
		$ds = DIRECTORY_SEPARATOR;
		$destination = Config::get(Config::BASE_PATH);
		$this->dirReports = $destination . self::DIR_NAME_REPORT . $ds;
		$this->dirWorkings = $destination . self::DIR_NAME_WORKING . $ds;
		$this->dirCache = $destination . self::DIR_NAME_CACHE . $ds;
		$this->dirImage = $destination . self::DIR_NAME_IMAGES . $ds;
		$this->lockedForCommands = $destination . self::DIR_NAME_CACHE . $ds . 'lockedForCommands.dat';
	}

	/**
	 * @param $imageFile
	 * @return string
	 */
	public function getFullImagePath($imageFile) {
		return $this->dirImage . $imageFile;
	}

	/**
	 * @return FileManager
	 */
	public static function get() {
		if (null === self::$instance) {
			self::$instance = new self();
		}

		return self::$instance;
	}

	/**
	 * @param $day
	 * @param $string
	 */
	public function saveReport($day, $string) {
		$file = $this->dirReports . $day . '.txt';
		file_put_contents($file, $string);
	}

	/**
	 * @param $file
	 * @param $data
	 * @param bool $doAppend
	 */
	private function saveFile($file, $data, $doAppend = false) {
		$flag = 0;
		if (true === $doAppend) {
			$flag = FILE_APPEND;
		}

		switch (true) {
			case is_string($data):
				$data = array(
					'isString' => true,
					'value' => $data
				);
				break;

			case is_object($data):
				$data = array(
					'isObject' => true,
					'value' => serialize($data)
				);
				break;
		}

		file_put_contents($file, json_encode($data), $flag);
	}

	/**
	 * @param $file
	 * @return mixed|null|string
	 */
	public function loadData($file) {
		if (!file_exists($file)) {
			return null;
		}

		$data = file_get_contents($file);

		if (!empty($data)) {
			$data = json_decode($data, true);
			if (!empty($data['isString'])) {
				return $data['value'];
			}

			if (!empty($data['isObject'])) {
				return unserialize($data['value']);
			}
		}

		return $data;
	}

	/**
	 * @param $currentCommand
	 * @return bool
	 */
	public function isLockedAndCurrentCommandIsDisallowed($currentCommand) {
		$allowedCommands = $this->getLockActions();
		if (false === $this->isLocked() || empty($allowedCommands)) {
			return false;
		}

		return !isset($allowedCommands[strtolower($currentCommand)]);
	}

	/**
	 * @return bool
	 */
	private function isLocked() {
		$data = $this->getLockActions();
		if (empty($data)) {
			return false;
		}

		return true;
	}

	/**
	 * @return mixed|null|string
	 */
	public function getLockActions() {
		return $this->loadData($this->lockedForCommands);
	}

	/**
	 * @param array $allowedCommands
	 */
	public function lockActionsForCommands(array $allowedCommands) {
		$data = array();
		foreach ($allowedCommands as $cmd) {
			$cmd = strtolower($cmd);
			$data[$cmd] = $cmd;
		}
		$this->saveFile($this->lockedForCommands, $data);
	}

	/**
	 *
	 */
	public function unlockCommands() {
		$this->saveFile($this->lockedForCommands, '');
	}

	/**
	 * @param $className
	 * @param $data
	 */
	public function storeCacheData($className, $data) {
		$this->saveFile($this->dirCache . $className, $data);
	}

	/**
	 * @param $className
	 * @return mixed|null|string
	 */
	public function loadCacheData($className) {
		return $this->loadData($this->dirCache . $className);
	}

	/**
	 * @param Work_Container $workContainerObject
	 * @throws Command_Exception
	 */
	public function storeWork(Work_Container $workContainerObject) {
		$startTimeStamp = $workContainerObject->getStarted();
		$startTimeStamp = date('Y-m-d 00:00:00', $startTimeStamp);
		$startTimeStamp = strtotime($startTimeStamp);

		$workName = $workContainerObject->getLabel();
		if (empty($workName)) {
			throw new Command_Exception('Missing work name!');
		}

		$fileName = $this->getWorkFilePathByName($startTimeStamp, $workContainerObject->getLabel());
		$fileData = $workContainerObject->getAsArray();
		$this->saveFile($fileName, $fileData);
	}

	/**
	 * @param Work_Container $workContainerObject
	 * @throws Command_Exception
	 */
	public function removeWork(Work_Container $workContainerObject) {
		$startTimeStamp = $workContainerObject->getStarted();
		$startTimeStamp = date('Y-m-d 00:00:00', $startTimeStamp);
		$startTimeStamp = strtotime($startTimeStamp);

		$workName = $workContainerObject->getLabel();
		if (empty($workName)) {
			throw new Command_Exception('Missing work name!');
		}

		$fileName = $this->getWorkFilePathByName($startTimeStamp, $workContainerObject->getLabel());
		unlink($fileName);
	}

	/**
	 * @return bool|string
	 */
	protected function getCurrentDate() {
		return date('Y-m-d 00:00:00', time());
	}

	/**
	 * @param $workName
	 * @return null|Work_LoadByData
	 */
	public function getWorkContainerByWorkNameFromToday($workName) {
		$startTimeStamp = $this->getCurrentDate();
		$startTimeStamp = strtotime($startTimeStamp);
		$fileName = $this->getWorkFilePathByName($startTimeStamp, $workName);
		if (!file_exists($fileName)) {
			return null;
		}

		$data = FileManager::get()->loadData($fileName);
		$workContainerObject = new Work_LoadByData($data);
		return $workContainerObject;
	}

	/**
	 * @param $timeStamp
	 * @param $workName
	 * @return string
	 */
	private function getWorkFilePathByName($timeStamp, $workName) {
		$label = strtolower($workName);
		$replace = array(
			'ö' => 'oe',
			'ä' => 'ae',
			'ü' => 'ue'
		);

		foreach ($replace as $searchPattern => $replacePattern) {
			$label = str_replace($searchPattern, $replacePattern, $label);
		}

		$label = preg_replace('~[^a-z0-9-]~i', '', $label);
		$fileName = $this->dirWorkings . $timeStamp . '_' . $label . '.dat';
		return $fileName;
	}

	/**
	 * @return null|string
	 */
	public function getDirWorkings() {
		return $this->dirWorkings;
	}

	/**
	 * @return null|string
	 */
	public function getDirCache() {
		return $this->dirCache;
	}

	/**
	 * @return null
	 */
	public function getDirImage() {
		return $this->dirImage;
	}

	/**
	 * @return null|string
	 */
	public function getDirReports() {
		return $this->dirReports;
	}

	/**
	 * @return \FileManager
	 */
	public static function getInstance() {
		return self::$instance;
	}

	/**
	 * @return null|string
	 */
	public function getLockedForCommands() {
		return $this->lockedForCommands;
	}
}
