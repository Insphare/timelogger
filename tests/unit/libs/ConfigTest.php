<?php

class ConfigTest extends PHPUnit_Framework_TestCase {

	public function testSetGet() {
		Config::set('testkey', 'testvalue');
		$this->assertEquals('testvalue', Config::get('testkey'));
	}

	public function testGetFallback() {
		$this->assertEquals(null, Config::get('testkey2'));
	}

}
