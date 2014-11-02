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
	}

	/** @param Cell[][] $environment */
	public function setWorld(array $environment) {
		$this->matrix = $environment;
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
			// todo
			return $this->god->createRandomCell($x, $y);
		});
	}

	/** @return Cell[][] */
	public function serialize() {
		return $this->matrix;
	}

	/** @return Cell[][] */
	public static function createEmptyWorld(Config $universe, CellBuilder $god) {
		$world = new CellMatrix($universe, $god);
		return $world->liveCycleEvent(function($x, $y) use ($god) {
			return $god->createDeadCell($x, $y);
		});
	}

	/** @return Cell[][] */
	public static function createRandomWorld(Config $universe, CellBuilder $god) {
		$world = new CellMatrix($universe, $god);
		return $world->liveCycleEvent(function($x, $y) use ($god) {
			return $god->createRandomCell($x, $y);
		});
	}

}
