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
		$this->assertActiveLocking();
		$workObject = $this->getWorkObjectFromCacheData();

		$taskLabel = $workObject->getLabel();
		$workObject->stopWorkTime();
		$workObject->setIsNew(false);
		$this->getFileManager()->storeWork($workObject);
		$this->saveCacheData(null, 'Start');
		$this->getFileManager()->unlockCommands();
		return 'Work on \'' . $taskLabel . '\' stopped. ' . $this->getDurationLine($workObject);
	}

	/**
	 * @param Work_Container $workContainer
	 * @return string
	 */
	protected function getDurationLine(Work_Container $workContainer) {
		$workContainer->calculate();
		$duration = trim($this->getCalculator()->getHumanAbleList($workContainer->getWorkTime()));
		$break = trim($this->getCalculator()->getHumanAbleList($workContainer->getBreakTime()));
		return '(Duration: ' . $duration . ' excl. break: ' . $break . ')';
	}

	/**
	 *
	 */
	protected function assertActiveLocking() {
		/** @var Work_Container $data */
		$data = $this->loadCacheData('Start');
		if (empty($data) || !$data instanceof Work_Container) {
			$this->throwError('Currently, no log is active.');
		}
	}
}
