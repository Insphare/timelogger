<?php

/**
 * Class Command_Info
 */
class Command_Info extends Command_Stop {

	/**
	 * @return string
	 */
	public function execute() {
		$this->assertActiveLocking();

		$workObject = $this->getWorkObject();

		$taskLabel = $workObject->getLabel();
		$workObject->setStopped(time());

		if (true === $workObject->hasBreakTime()) {
			$workObject->stopBreakTime();
		}

		$string = 'You are working on \'' . $taskLabel . '\'. ' . $this->getDurationLine($workObject);
		return $string;
	}
}

