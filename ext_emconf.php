<?php

########################################################################
# Extension Manager/Repository config file for ext: "pagepath"
#
# Auto generated 14-12-2009 12:13
#
# Manual updates:
# Only the data in the array - anything else is removed by next write.
# "version" and "dependencies" must not be touched!
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
	'state' => 'beta',
	'internal' => '',
	'uploadfolder' => 0,
	'createDirs' => '',
	'modify_tables' => '',
	'clearCacheOnLoad' => 0,
	'lockType' => '',
	'author_company' => '',
	'version' => '0.1.5',
	'constraints' => array(
		'depends' => array(
			'typo3' => '4.2.0-4.99.99',
			'php' => '5.2.0-10.0.0',
		),
		'conflicts' => array(
		),
		'suggests' => array(
		),
	),
	'_md5_values_when_last_written' => 'a:6:{s:9:"ChangeLog";s:4:"490d";s:25:"class.tx_pagepath_api.php";s:4:"2e47";s:30:"class.tx_pagepath_resolver.php";s:4:"c4dc";s:12:"ext_icon.gif";s:4:"a89b";s:17:"ext_localconf.php";s:4:"81af";s:14:"doc/manual.sxw";s:4:"0f8f";}',
	'suggests' => array(
	),
);

?>