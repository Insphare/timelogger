<?php

/**
 * Class Command_Continue
 *
 * @author Manuel Will <insphare@gmail.com>
 * @copyright Copyright (c) 2014, Manuel Will
 */
class Command_Continue extends Command_Pause {

	/**
	 * @throws Command_Exception
	 * @return string
	 */
	public function execute() {
		$this->assertActiveLocking();

		$workObject = $this->getWorkObjectFromCacheData();

		if (false === $workObject->hasActiveBreakTime()) {
			throw new Command_Exception('Currently, you are not in breaking.');
		}

		$workObject->stopBreakTime();
		$workObject->startWorkTime();
		$this->saveCacheData($workObject, 'Start');
		$this->getFileManager()->lockActionsForCommands($this->lockStart);

		$string = 'Resuming on task \'' . $workObject->getLabel() . '\'.';
		return $string;
	}
}
