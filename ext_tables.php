<?php
if (!defined ('TYPO3_MODE')) {
	die ('Access denied.');
}
if (TYPO3_MODE === 'BE') {
		// GDlib is a requirement for the BE module
	if ($GLOBALS['TYPO3_CONF_VARS']['GFX']['gdlib']) {
		t3lib_extMgm::addModule('tools', 'txsrfreecapM1', '', t3lib_extMgm::extPath($_EXTKEY).'mod1/');
	}
}
?>
