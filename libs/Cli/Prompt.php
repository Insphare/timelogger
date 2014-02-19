<?php

ignore_user_abort(true);
declare(ticks = 1);

/**
 * Class Cli_Prompt
 *
 * @author Manuel Will <insphare@gmail.com>
 * @copyright Copyright (c) 2014, Manuel Will
 */
class Cli_Prompt {

	/**
	 * @var bool
	 */
	private static $breakSignal = false;

	/**
	 *
	 */
	public function __construct() {
		// Prevent breaking out of the console.
		$this->registerHandler();

		self::setBreakSignal(false);
	}

	/**
	 * @return bool
	 */
	public static function getBreakSignal() {
		return self::$breakSignal;
	}

	/**
	 * @param $state
	 */
	public static function setBreakSignal($state) {
		self::$breakSignal = (bool)$state;
	}

	/**
	 * Prevent breaking out of the console.
	 */
	private function registerHandler() {
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
	 * @param $signal
	 */
	protected function signalHandler($signal) {
		switch ($signal) {
			case SIGINT:
			case SIGTSTP:
				self::$breakSignal = true;
				break;
		}
	}

	/**
	 * @return string
	 */
	public function promptToUserInput() {
		self::$breakSignal = false;
		stream_set_blocking(STDIN, true);
		$handle = fopen('php://stdin', 'r');
		$line = fgets($handle);
		$line = trim($line);
		return $line;
	}
}
