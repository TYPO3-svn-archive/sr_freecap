<?php
if (!defined('TYPO3_MODE')) {
	die('Access denied.');
}
// Unserializing the configuration so we can use it here
$_EXTCONF = unserialize($_EXTCONF);

// Setting the encryption algorithm
$GLOBALS['TYPO3_CONF_VARS']['EXTCONF']['sr_freecap']['encryptionAlgorithm'] = isset($_EXTCONF['encryptionAlgorithm']) ? $_EXTCONF['encryptionAlgorithm'] : 'blowfish';

// Dispatching requests to image generator and audio player
$GLOBALS['TYPO3_CONF_VARS']['FE']['eID_include']['sr_freecap_EidDispatcher'] = 'EXT:' . $_EXTKEY . '/Resources/Private/Eid/EidDispatcher.php';

// Configuring the captcha image generator
\TYPO3\CMS\Extbase\Utility\ExtensionUtility::configurePlugin(
	// The extension name (in UpperCamelCase) or the extension key (in lower_underscore)
	$_EXTKEY,
	// A unique name of the plugin in UpperCamelCase
	'ImageGenerator',
	// An array holding the controller-action-combinations that are accessible
	array (
		// The first controller and its first action will be the default
		'ImageGenerator' => 'show',
	),
	// An array of non-cachable controller-action-combinations (they must already be enabled)
	array(
		'ImageGenerator' => 'show',
	)
);

// Configuring the audio captcha player
\TYPO3\CMS\Extbase\Utility\ExtensionUtility::configurePlugin(
	// The extension name (in UpperCamelCase) or the extension key (in lower_underscore)
	$_EXTKEY,
	// A unique name of the plugin in UpperCamelCase
	'AudioPlayer',
	// An array holding the controller-action-combinations that are accessible
	array (
		// The first controller and its first action will be the default
		'AudioPlayer' => 'play',
	),
	// An array of non-cachable controller-action-combinations (they must already be enabled)
	array(
		'AudioPlayer' => 'play',
	)
);

?>
