<?php
/***************************************************************
*  Copyright notice
*
*  (c) 2005-2009 Stanislas Rolland <typo3(arobas)sjbr.ca>
*  All rights reserved
*
*  This script is part of the Typo3 project. The Typo3 project is
*  free software; you can redistribute it and/or modify
*  it under the terms of the GNU General Public License as published by
*  the Free Software Foundation; either version 2 of the License, or
*  (at your option) any later version.
*
*  The GNU General Public License can be found at
*  http://www.gnu.org/copyleft/gpl.html.
*
*  This script is distributed in the hope that it will be useful,
*  but WITHOUT ANY WARRANTY; without even the implied warranty of
*  MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
*  GNU General Public License for more details.
*
*  This copyright notice MUST APPEAR in all copies of the script!
***************************************************************/
/**
* This script invokes the freecap CAPTCHA image generation
*
*/
error_reporting (E_ALL ^ E_NOTICE);
define('PATH_thisScript',str_replace('//','/', str_replace('\\','/', (php_sapi_name()=='cgi'||php_sapi_name()=='xcgi'||php_sapi_name()=='isapi' ||php_sapi_name()=='cgi-fcgi')&&((!empty($_SERVER['ORIG_PATH_TRANSLATED'])&&isset($_SERVER['ORIG_PATH_TRANSLATED']))?$_SERVER['ORIG_PATH_TRANSLATED']:$_SERVER['PATH_TRANSLATED'])? ((!empty($_SERVER['ORIG_PATH_TRANSLATED'])&&isset($_SERVER['ORIG_PATH_TRANSLATED']))?$_SERVER['ORIG_PATH_TRANSLATED']:$_SERVER['PATH_TRANSLATED']):((!empty($_SERVER['ORIG_SCRIPT_FILENAME'])&&isset($_SERVER['ORIG_SCRIPT_FILENAME']))?$_SERVER['ORIG_SCRIPT_FILENAME']:$_SERVER['SCRIPT_FILENAME']))));
if (!defined('PATH_site')) define('PATH_site', dirname(dirname(dirname(dirname(dirname(PATH_thisScript))))).'/');
if (!defined('PATH_t3lib')) define('PATH_t3lib', PATH_site.'t3lib/');
define('PATH_typo3conf', PATH_site.'typo3conf/');
define('TYPO3_mainDir', 'typo3/');
if (!defined('PATH_typo3')) define('PATH_typo3', PATH_site.TYPO3_mainDir);
if (!defined('PATH_tslib')) {
	if (@is_dir(PATH_site.'typo3/sysext/cms/tslib/')) {
		define('PATH_tslib', PATH_site.'typo3/sysext/cms/tslib/');
	} elseif (@is_dir(PATH_site.'tslib/')) {
		define('PATH_tslib', PATH_site.'tslib/');
	}
}
define('TYPO3_MODE','FE');
define('TYPO3_OS', stristr(PHP_OS,'win')&&!stristr(PHP_OS,'darwin')?'WIN':'');

require_once(PATH_t3lib.'class.t3lib_div.php');
require_once(PATH_t3lib.'class.t3lib_extmgm.php');
require_once(PATH_t3lib.'config_default.php');
require_once(PATH_typo3conf.'localconf.php');
require_once(PATH_tslib.'class.tslib_fe.php');
require_once(PATH_t3lib.'class.t3lib_tstemplate.php');
require_once(PATH_t3lib.'class.t3lib_page.php');
require_once(PATH_tslib.'class.tslib_content.php');
require_once(t3lib_extMgm::extPath('sr_freecap').'pi1/class.tx_srfreecap_pi1.php');
require_once(PATH_t3lib.'class.t3lib_userauth.php');
require_once(PATH_tslib.'class.tslib_feuserauth.php');
require_once(PATH_t3lib.'class.t3lib_cs.php');

if (!defined ('TYPO3_db'))  die ('The configuration file was not included.');
if (isset($_GET['GLOBALS']) || isset($_POST['GLOBALS']) || isset($_FILES['GLOBALS']) || isset($_COOKIE['GLOBALS'])) die('You cannot set the GLOBALS-array from outside this script.');

require_once(PATH_t3lib.'class.t3lib_db.php');
$GLOBALS['TYPO3_DB'] = t3lib_div::makeInstance('t3lib_DB');

require_once(PATH_t3lib.'class.t3lib_timetrack.php');
$GLOBALS['TT'] = new t3lib_timeTrack;

// ***********************************
// Creating a $TSFE object
// ***********************************
$TSFEclassName = t3lib_div::makeInstanceClassName('tslib_fe');
$id = t3lib_div::_GET('id');
if (!isset($id)) $id = 0;
$id = htmlspecialchars($id);
$GLOBALS['TSFE'] = new $TSFEclassName($TYPO3_CONF_VARS, $id, '0', 1, '', '','','');
$GLOBALS['TSFE']->connectToDB();
$GLOBALS['TSFE']->initFEuser();
$GLOBALS['TSFE']->determineId();
// *******************************************
// Get compressed $TCA-Array();
// After this, we should now have a valid $TCA, though minimized
// *******************************************
$GLOBALS['TSFE']->getCompressedTCarray();
$GLOBALS['TSFE']->initTemplate();
$GLOBALS['TSFE']->tmpl->getFileName_backPath = PATH_site;

// ******************************************************
// Get config if not already gotten
// After this, we should have a valid config-array ready
// ******************************************************
$GLOBALS['TSFE']->getConfigArray();

// *******************************************
// Setting language and locale
// *******************************************
$GLOBALS['TT']->push('Setting language and locale','');
	$GLOBALS['TSFE']->settingLanguage();
	$GLOBALS['TSFE']->settingLocale();
$GLOBALS['TT']->pull();

// *******************************************
// Invoke the freecap plugin
// *******************************************
$freecap = t3lib_div::makeInstance('tx_srfreecap_pi1');
$freecap->cObj = t3lib_div::makeInstance('tslib_cObj');
$conf = $GLOBALS['TSFE']->tmpl->setup['plugin.'][$freecap->prefixId.'.'];
$freecap->main($conf);
?>