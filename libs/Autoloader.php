<?php

/**
 * Autloader class logic.
 */
class Autoloader {

	/**
	 * @var null
	 */
	private $basePath = null;

	/**
	 * @var array
	 */
	private $includePathDirectories = array();

	/**
	 * @param $basePath
	 */
	public function __construct($basePath) {
		$this->basePath = $basePath;
		$this->includePathDirectories = explode(':', get_include_path());
	}

	/**
	 * @param $className
	 * @return bool
	 * @throws Exception
	 */
	public function loadFileByClassName($className) {
		// already loaded
		if (true === class_exists($className)) {
			return true;
		}

		$path = $this->getFilePath($className);
		if (false !== $path) {
			include_once($path);
			return true;
		}

		throw new Autoloader_Exception('Class: "' . $className . '" can not found or does not exists.');
	}

	/**
	 * @param $className
	 * @return bool
	 */
	private function getFilePath($className) {
		$fileName = str_replace('_', DIRECTORY_SEPARATOR, $className) . '.php';
		foreach ($this->includePathDirectories as $directory) {
			$path = $directory . DIRECTORY_SEPARATOR . $fileName;
			$path = preg_replace('~(' . preg_quote(DIRECTORY_SEPARATOR) . ')\1+~', '$1', $path);

			if (true === file_exists($path)) {
				return $path;
			}
		}

		return false;
	}
}

/**
 * Class Autoloader_Exception
 */
class Autoloader_Exception extends Exception {
}
