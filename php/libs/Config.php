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
