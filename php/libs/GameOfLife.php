<?php

class GameOfLife {

	private $iterations;

	/** @var CellMatrix */
	private $world;

	/**
	 * @param Config $universe
	 * @param CellMatrix $world
	 */
	public function __construct(Config $universe, CellMatrix $world) {
		$this->iterations = $universe->getIterations();

		$this->world = $world;
	}

	/** @param Cell[][] $environment */
	public function setWorldEnvironment(array $environment) {
		$this->world->setWorld($environment);
	}

	/** @return CellMatrix */
	public function live() {
		for($i = 0; $i < $this->iterations; $i++) {
			$this->world->liveCycle();
		}
		return $this->world;
	}
}
