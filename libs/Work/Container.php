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
	 * @var int
	 */
	private $lastBreakTimeBegin = null;

	/**
	 * @var array
	 */
	private $breakTime = array();

	/**
	 */
	public function setDuration() {
		$stop = $this->getStopped();
		$start = $this->getStarted();
		$duration = $stop - $start - $this->getBreakTime();
		if ($duration < 0) {
			$duration = 0;
		}

		$this->duration = (int)$duration;
	}

	/**
	 * @return int
	 */
	public function getDuration() {
		$this->setDuration();
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
	 * @return int
	 */
	public function getBreakTime() {
		$breakTime = 0;

		foreach ($this->breakTime as $break) {
			$stop = (int)$break['stop'];
			$start = (int)$break['start'];
			$diff = $stop - $start;
			$breakTime += $diff;
		}

		return $breakTime;
	}

	/**
	 * @return bool
	 */
	public function hasBreakTime() {
		return null !== $this->lastBreakTimeBegin;
	}

	/**
	 *
	 */
	public function startBreakTime() {
		$this->lastBreakTimeBegin = time();
	}

	/**
	 *
	 */
	public function stopBreakTime() {
		$this->breakTime[] = array(
			'start' => $this->lastBreakTimeBegin,
			'stop' => time(),
		);

		$this->lastBreakTimeBegin = null;
	}

	/**
	 * @param array $breakTime
	 */
	public function setBreakTime($breakTime) {
		$this->breakTime = $breakTime;
	}

	/**
	 * @param int $lastBreakTimeBegin
	 */
	public function setLastBreakTimeBegin($lastBreakTimeBegin) {
		$this->lastBreakTimeBegin = $lastBreakTimeBegin;
	}

	/**
	 * @return array
	 */
	protected function getProperties() {
		return get_object_vars($this);
	}
}
