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
	 * @var Cli_Prompt
	 */
	private $cliPrompt;

	/**
	 * @var Cli_Output
	 */
	private $cliOutput;

	/**
	 * @var array
	 */
	protected $lockStart = array(
		'start',
		'resume',
		'stop',
		'change',
		'info',
		'pause',
		'show',
		'note',
	);

	/**
	 * @var array
	 */
	protected $lockBreak = array(
		'continue',
		'info',
		'show',
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
		$this->cliPrompt = new Cli_Prompt();
		$this->cliOutput = new Cli_Output();
	}

	/**
	 * @return \Cli_Output
	 */
	public function getCliOutput() {
		return $this->cliOutput;
	}

	/**
	 * @return \Cli_Prompt
	 */
	public function getCliPrompt() {
		return $this->cliPrompt;
	}

	/**
	 */
	protected function summaryArgumentsToOne() {
		$this->summaryArgumentsToOne = true;
	}

	/**
	 * @throws Command_Exception
	 */
	protected function assertArguments($overwriteText = null) {
		$message = 'This command requires arguments.';

		if (null !== $overwriteText) {
			$message = $overwriteText;
		}

		if (empty($this->arguments)) {
			throw new Command_Exception($message);
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
	 * @param Work_Container $workContainer
	 * @return string
	 */
	protected function getDurationLine(Work_Container $workContainer) {
		$workContainer->calculate();
		$duration = trim($this->getCalculator()->getHumanAbleList($workContainer->getWorkTime()));
		$break = trim($this->getCalculator()->getHumanAbleList($workContainer->getBreakTime()));
		return '(Duration: ' . $duration . ' excl. break: ' . $break . ')';
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
	 * @param $workName
	 * @return null|Work_Container|Work_LoadByData
	 */
	protected function getWorkContainerByName($workName) {
		$this->checkLength('Task name', $workName, Command_Abstract::TASK_LENGTH_NAME);

		if (empty($workName)) {
			$this->throwError('Please enter a task name.');
		}

		$workContainer = $this->getFileManager()->getWorkContainerByWorkNameFromToday($workName);

		if (empty($workContainer)) {
			$workContainer = new Work_Container();
			$workContainer->setStarted(time());
			$workContainer->startWorkTime();
			$workContainer->setLabel($workName);
		}

		return $workContainer;
	}

	/**
	 * @return Work_Container
	 */
	protected function getWorkObjectFromCacheData() {
		/** @var Work_Container $workObjectOrNull */
		$workObjectOrNull = $this->loadCacheData('Start');
		return $workObjectOrNull;
	}

	/**
	 * @param $name
	 * @param array $arguments
	 * @return string
	 */
	protected function callForeignCommand($name, array $arguments) {
		$className = 'Command_' . ucfirst($name);
		/** @var Command_Abstract $stopCommand */
		$stopCommand = new $className($arguments);
		return $stopCommand->execute();
	}

	/**
	 * @param $workName
	 * @return null|Work_LoadByData
	 * @throws Command_Exception
	 */
	protected function getStoredWorkObjectByNameOfTheDay($workName) {
		$exceptionMessage = 'Work \''.$workName.'\' not found for current day.';
		$workObject = $this->getFileManager()->getWorkContainerByWorkNameFromToday($workName);

		if (empty($workObject)) {
			throw new Command_Exception($exceptionMessage);
		}

		return $workObject;
	}

	/**
	 *
	 */
	protected function assertActiveLogging() {
		/** @var Work_Container $data */
		$data = $this->loadCacheData('Start');
		if (empty($data) || !$data instanceof Work_Container) {
			$this->throwError('Currently, no log is active.');
		}
	}

	/**
	 *
	 */
	protected function assertInactiveLogging() {
		/** @var Work_Container $data */
		$data = $this->loadCacheData('Start');
		if (!empty($data)) {
			$this->throwError('Currently, a work log is active. You have to stopped the current work, first.');
		}
	}

	/**
	 * @return mixed
	 */
	abstract public function execute();
}
