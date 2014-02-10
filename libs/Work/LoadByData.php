<?php

/**
 * Class Work_LoadByData
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
