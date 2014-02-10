<?php

/**
 * Class Command_Stop
 */
class Command_Stop extends Command_Abstract {

	/**
	 * @return string
	 */
	public function execute() {
		$this->assertActiveLocking();
		$workObject = $this->getWorkObject();

		$taskLabel = $workObject->getLabel();
		$workObject->setStopped(time());

		$this->getFileManager()->storeWork($workObject);
		$this->saveCacheData(null, 'Start');
		$this->getFileManager()->unlockCommands();
		return 'Work on \'' . $taskLabel . '\' stopped. (Duration: ' . trim($this->getCalculator()->getHumanAbleList($workObject)) . ')';
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

	/**
	 * @return Work_Container
	 */
	protected function getWorkObject() {
		/** @var Work_Container $workObjectOrNull */
		$workObjectOrNull = $this->loadCacheData('Start');
		return $workObjectOrNull;
	}
}
