<?php

/**
 * Class Command_Compact
 *
 * @author Manuel Will <insphare@gmail.com>
 * @copyright Copyright (c) 2014, Manuel Will
 */
class Command_Compact extends Command_Show {

	public function execute() {
		return $this->getCompact(time());
	}

}

