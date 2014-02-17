<?php

/**
 * Class Cli_OutputTest

 */
class Cli_OutputTest extends PHPUnit_Framework_TestCase {

	/**
	 * @dataProvider testDataColorBackground
	 */
	public function testColoringBackground($color, $value) {
		$cliOutput = new Cli_Output();

		$cliOutput->createLine('testLine', false, null, $color);
		$returnValue = $cliOutput->__toString();
		$fixture = "\033[" . $value . "mtestLine\033[0m";
		$this->assertSame(md5($fixture), md5($returnValue));
	}

	/**
	 * @dataProvider testDataColorForeground
	 */
	public function testColoringForeground($color, $value) {
		$cliOutput = new Cli_Output();

		$cliOutput->createLine('testLine', false, $color);
		$returnValue = $cliOutput->__toString();
		$fixture = "\033[" . $value . "mtestLine\033[0m";
		$this->assertSame(md5($fixture), md5($returnValue));
	}

	/**
	 * @return array
	 */
	public static function testDataColorBackground() {
		$tests = array();

		$backgroundColor = array(
			Cli_Output::COLOR_BLACK => '40',
			Cli_Output::COLOR_RED => '41',
			Cli_Output::COLOR_GREEN => '42',
			Cli_Output::COLOR_YELLOW => '43',
			Cli_Output::COLOR_BLUE => '44',
			Cli_Output::COLOR_MAGENTA => '45',
			Cli_Output::COLOR_CYAN => '46',
			Cli_Output::COLOR_LIGHT_GRAY => '47',
		);

		foreach ($backgroundColor as $colorName => $colorValue) {
			$tests[] = array(
				$colorName,
				$colorValue
			);
		}

		return $tests;

	}

	/**
	 * @return array
	 * @author Manuel Will
	 * @since 2013
	 */
	public static function testDataColorForeground() {
		$tests = array();

		$foregroundColors = array(
			Cli_Output::COLOR_BLACK => '0;30',
			Cli_Output::COLOR_DARK_GREY => '1;30',
			Cli_Output::COLOR_BLUE => '0;34',
			Cli_Output::COLOR_LIGHT_BLUE => '1;34',
			Cli_Output::COLOR_GREEN => '0;32',
			Cli_Output::COLOR_LIGHT_GREEN => '1;32',
			Cli_Output::COLOR_CYAN => '0;36',
			Cli_Output::COLOR_LIGHT_CYAN => '1;36',
			Cli_Output::COLOR_RED => '0;31',
			Cli_Output::COLOR_LIGHT_RED => '1;31',
			Cli_Output::COLOR_PURPLE => '0;35',
			Cli_Output::COLOR_LIGHT_PURPLE => '1;35',
			Cli_Output::COLOR_LIGHT_YELLOW => '0;33',
			Cli_Output::COLOR_YELLOW => '1;33',
			Cli_Output::COLOR_LIGHT_GRAY => '0;37',
			Cli_Output::COLOR_WHITE => '1;37'
		);

		foreach ($foregroundColors as $colorName => $colorValue) {
			$tests[] = array(
				$colorName,
				$colorValue
			);
		}

		return $tests;

	}

	/**
	 */
	public function testInvalidColorForeground() {
		$this->setExpectedException('InvalidArgumentException', 'Unknown foreground color.');
		$cliOutput = new Cli_Output();
		$cliOutput->createLine('testLine', false, 'asdf');
	}

	/**
	 */
	public function testInvalidColorBackground() {
		$this->setExpectedException('InvalidArgumentException', 'Unknown background color.');
		$cliOutput = new Cli_Output();
		$cliOutput->createLine('testLine', false, null, 'asd');
	}

}
