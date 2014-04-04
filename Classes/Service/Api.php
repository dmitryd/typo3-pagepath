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

use TYPO3\CMS\Backend\Utility\BackendUtility;
use TYPO3\CMS\Core\SingletonInterface;
use TYPO3\CMS\Core\Utility\GeneralUtility;

/**
 * This class create frontend page address from the page id value and parameters.
 *
 * @package DmitryD\PagePath\Service
 * @author	Dmitry Dulepov <dmitry@typo3.org>
 * @author	Jacob Rasmussen <jacob@omnius.dk>
 */
class Api {
	/**
	 * Creates URL to page using page id and parameters
	 * @param integer $pageId
	 * @param string $parameters
	 * @param boolean $useCacheHash
	 * @return string Path to page or empty string
	 */
	static public function getPagePath($pageId, $parameters = '', $useCacheHash = TRUE) {
		$result = '';
		if (is_array($parameters)) {
			$parameters = GeneralUtility::implodeArrayForUrl('', $parameters);
		}
		$data = array(
			'id' => intval($pageId),
			'useCacheHash' => (bool)$useCacheHash
		);
		if ($parameters != '' && $parameters{0} == '&') {
			$data['parameters'] = $parameters;
		}
		$siteUrl = self::getSiteUrl($pageId);

		if ($siteUrl) {
			$url = $siteUrl . 'index.php?eID=pagepath&data=' . base64_encode(serialize($data));
			// Send TYPO3 cookies as this may affect path generation
			$headers = array(
				'Cookie: fe_typo_user=' . $_COOKIE['fe_typo_user']
			);

			$result = GeneralUtility::getURL($url, false, $headers);
			$urlParts = parse_url($result);
			if (is_array($urlParts) && $result) {
				// See if we need to prepend domain part
				if ($urlParts['host'] == '') {
					$result = rtrim($siteUrl, '/') .  '/' . ltrim($result, '/');
				}
			}
		}
		return $result;
	}

	/**
	 * Obtains site URL.
	 *
	 * @static
	 * @param integer $pageId
	 * @return string
	 */
	static public function getSiteUrl($pageId) {
		$domain = GeneralUtility::getIndpEnv('TYPO3_SITE_URL');
		$rootLine = BackendUtility::BEgetRootLine($pageId);
		// Checks alternate domains
		if (count($rootLine) > 0) {
			$urlParts = parse_url($domain);
			/** @var PageRepository $sysPage */
			$sysPage = GeneralUtility::makeInstance('TYPO3\\CMS\\Frontend\\Page\\PageRepository');
			$page = (array) $sysPage->getPage($pageId);
			$protocol = 'http';
			if ($page['url_scheme'] == \TYPO3\CMS\Core\Utility\HttpUtility::SCHEME_HTTPS || $page['url_scheme'] == 0 && GeneralUtility::getIndpEnv('TYPO3_SSL')) {
				$protocol = 'https';
			}
			$domainName = BackendUtility::firstDomainRecord($rootLine);

			if ($domainName) {
				$domain = $domainName;
			} else {
				$domainRecord = BackendUtility::getDomainStartPage($urlParts['host'], $urlParts['path']);
				$domain = $domainRecord['domainName'];
			}
			if ($domain) {
				$domain = $protocol . '://' . $domain;
			} else {
				$domain = rtrim(GeneralUtility::getIndpEnv('TYPO3_SITE_URL'), '/');
			}
			// Append port number if lockSSLPort is not the standard port 443
			$portNumber = (int)$GLOBALS['TYPO3_CONF_VARS']['BE']['lockSSLPort'];
			if ($portNumber > 0 && $portNumber !== 443 && $portNumber < 65536 && $protocol === 'https') {
				$domain .= ':' . strval($portNumber);
			}
			$domain .= '/';
		}
		return $domain;
	}
}
