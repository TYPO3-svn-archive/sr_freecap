<?php
if (!defined ('TYPO3_MODE')) die ('Access denied.');

$GLOBALS['TYPO3_CONF_VARS']['FE']['eID_include']['sr_freecap_captcha'] = 'EXT:'.$_EXTKEY.'/pi1/captcha.php';
$GLOBALS['TYPO3_CONF_VARS']['FE']['eID_include']['sr_freecap_audioCaptcha'] = 'EXT:'.$_EXTKEY.'/pi3/audioCaptcha.php';

?>
