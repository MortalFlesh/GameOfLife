<?php

class GameOfLife {

	private $iterations;
	private $cells;

	/** @var CellMatrix */
	private $world;

	/**
	 * @param Config $universe
	 * @param CellMatrix $world
	 */
	public function __construct(Config $universe, CellMatrix $world) {
		$this->iterations = $universe->getIterations();
		$this->cells = $universe->getCells();

		$this->world = $world;
	}

	/** @return CellMatrix */
	public function live() {
		for($i = 0; $i < $this->iterations; $i++) {
			$this->world->liveCycle();
		}
		return $this->world;
	}
}
