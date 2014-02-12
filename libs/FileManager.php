<?php

/**
 * Class FileServer
 *
 *  @author Manuel Will <insphare@gmail.com>
 *  @copyright Copyright (c) 2014, Manuel Will
 */
class FileManager {

	/**
	 * @var null|string
	 */
	private $dirReports = null;

	/**
	 * @var null|string
	 */
	private $dirTasks = null;

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
		$this->dirReports = $destination . 'report' . $ds;
		$this->dirTasks = $destination . 'task' . $ds;
		$this->dirCache = $destination . 'cache' . $ds;
		$this->dirImage = $destination . 'images' . $ds;
		$this->lockedForCommands = $destination . 'cache' . $ds . 'lockedForCommands.dat';
	}

	/**
	 * @param $imageFile
	 * @return string
	 */
	public function getImage($imageFile) {
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
	 * @param Work_Container $work
	 */
	public function storeWork(Work_Container $work) {
		$startTimestamp = $work->getStarted();
		$label = strtolower($work->getLabel());
		$label = preg_replace('~[^a-z0-9-]~i', '', $label);
		$fileName = $this->dirTasks . $startTimestamp . '_' . $label . '.dat';
		$fileData = $work->getAsArray();
		$this->saveFile($fileName, $fileData);
	}

	/**
	 * @return null|string
	 */
	public function getDirTasks() {
		return $this->dirTasks;
	}
}
