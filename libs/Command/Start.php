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
		$output = array();
		$workName = $this->getArgument(1);
		$this->assertArguments();

		$workObject = $this->getWorkObjectFromCacheData();
		if (null !== $workObject) {
			$stopCommand = new Command_Stop($this->arguments);
			$output[] = $stopCommand->execute();
			$this->saveCacheData($workObject->getLabel(), 'Resume');
		}

		$workObject = $this->getWorkContainerByName($workName);
		$workObject->startWorkTime();
		$workObject->setLabel($workName);
		$this->saveCacheData($workObject);

		$this->getFileManager()->lockActionsForCommands($this->lockStart);
		$output[] = 'Work on \'' . $workName . '\' started';

		return implode(PHP_EOL, $output);
	}

}
