<?php

/**
 * Class Command_Change
 *
 * @author Manuel Will <insphare@gmail.com>
 * @copyright Copyright (c) 2014, Manuel Will
 */
class Command_Change extends Command_Stop {

	/**
	 * @param array $arguments
	 */
	public function __construct(array $arguments) {
		$this->summaryArgumentsToOne();
		parent::__construct($arguments);
	}

	/**
	 * @return string
	 */
	public function execute() {
		$this->assertActiveLogging();
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

		return 'Change name from \'' . $workNameOld . '\' to \'' . $workNameNew . '\'.';
	}
}
