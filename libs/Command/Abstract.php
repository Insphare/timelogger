<?php

/**
 * Class Command_Abstract
 *
 * @author Manuel Will <insphare@gmail.com>
 * @copyright Copyright (c) 2014, Manuel Will
 */
abstract class Command_Abstract {

	/**
	 *
	 */
	const TASK_LENGTH_NAME = 25;

	/**
	 * @var array
	 */
	protected $arguments = array();

	/**
	 * @var Calculate
	 */
	protected $calculator;

	/**
	 * @var array
	 */
	protected $lockStart = array(
		'stop',
		'change',
		'info',
		'pause',
	);

	/**
	 * @var array
	 */
	protected $lockBreak = array(
		'continue',
		'info',
	);

	/**
	 * @var bool
	 */
	private $summaryArgumentsToOne = false;

	/**
	 * @param array $arguments
	 */
	public function __construct(array $arguments) {
		if (true === $this->summaryArgumentsToOne) {
			$summary = implode(' ', $arguments);
			$arguments = array(1 => $summary);
		}

		$this->arguments = $arguments;
		$this->calculator = new Calculate();
	}

	/**
	 */
	protected function summaryArgumentsToOne() {
		$this->summaryArgumentsToOne = true;
	}

	/**
	 * @throws Command_Exception
	 */
	protected function assertArguments() {
		if (empty($this->arguments)) {
			throw new Command_Exception('This command requires arguments.');
		}
	}

	/**
	 * @param $pos
	 * @return null
	 */
	protected function getArgument($pos) {
		if (isset($this->arguments[$pos])) {
			return $this->arguments[$pos];
		}

		return null;
	}

	/**
	 * @return FileManager
	 */
	protected function getFileManager() {
		return FileManager::get();
	}

	/**
	 * @param $data
	 * @param null $overwriteClass
	 */
	protected function saveCacheData($data, $overwriteClass = null) {
		$class = $this->getClassName($overwriteClass);
		$this->getFileManager()->storeCacheData($class, $data);
	}

	/**
	 * @param null $overwriteClass
	 * @return string
	 */
	private function getClassName($overwriteClass = null) {
		$class = get_class($this);
		if (null !== $overwriteClass) {
			$class = 'Command_' . $overwriteClass;
		}

		return $class;
	}

	/**
	 * @param null $overwriteClass
	 * @return null|string
	 */
	protected function loadCacheData($overwriteClass = null) {
		$class = $this->getClassName($overwriteClass);
		return $this->getFileManager()->loadCacheData($class);
	}

	/**
	 * @param $message
	 * @throws Command_Exception
	 */
	protected function throwError($message) {
		throw new Command_Exception($message);
	}

	/**
	 * @param $name
	 * @param $value
	 * @param $maxLength
	 * @throws Command_Exception
	 */
	protected function checkLength($name, $value, $maxLength) {
		$maxLength = (int)$maxLength;
		if (strlen($value) > $maxLength) {
			throw new Command_Exception('Argument: \'' . $name . '\' must consists of ' . $maxLength . ' characters.');
		}
	}

	/**
	 * @return Calculate
	 */
	protected function getCalculator() {
		return $this->calculator;
	}

	/**
	 * @return mixed
	 */
	abstract public function execute();
}
