<?php

/**
 * Class Work_Etter
 *
 * @author Manuel Will <insphare@gmail.com>
 * @copyright Copyright (c) 2014, Manuel Will
 */
abstract class Work_Etter {

	/**
	 * @var string
	 */
	protected $label = '';

	/**
	 * @var int
	 */
	protected $started = 0;

	/**
	 * @var int
	 */
	protected $stopped = 0;

	/**
	 * @var int
	 */
	protected $duration = 0;

	/**
	 * @var array
	 */
	protected $workTime = array();

	/**
	 * @var int
	 */
	protected $lastWorkTimeBegin = null;

	/**
	 * @var int
	 */
	protected $lastBreakTimeBegin = null;

	/**
	 * @var array
	 */
	protected $breakTime = array();

	/**
	 * @var bool
	 */
	protected $isNew = false;

	/**
	 * @var bool
	 */
	protected $markedAsActive = false;

	/**
	 * @param boolean $markColored
	 */
	public function setMarkedAsActive($markColored) {
		$this->markedAsActive = (bool)$markColored;
	}

	/**
	 * @return boolean
	 */
	public function getMarkedAsActive() {
		return $this->markedAsActive;
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
		if ($this->started > 0) {
			return;
		}

		$this->started = $started;
	}

	/**
	 * @return int
	 */
	public function getStarted() {
		return $this->started;
	}

	/**
	 * @return int
	 */
	public function getStopped() {
		return $this->stopped;
	}

	/**
	 * @param int $duration
	 */
	public function setDuration($duration) {
		$this->duration = $duration;
	}

	/**
	 * @param int $lastWorkTimeBegin
	 */
	public function setLastWorkTimeBegin($lastWorkTimeBegin) {
		$this->lastWorkTimeBegin = $lastWorkTimeBegin;
	}

	/**
	 * @param int $stopped
	 */
	public function setStopped($stopped) {
		$this->stopped = $stopped;
	}

	/**
	 * @param array $workTime
	 */
	public function setWorkTime($workTime) {
		$this->workTime = $workTime;
	}

	/**
	 * @return array
	 */
	public function getWorkTimeItems() {
		return $this->workTime;
	}

	/**
	 * @return array
	 */
	public function getBreakTimeItems() {
		return $this->breakTime;
	}

	/**
	 * @return int
	 */
	public function getLastWorkTimeBegin() {
		return $this->lastWorkTimeBegin;
	}

	/**
	 * @return array
	 */
	protected function getProperties() {
		return get_object_vars($this);
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
	 * @return boolean
	 */
	public function getIsNew() {
		return $this->isNew;
	}

	/**
	 * @param boolean $isNew
	 */
	public function setIsNew($isNew) {
		$this->isNew = (bool)$isNew;
	}
}
