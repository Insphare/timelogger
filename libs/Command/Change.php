<?php

/**
 * Class Command_Change
 *
 *  @author Manuel Will <insphare@gmail.com>
 *  @copyright Copyright (c) 2014, Manuel Will
 */
class Command_Change extends Command_Stop {

	/**
	 * @return string
	 */
	public function execute() {
		$this->assertActiveLocking();
		$workObject = $this->getWorkObjectFromCacheData();

		$lastWorkTimeBegin = $workObject->getLastWorkTimeBegin();
		$workNameOld = $workObject->getLabel();
		$workNameNew = $this->getArgument(1);
		$workObjectFromFile = $this->getWorkContainerByName($workNameNew);
		$this->assertArguments();

		if (true === $workObjectFromFile->getIsNew()) {
			$workObject->setLabel($workNameNew);
			$this->saveCacheData($workObject, 'Start');
		}
		else {
			$workObjectFromFile->setLastWorkTimeBegin($lastWorkTimeBegin);
			$this->saveCacheData($workObjectFromFile, 'Start');
		}

		return 'Change name from \'' . $workNameOld . '\' to \'' . $workNameNew . '\'';
	}
}
