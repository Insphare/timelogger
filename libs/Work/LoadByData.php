<?php

/**
 * Class Work_LoadByData
 *
 * @author Manuel Will <insphare@gmail.com>
 * @copyright Copyright (c) 2014, Manuel Will
 */
class Work_LoadByData extends Work_Container {

	/**
	 * @param array $data
	 */
	public function __construct(array $data) {
		foreach ($this->getProperties() as $keyName => $memberVariableName) {
			if (isset($data[$keyName])) {
				$method = 'set' . ucfirst($keyName);
				$this->{$method}($data[$keyName]);
			}
		}
	}
}
