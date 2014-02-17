<?php

/**
 * Class Command_Show
 *
 * @author Manuel Will <insphare@gmail.com>
 * @copyright Copyright (c) 2014, Manuel Will
 */
class Command_Show extends Command_Abstract {

	/**
	 * Length
	 */
	const DURATION_SPACE_LENGTH = 13;

	/**
	 *
	 */
	const SEPARATOR_ONE_LENGTH = 110;
	/**
	 *
	 */
	const SEPARATOR_TWO_LENGTH = 81;

	/**
	 * @var CLI_Output
	 */
	private static $output = null;

	/**
	 * @var array
	 */
	private $finallyOutput = array();

	/**
	 * @return string
	 */
	public function execute() {
		$day = $this->getDay(time());
		return $day;
	}

	/**
	 * @param bool $reset
	 * @return Cli_Output
	 */
	private function getTempLineObject($reset = false) {
		if (null === self::$output) {
			self::$output = new Cli_Output();
		}

		if (true === $reset) {
			self::$output->reset();
		}

		return self::$output;
	}

	/**
	 * @param $string
	 * @param int $amountTabulators
	 */
	private function appendToTemp($string, $amountTabulators = 0) {
		$this->getTempLineObject()->createLine($string, false);

		if ($amountTabulators > 0) {
			$this->getTempLineObject()->createLine($this->generateTabulatorByAmount($amountTabulators), false);
		}
	}

	/**
	 * @return string
	 */
	private function getTempLine() {
		$string = (string)$this->getTempLineObject();
		$this->getTempLineObject(true);
		return $string;
	}

