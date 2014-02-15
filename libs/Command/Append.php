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

	protected function getWork($workObject) {
		$exceptionMessage = 'Work not found for current day.';
		$workObject = $this->getFileManager()->getWorkContainerByWorkName($workObject);
		if (empty($workObject)) {
			throw new Command_Exception($exceptionMessage);
		}

		return $workObject;
	}

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

	private function handleAction($workName, $startTime, $duration) {
		$workObject = $this->getWork($workName);

		$output = array();
		$output[] = $this->getDurationLine($workObject);
		$output[] = 'Append time to work: ' . $workName;
		$output[] = 'Start time: ' . $startTime;
		$output[] = 'Duration: ' . $duration;

		$fromSec = time() - $this->getHourInSeconds($startTime);
		$duration = $this->getHourInSeconds($duration, false);
		$workObject->appendManualWorkTime($fromSec, $duration);
		$output[] = $this->getDurationLine($workObject);

		$this->getFileManager()->storeWork($workObject);
		return implode(PHP_EOL, $output);
	}
}
