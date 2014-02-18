<?php

/**
 * Class Command_Note
 *
 * @author Manuel Will <insphare@gmail.com>
 * @copyright Copyright (c) 2014, Manuel Will
 */
class Command_Note extends Command_Stop {

	/**
	 * @param array $arguments
	 */
	public function __construct(array $arguments) {
		$this->summaryArgumentsToOne();
		parent::__construct($arguments);
	}

	/**
	 * @throws Command_Exception
	 * @return string
	 */
	public function execute() {
		$this->assertActiveLogging();
		$note = $this->getArgument(1);

		if (empty($note)) {
			throw new Command_Exception('This command requires an argument as note.');
		}

		$workObject = $this->getWorkObjectFromCacheData();
		$workObject->addNote($note);
		$this->saveCacheData($workObject, 'Start');
		$string = 'Added note to work \'' . $workObject->getLabel() . '\'.';
		return $string;
	}
}
