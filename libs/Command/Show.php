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
	 * @param $timestamp
	 * @return string
	 */
	protected function getDay($timestamp) {
		$workObjects = $this->getWorkedObject($timestamp);

		if (empty($workObjects)) {
			return 'There are no logs, yet.';
		}

		$separator1 = str_pad('', 125, '-');
		$separator2 = str_pad('', 33, '-');

		$data = array();
		$data[] = '';
		$data[] = $separator1;
		$data[] = 'Details';
		$data[] = $separator1;
		$data[] = 'Task		Start			Stop			' . str_pad('Duration', 13, ' ', STR_PAD_LEFT) . '		' . str_pad('Break', 13, ' ', STR_PAD_LEFT) . '		Rounded hours';
		$data[] = $separator1;

		$today = 0;
		$group = array();

		foreach ($workObjects as $workObject) {
			$hourUnit = $this->getCalculator()->getHourUnit($workObject->getDuration());
			$today += ($workObject->getDuration());
			if (!isset($group[$workObject->getLabel()])) {
				$group[$workObject->getLabel()] = 0;
			}
			$group[$workObject->getLabel()] += ($workObject->getDuration());

			$line = array();
			$line[] = $this->fixTasksLength($workObject->getLabel()) . "\t";
			$line[] = date('Y-m-d H:i:s', $workObject->getStarted()) . "\t";
			$line[] = date('Y-m-d H:i:s', $workObject->getStopped()) . "\t";
			$line[] = $this->getCalculator()->getHumanAbleList($workObject->getDuration()) . "\t\t";
			$line[] = str_pad($this->getCalculator()->getHumanAbleList($workObject->getBreakTime()), 13, ' ', STR_PAD_LEFT);
			$line[] = str_pad($hourUnit, 13, ' ', STR_PAD_LEFT);

			$data[] = implode('', $line);
		}
		$data[] = $separator1;

		$data[] = '';
		$data[] = '';

		// summary
		$data[] = '';
		$data[] = $separator2;
		$data[] = 'Task			Log hours';
		$data[] = $separator2;
		$summaryTime = 0;
		foreach ($group as $task => $seconds) {
			$rounded = $this->getCalculator()->getHourUnit($seconds);
			$summaryTime += $this->fixStringToFloat($rounded);
			$data[] = $this->fixTasksLength($task) . "\t\t" . str_pad($rounded, 9, ' ', STR_PAD_LEFT);
		}
		$data[] = $separator2;
		$data[] = '';
		$data[] = '';
		$data[] = $separator2;
		$data[] = $this->fixTasksLength('Summary') . "\t\t" . str_pad($this->fixFloatToString($summaryTime), 9, ' ', STR_PAD_LEFT);
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
