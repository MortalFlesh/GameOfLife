<?php

class CellBuilder {

	private $spieces;

	/** @param Config $config */
	public function __construct(Config $config) {
		$this->spieces = $config->getSpecies();
	}

	/**
	 * @param int $x
	 * @param int $y
	 * @return Cell
	 */
	public function createDeadCell($x, $y) {
		return new Cell(Cell::DEAD, $x, $y);
	}

	/**
	 * @param int $x
	 * @param int $y
	 * @return Cell
	 */
	public function createRandomCell($x, $y) {
		$type = rand(Cell::DEAD + 1, $this->spieces);
		return new Cell($type, $x, $y);
	}

	/**
	 * @param int $type
	 * @param int $x
	 * @param int $y
	 * @return Cell
	 */
	public function createCell($type, $x, $y) {
		return new Cell($type, $x, $y);
	}
}
