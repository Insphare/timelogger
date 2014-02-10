<?php

ignore_user_abort(true);
declare(ticks = 1);

/**
 * Class Logger
 *
 * @author Manuel Will
 * @since 2013
 */
class TimeLogger {

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
		$this->registryHandler();
		$this->cliOutput = new Cli_Output();
	}

	/**
	 * Prevent breaking out of the console.
	 */
	private function registryHandler() {
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
			$this->printLine('Time logger v.1.0 © Manuel Will', true, Cli_Output::COLOR_DARK_GREY);
			$this->printLine('> ', false, Cli_Output::COLOR_LIGHT_BLUE);
		}

		stream_set_blocking(STDIN, true);

		$handle = fopen('php://stdin', 'r');
		$line = fgets($handle);

		return trim(strtolower($line));
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
		//$this->printLine('Welcome!');
		$this->printWelcome();

		while (true) {
			$userInput = $this->setAndGetPromptLine();
			$this->executeCommand($userInput);

			if (true === $this->breakSignal) {
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
			'start				- Start work',
			'|->	stop		- Stop current work',
			'|->	change		- Change current work name',
			'|->	info		- Information about the current work',
			'|->	pause		- Pause the current work',
			'|->	continue	- Continue the work',
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
			$line = trim($commandLine);
			$line = trim(str_replace('  ', ' ', $line));

			if (empty($line)) {

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

			$this->isEmptyCommand = false;

			$line = explode(' ', $line);

			$commandOperator = strtolower($line[0]);
			$commandClass = 'Command_' . ucfirst($commandOperator);
			unset($line[0]);
			$arguments = $line;

			// check whether the current command is allowed or blocked.
			if (true === $this->getFileManager()->isLockedAndCurrentCommandIsDisallowed($commandOperator)) {
				$commandsAllowed = array();

				$cmd = $this->getFileManager()->getLockActions();
				foreach ($cmd as $cmdName) {
					$commandsAllowed[$cmdName] = "'" . $cmdName . "'";
				}

				$message = 'Next you have to be perform ' . implode(' or ', $commandsAllowed) . '.';
				throw new Command_Exception($message);
			}

			if (!empty($commandLine)) {
				$this->executeCommandClass($commandClass, $arguments);

				switch ($commandOperator) {
					case 'exit':
						$this->printByeBye();
						sleep(1);
						exit(1);
						break;

					case 'help':
						$this->showHelp();
						break;
				}
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
			//		throw new Command_Exception($e->getMessage());
			$this->stupidCounter++;

			if ($this->stupidCounter > 0 && !($this->stupidCounter % 3)) {
				$this->printWtfStupid();
			}

			throw new Command_Exception('Unknown command.');
		}
	}
}
