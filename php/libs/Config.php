<?php

class Config {
	const CONFIG_FILE = 'config.xml';
	const OUTPUT_FILE = 'out.xml';

	/** @var XmlParser */
	private $xmlParser;

	/** @var SimpleXMLElement */
	private $configXml;

	private $in, $out;

	/**
	 * @param XmlParser $xmlParser
	 * @param string $in
	 * @param string $out
	 */
	public function __construct(XmlParser $xmlParser, $in = self::CONFIG_FILE, $out = self::OUTPUT_FILE) {
		$this->xmlParser = $xmlParser;
		$this->in = $in;
		$this->out = $out;
	}

	/** @return int */
	public function getCells() {
		$this->readConfig();
		return (int)$this->configXml->world->cells;
	}

	private function readConfig() {
		if (!isset($this->configXml)) {
			$this->configXml = $this->xmlParser->loadXml(__DIR__ . '/../' . $this->in);
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

	/** @return array [x][y]=>type */
	public function getOrganisms() {
		$organisms = array();

		$this->readConfig();
		$organismsNode = $this->configXml->organisms;
		/* @var $organismsNode SimpleXMLElement */
		foreach($organismsNode->children() as $organism) {
			$x = (int)$organism->x_pos;
			$y = (int)$organism->y_pos;
			$type = (string)$organism->species;

			$this->initArray($organisms[$x]);
			$organisms[$x][$y] = $this->getSpieces($type);
		}
		return $organisms;
	}

	private function initArray(&$array) {
		if (!is_array($array)) {
			$array = [];
		}
	}

	private function getSpieces($type) {
		switch($type) {
			case 'A': return 1;
			case 'B': return 2;
			case 'C': return 3;
			case 'D': return 4;
			case 'E': return 5;
			case 'F': return 6;
			case 'G': return 7;
		}
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

		$this->xmlParser->saveXml(__DIR__ . '/../' . $this->out, $xml);
	}
}
