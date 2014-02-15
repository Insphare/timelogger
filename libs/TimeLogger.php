<?php

ignore_user_abort(true);
declare(ticks = 1);

/**
 * Class Logger
 *
 * @author Manuel Will <insphare@gmail.com>
 * @copyright Copyright (c) 2014, Manuel Will
 */
class TimeLogger {

	/**
	 * Version number
	 */
	const VERSION = 'v.1.1';

	/**
	 * @var bool
	 */
	private $breakSignal = false;

	/**
	 * @var int
	 */
	private $stupidCounter = 0;

	/**
	 * @var int
	 */
	private $boringCounter = 0;

	/**
	 * @var bool
	 */
	private $isEmptyCommand = false;

	/**
	 * @var Cli_Output
	 */
	private $cliOutput;

	/**
	 * @var bool
	 */
	private $preventHelp = false;

	/**
	 *
	 */
	public function __construct() {
		// Prevent breaking out of the console.
		$this->registHandler();
		$this->cliOutput = new Cli_Output();
	}

	/**
	 * Prevent breaking out of the console.
	 */
	private function registHandler() {
		pcntl_signal(SIGINT, array(
			$this,
			'signalHandler'
		));
		pcntl_signal(SIGTSTP, array(
			$this,
			'signalHandler'
		));
	}

	/**
	 * @param bool $showLine
	 * @return string
	 */
	protected function setAndGetPromptLine($showLine = true) {
		if (true === $showLine) {
			$this->printLine('Time logger ' . self::VERSION . ' © Manuel Will', true, Cli_Output::COLOR_DARK_GREY);
			$this->printLine('> ', false, Cli_Output::COLOR_LIGHT_BLUE);
		}

		stream_set_blocking(STDIN, true);

		$handle = fopen('php://stdin', 'r');
		$line = fgets($handle);

		return trim($line);
	}

	/**
	 * @param $signal
	 */
	protected function signalHandler($signal) {
		switch ($signal) {
			case SIGINT:
			case SIGTSTP:
				$this->breakSignal = true;
				break;
		}
	}

	/**
	 * @author Manuel Will
	 * @since 2013
	 */
	public function run() {
		$this->printWelcome();

		while (true) {
			$userInput = $this->setAndGetPromptLine();
			$this->executeCommand($userInput);

			if (true === $this->breakSignal) {
				$this->confirmToExit();
			}
		}
	}

	/**
	 *
	 */
	private function confirmToExit() {
		$counter = 0;
		$this->printLine('');
		$message = 'You wanted to kill the current running script. Would you like end it now? [Y/N] ';
		$this->printLine($message, false, Cli_Output::COLOR_LIGHT_PURPLE);
		while (true) {
			$userInput = $this->setAndGetPromptLine(false);
			switch ($userInput) {
				case 'y':
					$this->printByeBye();
					exit(1);

				case 'n':
					$this->breakSignal = false;
					$this->showHelp();
					break 2;

				default:
					$this->printLine('Wrong input.', true, Cli_Output::COLOR_LIGHT_YELLOW);
					$counter++;
					if ($counter > 2) {
						$this->breakSignal = false;
						$this->printLine('');
						$this->printCoffee();
						break 2;
					}
			}
		}
	}

	/**
	 *
	 */
	private function printCoffee() {
		$this->printImage('coffee.dat');
		$message = 'It looks like you were too stupid to answer. Get a cup of coffee! Program continues.';
		$this->printLine($message, true, Cli_Output::COLOR_LIGHT_RED);
	}

	/**
	 *
	 */
	private function printByeBye() {
		$this->printImage('bye.dat');
	}

	/**
	 *
	 */
	private function printWtfBoring() {
		$this->printImage('stop.dat');
		$message = 'Stop this nonsense! Are you stupid?';
		$this->printLine($message, true, Cli_Output::COLOR_LIGHT_RED);
	}

	/**
	 *
	 */
	private function printWtfStupid() {
		$this->printImage('question.dat');
		$message = 'WTF is wrong with you? It looks like you were too stupid use the program.';
		$this->printLine($message, true, Cli_Output::COLOR_LIGHT_RED);
	}

	/**
	 *
	 */
	private function printWelcome() {
		$this->printImage('welcome.dat');
	}

	/**
	 * @param $imageFile
	 */
	private function printImage($imageFile) {
		$file = $this->getFileManager()->getImage($imageFile);
		$file = file_get_contents($file);
		$file = explode(PHP_EOL, $file);
		foreach ($file as $line) {
			$this->printLine($line, true, Cli_Output::COLOR_LIGHT_CYAN);
		}
	}

