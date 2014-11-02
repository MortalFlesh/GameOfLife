<?php

class Program {

	public function main() {
		$universe = new Config(new XmlParser());
		$god = new CellBuilder($universe);
		$world = new GameOfLife($universe, new CellMatrix($universe, $god));

		$world->setWorldEnvironment(CellMatrix::createRandomWorld($universe, $god));
		$newWorld = $world->live();

		$universe->saveNewWorld($newWorld);
	}

	public function test() {
		$universe = new Config(new XmlParser(), 'test.xml');
		$god = new CellBuilder($universe);
		$world = new GameOfLife($universe, new CellMatrix($universe, $god));

		$world->setWorldEnvironment([
			[new Cell(1, 0, 0), new Cell(1, 0, 1), new Cell(2, 0, 2)],
			[new Cell(3, 1, 0), new Cell(1, 1, 1), new Cell(3, 1, 2)],
			[new Cell(2, 2, 0), new Cell(2, 2, 1), new Cell(1, 2, 2)],
		]);
		$newWorld = $world->live();

		$newWorldMatrix = $newWorld->serialize();

		$happyResult1 = [
			[new Cell(Cell::DEAD, 0, 0), new Cell(Cell::DEAD, 0, 1), new Cell(Cell::DEAD, 0, 2)],
			[new Cell(1, 1, 0), new Cell(Cell::DEAD, 1, 1), new Cell(1, 1, 2)],
			[new Cell(Cell::DEAD, 2, 0), new Cell(1, 2, 1), new Cell(Cell::DEAD, 2, 2)],
		];
		$happyResult2 = [
			[new Cell(1, 0, 0), new Cell(1, 0, 1), new Cell(Cell::DEAD, 0, 2)],
			[new Cell(1, 1, 0), new Cell(1, 1, 1), new Cell(Cell::DEAD, 1, 2)],
			[new Cell(Cell::DEAD, 2, 0), new Cell(Cell::DEAD, 2, 1), new Cell(Cell::DEAD, 2, 2)],
		];

		$result = $this->testResults($newWorldMatrix, $happyResult1);
		$result['type'] = 1;
		if ($result['status'] !== true) {
			$result = $this->testResults($newWorldMatrix, $happyResult2);
			$result['type'] = 2;
		}
		
		var_dump('TEST:', $result);
	}

	/**
	 * @param Cell[][] $result
	 * @param Cell[][] $happyResult
	 * @return array
	 */
	private function testResults(array $result, array $happyResult) {
		$status = true;
		$errors = array();
		foreach($result as $row) {
			foreach($row as $cell) {
				/* @var $cell Cell */
				$x = $cell->getX();
				$y = $cell->getY();

				$happyCell = $happyResult[$x][$y];
				/* @var $happyCell Cell */

				if ($happyCell->getType() !== $cell->getType()) {
					$status = false;
					$errors[] = [
						'happy' => $happyCell,
						'real' => $cell,
					];
				}
			}
		}
		return [
			'status' => $status,
			'errors' => $errors,
		];
	}
}
