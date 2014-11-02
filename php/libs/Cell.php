<?php

class Cell {
	const DEAD = 0;

	private $type;
	private $x;
	private $y;

	/**
	 * @param int $type
	 * @param int $x
	 * @param int $y
	 */
	public function __construct($type, $x, $y) {
		$this->type = $type;
		$this->x = $x;
		$this->y = $y;
	}

	/** @return int */
	public function getType() {
		return $this->type;
	}

	/** @return int */
	public function getX() {
		return $this->x;
	}

	/** @return int */
	public function getY() {
		return $this->y;
	}

	/** @return bool */
	public function isDead() {
		return ($this->type === self::DEAD);
	}
}
