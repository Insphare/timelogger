<?php

/**
 * Class CalculateTest
 */
class CalculateTest extends PHPUnit_Framework_TestCase {

	/**
	 * @return Calculate
	 */
	private function getCalculator() {
		return new Calculate();
	}

	/**
	 * @param integer $seconds
	 * @param $expectedString
	 * @dataProvider testDataSecondsTwo
	 */
	public function testHumanReadableView($seconds, $expectedString) {
		$list = $this->getCalculator()->getHumanAbleList($seconds);
		$expected = str_pad($expectedString, Calculate::PAD_LENGTH, Calculate::FILL_CHARACTER, STR_PAD_LEFT);
		$this->assertSame($expected, $list);
	}

	/**
	 * @return array
	 */
	public static function testDataSecondsTwo() {
		return array(
			array(
				0,
				'0s'
			),

			array(
				58,
				'58s'
			),
			array(
				60,
				'1m' . self::getGap(0) . 's'
			),
			array(
				62,
				'1m' . self::getGap(2) . 's'
			),
			array(
				600,
				'10m' . self::getGap(0) . 's'
			),
			array(
				3540,
				'59m' . self::getGap(0) . 's'
			),
			array(
				3550,
				'59m' . self::getGap(10) . 's'
			),
			array(
				3600,
				'1h' . self::getGap(0) . 'm' . self::getGap(0) . 's'
			),
			array(
				5641,
				'1h' . self::getGap(34) . 'm' . self::getGap(1) . 's'
			),
			array(
				3600 * 8 + 54 + 65,
				'8h' . self::getGap(1) . 'm' . self::getGap(59) . 's'
			),
			array(
				3600 * 15 + 323 + 544,
				'15h' . self::getGap(14) . 'm' . self::getGap(27) . 's'
			),
			array(
				3600 * 22 + 323 + 65,
				'22h' . self::getGap(6) . 'm' . self::getGap(28) . 's'
			),

		);
	}

	/**
	 * @param $amount
	 * @return string
	 */
	private static function getGap($amount) {
		return str_pad($amount, Calculate::PAD_LENGTH_GAP, ' ', STR_PAD_LEFT);
	}

	/**
	 * @param integer $seconds
	 * @param $expectedString
	 * @dataProvider testDataSeconds
	 */
	public function testHourUnit($seconds, $expectedString) {
		$hourUnit = $this->getCalculator()->getHourUnit($seconds);
		$this->assertSame($expectedString, $hourUnit);
	}

	/**
	 * @return array
	 */
	public static function testDataSeconds() {
		return array(
			array(
				0,
				'0,00'
			),
			array(
				3600,
				'1,00'
			),
			array(
				3650,
				'1,00'
			),
			array(
				3700,
				'1,00'
			),
			array(
				4200,
				'1,25'
			),
			array(
				5000,
				'1,50'
			),
			array(
				5340,
				'1,50'
			),
			array(
				3600 + (30 * 60),
				'1,50'
			),
			array(
				3600 + (35 * 60),
				'1,50'
			),
			array(
				3600 + (38 * 60),
				'1,75'
			),
			array(
				3600 + (44 * 60),
				'1,75'
			),
			array(
				3600 + (45 * 60),
				'1,75'
			),
			array(
				3600 + (48 * 60),
				'1,75'
			),
			array(
				3600 + (50 * 60),
				'1,75'
			),
			array(
				3600 + (55 * 60),
				'2,00'
			),
		);
	}
}
