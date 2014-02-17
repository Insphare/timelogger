<?php

/**
 * Class Command_Export
 *
 * @author Manuel Will <insphare@gmail.com>
 * @copyright Copyright (c) 2014, Manuel Will
 */
class Command_Export extends Command_Show {

	/**
	 * @return string
	 */
	public function execute() {
		$files = $this->getFiles();
		$dates = array_keys($files);

		$x = 0;
		foreach ($dates as $date) {
			$x++;
			$report = $this->getDay(strtotime($date), true);
			$this->getFileManager()->saveReport($date, $report);
		}

		return $x . ' files exported.';
	}
}

