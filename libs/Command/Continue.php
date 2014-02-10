<?php

/**
 * Class Command_Continue
 */
class Command_Continue extends Command_Pause {

	/**
	 * @throws Command_Exception
	 * @return string
	 */
	public function execute() {
		$this->assertActiveLocking();

		$workObject = $this->getWorkObject();

		if (false === $workObject->hasBreakTime()) {
			throw new Command_Exception('Currently, you are not in breaking.');
		}

		$workObject->stopBreakTime();
		$this->saveCacheData($workObject, 'Start');
		$this->getFileManager()->lockActionsForCommands($this->lockStart);

		$string = 'Resuming on task \'' . $workObject->getLabel() . '\'';
		return $string;
	}
}
