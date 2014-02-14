<?php

/**
 * Class Calculate
 *
 * @author Manuel Will <insphare@gmail.com>
 * @copyright Copyright (c) 2014, Manuel Will
 */
class Calculate {

	/**
	 * @param $intSeconds
	 * @return string
	 */
	public function getHourUnit($intSeconds) {
		if ($intSeconds === 0) {
			return '0,00';
		}

		$hourUnit = number_format(round(($intSeconds / 60) / 60, 3), 2, ',', '.');

		$check = (int)$hourUnit{2} . (int)$hourUnit{3};

		$round = false;
		$plusH = false;
		$plusM = 0;

		$tolerance = 10;

		switch (true) {
			case $check <= 25 + $tolerance:
				$round = true;
				$plusH = false;
				$plusM = 25;
				break;

			case $check <= 50 + $tolerance:
				$round = true;
				$plusH = false;
				$plusM = 50;
				break;

			case $check <= 75 + $tolerance:
				$round = true;
				$plusH = false;
				$plusM = 75;
				break;

			case $check < 99:
				$round = true;
				$plusH = true;
				$plusM = 00;
				break;
		}

		$hour = (int)$hourUnit{0};
		if (true === $round) {
			if (true === $plusH) {
				$hour++;
			}

			$hourUnit = $hour . ',' . $plusM;
		}

		return $hourUnit;
	}

	/**
	 * @param $string
	 * @param $length
	 *
	 * @param string $fillCharacter
	 * @return string
	 */
	private function padString($string, $length, $fillCharacter = ' ') {
		return str_pad($string, $length, $fillCharacter, STR_PAD_LEFT);
	}

	/**
	 * @param $time
	 * @return string
	 */
	public function getHumanAbleList($time) {
		$diffSeconds = $time;
		// is not needed
		//		$years = floor($diffSeconds / 31556926);
		//
		//		$month = floor($diffSeconds / 262974383);
		//		$month = $this->padString($month, 2, 0);
		//
		//		$diffSeconds = $diffSeconds % 31556926;
		//		$days = floor($diffSeconds / 86400);
		//		$days = $this->padString($days, 3, 0);

		$diffSeconds = $diffSeconds % 86400;
		$hours = floor($diffSeconds / 3600);
		$hours = $this->padString($hours, 1);
		$diffSeconds = $diffSeconds % 3600;

		$minutes = floor($diffSeconds / 60);
		$minutes = $this->padString($minutes, 2);

		$diffSeconds = $diffSeconds % 60;
		$diffSeconds = $this->padString($diffSeconds, 2);

		if ($hours > 0) {
			$return = sprintf('%sh  %sm  %ss', $hours, $minutes, $diffSeconds);
		}
		elseif ((int)$hours <= 0 && (int)$minutes <= 0) {
			$return = sprintf('%ss', $diffSeconds);
		}
		else {
			$return = sprintf('%sm  %ss', $minutes, $diffSeconds);
		}

		return $this->padString($return, 13);
	}
}
