<?php

/**
 * Class Command_Info
 *
 * @author Manuel Will <insphare@gmail.com>
 * @copyright Copyright (c) 2014, Manuel Will
 */
class Command_Info extends Command_Stop {

	/**
	 * @return string
	 */
	public function execute() {
		$this->assertActiveLogging();

		$workObject = $this->getWorkObjectFromCacheData();
		$workName = $workObject->getLabel();

		if ($workObject->hasActiveWorkTime()) {
			$workObject->stopWorkTime();
		}

		if (true === $workObject->hasActiveBreakTime()) {
			$workObject->stopBreakTime();
		}

		$string = 'You are working on \'' . $workName . '\'. ' . $this->getDurationLine($workObject);
		return $string;
	}
}

