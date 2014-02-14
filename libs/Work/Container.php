<?php

/**
 * Class Work_Container
 *
 *  @author Manuel Will <insphare@gmail.com>
 *  @copyright Copyright (c) 2014, Manuel Will
 */
class Work_Container extends Work_Etter {



	public function __construct() {
		$this->isNew = true;
	}

	/**
	 * @return int
	 */
	public function getDuration() {
		$this->calculate();
		return $this->duration;
	}


	/**
	 * @param int $stopped
	 */
	public function setStopped($stopped) {
		$this->stopped = $stopped;
		$this->calculate();
	}

	/**
	 */
	public function calculate() {
		$workTime = $this->getWorkTime();
		$duration = $workTime - $this->getBreakTime();
		if ($duration < 0) {
			$duration = 0;
		}

		$this->duration = (int)$duration;
	}


	/**
	 *
	 */
	public function startWorkTime() {
		$this->lastWorkTimeBegin = time();
	}

	/**
	 * @return bool
	 */
	public function hasActiveWorkTime() {
		return null !== $this->lastWorkTimeBegin;
	}

	/**
	 *
	 */
	public function stopWorkTime() {
		$currentTime = time();
		$this->workTime[] = array(
			'start' => $this->lastWorkTimeBegin,
			'stop' => $currentTime,
		);

		$this->lastWorkTimeBegin = null;
		$this->stopped = $currentTime;
		$this->calculate();
	}

	/**
	 * @return int
	 */
	public function getWorkTime() {
		$workTime = 0;

		foreach ($this->workTime as $work) {
			$stop = (int)$work['stop'];
			$start = (int)$work['start'];
			$diff = $stop - $start;
			$workTime += $diff;
		}

		return $workTime;
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
	public function hasActiveBreakTime() {
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

}
