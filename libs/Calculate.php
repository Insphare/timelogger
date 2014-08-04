<?php

/**
 * Class Calculate
 *
 * @author Manuel Will <insphare@gmail.com>
 * @copyright Copyright (c) 2014, Manuel Will
 */
class Calculate {

	/**
	 * Length
	 */
	const PAD_LENGTH = 13;

	/**
	 * Fill character
	 */
	const FILL_CHARACTER = ' ';
	/**
	 *
	 */
	const PAD_LENGTH_GAP = 4;

	/**
	 * @param $intSeconds
	 * @return string
	 */
	public function getHourUnit($intSeconds) {
		if ($intSeconds === 0) {
			return '0,00';
		}

		$hourUnit = number_format(round(($intSeconds / 60) / 60, 3), 2, ',', '.');
		preg_match('~-?(?<before>\d+),(?<comma>\d+)~i', $hourUnit, $match);
		$check = $match['comma'];
		$round = false;
		$plusH = false;
		$plusM = 0;
		$tolerance = 10;

		switch (true) {
			case $check <= 10:
				$round = true;
				$plusH = false;
				$plusM = 00;
				break;

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

		$hour = (int)$match['before'];
		if (true === $round) {
			if (true === $plusH) {
				$hour++;
			}

			if ($plusM == 0) {
				$plusM = '00';
			}

			$hourUnit = $hour . ',' . $plusM;
		}

		return (($intSeconds<0) ? '-' : '') . $hourUnit;
	}

	/**
	 * @param $string
	 * @param $length
	 *
	 * @param string $fillCharacter
	 * @return string
	 */
	private function padString($string, $length, $fillCharacter = self::FILL_CHARACTER) {
		return str_pad($string, $length, $fillCharacter, STR_PAD_LEFT);
	}

	/**
	 * @param $diffSeconds
	 * @return string
	 */
	public function getHumanAbleList($diffSeconds) {
		$dateTime = new DateTime();
		$dateTimeDiff = $dateTime->diff(new DateTime('@'.(time()+$diffSeconds)));

		$hours = $this->padString($dateTimeDiff->h, 1);
		$minutes = $this->padString($dateTimeDiff->i, self::PAD_LENGTH_GAP);
		$seconds = $this->padString($dateTimeDiff->s, self::PAD_LENGTH_GAP);

		if ($hours > 0) {
			$return = sprintf('%sh%sm%ss', $hours, $minutes, $seconds);
		}
		elseif ((int)$hours <= 0 && (int)$minutes <= 0) {
			$return = sprintf('%ss', $seconds);
		}
		else {
			$return = sprintf('%sm%ss', $minutes, $seconds);
		}

		return $this->padString(($diffSeconds<0?'-':'').$return, self::PAD_LENGTH);
	}
}
