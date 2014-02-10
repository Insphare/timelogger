<?php

/**
 * Class FileDirectoryIterator
 */
class FileDirectoryIterator {

	/**
	 * @var string
	 */
	private $destination = '';

	/**
	 * @var bool
	 */
	private $recursive = false;

	/**
	 * @var array
	 */
	private $allowedExtensions = array();

	/**
	 * @var array
	 */
	private $files = array();

	/**
	 * @var array
	 */
	private $excluded = array();

	/**
	 * @var array
	 */
	private $directories = array();

	/**
	 * @param $destination
	 *
	 * @throws InvalidArgumentException
	 */
	public function __construct($destination) {

		if (!file_exists($destination)) {
			throw new InvalidArgumentException('File-Path-Destination does not exists. ' . $destination);
		}

		$this->destination = $destination;
	}

	/**
	 * @param $state
	 *
	 * @internal param bool $recursive
	 */
	public function setRecursive($state) {
		$this->recursive = (bool)$state;
	}

	/**
	 * @return boolean
	 */
	public function getRecursive() {
		return $this->recursive;
	}

	/**
	 * @param $extensionName
	 */
	public function addAllowedExtension($extensionName) {
		$extensionName = strtolower((string)$extensionName);
		$this->allowedExtensions[$extensionName] = $extensionName;
	}

	/**
	 * @param $dir
	 */
	protected function read($dir) {
		$iterator = new RecursiveDirectoryIterator($dir, FilesystemIterator::SKIP_DOTS);

		/**
		 * @var $fileInfo SplFileInfo
		 */
		foreach ($iterator as $fileInfo) {
			if (true === $fileInfo->isDir()) {
				$realPath = $fileInfo->getRealPath();
				if (false === $this->isExcluded($realPath)) {
					$this->directories[$realPath] = $realPath;
				}
			}

			if (true === $fileInfo->isFile()) {
				$fileName = $fileInfo->getRealPath();

				if (count($this->allowedExtensions)) {
					$extension = strtolower(pathinfo($fileInfo->getFilename(), PATHINFO_EXTENSION));
					if (!isset($this->allowedExtensions[$extension])) {
						continue;
					}
				}

				if (true === $this->isExcluded($fileName)) {
					continue;
				}

				$this->files[$fileName] = $fileName;
			}

			if (true === $fileInfo->isDir() && true === $this->getRecursive()) {
				$this->read($fileInfo->getPath() . DIRECTORY_SEPARATOR . $fileInfo->getFilename());
			}
		}
	}

	/**
	 * @param $string
	 * @return bool
	 */
	private function isExcluded($string) {
		if (count($this->excluded)) {
			foreach ($this->excluded as $regex) {
				if (preg_match($regex, $string)) {
					return true;
				}
			}
		}

		return false;
	}

	/**
	 * @return array
	 */
	public function getDirectories() {
		return $this->directories;
	}

	/**
	 * @return string
	 */
	public function getDestination() {
		return $this->destination;
	}

	/**
	 * @param $regEx
	 */
	public function addExclusion($regEx) {
		$this->excluded[] = '~' . $regEx . '~i';
	}

	/**
	 * Return files names
	 *
	 * @return array
	 */
	public function getFiles() {
		$this->read($this->destination);
		return $this->files;
	}
}
