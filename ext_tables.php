<?php
if (!defined ('TYPO3_MODE')) {
	die ('Access denied.');
}
$extensionName = \TYPO3\CMS\Core\Utility\GeneralUtility::underscoredToUpperCamelCase($_EXTKEY);

// Add TypoScript settings
\TYPO3\CMS\Core\Utility\ExtensionManagementUtility::addStaticFile($_EXTKEY, 'Configuration/TypoScript', 'freeCap CAPTCHA');

if (TYPO3_MODE == 'BE' && !(TYPO3_REQUESTTYPE & TYPO3_REQUESTTYPE_INSTALL)) {
	/**
	 * Registers a Backend Module
	 */
	// GDlib is a requirement for the Font Maker module
	if ($GLOBALS['TYPO3_CONF_VARS']['GFX']['gdlib']) {
		\TYPO3\CMS\Extbase\Utility\ExtensionUtility::registerModule(
			$_EXTKEY,
			// Make module a submodule of 'tools'
			'tools',
			// Submodule key
			'FontMaker',
			// Position
			'',
			// An array holding the controller-action combinations that are accessible
			array(
				'FontMaker' => 'new,create'
			),
			array(
				'access' => 'user,group',
				'icon' => 'EXT:' . $_EXTKEY . '/Resources/Public/Images/moduleicon.gif',
				'labels' => 'LLL:EXT:' . $_EXTKEY . '/Resources/Private/Language/locallang_mod.xlf'
			)
		);
		// Add configuration setup
		\TYPO3\CMS\Core\Utility\ExtensionManagementUtility::addTypoScript($_EXTKEY, 'setup', '<INCLUDE_TYPOSCRIPT: source="FILE:EXT:' . $_EXTKEY . '/Configuration/TypoScript/setup.txt">');
	}
}
?>
