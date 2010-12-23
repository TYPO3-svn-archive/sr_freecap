<?php
if (!defined ('TYPO3_MODE')) die ('Access denied.');
if (TYPO3_MODE === 'BE') {
		// GDlib 2 is a requirement for the BE module
		// For TYPO3 4.4+, if GDlib is available, it is GDlib 2
	if ((t3lib_div::int_from_ver($GLOBALS['TYPO_VERSION']) >= 4004000 && $GLOBALS['TYPO3_CONF_VARS']['GFX']['gdlib']) || $GLOBALS['TYPO3_CONF_VARS']['GFX']['gdlib_2']) {
		t3lib_extMgm::addModule('tools', 'txsrfreecapM1', '', t3lib_extMgm::extPath($_EXTKEY).'mod1/');
	}
}
?>
