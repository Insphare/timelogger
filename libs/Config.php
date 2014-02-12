<?php

/**
 * Class Config
 *
 *  @author Manuel Will <insphare@gmail.com>
 *  @copyright Copyright (c) 2014, Manuel Will
 */
class Config {

	/**
	 * For auto completion only.
	 */
	const BASE_PATH = 'basePath';

	/**
	 * @var array
	 */
	private static $data = array();

	/**
	 * Never init this.
	 */
	private function __construct() {
	}

	/**
	 * @param $key
	 * @param $value
	 * @return bool
	 */
	public static function set($key, $value) {
		self::$data[(string)$key] = $value;
		return true;
	}

	/**
	 * @param $key
	 * @return null
	 */
	public static function get($key) {
		if (isset(self::$data[(string)$key])) {
			return self::$data[(string)$key];
		}

		return null;
	}
}
