<?php
/***************************************************************
*  Copyright notice
*
*  (c) 2008-2009 Stanislas Rolland <typo3(arobas)sjbr.ca>
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

require_once(t3lib_extMgm::extPath('sr_freecap').'pi3/class.tx_srfreecap_pi3.php');

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
$freecap = t3lib_div::makeInstance('tx_srfreecap_pi3');
$freecap->cObj = t3lib_div::makeInstance('tslib_cObj');
$conf = $GLOBALS['TSFE']->tmpl->setup['plugin.'][$freecap->prefixId.'.'];
$freecap->main($conf);
?>