<?php
namespace Dmitryd\Pagepath;
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
/**
 * $Id$
 *
 * [CLASS/FUNCTION INDEX of SCRIPT]
 */
use TYPO3\CMS\Core\Utility\GeneralUtility;
use TYPO3\CMS\Frontend\ContentObject\ContentObjectRenderer;
use TYPO3\CMS\Frontend\Controller\TypoScriptFrontendController;
use TYPO3\CMS\Frontend\Page\PageGenerator;
use TYPO3\CMS\Frontend\Utility\EidUtility;

/**
 * This class create frontend page address from the page id value and parameters.
 *
 * @author    Dmitry Dulepov <dmitry@typo3.org>
 * @package    TYPO3
 * @subpackage    tx_pagepath
 */
class Resolver {

    protected $pageId;
    protected $parameters;

    /**
     * Initializes the instance of this class.
     */
    public function __construct() {
        $params = unserialize(base64_decode(GeneralUtility::_GP('data')));
        if (is_array($params)) {
            $this->pageId = $params['id'];
            $this->parameters = $params['parameters'];
        }
    }

    /**
     * Handles incoming trackback requests
     *
     * @return    void
     */
    public function main() {
        header('Content-type: text/plain; charset=iso-8859-1');
        if ($this->pageId) {
            $this->createTSFE();
            $cObj = GeneralUtility::makeInstance(ContentObjectRenderer::class);
            /* @var $cObj ContentObjectRenderer */
            $typolinkConf = array(
                'parameter'    => $this->pageId,
                'useCacheHash' => $this->parameters != '',
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
     * @return    void
     */
    protected function createTSFE() {
        $GLOBALS['TSFE'] = GeneralUtility::makeInstance(TypoScriptFrontendController::class, $GLOBALS['TYPO3_CONF_VARS'], $this->pageId, '');
        $GLOBALS['TSFE']->connectToDB();
        $GLOBALS['TSFE']->initFEuser();
        $GLOBALS['TSFE']->checkAlternativeIdMethods();
        EidUtility::initTCA();
        $GLOBALS['TSFE']->determineId();
        $GLOBALS['TSFE']->initTemplate();
        $GLOBALS['TSFE']->getFromCache();
        $GLOBALS['TSFE']->getConfigArray();
        $GLOBALS['TSFE']->settingLanguage();
        $GLOBALS['TSFE']->settingLocale();
        // Set linkVars, absRefPrefix, etc
        PageGenerator::pagegenInit();
    }
}

$conf = unserialize($GLOBALS['TYPO3_CONF_VARS']['EXT']['extConf']['pagepath']);
if ($conf === FALSE) {
    $conf = [
        'allow_ip' => '127.0.0.1',
    ];
}
if (GeneralUtility::getIndpEnv('REMOTE_ADDR') != $_SERVER['SERVER_ADDR'] &&
    !GeneralUtility::cmpIP(GeneralUtility::getIndpEnv('REMOTE_ADDR'), $conf['allow_ip'])) {
    header('HTTP/1.0 403 Access denied');
    // Empty output!!!
} else {
    $resolver = GeneralUtility::makeInstance(Resolver::class);
    /* @var $resolver Resolver */
    $resolver->main();
}
