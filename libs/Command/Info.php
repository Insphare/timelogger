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

		$string = 'You are working on \'' . $taskLabel . '\'. ';
		$string .= '(Duration: ' . trim($this->getCalculator()->getHumanAbleList($workObject)) . ')';

		return $string;
	}
}

