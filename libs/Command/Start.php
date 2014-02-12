<?php

/**
 * Class Command_Start
 *
 *  @author Manuel Will <insphare@gmail.com>
 *  @copyright Copyright (c) 2014, Manuel Will
 */
class Command_Start extends Command_Abstract {

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
		$taskLabel = $this->getArgument(1);
		$this->checkLength('Task name', $taskLabel, Command_Abstract::TASK_LENGTH_NAME);

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
