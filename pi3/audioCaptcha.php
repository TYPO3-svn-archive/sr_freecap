<?php
/***************************************************************
*  Copyright notice
*
*  (c) 2008 Stanislas Rolland <typo3(arobas)sjbr.ca>
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
 * This script invokes SysTurn speech generation
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
require_once(t3lib_extMgm::extPath('sr_freecap').'pi3/class.tx_srfreecap_pi3.php');
require_once(PATH_t3lib.'class.t3lib_userauth.php');
require_once(PATH_tslib.'class.tslib_feuserauth.php');
require_once(PATH_t3lib.'class.t3lib_cs.php');

if (!defined ('TYPO3_db'))  die ('The configuration file was not included.');
if (isset($_GET['GLOBALS']) || isset($_POST['GLOBALS']) || isset($_FILES['GLOBALS']) || isset($_COOKIE['GLOBALS'])) die('You cannot set the GLOBALS-array from outside this script.');

require_once(PATH_t3lib.'class.t3lib_db.php');
$TYPO3_DB = t3lib_div::makeInstance('t3lib_DB');

require_once(PATH_t3lib.'class.t3lib_timetrack.php');
$GLOBALS['TT'] = new t3lib_timeTrack;

if (t3lib_div::int_from_ver( TYPO3_version ) >= 4003000) {
	// ***********************************
	// Initializing the Caching System
	// ***********************************
	$GLOBALS['TT']->push('Initializing the Caching System','');
		require_once(PATH_t3lib . 'class.t3lib_cache.php');

		require_once(PATH_t3lib . 'cache/class.t3lib_cache_abstractbackend.php');
		require_once(PATH_t3lib . 'cache/class.t3lib_cache_abstractcache.php');
		require_once(PATH_t3lib . 'cache/class.t3lib_cache_exception.php');
		require_once(PATH_t3lib . 'cache/class.t3lib_cache_factory.php');
		require_once(PATH_t3lib . 'cache/class.t3lib_cache_manager.php');
		require_once(PATH_t3lib . 'cache/class.t3lib_cache_variablecache.php');

		require_once(PATH_t3lib . 'cache/exception/class.t3lib_cache_exception_classalreadyloaded.php');
		require_once(PATH_t3lib . 'cache/exception/class.t3lib_cache_exception_duplicateidentifier.php');
		require_once(PATH_t3lib . 'cache/exception/class.t3lib_cache_exception_invalidbackend.php');
		require_once(PATH_t3lib . 'cache/exception/class.t3lib_cache_exception_invalidcache.php');
		require_once(PATH_t3lib . 'cache/exception/class.t3lib_cache_exception_invaliddata.php');
		require_once(PATH_t3lib . 'cache/exception/class.t3lib_cache_exception_nosuchcache.php');

		$typo3CacheManager = t3lib_div::makeInstance('t3lib_cache_Manager');
		$cacheFactoryClass = t3lib_div::makeInstanceClassName('t3lib_cache_Factory');
		$typo3CacheFactory = new $cacheFactoryClass($typo3CacheManager);

		unset($cacheFactoryClass);
	$GLOBALS['TT']->pull();
}

// ***********************************
// Creating a $TSFE object
// ***********************************
$TSFEclassName = t3lib_div::makeInstanceClassName('tslib_fe');
$id = t3lib_div::_GET('id');
if (!isset($id)) $id = 0;
$id = htmlspecialchars($id);
$TSFE = new $TSFEclassName($TYPO3_CONF_VARS, $id, '0', 1, '', '','','');
if (t3lib_div::int_from_ver( TYPO3_version ) >= 4003000) {
	$TSFE->initCaches();
}
$TSFE->set_no_cache();
$TSFE->connectToDB();
$TSFE->initFEuser();
$TSFE->determineId();
$TSFE->initTemplate();
$TSFE->tmpl->getFileName_backPath = PATH_site;

// ******************************************************
// Get config if not already gotten
// After this, we should have a valid config-array ready
// ******************************************************
$TSFE->getConfigArray();

// *******************************************
// Setting language and locale
// *******************************************
$GLOBALS['TT']->push('Setting language and locale','');
	$TSFE->settingLanguage();
	$TSFE->settingLocale();
$GLOBALS['TT']->pull();

// *******************************************
// Invoke the freecap plugin
// *******************************************
$freecap = t3lib_div::makeInstance('tx_srfreecap_pi3');
$freecap->cObj = t3lib_div::makeInstance('tslib_cObj');
$conf = $TSFE->tmpl->setup['plugin.'][$freecap->prefixId.'.'];
$freecap->main($conf);
?>