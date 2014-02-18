<?php

/**
 * Class Command_Stop
 *
 * @author Manuel Will <insphare@gmail.com>
 * @copyright Copyright (c) 2014, Manuel Will
 */
class Command_Stop extends Command_Abstract {

	/**
	 * @return string
	 */
	public function execute() {
		$this->assertActiveLogging();
		$workObject = $this->getWorkObjectFromCacheData();

		$taskLabel = $workObject->getLabel();
		$workObject->stopWorkTime();
		$workObject->setIsNew(false);
		$this->getFileManager()->storeWork($workObject);
		$this->saveCacheData(null, 'Start');
		$this->getFileManager()->unlockCommands();
		return 'Work on \'' . $taskLabel . '\' stopped. ' . $this->getDurationLine($workObject);
	}
}
