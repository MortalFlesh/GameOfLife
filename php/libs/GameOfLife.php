<?php

class GameOfLife {

	private $iterations;
	private $cells;

	/** @var CellMatrix */
	private $world;

	/** @var CellBuilder */
	private $god;

	/**
	 * @param Config $universe
	 * @param CellMatrix $world
	 * @param CellBuilder $god
	 */
	public function __construct(Config $universe, CellMatrix $world, CellBuilder $god) {
		$this->iterations = $universe->getIterations();
		$this->cells = $universe->getCells();

		$this->world = $world;
		$this->god = $god;
	}

	/** @return CellMatrix */
	public function live() {
		for($i = 0; $i < $this->iterations; $i++) {
			$this->world->liveCycle();
		}
		return $this->world;
	}
}
