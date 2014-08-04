<?php

/**
 * Class Command_Append
 *
 * @author Manuel Will <insphare@gmail.com>
 * @copyright Copyright (c) 2014, Manuel Will
 */
class Command_Append extends Command_Abstract {

	/**
	 * @throws Command_Exception
	 * @return string
	 */
	public function execute() {
		$this->assertArguments('Usage: append <workname> (-)<start>h|now <to>h, E.g. append refactoring -1h 1,25h');

		$work = $this->getArgument(1);
		$startTime = $this->getArgument(2);
		$duration = $this->getArgument(3);

		if (empty($startTime)) {
			throw new Command_Exception('Argument "from" is required.');
		}

		if (empty($duration)) {
			throw new Command_Exception('Argument "to" is required.');
		}

		$message = $this->handleAction($work, $startTime, $duration);
		return $message;
	}

	/**
	 * @param $string
	 * @param bool $canNegative
	 * @return float|int|mixed
	 * @throws Command_Exception
	 */
	protected function getHourInSeconds($string, $canNegative = true) {
		if ($string === 'now' && true === $canNegative) {
			return 0;
		}

		if (!preg_match('~\A(?<minus>-)?(?<float>\d+((,|\.)(0|00|25|5|50|75))?)h\z~', $string, $arrMatch)) {
			throw new Command_Exception('Invalid time argument: ' . $string);
		}

		$hour = $arrMatch['float'];
		$hour = str_replace(',', '.', $hour);
		$hour = (float)$hour;
		$minus = $arrMatch['minus'];

		$hour = $hour * 60 * 60;

		if (false === $canNegative && !empty($minus)) {
			throw new Command_Exception('Argument "to" can not negative!');
		}

		if (!empty($minus)) {
			$hour *= -1;
		}

		return $hour;
	}

	/**
	 * @param $workName
	 * @param $startTime
	 * @param $duration
	 * @throws Command_Exception
	 * @return string
	 */
	private function handleAction($workName, $startTime, $duration) {
		$workObject = $this->getStoredWorkObjectByNameOfTheDay($workName);

		$oldDurationString = $this->getDurationLine($workObject);

		$fromSec = time() - $this->getHourInSeconds($startTime);
		$duration = $this->getHourInSeconds($duration, true); // allow negative seconds to correct time
		$workObject->appendManualWorkTime($fromSec, $duration);
		$newDurationString = $this->getDurationLine($workObject);

		$confirmMessage = 'Would you really append this time to work: \'' . $workName . '\' with following changes:' . PHP_EOL;
		$this->getCliOutput()->createLine($confirmMessage, true, Cli_Output::COLOR_LIGHT_RED);
		$this->getCliOutput()->createLine('Append time to work: ' . $workName, true);
		$this->getCliOutput()->createLine('Start time: ' . $startTime, true);
		$this->getCliOutput()->createLine('Duration: ' . $duration, true);
		$this->getCliOutput()->createLine('----', true);
		$this->getCliOutput()->createLine('Old duration time. ' . $oldDurationString, true);
		$this->getCliOutput()->createLine('New duration time. ' . $newDurationString, true);
		$this->getCliOutput()->createLine('', true);
		$this->getCliOutput()->createLine('[y/n]', true);
		$this->getCliOutput()->flush();

		while (true) {
			$line = $this->getCliPrompt()->promptToUserInput();
			$line = trim(strtolower($line));

			switch ($line) {
				case 'n':
					throw new Command_Exception('You have canceled appending works.');

				case 'y':
					$this->getFileManager()->storeWork($workObject);

					break 2;

				default:
					break;
			}

			$this->getCliOutput()->createLine('Please answer with [y/n]', Cli_Output::COLOR_LIGHT_RED)->flush();
		}

		return 'Appended time to work:  \'' . $workName . '\'';
	}
}
