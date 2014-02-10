<?php

class Cli_Output {

	const COLOR_BLACK = 'black';
	const COLOR_DARK_GREY = 'dark_gray';
	const COLOR_BLUE = 'blue';
	const COLOR_LIGHT_BLUE = 'light_blue';
	const COLOR_GREEN = 'green';
	const COLOR_LIGHT_GREEN = 'light_green';
	const COLOR_CYAN = 'cyan';
	const COLOR_LIGHT_CYAN = 'light_cyan';
	const COLOR_RED = 'red';
	const COLOR_LIGHT_RED = 'light_red';
	const COLOR_PURPLE = 'purple';
	const COLOR_LIGHT_PURPLE = 'light_purple';
	const COLOR_LIGHT_YELLOW = 'light_yellow';
	const COLOR_YELLOW = 'yellow';
	const COLOR_LIGHT_GRAY = 'light_gray';
	const COLOR_WHITE = 'white';
	const COLOR_MAGENTA = 'magenta';

	/**
	 * @var array
	 */
	private $output = array();

	/**
	 * @var array
	 */
	private $foregroundColors = array(
		self::COLOR_BLACK => '0;30',
		self::COLOR_DARK_GREY => '1;30',
		self::COLOR_BLUE => '0;34',
		self::COLOR_LIGHT_BLUE => '1;34',
		self::COLOR_GREEN => '0;32',
		self::COLOR_LIGHT_GREEN => '1;32',
		self::COLOR_CYAN => '0;36',
		self::COLOR_LIGHT_CYAN => '1;36',
		self::COLOR_RED => '0;31',
		self::COLOR_LIGHT_RED => '1;31',
		self::COLOR_PURPLE => '0;35',
		self::COLOR_LIGHT_PURPLE => '1;35',
		self::COLOR_LIGHT_YELLOW => '0;33',
		self::COLOR_YELLOW => '1;33',
		self::COLOR_LIGHT_GRAY => '0;37',
		self::COLOR_WHITE => '1;37'
	);

	/**
	 * @var array
	 */
	private $backgroundColor = array(
		self::COLOR_BLACK => '40',
		self::COLOR_RED => '41',
		self::COLOR_GREEN => '42',
		self::COLOR_YELLOW => '43',
		self::COLOR_BLUE => '44',
		self::COLOR_MAGENTA => '45',
		self::COLOR_CYAN => '46',
		self::COLOR_LIGHT_GRAY => '47',
	);

	/**
	 * @param $text
	 * @param bool $newLine
	 * @param null $foregroundColor
	 * @param null $backgroundColor
	 */
	public function createLine($text, $newLine = true, $foregroundColor = null, $backgroundColor = null) {
		$foregroundColor = $this->validateForegroundColor($foregroundColor);
		$backgroundColor = $this->validateBackgroundColor($backgroundColor);
		$outputString = '';
		$haveColor = false;

		if (!empty($foregroundColor) || !empty($foregroundColor)) {
			$haveColor = true;
		}

		if (!empty($foregroundColor)) {
			$outputString .= "\033[" . $foregroundColor . "m";
		}

		if (!empty($backgroundColor)) {
			$outputString .= "\033[" . $backgroundColor . "m";
		}

		$outputString .= $text;

		if (true === $haveColor) {
			$outputString .= "\033[0m";
		}

		if (true === $newLine) {
			$outputString .= PHP_EOL;
		}

		$this->output[] = $outputString;
	}

	/**
	 *
	 */
	public function flush() {
		echo implode('', $this->output);
		$this->output = array();
	}

	/**
	 * @param $colorName
	 * @return null
	 * @throws InvalidArgumentException
	 */
	private function validateForegroundColor($colorName) {
		if (null == $colorName) {
			return null;
		}

		if (!isset($this->foregroundColors[$colorName])) {
			throw new InvalidArgumentException('Unknown foreground color.');
		}

		return $this->foregroundColors{$colorName};
	}

	/**
	 * @param $colorName
	 * @return null
	 * @throws InvalidArgumentException
	 */
	private function validateBackgroundColor($colorName) {
		if (null == $colorName) {
			return null;
		}
		if (!isset($this->backgroundColor[$colorName])) {
			throw new InvalidArgumentException('Unknown background color.');
		}

		return $this->backgroundColor{$colorName};
	}
}
