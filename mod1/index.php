<?php
/***************************************************************
*  Copyright notice
*
*  (c) 2006-2008 Stanislas Rolland <typo3(arobas)sjbr.ca>
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
* Module 'Font Maker' for the 'sr_freecap' extension.
*
* @author Stanislas Rolland <typo3(arobas)sjbr.ca>
*/
	// initialization of the module
unset($MCONF);
require('conf.php');
require($BACK_PATH.'init.php');
require($BACK_PATH.'template.php');
$LANG->includeLLFile(t3lib_extMgm::extPath('sr_freecap').'mod1/locallang.xml');
require_once(t3lib_extMgm::extPath('sr_freecap').'mod1/class.tx_srfreecap_fontmaker.php');

$BE_USER->modAccess($MCONF, 1); // This checks permissions and exits if the users has no permission for entry.

	// Make instance:
$SOBE = t3lib_div::makeInstance('tx_srfreecap_fontmaker');
$SOBE->init();

	// Include files?
reset($SOBE->include_once);
while (list(, $INC_FILE) = each($SOBE->include_once)) {
	include_once($INC_FILE);
}

$SOBE->main();
$SOBE->printContent();

?>
