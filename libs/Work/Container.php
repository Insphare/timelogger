<?php

/**
 * Class Work_Container
 */
class Work_Container {

	/**
	 * @var string
	 */
	private $label = '';

	/**
	 * @var int
	 */
	private $started = 0;

	/**
	 * @var int
	 */
	private $stopped = 0;

	/**
	 * @var int
	 */
	private $duration = 0;

	/**
	 */
	public function setDuration() {
		$stop = $this->getStopped();
		$start = $this->getStarted();
		$duration = $stop - $start;
		if ($duration < 0) {
			$duration = 0;
		}

		$this->duration = (int)$duration;
	}

	/**
	 * @return int
	 */
	public function getDuration() {
		return $this->duration;
	}

	/**
	 * @param string $label
	 */
	public function setLabel($label) {
		$this->label = $label;
	}

	/**
	 * @return string
	 */
	public function getLabel() {
		return $this->label;
	}

	/**
	 * @param int $started
	 */
	public function setStarted($started) {
		$this->started = $started;
	}

	/**
	 * @return int
	 */
	public function getStarted() {
		return $this->started;
	}

	/**
	 * @param int $stopped
	 */
	public function setStopped($stopped) {
		$this->stopped = $stopped;
		$this->setDuration();
	}

	/**
	 * @return int
	 */
	public function getStopped() {
		return $this->stopped;
	}

	/**
	 * @return array
	 */
	public function getAsArray() {
		$data = array();
		foreach ($this->getProperties() as $keyName => $memberVariableName) {
			$data[$keyName] = $memberVariableName;
		}

		return $data;
	}

	/**
	 * @return array
	 */
	protected function getProperties() {
		return get_object_vars($this);
	}
}
