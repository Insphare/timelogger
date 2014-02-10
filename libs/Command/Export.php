<?php

/**
 * Class Command_Export
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
			$report = $this->getDay(strtotime($date));
			$this->getFileManager()->saveReport($date, $report);
		}

		return $x . ' files exported.';
	}
}

