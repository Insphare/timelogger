<?php

/**
 * Class Command_Show
 */
class Command_Show extends Command_Abstract {

	/**
	 * @return string
	 */
	public function execute() {
		return $this->getDay(time());
	}



	/**
	 * @param $amount
	 *
	 * @return string
	 */
	private function tabs($amount) {
		$str = '';
		$count = 0;

		while(true) {
			$count++;
			$str .= "\t";
			if ($count === $amount) {
				break;
			}
		}

		return $str;
	}



	/**
	 * @param $string
	 * @param $amount
	 *
	 * @return string
	 */
	private function padLeft($string, $amount) {
		return str_pad($string, $amount, ' ', STR_PAD_LEFT);
	}

	/**
	 * @param $timestamp
	 * @return string
	 */
	protected function getDay($timestamp) {
		$workObjects = $this->getWorkedObject($timestamp);

		if (empty($workObjects)) {
			return 'There are no logs, yet.';
		}

		$separator1 = str_pad('', 110, '-');
		$separator2 = str_pad('', 41, '-');
		$durationSpace = 13;

		$data = array();
		$data[] = '';
		$data[] = $separator1;
		$data[] = 'Details (' . date('Y-m-d', $timestamp) . ')';
		$data[] = $separator1;

		$line = array();
		$line[] = $this->fixTasksLength('Task') . $this->tabs(1) ;
		$line[] = 'Start' . $this->tabs(2);
		$line[] = 'Stop' . $this->tabs(2);
		$line[] = $this->padLeft('Duration', $durationSpace) . $this->tabs(1);
		$line[] = $this->padLeft('Break', $durationSpace) . $this->tabs(1);
		$line[] = $this->padLeft('Round (h)', $durationSpace+1);

		$data[] = implode('', $line);
		$data[] = $separator1;

		$today = 0;
		$summaryBreak = 0;
		$group = array();

		foreach ($workObjects as $workObject) {
			$hourUnit = $this->getCalculator()->getHourUnit($workObject->getDuration());
			$today += ($workObject->getDuration());
			if (!isset($group[$workObject->getLabel()])) {
				$group[$workObject->getLabel()] = 0;
			}
			$group[$workObject->getLabel()] += ($workObject->getDuration());

			$line = array();
			$line[] = $this->fixTasksLength($workObject->getLabel()) . $this->tabs(1);
			$line[] = date('H:i:s', $workObject->getStarted()) . $this->tabs(1);
			$line[] = date('H:i:s', $workObject->getStopped()) . $this->tabs(1);
			$line[] = $this->getCalculator()->getHumanAbleList($workObject->getDuration()) . $this->tabs(1);
			$line[] = $this->padLeft($this->getCalculator()->getHumanAbleList($workObject->getBreakTime()), $durationSpace);
			$line[] = $this->padLeft($hourUnit, $durationSpace-1);

			$summaryBreak += $workObject->getBreakTime();

			$data[] = implode('', $line);
		}
		$data[] = $separator1;

		$data[] = '';

		// summary
		$data[] = '';
		$data[] = $separator2;
		$data[] = $this->fixTasksLength('Task').$this->tabs(1).'Log hours';
		$data[] = $separator2;
		$summaryTime = 0;
		foreach ($group as $task => $seconds) {
			$rounded = $this->getCalculator()->getHourUnit($seconds);
			$summaryTime += $this->fixStringToFloat($rounded);
			$data[] = $this->fixTasksLength($task) .$this->tabs(1) . $rounded;
		}
		$data[] = $separator2;
		$data[] = '';
		$data[] = '';
		$data[] = $separator2;
		$data[] = $this->fixTasksLength('Summary break') . $this->tabs(1) . $this->getCalculator()->getHourUnit($summaryBreak);
		$data[] = $separator2;
		$data[] = $this->fixTasksLength('Summary worked') . $this->tabs(1) . $this->fixFloatToString($summaryTime);
		$data[] = $separator2;

		return implode(PHP_EOL, $data);
	}

	/**
	 * @param $task
	 * @return string
	 */
	protected function fixTasksLength($task) {
		return str_pad($task, Command_Abstract::TASK_LENGTH, ' ');
	}

	/**
	 * @param $value
	 * @return float
	 */
	protected function fixStringToFloat($value) {
		return (float)str_replace(',', '.', $value);
	}

	/**
	 * @param $value
	 * @return string
	 */
	protected function fixFloatToString($value) {
		$float = number_format($value, 2, '.', ',');
		$float = str_replace('.', ',', $float);
		return str_pad($float, 4, '0');
	}

	/**
	 * @param $timestampToday
	 * @return Work_Container[]
	 */
	protected function getWorkedObject($timestampToday) {
		/**
		 * @var $arrObjects Work_Container[]
		 */
		$arrObjects = array();
		$currentDate = date('Y-m-d', $timestampToday);
		$files = $this->getFiles();
		if (isset($files{$currentDate})) {
			foreach ($files{$currentDate} as $file) {
				$data = FileManager::get()->loadData($file);
				$work = new Work_LoadByData($data);
				$arrObjects[$work->getStarted()] = $work;
			}
		}

		ksort($arrObjects);
		return $arrObjects;
	}

	/**
	 * @return array
	 */
	protected function getFiles() {
		$destination = FileManager::get()->getDirTasks();
		$search = new FileDirectoryIterator($destination);
		$search->addAllowedExtension('dat');
		$search->setRecursive(true);
		$files = $search->getFiles();
		$arrResult = array();
		foreach ($files as $file) {
			$fileName = str_replace($destination, '', $file);
			$timeStamp = preg_replace('~^([0-9]+).*~', '$1', $fileName);
			$fileDate = date('Y-m-d', $timeStamp);

			$arrResult[$fileDate][] = $file;
		}

		ksort($arrResult);

		return $arrResult;
	}
}
