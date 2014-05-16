<?php

class tx_pagepath_testcase extends Tx_Phpunit_TestCase {

	/**
	 * test
	 */
	public function testPid1() {
		$api = t3lib_div::makeInstance('tx_pagepath_api');
		/** @var tx_pagepath_api $api */
		$this->assertEquals('http://', substr($api->getPagePath(1), 0, 7), 'Path to page with uid=1 is not a URL');
	}

}
