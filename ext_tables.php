<?php
if (!defined ('TYPO3_MODE')) {
	die ('Access denied.');
}
$extensionName = \TYPO3\CMS\Core\Utility\GeneralUtility::underscoredToUpperCamelCase($_EXTKEY);

// Add TypoScript settings
\TYPO3\CMS\Core\Utility\ExtensionManagementUtility::addStaticFile($_EXTKEY, 'Configuration/TypoScript', 'freeCap CAPTCHA');

if (TYPO3_MODE === 'BE') {
		// GDlib is a requirement for the BE module
	if ($GLOBALS['TYPO3_CONF_VARS']['GFX']['gdlib']) {
		\TYPO3\CMS\Core\Utility\ExtensionManagementUtility::addModule('tools', 'txsrfreecapM1', '', \TYPO3\CMS\Core\Utility\ExtensionManagementUtility::extPath($_EXTKEY).'mod1/');
	}
}
?>
