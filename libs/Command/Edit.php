<?php

/**
 * IN DEVELOPMENT
 *
 * Class Command_Edit
 *
 *  @author Manuel Will <insphare@gmail.com>
 *  @copyright Copyright (c) 2014, Manuel Will
 */
class Command_Edit extends Command_Show {

	/**
	 * @return string
	 */
	public function execute() {
		$this->assertArguments();

		$work = $this->getArgument(1);
		$action = $this->getArgument(2);
		$value = $this->getArgument(3);

		$message = $this->handleAction($action, $work, $value);
		return $message;
	}

	private function validateAction($action, $work, $value) {
		$validAction = array(
			'time' => true,
		);

		if (!isset($validAction[$action])) {
			throw new Command_Exception('Invalid edit action: \''.$action.\'');
		}

	}

	private function getTask($work) {
		$exceptionMessage = 'Work not found for current day.';

		$files = $this->getFiles();
		$day = date('Y-m-d', time());
		if (empty($files[$day])) {
			throw new Command_Exception($exceptionMessage);
		}

		// load objects search by name?
	}

	private function handleAction($action, $work, $value) {
		$this->validateAction($action, $work, $value);

		// edit vs-123 time +-33
		echo 'action: ' . $action;
		echo PHP_EOL;
		echo 'work: ' . $work;
		echo PHP_EOL;
		echo 'value: ' . $value;
		echo PHP_EOL;
	}
}
