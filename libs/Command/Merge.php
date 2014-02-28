<?php

/**
 * Class Command_Merge
 *
 * @author Manuel Will <insphare@gmail.com>
 * @copyright Copyright (c) 2014, Manuel Will
 */
class Command_Merge extends Command_Stop {

	/**
	 * @throws Command_Exception
	 * @return string
	 */
	public function execute() {
		$this->assertInactiveLogging();

		$workNameFrom = $this->getArgument(1);
		$workNameTo = $this->getArgument(2);

		if (empty($workNameFrom) && empty($workNameTo)) {
			throw new Command_Exception('Wrong usage. Example: merge <workname-from> <workname-to>');
		}

		if (empty($workNameFrom)) {
			throw new Command_Exception('You have to enter the desired work name to merge.');
		}

		if (empty($workNameFrom)) {
			throw new Command_Exception('You have to enter the work name they should be merged into.');
		}

		if (trim(strtolower($workNameFrom)) == trim(strtolower($workNameTo))) {
			throw new Command_Exception('You can not merge into the same work.');
		}

		// load from work
		$workObjectFrom = $this->getStoredWorkObjectByNameOfTheDay($workNameFrom);

		// load to work
		$workObjectTo = $this->getStoredWorkObjectByNameOfTheDay($workNameTo);

		$workToDuration = $this->getDurationLine($workObjectTo);

		foreach ($workObjectFrom->getWorkTimeItems() as $arrItems) {
			$start = $arrItems['start'];
			$duration = (int)$arrItems['stop'] - (int)$start;
			$workObjectTo->appendManualWorkTime($start, $duration);
		}

		foreach ($workObjectFrom->getBreakTimeItems() as $arrItems) {
			$start = $arrItems['start'];
			$duration = (int)$arrItems['stop'] - (int)$start;
			$workObjectTo->appendManualBreakTime($start, $duration);
		}

		$message = sprintf('Merged from \'%s\' to \'%s\'. ', $workNameFrom, $workNameTo);
		$workObjectTo->addNote($message . ''.$this->getDurationLine($workObjectFrom).'');

		foreach ($workObjectFrom->getNotes() as $note) {
			$workObjectTo->addNote($note);
		}

		$confirmMessage = 'Would you really merge the work: \'' . $workNameFrom . '\' with following changes:' . PHP_EOL;
		$this->getCliOutput()->createLine($confirmMessage, true, Cli_Output::COLOR_LIGHT_RED);
		$this->getCliOutput()->createLine($message, true);
		$this->getCliOutput()->createLine('Duration time \'' . $workNameFrom . '\'. ' . $this->getDurationLine($workObjectFrom), true);
		$this->getCliOutput()->createLine('Duration time \'' . $workNameTo . '\'. ' . $workToDuration, true);
		$this->getCliOutput()->createLine('----', true);
		$this->getCliOutput()->createLine('Merged duration time. ' . $this->getDurationLine($workObjectTo), true);
		$this->getCliOutput()->createLine('', true);
		$this->getCliOutput()->createLine('[y/n]', true);
		$this->getCliOutput()->flush();

		while (true) {
			$line = $this->getCliPrompt()->promptToUserInput();
			$line = trim(strtolower($line));

			switch ($line) {
				case 'n':
					throw new Command_Exception('You have canceled merging works.');

				case 'y':
					$this->getFileManager()->storeWork($workObjectTo);
					$this->getFileManager()->removeWork($workObjectFrom);

					break 2;

				default:
					break;
			}

			$this->getCliOutput()->createLine('Please answer with [y/n]', Cli_Output::COLOR_LIGHT_RED)->flush();
		}

		return $message;
	}
}
