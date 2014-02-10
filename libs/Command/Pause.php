<?php

/**
 * Class Command_Break
 */
class Command_Pause extends Command_Stop {

	/**
	 * @throws Command_Exception
	 * @return string
	 */
	public function execute() {
		$this->assertActiveLocking();

		$workObject = $this->getWorkObject();
		if (true === $workObject->hasBreakTime()) {
			throw new Command_Exception('Currently, this work is breaking.');
		}

		$workObject->startBreakTime();
		$this->saveCacheData($workObject, 'Start');
		$this->getFileManager()->lockActionsForCommands($this->lockBreak);

		$string = 'Breaking on task \'' . $workObject->getLabel() . '\'';
		return $string;
	}
}
