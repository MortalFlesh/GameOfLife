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

	/** @return CellMatrix */
	public function liveCycle() {
		$this->matrix = $this->liveCycleEvent([$this, 'cellLifeCycle']);
		return $this;
	}

	/**
	 * @param int $x
	 * @param int $y
	 * @return Cell
	 */
	private function cellLifeCycle($x, $y) {
		$currentCell = $this->matrix[$x][$y];
		$surroundings = $this->getSurroundings($x, $y);

		if ($currentCell->isDead()) {
			$typesToBirth = array_keys($surroundings, 3, true);
			$typesToBirthCount = count($typesToBirth);

			if ($typesToBirthCount === 1) {
				return $this->god->createCell($typesToBirth[0], $x, $y);
			} elseif ($typesToBirthCount > 1) {
				return $this->god->createRandomCell($x, $y);
			}
		} else {
			$cellType = $currentCell->getType();
			$currentCellTypeAround = (array_key_exists($cellType, $surroundings) ? $surroundings[$cellType] : 0);

			if ((count($surroundings) > 0 && max($surroundings) >= 4) || $currentCellTypeAround < 2) {
				return $this->god->createDeadCell($x, $y);
			}
		}
		return $currentCell;
	}

	/**
	 * @param int $x
	 * @param int $y
	 * @return array [type => count]
	 */
	private function getSurroundings($x, $y) {
		$surroundings = array();
		for($sx = -1; $sx < 2; $sx++) {
			for($sy = -1; $sy < 2; $sy++) {
				$posX = $sx + $x;
				$posY = $sy + $y;

				if ($posX < 0 || $posY < 0 || ($posX === $x && $posY === $y) || $posX >= $this->cells || $posY >= $this->cells) {
					continue;
				}

				$currentCell = $this->matrix[$posX][$posY];
				if ($currentCell->isDead()) {
					continue;
				}

				$type = $currentCell->getType();
				if (!array_key_exists($type, $surroundings)) {
					$surroundings[$type] = 0;
				}
				$surroundings[$type]++;
			}
		}
		return $surroundings;
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

	public function render() {
		?>
		<table class="world">
			<?
			foreach($this->matrix as $row) {
				?><tr><?
					foreach($row as $cell) {
						?><td<?=($cell->isDead() ? ' class="dead"' : '')?>><?=$cell->getType()?></td><?
					}
				?></tr><?
			}
			?>
		</table>
		<?
	}

}
