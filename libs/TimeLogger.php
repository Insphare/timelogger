<?php

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
	 * @var Cli_Prompt
	 */
	private $cliPrompt;

	/**
	 * @var bool
	 */
	private $preventHelp = false;

	/**
	 *
	 */
	public function __construct() {
		$this->cliOutput = new Cli_Output();
		$this->cliPrompt = new Cli_Prompt();
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

		$line = $this->cliPrompt->promptToUserInput();
		return trim($line);
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

			$this->breakSignal = Cli_Prompt::getBreakSignal();
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
		$file = $this->getFileManager()->getFullImagePath($imageFile);
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
			'correct 	- Correct current work name',
			'info		- Information about the current work',
			'pause		- Pause the current work',
			'continue	- Continue the current work',
			'note		- Add a note to current work',
			'merge		- Merge two works to one',
			'resume		- Resume previous work',
			'show		- Show tasks',
			'compact	- Compact view',
			'clear		- Clearing the screen',
			'export		- Export all to text files (with notes)',
			'append		- Append time to work',
			'help		- Show help.',
			'quit		- Quit',
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
			$line = $this->parseArguments($line);
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
	 * @param $string
	 * @return array
	 */
	private function parseArguments($string) {
		$arguments = array();
		$regEx = '~("|\'|)(?<argument>[a-z0-9ßäöü [:punct:]]+)(\1)( |$)~iU';
		preg_match_all($regEx, $string, $arrMatches, PREG_SET_ORDER);
		foreach ($arrMatches as $arrMatch) {
			$arguments[] = $arrMatch['argument'];
		}

		return $arguments;
	}

	/**
	 * @param $commandName
	 */
	private function executeCommandCallback($commandName) {
		switch ($commandName) {
			case 'quit':
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
	 * Testing method.
	 *
	 * @param $string
	 *
	 * @author Manuel Will
	 * @since 2014-04
	 */
	private function guiNotify($string) {
		// not supported now. should check if notify-send is available on system.
		return;
		if (false !== strrpos($string, PHP_EOL)) {
			return;
		}
		$command = 'notify-send "'.($string).'"';
		`$command`;
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
			$this->guiNotify($resultString);
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
