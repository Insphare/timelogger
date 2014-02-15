<?php

/**
 * Class Command_Break
 *
 * @author Manuel Will <insphare@gmail.com>
 * @copyright Copyright (c) 2014, Manuel Will
 */
class Command_Pause extends Command_Stop {

	/**
	 * @throws Command_Exception
	 * @return string
	 */
	public function execute() {
		$this->assertActiveLocking();

		$workObject = $this->getWorkObjectFromCacheData();
		if (true === $workObject->hasActiveBreakTime()) {
			throw new Command_Exception('Currently, this work is breaking.');
		}

		if ($workObject->hasActiveWorkTime()) {
			$workObject->stopWorkTime();
		}

		$workObject->startBreakTime();
		$this->saveCacheData($workObject, 'Start');
		$this->getFileManager()->lockActionsForCommands($this->lockBreak);

		$string = 'Breaking on task \'' . $workObject->getLabel() . '\'';
		return $string;
	}
}
