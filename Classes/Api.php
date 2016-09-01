<?php
namespace Dmitryd\Pagepath;

/***************************************************************
 *  Copyright notice
 *
 *  (c) 2008-2014 Dmitry Dulepov <dmitry@typo3.org>
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
use TYPO3\CMS\Backend\Utility\BackendUtility;
use TYPO3\CMS\Core\Utility\GeneralUtility;
use TYPO3\CMS\Core\Utility\HttpUtility;

/**
 * This class create frontend page address from the page id value and parameters.
 *
 * @author    Dmitry Dulepov <dmitry@typo3.org>
 * @package    TYPO3
 * @subpackage    tx_pagepath
 */
class Api {

    /**
     * Creates URL to page using page id and parameters
     *
     * @param int $pageId
     * @param string $parameters
     * @return    string    Path to page or empty string
     */
    static public function getPagePath($pageId, $parameters = '') {
        if (is_array($parameters)) {
            $parameters = GeneralUtility::implodeArrayForUrl('', $parameters);
        }
        $data = array(
            'id' => intval($pageId),
        );
        if ($parameters != '' && $parameters{0} == '&') {
            $data['parameters'] = $parameters;
        }
        $siteUrl = self::getSiteUrl($pageId);
        if ($siteUrl) {
            $url = $siteUrl . 'index.php?eID=pagepath&data=' . base64_encode(serialize($data));
            // Send TYPO3 cookies as this may affect path generation
            $headers = array(
                'Cookie: fe_typo_user=' . $_COOKIE['fe_typo_user'],
            );
            $result = GeneralUtility::getUrl($url, FALSE, $headers);
            $urlParts = parse_url($result);
            if (!is_array($urlParts)) {
                // filter_var is too strict (for example, underscore characters make it fail). So we use parse_url here for a quick check.
                $result = '';
            } elseif ($result) {
                // See if we need to prepend domain part
                if ($urlParts['host'] == '') {
                    $result = rtrim($siteUrl, '/') . '/' . ltrim($result, '/');
                }
            }
        } else {
            $result = '';
        }
        return $result;
    }

    /**
     * Obtains site URL.
     *
     * @static
     * @param int $pageId
     * @return string
     */
    static protected function getSiteUrl($pageId) {
        $domain = BackendUtility::firstDomainRecord(BackendUtility::BEgetRootLine($pageId));
        $pageRecord = BackendUtility::getRecord('pages', $pageId);
        $scheme = is_array($pageRecord) && isset($pageRecord['url_scheme']) && $pageRecord['url_scheme'] == HttpUtility::SCHEME_HTTPS ? 'https' : 'http';
        return $domain ? $scheme . '://' . $domain . '/' : GeneralUtility::getIndpEnv('TYPO3_SITE_URL');
    }
}
