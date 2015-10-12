<?php
defined('TYPO3_MODE') or die();

// Register SrFreecap static template
\TYPO3\CMS\Core\Utility\ExtensionManagementUtility::addStaticFile('sr_freecap', 'Configuration/TypoScript', 'freeCap CAPTCHA');