	/**
	 * @param $amount
	 *
	 * @return string
	 */
	private function generateTabulatorByAmount($amount) {
		$str = '';
		$count = 0;

		while (true) {
			$count++;
			$str .= "\t";
			if ($count >= $amount) {
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
	private function padWithSpaceToLeft($string, $amount) {
		return str_pad($string, $amount, ' ', STR_PAD_LEFT);
	}

	/**
	 * @param $task
	 * @return string
	 */
	protected function fixTasksLength($task) {
		$length = Command_Abstract::TASK_LENGTH_NAME;

		// fix console length on these special character
		$specialWordsCount = preg_match_all('~(ö|ä|ü)~i', $task, $matches);
		if ($specialWordsCount > 0) {
			$length += $specialWordsCount;
		}

		return str_pad($task, $length, ' ');
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
	protected function getWorkedObjects($timestampToday) {
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

		$workObject = $this->getWorkObjectFromCacheData();
		if (!empty($workObject) && $workObject instanceof Work_Container) {
			if ($workObject->hasActiveWorkTime()) {
				$workObject->stopWorkTime();
			}

			if (true === $workObject->hasActiveBreakTime()) {
				$workObject->stopBreakTime();
			}
			$workObject->setMarkedAsActive(true);
			$arrObjects[$workObject->getStarted()] = $workObject;
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

	/**
	 * @param Work_Container $workObject
	 * @param $start
	 * @param $stop
	 * @param bool $isBreak
	 *
	 * @return array
	 * @author Manuel Will
	 * @since 2013
	 */
	protected function getDetailLine(Work_Container $workObject, $start, $stop, $isBreak = false) {
		$duration = (int)($stop - $start);
		$hourUnit = $this->getCalculator()->getHourUnit($duration);

		$line = array();
		$line[] = $this->fixTasksLength($workObject->getLabel()) . $this->generateTabulatorByAmount(1);
		$line[] = date('H:i:s', $start) . $this->generateTabulatorByAmount(1);
		$line[] = date('H:i:s', $stop) . $this->generateTabulatorByAmount(1);

		if (true === $isBreak) {
			$line[] = $this->getCalculator()->getHumanAbleList(0) . $this->generateTabulatorByAmount(1);
			$line[] = $this->padWithSpaceToLeft($this->getCalculator()->getHumanAbleList($duration), self::DURATION_SPACE_LENGTH);
		}
		else {
			$line[] = $this->getCalculator()->getHumanAbleList($duration) . $this->generateTabulatorByAmount(1);
			$line[] = $this->padWithSpaceToLeft($this->getCalculator()->getHumanAbleList(0), self::DURATION_SPACE_LENGTH);
		}

		$line[] = $this->padWithSpaceToLeft($hourUnit, self::DURATION_SPACE_LENGTH - 1);
		$line = implode('', $line);

		return $line;
	}

	/**
	 * @param $mxValue
	 */
	private function appendToOutput($mxValue) {
		if (is_array($mxValue)) {
			foreach ($mxValue as $line) {
				$this->finallyOutput[] = $line;
			}
		}
		else {
			$this->finallyOutput[] = $mxValue;
		}
	}

	/**
	 * @return string
	 */
	private function getSeparatorLineOne() {
		return str_pad('', self::SEPARATOR_ONE_LENGTH, '-');
	}

	/**
	 * @return string
	 */
	private function getSeparatorLineTwo() {
		return str_pad('', self::SEPARATOR_TWO_LENGTH, '-');
	}

	/**
	 * @param $timestamp
	 * @return string
	 */
	protected function getDay($timestamp, $showNotes = false) {
		$workObjects = $this->getWorkedObjects($timestamp);

		if (empty($workObjects)) {
			return 'There are no logs, yet.';
		}

		$this->appendToOutput('');
		$this->appendToOutput(str_pad('', 30, '='));
		$this->appendToOutput(':: Overview from ' . date('Y-m-d', $timestamp) . ' ::');
		$this->appendToOutput(str_pad('', 30, '='));
		$this->appendToOutput('');

		$this->appendToOutput($this->getSeparatorLineOne());
		$this->appendToTemp($this->fixTasksLength('Task'), 1);
		$this->appendToTemp('Start', 2);
		$this->appendToTemp('Stop', 2);
		$this->appendToTemp($this->padWithSpaceToLeft('Duration', self::DURATION_SPACE_LENGTH), 1);
		$this->appendToTemp($this->padWithSpaceToLeft('Break', self::DURATION_SPACE_LENGTH), 1);
		$this->appendToTemp($this->padWithSpaceToLeft('Round (h)', self::DURATION_SPACE_LENGTH), 1);

		$this->appendToOutput($this->getTempLine());
		$this->appendToOutput($this->getSeparatorLineOne());

		$todayWorked = 0;
		$summaryBreak = 0;
		$group = array();
		$tmpLines = array();
		$isActive = false;

		foreach ($workObjects as $workObject) {
			$arrayWork = $workObject->getWorkTimeItems();
			$arrayBreaks = $workObject->getBreakTimeItems();

			if (true === $workObject->getMarkedAsActive()) {
				$isActive = true;
			}

			if (!isset($group[$workObject->getLabel()])) {
				$group[$workObject->getLabel()] = array(
					'work' => 0,
					'break' => 0
				);
			}

			/**
			 * detail lines from work
			 */
			foreach ($arrayWork as $arrRow) {
				$start = (int)($arrRow['start']);
				$stop = (int)($arrRow['stop']);
				$duration = (int)($stop - $start);
				$tmpLines[$start] = $this->getDetailLine($workObject, $start, $stop);

				$group[$workObject->getLabel()]['work'] += ($duration);
			}

			/**
			 * detail lines from break
			 */
			foreach ($arrayBreaks as $arrRow) {
				$start = (int)($arrRow['start']);
				$stop = (int)($arrRow['stop']);
				$duration = (int)($stop - $start);
				$tmpLines[$start] = $this->getDetailLine($workObject, $start, $stop, true);

				$group[$workObject->getLabel()]['break'] += ($duration);
			}

			$todayWorked += $workObject->getWorkTime();
			$summaryBreak += $workObject->getBreakTime();
		}

		ksort($tmpLines);

		if (true === $isActive) {
			$lastItem = array_pop($tmpLines);
			$tmpLines[] = $this->getSeparatorLineOne();
			$tmpLines[] = $lastItem;
		}

		$this->appendToOutput($tmpLines);

		$tmpLines = array();
		$summaryTime = 0;

		foreach ($group as $task => $data) {
			$workSeconds = $data['work'];
			$breakSeconds = $data['break'];
			$rounded = $this->getCalculator()->getHourUnit($workSeconds);
			$summaryTime += $this->fixStringToFloat($rounded);

			$this->appendToTemp($this->fixTasksLength($task), 1);
			$this->appendToTemp($this->padWithSpaceToLeft($this->getCalculator()->getHumanAbleList($workSeconds), self::DURATION_SPACE_LENGTH), 1);
			$this->appendToTemp($this->padWithSpaceToLeft($this->getCalculator()->getHumanAbleList($breakSeconds), self::DURATION_SPACE_LENGTH), 2);
			$this->appendToTemp($rounded);
			$tmpLines[] = $this->getTempLine();
		}

		$summaryBreak = $this->getCalculator()->getHourUnit($summaryBreak);
		$this->appendToOutput($this->getSeparatorLineOne());

		$this->appendToOutput('');
		$this->appendToOutput('');
		$this->appendToOutput($this->getSeparatorLineOne());
		$this->appendToTemp($this->fixTasksLength('Summary'), 6);
		$this->appendToTemp('');

		$this->appendToTemp($this->fixFloatToString($summaryTime), 2);
		$this->appendToTemp($summaryBreak);
		$this->appendToOutput($this->getTempLine());

		$this->appendToOutput($this->getSeparatorLineOne());

		// summary
		$this->appendToOutput('');
		$this->appendToOutput('');
		$this->appendToOutput('');
		$this->appendToOutput($this->getSeparatorLineTwo());

		$this->appendToTemp($this->fixTasksLength('Task'), 1);
		$this->appendToTemp($this->padWithSpaceToLeft('Duration', self::DURATION_SPACE_LENGTH), 1);
		$this->appendToTemp($this->padWithSpaceToLeft('Break', self::DURATION_SPACE_LENGTH), 2);
		$this->appendToTemp('Log hours');

		$this->appendToOutput($this->getTempLine());
		$this->appendToOutput($this->getSeparatorLineTwo());

		$this->appendToOutput($tmpLines);
		$this->appendToOutput($this->getSeparatorLineTwo());

		if (true === $showNotes) {
			$this->appendNotes($workObjects);
		}

		$output = implode(PHP_EOL, $this->finallyOutput);
		$this->finallyOutput = array();
		return $output;
	}



	/**
	 */
	private function appendNotes($workObjects) {

		$this->appendToOutput('');
		$this->appendToOutput('');
		$this->appendToOutput('');
		$this->appendToOutput(str_pad('', 30, '='));
		$this->appendToOutput(':: Notes ::');
		$this->appendToOutput(str_pad('', 30, '='));
		$this->appendToOutput('');

		/**
		 * @var $workObject Work_Container
		 */
		foreach ($workObjects as $workObject) {
			$this->appendToOutput('[Notes for work: ' . $workObject->getLabel() . ']');
			foreach ($workObject->getNotes() as $note) {
				$this->appendToOutput('- ' . $note);
			}
			$this->appendToOutput('');
			$this->appendToOutput('');
		}
	}
}
