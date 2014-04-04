<?php
if (!defined ('TYPO3_MODE')) {
	die ('Access denied.');
}

// eID
$GLOBALS['TYPO3_CONF_VARS']['FE']['eID_include'][$_EXTKEY] = 'EXT:' . $_EXTKEY . '/Classes/Service/Resolver.php';

?>