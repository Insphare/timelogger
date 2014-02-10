<?php

/**
 * Class Calculate
 */
class Calculate {

	/**
	 * @param Work_Container $workContainer
	 * @return string
	 */
	public function getHourUnit($seconds) {
		$hourUnit = number_format(round(($seconds / 60) / 60, 3), 2, ',', '.');

		$check = (int)$hourUnit{2} . (int)$hourUnit{3};

		$round = false;
		$plusH = false;
		$plusM = 0;

		$tolerance = 10;

		switch (true) {
			case $check <= 25+$tolerance:
				$round = true;
				$plusH = false;
				$plusM = 25;
				break;

			case $check <= 50+$tolerance:
				$round = true;
				$plusH = false;
				$plusM = 50;
				break;

			case $check <= 75+$tolerance:
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
	 * @param $time
	 * @return string
	 */
	public function getHumanAbleList($time) {
		$diffSeconds = $time;
		$years = floor($diffSeconds / 31556926);

		$month = floor($diffSeconds / 262974383);
		$month = str_pad($month, 2, "0", STR_PAD_LEFT);

		$diffSeconds = $diffSeconds % 31556926;
		$days = floor($diffSeconds / 86400);
		$days = str_pad($days, 3, "0", STR_PAD_LEFT);

		$diffSeconds = $diffSeconds % 86400;
		$hours = floor($diffSeconds / 3600);
		$hours = str_pad($hours, 1, " ", STR_PAD_LEFT);
		$diffSeconds = $diffSeconds % 3600;

		$minutes = floor($diffSeconds / 60);
		$minutes = str_pad($minutes, 2, " ", STR_PAD_LEFT);

		$diffSeconds = $diffSeconds % 60;
		$diffSeconds = str_pad($diffSeconds, 2, " ", STR_PAD_LEFT);

		if ($hours > 0) {
			$return = sprintf('%sh  %sm  %ss', $hours, $minutes, $diffSeconds);
		}
		elseif ((int)$hours <= 0 && (int)$minutes <= 0) {
			$return = sprintf('%ss', $diffSeconds);
		}
		else {
			$return = sprintf('%sm  %ss', $minutes, $diffSeconds);
		}

		return str_pad($return, 13, ' ', STR_PAD_LEFT);
	}
}
