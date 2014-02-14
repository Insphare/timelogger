<?php

/**
 * Class Command_Resume
 *
 *  @author Manuel Will <insphare@gmail.com>
 *  @copyright Copyright (c) 2014, Manuel Will
 */
class Command_Resume extends Command_Abstract {

	/**
	 * @throws Command_Exception
	 * @return string
	 */
	public function execute() {
		$cacheDataWorkName = $this->loadCacheData();

		if (empty($cacheDataWorkName)) {
			throw new Command_Exception('No work available to resume.');
		}

		$startCommand = new Command_Start(array($cacheDataWorkName));
		$output = $startCommand->execute();
		return $output;
	}

}
