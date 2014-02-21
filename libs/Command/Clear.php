<?php

/**
 * Class Command_Clear
 *
 * @author Manuel Will <insphare@gmail.com>
 * @copyright Copyright (c) 2014, Manuel Will
 */
class Command_Clear extends Command_Abstract {

	/**
	 * @return string
	 */
	public function execute() {
		return chr(12);
	}
}
