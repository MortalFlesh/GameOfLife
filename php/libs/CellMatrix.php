<?php

class CellMatrix {

	private $cells;

	/** @var CellBuilder */
	private $god;

	/** @var Cell[][] */
	private $matrix;

	/**
	 * @param Config $universe
	 * @param CellBuilder $god
	 */
	public function __construct(Config $universe, CellBuilder $god) {
		$this->cells = $universe->getCells();
		$this->god = $god;

		$this->matrix = $this->createEmptyWorld();
	}

	/** @return Cell[][] */
	private function createEmptyWorld() {
		return $this->liveCycleEvent(function($x, $y){
			return $this->god->createDeadCell($x, $y);
		});
	}

	/**
	 * @param callable $event (x,y)->Cell
	 * @return Cell[][]
	 */
	private function liveCycleEvent(callable $event) {
		$world = array();
		for($x = 0; $x < $this->cells; $x++) {
			for($y = 0; $y < $this->cells; $y++) {
				$world[$x][$y] = $event($x, $y);
			}
		}
		return $world;
	}

	public function liveCycle() {
		$this->matrix = $this->liveCycleEvent(function($x, $y){
			return $this->god->createRandomCell($x, $y);
		});
	}

	/** @return Cell[][] */
	public function serialize() {
		return $this->matrix;
	}
}
