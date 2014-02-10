<?php

/**
 * Class Command_Start
 */
class Command_Start extends Command_Abstract {

	/**
	 * @return string
	 */
	public function execute() {
		$taskLabel = $this->getArgument(1);
		$this->checkLength('Task name', $taskLabel, Command_Abstract::TASK_LENGTH);

		if (empty($taskLabel)) {
			$this->throwError('Please enter a task name.');
		}

		$this->assertArguments();

		$workObject = new Work_Container();
		$workObject->setStarted(time());
		$workObject->setLabel($taskLabel);
		$this->saveCacheData($workObject);

		$this->getFileManager()->lockActionsForCommands($this->lockStart);

		return 'Work on \'' . $taskLabel . '\' started';
	}
}
