<?php

class Program {

	public function main() {
		$universe = new Config(new XmlParser());
		$world = new GameOfLife($universe, new CellMatrix($universe, new CellBuilder($universe)));

		$newWorld = $world->live();

		$universe->saveNewWorld($newWorld);
	}
}
