<?php

/**
 * Class Command_Compact
 *
 * @author Manuel Will <insphare@gmail.com>
 * @copyright Copyright (c) 2014, Manuel Will
 */
class Command_Compact extends Command_Show {

	public function execute() {
		$workObjects = $this->getWorkedObjects(time());

		if (empty($workObjects)) {
			return 'There are no logs, yet.';
		}

		$spaceLabel = Command_Abstract::WORK_LENGTH_NAME;
		$spaceWork = 7;
		$spaceNotes = 30;

		$output = array();
		$output[] = '';
		//$output[] = '┌' . str_pad('', $spaceLabel, '─', STR_PAD_RIGHT) . str_pad('', '┬', 1,STR_PAD_RIGHT) . str_pad('', $spaceWork, '─') . '┬' . str_pad('', $spaceNotes, '─') . '┐';

//		$output[] = str_pad('', $spaceLabel, '-', STR_PAD_RIGHT) . '-' . str_pad('', $spaceWork, '-') . '-' . str_pad('', $spaceNotes, '-') . '-';
		/** @var $wo Work_Container */
		foreach ($workObjects as $wo) {
			$tmp = array();
//			$tmp[] = '│';
			$tmp[] = '| ';
			$tmp[] = str_pad($wo->getLabel(), $spaceLabel, ' ', STR_PAD_RIGHT);
//			$tmp[] = '┼';
			$tmp[] = '|';
			$tmp[] = str_pad(' ' . $this->getCalculator()->getHourUnit($wo->getWorkTime()).' h ', $spaceWork, ' ', STR_PAD_RIGHT);
//			$tmp[] = '┼';
			$tmp[] = '|';
			$notes = implode(', ', $wo->getNotes());
			if (empty($notes)) {
				$notes = '-';
			}
			$tmp[] = $notes;
//			$tmp[] = '│';
			$tmp[] = ' |';

			$output[] = implode('', $tmp);
		}

		return implode(PHP_EOL, $output);
	}

}

