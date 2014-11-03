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

	/** @return string */
	public function getHash() {
		$hash = '';
		foreach($this->matrix as $row) {
			foreach($row as $cell) {
				$hash .= $cell->getType();
			}
		}
		return $hash;
	}

	/**
	 * @param Config $universe
	 * @param CellBuilder $god
	 * @return Cell[][]
	 */
	public static function createEmptyWorld(Config $universe, CellBuilder $god) {
		$world = new CellMatrix($universe, $god);
		return $world->liveCycleEvent(function($x, $y) use ($god) {
			return $god->createDeadCell($x, $y);
		});
	}

	/**
	 * @param Config $universe
	 * @param CellBuilder $god
	 * @return Cell[][]
	 */
	public static function createRandomWorld(Config $universe, CellBuilder $god) {
		$world = new CellMatrix($universe, $god);
		return $world->liveCycleEvent(function($x, $y) use ($god) {
			return $god->createRandomCell($x, $y);
		});
	}

	/**
	 * @param Config $universe
	 * @param CellBuilder $god
	 * @param int $type 1 | 2 | 3
	 * @return Cell[][]
	 */
	public static function createOscilator(Config $universe, CellBuilder $god, $type) {
		$oscilator1 = CellMatrix::createEmptyWorld($universe, $god);
		$oscilator1[2][1] = $god->createCell(1, 2, 1);
		$oscilator1[2][2] = $god->createCell(1, 2, 2);
		$oscilator1[2][3] = $god->createCell(1, 2, 3);

		$oscilator2 = CellMatrix::createEmptyWorld($universe, $god);
		$oscilator2[2][2] = $god->createCell(1, 2, 2);
		$oscilator2[2][3] = $god->createCell(1, 2, 3);
		$oscilator2[2][4] = $god->createCell(1, 2, 4);
		$oscilator2[3][1] = $god->createCell(1, 3, 1);
		$oscilator2[3][2] = $god->createCell(1, 3, 2);
		$oscilator2[3][3] = $god->createCell(1, 3, 3);

		$oscilator3 = CellMatrix::createEmptyWorld($universe, $god);
		$oscilator3[1][1] = $god->createCell(1, 1, 1);
		$oscilator3[1][2] = $god->createCell(1, 1, 2);
		$oscilator3[2][1] = $god->createCell(1, 2, 1);
		$oscilator3[2][2] = $god->createCell(1, 2, 2);
		$oscilator3[3][3] = $god->createCell(1, 3, 3);
		$oscilator3[3][4] = $god->createCell(1, 3, 4);
		$oscilator3[4][3] = $god->createCell(1, 4, 3);
		$oscilator3[4][4] = $god->createCell(1, 4, 4);

		return ${'oscilator' . $type};
	}

	/**
	 * @param Config $universe
	 * @param CellBuilder $god
	 * @return Cell[][]
	 */
	public static function createPulzar(Config $universe, CellBuilder $god) {
		$pulzar = CellMatrix::createEmptyWorld($universe, $god);
		$pulzar[2][4] = $god->createCell(1, 2, 4);
		$pulzar[2][5] = $god->createCell(1, 2, 5);
		$pulzar[2][6] = $god->createCell(1, 2, 6);
		$pulzar[2][10] = $god->createCell(1, 2, 10);
		$pulzar[2][11] = $god->createCell(1, 2, 11);
		$pulzar[2][12] = $god->createCell(1, 2, 12);

		$pulzar[4][2] = $god->createCell(1, 4, 2);
		$pulzar[4][7] = $god->createCell(1, 4, 7);
		$pulzar[4][9] = $god->createCell(1, 4, 9);
		$pulzar[4][14] = $god->createCell(1, 4, 14);
		$pulzar[5][2] = $god->createCell(1, 5, 2);
		$pulzar[5][7] = $god->createCell(1, 5, 7);
		$pulzar[5][9] = $god->createCell(1, 5, 9);
		$pulzar[5][14] = $god->createCell(1, 5, 14);
		$pulzar[6][2] = $god->createCell(1, 6, 2);
		$pulzar[6][7] = $god->createCell(1, 6, 7);
		$pulzar[6][9] = $god->createCell(1, 6, 9);
		$pulzar[6][14] = $god->createCell(1, 6, 14);

		$pulzar[7][4] = $god->createCell(1, 7, 4);
		$pulzar[7][5] = $god->createCell(1, 7, 5);
		$pulzar[7][6] = $god->createCell(1, 7, 6);
		$pulzar[7][10] = $god->createCell(1, 7, 10);
		$pulzar[7][11] = $god->createCell(1, 7, 11);
		$pulzar[7][12] = $god->createCell(1, 7, 12);

		// *******************************************

		$pulzar[9][4] = $god->createCell(1, 9, 4);
		$pulzar[9][5] = $god->createCell(1, 9, 5);
		$pulzar[9][6] = $god->createCell(1, 9, 6);
		$pulzar[9][10] = $god->createCell(1, 9, 10);
		$pulzar[9][11] = $god->createCell(1, 9, 11);
		$pulzar[9][12] = $god->createCell(1, 9, 12);

		$pulzar[10][2] = $god->createCell(1, 10, 2);
		$pulzar[10][7] = $god->createCell(1, 10, 7);
		$pulzar[10][9] = $god->createCell(1, 10, 9);
		$pulzar[10][14] = $god->createCell(1, 10, 14);
		$pulzar[11][2] = $god->createCell(1, 11, 2);
		$pulzar[11][7] = $god->createCell(1, 11, 7);
		$pulzar[11][9] = $god->createCell(1, 11, 9);
		$pulzar[11][14] = $god->createCell(1, 11, 14);
		$pulzar[12][2] = $god->createCell(1, 12, 2);
		$pulzar[12][7] = $god->createCell(1, 12, 7);
		$pulzar[12][9] = $god->createCell(1, 12, 9);
		$pulzar[12][14] = $god->createCell(1, 12, 14);

		$pulzar[14][4] = $god->createCell(1, 14, 4);
		$pulzar[14][5] = $god->createCell(1, 14, 5);
		$pulzar[14][6] = $god->createCell(1, 14, 6);
		$pulzar[14][10] = $god->createCell(1, 14, 10);
		$pulzar[14][11] = $god->createCell(1, 14, 11);
		$pulzar[14][12] = $god->createCell(1, 14, 12);

		return $pulzar;
	}

	/**
	 * @param Config $universe
	 * @param CellBuilder $god
	 * @return Cell[][]
	 */
	public static function loadWorldFromConfig(Config $universe, CellBuilder $god, $file = null) {
		$world = new CellMatrix($universe, $god);
		$organisms = $universe->getOrganisms($file);

		return $world->liveCycleEvent(function($x, $y) use ($organisms, $god) {
			if (isset($organisms[$x][$y])) {
				return $god->createCell($organisms[$x][$y], $x, $y);
			}
			return $god->createDeadCell($x, $y);
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
