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

	/**
	 * @param bool $render
	 * @return CellMatrix
	 */
	public function live($render = false) {
		for($i = 0; $i < $this->iterations; $i++) {
			$this->world->liveCycle();

			if ($render) {
				$this->renderWorld();
			}
		}
		return $this->world;
	}

	public function renderWorld() {
		$this->world->render();
	}
}
