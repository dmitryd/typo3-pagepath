<?php
/***************************************************************
 *  Copyright notice
 *
 *  (c) 2008 Dmitry Dulepov <dmitry@typo3.org>
 *  All rights reserved
 *
 *  This script is part of the TYPO3 project. The TYPO3 project is
 *  free software; you can redistribute it and/or modify
 *  it under the terms of the GNU General Public License as published by
 *  the Free Software Foundation; either version 2 of the License, or
 *  (at your option) any later version.
 *
 *  The GNU General Public License can be found at
 *  http://www.gnu.org/copyleft/gpl.html.
 *
 *  This script is distributed in the hope that it will be useful,
 *  but WITHOUT ANY WARRANTY; without even the implied warranty of
 *  MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 *  GNU General Public License for more details.
 *
 *  This copyright notice MUST APPEAR in all copies of the script!
 ***************************************************************/

namespace DmitryD\Pagepath\Service;

use TYPO3\CMS\Core\Utility\GeneralUtility;

/**
 * This class create frontend page address from the page id value and parameters.
 *
 * @author	Dmitry Dulepov <dmitry@typo3.org>
 * @author	Jacob Rasmussen <jacob@omnius.dk>
 * @package DmitryD\PagePath\Service
 */
class Resolver {
	/**
	 * @var string
	 */
	protected	$pageId;

	/**
	 * @var string
	 */
	protected	$parameters;

	/**
	 * @var boolean
	 */
	protected $useCacheHash;

	/**
	 * Initializes the instance of this class.
	 */
	public function __construct() {
		$params = unserialize(base64_decode(GeneralUtility::_GP('data')));
		if (is_array($params)) {
			$this->pageId = $params['id'];
			$this->parameters = $params['parameters'];
			$this->useCacheHash = $params['useCacheHash'];
		}
	}

	/**
	 * Handles incoming trackback requests
	 *
	 * @return	void
	 */
	public function main() {
		header('Content-type: text/plain; charset=iso-8859-1');
		if ($this->pageId) {
			$this->createTSFE();

			/* @var \TYPO3\CMS\Frontend\ContentObject\ContentObjectRenderer $cObj */
			$cObj = GeneralUtility::makeInstance('TYPO3\\CMS\\Frontend\\ContentObject\\ContentObjectRenderer');
			$typolinkConf = array(
				'parameter' => $this->pageId,
				'useCacheHash' => $this->useCacheHash,
			);
			if ($this->parameters) {
				$typolinkConf['additionalParams'] = $this->parameters;
			}
			$url = $cObj->typoLink_URL($typolinkConf);
			if ($url == '') {
				$url = '/';
			}
			$parts = parse_url($url);
			if ($parts['host'] == '') {
				$url = GeneralUtility::locationHeaderUrl($url);
			}
			echo $url;
		}
	}

	/**
	 * Initializes TSFE. This is necessary to have proper environment for typoLink.
	 *
	 * @return	void
	 */
	protected function createTSFE() {
		$pageId = $this->pageId;
		/** @var \TYPO3\CMS\Frontend\Page\PageRepository $pageRepository */
		$pageRepository = GeneralUtility::makeInstance('TYPO3\\CMS\\Frontend\\Page\\PageRepository');
		$pageRepository->init(FALSE);
		$mountPageInfo = $pageRepository->getMountPointInfo($this->pageId);
		if (is_array($mountPageInfo) && count($mountPageInfo)) {
			$pageId = $mountPageInfo['mount_pid'];
			$GLOBALS['TSFE'] = GeneralUtility::makeInstance('TYPO3\\CMS\\Frontend\\Controller\\TypoScriptFrontendController', $GLOBALS['TYPO3_CONF_VARS'], $pageId, '', '', '', '', $mountPageInfo['MPvar']);
		} else {
			$GLOBALS['TSFE'] = GeneralUtility::makeInstance('TYPO3\\CMS\\Frontend\\Controller\\TypoScriptFrontendController', $GLOBALS['TYPO3_CONF_VARS'], $pageId, '');
		}

		$GLOBALS['TSFE']->connectToDB();
		$GLOBALS['TSFE']->initFEuser();
		$GLOBALS['TSFE']->determineId();
		$GLOBALS['TSFE']->getCompressedTCarray();
		$GLOBALS['TSFE']->initTemplate();
		$GLOBALS['TSFE']->getConfigArray();

		// Set linkVars, absRefPrefix, etc
		\TYPO3\CMS\Frontend\Page\PageGenerator::pagegenInit();
	}
}

if (GeneralUtility::getIndpEnv('REMOTE_ADDR') != $_SERVER['SERVER_ADDR']) {
	header('HTTP/1.0 403 Access denied');
	// Empty output!!!
}
else {
	\TYPO3\CMS\Frontend\Utility\EidUtility::initTCA();
	/* @var \DmitryD\PagePath\Service\Resolver $resolver */
	$resolver = GeneralUtility::makeInstance('DmitryD\\Pagepath\\Service\\Resolver');
	$resolver->main();
}