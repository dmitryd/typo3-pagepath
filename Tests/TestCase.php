<?php
namespace Dmitryd\Pagepath\Tests;
use TYPO3\CMS\Core\Tests\UnitTestCase;
use TYPO3\CMS\Core\Utility\GeneralUtility;

/**
 * Class Testcase
 * @package Dmitryd\Pagepath\Tests
 */
class TestCase extends UnitTestCase {

	/**
	 * test
	 */
	public function testPid1() {
		$api = GeneralUtility::makeInstance('Dmitryd\Pagepath\Api');
		$this->assertEquals('http://', substr($api->getPagePath(1), 0, 7), 'Path to page with uid=1 is not a URL');
	}

}
