<?php

/**
 * Class Command_Change
 */
class Command_Change extends Command_Stop {

	/**
	 * @return string
	 */
	public function execute() {
		$this->assertActiveLocking();
		$workObject = $this->getWorkObject();

		$taskLabelOld = $workObject->getLabel();
		$taskLabelNew = $this->getArgument(1);
		$this->checkLength('Task name', $taskLabelNew, Command_Abstract::TASK_LENGTH);

		if (empty($taskLabelNew)) {
			$this->throwError('Please enter a new task name.');
		}

		$this->assertArguments();
		$workObject->setLabel($taskLabelNew);

		$this->saveCacheData($workObject, 'Start');
		return 'Change name from \'' . $taskLabelOld . '\' to \'' . $taskLabelNew . '\'';
	}
}