	/**
	 * @author Manuel Will
	 * @since 2013
	 */
	private function showHelp($errorMessage = '') {
		if (!empty($errorMessage)) {
			$this->printLine('');
			$this->printLine($errorMessage, true, Cli_Output::COLOR_LIGHT_RED);
			$this->printLine('');
		}
		else {
			$this->printLine('');
		}

		if (true === $this->preventHelp) {
			$this->preventHelp = false;
			return;
		}

		$text = array(
			'start		- Start work',
			'stop		- Stop current work',
			'change		- Change current work name',
			'info		- Information about the current work',
			'pause		- Pause the current work',
			'continue	- Continue the work',
			'resume		- Resume previous work',
			'show		- Show tasks',
			'export		- Export all to text files',
			'help		- Show help.',
			'exit		- Exit',
		);

		$this->printLine(implode(PHP_EOL, $text));
		$this->printLine('');
	}

	/**
	 * @author Manuel Will
	 * @since 2013
	 */
	private function printLine($text, $newLine = true, $foregroundColor = null, $backgroundColor = null) {
		$this->cliOutput->createLine($text, $newLine, $foregroundColor, $backgroundColor);
		$this->cliOutput->flush();
	}

	/**
	 * @return FileManager
	 * @author Manuel Will
	 * @since 2013
	 */
	protected function getFileManager() {
		return FileManager::get();
	}

	/**
	 * @author Manuel Will
	 * @since 2013
	 */
	private function executeCommand($commandLine) {
		try {
			$line = $this->prepareInput($commandLine);

			if (empty($line)) {
				// throws an exception
				$this->processWhenInputIsEmpty();
			}

			$this->isEmptyCommand = false;
			$line = explode(' ', $line);
			$commandName = strtolower($line[0]);
			$commandClass = 'Command_' . ucfirst($commandName);
			unset($line[0]);
			$arguments = $line;

			// check whether the current command is allowed or blocked.
			$this->assertCommandIsAllowed($commandName);

			if (!empty($commandLine)) {
				if ($commandLine === 'show') {
					// clear
					$this->printLine(chr(12));
				}
				$this->executeCommandClass($commandClass, $arguments);
				$this->executeCommandCallback($commandName);
			}
			else {
				$this->showHelp();
			}
		}
		catch (Command_Exception $e) {
			$this->showHelp($e->getMessage());
		}
	}

	/**
	 * @param $commandName
	 */
	private function executeCommandCallback($commandName) {
		switch ($commandName) {
			case 'exit':
				$this->printByeBye();
				exit(1);
				break;

			case 'help':
				$this->showHelp();
				break;
		}
	}

	/**
	 * @param $commandName
	 * @throws Command_Exception
	 */
	private function assertCommandIsAllowed($commandName) {
		if (true === $this->getFileManager()->isLockedAndCurrentCommandIsDisallowed($commandName)) {
			$commandsAllowed = array();

			$commandsTheyAreAllowed = $this->getFileManager()->getLockActions();
			foreach ($commandsTheyAreAllowed as $cmdName) {
				$commandsAllowed[$cmdName] = "'" . $cmdName . "'";
			}

			$message = 'Next you have to be perform ' . implode(' or ', $commandsAllowed) . '.';
			throw new Command_Exception($message);
		}
	}

	/**
	 * @throws Command_Exception
	 */
	private function processWhenInputIsEmpty() {
		if (true === $this->isEmptyCommand) {
			$this->boringCounter++;
		}

		if ($this->boringCounter > 0 && !($this->boringCounter % 3)) {
			$this->printWtfBoring();
		}
		elseif ($this->boringCounter > 0 && !($this->boringCounter % 5)) {
			$wtf = array(
				'lampman.dat',
				'sleep.dat',
				'caniplay.dat',
				'policestop1.dat',
			);
			shuffle($wtf);
			$this->preventHelp = true;
			$this->printImage($wtf[0]);
		}

		$this->isEmptyCommand = true;
		throw new Command_Exception();
	}

	/**
	 * @param $string
	 * @return string
	 */
	private function prepareInput($string) {
		$string = trim($string);
		$string = trim(str_replace('  ', ' ', $string));
		return $string;
	}

	/**
	 * @author Manuel Will
	 * @since 2013
	 */
	private function executeCommandClass($class, array $arguments = array()) {
		try {

			/** @var Command_Abstract $classObject */
			$classObject = new $class($arguments);
			$resultString = $classObject->execute();
			$this->printLine($resultString, true, Cli_Output::COLOR_GREEN);
			$this->printLine('');
			$this->stupidCounter = 0;
		}
		catch (Autoloader_Exception $e) {
			$this->stupidCounter++;

			if ($this->stupidCounter > 0 && !($this->stupidCounter % 3)) {
				$this->printWtfStupid();
			}

			throw new Command_Exception('Unknown command.');
		}
	}
}
