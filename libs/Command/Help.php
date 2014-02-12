<?php

/**
 * Class Command_Help
 *
 *  @author Manuel Will <insphare@gmail.com>
 *  @copyright Copyright (c) 2014, Manuel Will
 */
class Command_Help extends Command_Abstract {

	/**
	 * @return string
	 */
	public function execute() {
		return 'Below are the available commands.';
	}
}
