<?php

########################################################################
# Extension Manager/Repository config file for ext "pagepath".
#
# Auto generated 16-12-2011 11:48
#
# Manual updates:
# Only the data in the array - everything else is removed by next
# writing. "version" and "dependencies" must not be touched!
########################################################################

$EM_CONF[$_EXTKEY] = array(
	'title' => 'Page path',
	'description' => 'Provides API for Backend modules to get a proper path to the Frontend page (simulateStatic/RealURL/CoolURI-like)',
	'category' => 'be',
	'author' => 'Dmitry Dulepov',
	'author_email' => 'dmitry@typo3.org',
	'shy' => '',
	'dependencies' => '',
	'conflicts' => '',
	'priority' => '',
	'module' => '',
	'state' => 'stable',
	'internal' => '',
	'uploadfolder' => 0,
	'createDirs' => '',
	'modify_tables' => '',
	'clearCacheOnLoad' => 0,
	'lockType' => '',
	'author_company' => '',
	'version' => '1.0.0',
	'constraints' => array(
		'depends' => array(
			'typo3' => '4.5.0-',
			'php' => '5.3.2-10.0.0',
		),
		'conflicts' => array(
		),
		'suggests' => array(
		),
	),
	'_md5_values_when_last_written' => 'a:6:{s:9:"ChangeLog";s:4:"f7b0";s:25:"class.tx_pagepath_api.php";s:4:"ec5b";s:30:"class.tx_pagepath_resolver.php";s:4:"614c";s:12:"ext_icon.gif";s:4:"a89b";s:17:"ext_localconf.php";s:4:"81af";s:14:"doc/manual.sxw";s:4:"7d24";}',
	'suggests' => array(
	),
);

?>