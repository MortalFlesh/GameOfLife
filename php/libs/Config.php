<?php

class Config {
	const CONFIG_FILE = 'config.xml';
	const OUTPUT_FILE = 'out.xml';

	/** @var XmlParser */
	private $xmlParser;

	/** @var SimpleXMLElement */
	private $configXml;

	/** @param XmlParser $xmlParser */
	public function __construct(XmlParser $xmlParser) {
		$this->xmlParser = $xmlParser;
	}

	/** @return int */
	public function getCells() {
		$this->readConfig();
		return (int)$this->configXml->world->cells;
	}

	private function readConfig() {
		if (!isset($this->configXml)) {
			$this->configXml = $this->xmlParser->loadXml(__DIR__ . '/../' . self::CONFIG_FILE);
		}
	}

	/** @return int */
	public function getSpecies() {
		$this->readConfig();
		return (int)$this->configXml->world->species;
	}

	/** @return int */
	public function getIterations() {
		$this->readConfig();
		return (int)$this->configXml->world->iterations;
	}

	/** @param CellMatrix $newWorld */
	public function saveNewWorld(CellMatrix $newWorld) {
		$xml = new SimpleXMLElement('<xml/>');
		$life = $xml->addChild('life');
		$life->addChild('cell', $this->getCells());
		$life->addChild('species', $this->getSpecies());
		$life->addChild('iterations', $this->getIterations());

		$organisms = $xml->addChild('organisms');

		foreach($newWorld->serialize() as $row) {
			foreach($row as $cell) {
				$organism = $organisms->addChild('organism');
				$organism->addChild('x_pos', $cell->getX());
				$organism->addChild('y_pos', $cell->getY());
				$organism->addChild('species', $cell->getType());
			}
		}

		$this->xmlParser->saveXml(__DIR__ . '/../' . self::OUTPUT_FILE, $xml);
	}
}
