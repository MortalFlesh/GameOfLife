<?php

class XmlParser {
	
	/**
	 * @param string $path
	 * @return SimpleXMLElement
	 */
	public function loadXml($path) {
		$xml = simplexml_load_file($path);
		/* @var $xml SimpleXMLElement */
		return $xml;
	}

	/**
	 * @param string $path
	 * @param SimpleXMLElement $xml
	 */
	public function saveXml($path, SimpleXMLElement $xml) {
		$xml->asXML($path);
	}
}
