<?php

class Program {

	public function main() {
		$universe = new Config(new XmlParser());
		$god = new CellBuilder($universe);
		$world = new GameOfLife($universe, new CellMatrix($universe, $god), $god);

		$newWorld = $world->live();

		$universe->saveNewWorld($newWorld);
	}
}
