<?php
/***************************************************************
 * Copyright notice
 *
 * 2010 Daniel Lienert <daniel@lienert.cc>, Michael Knoll <mimi@kaktusteam.de>
 * 2012 Stanislas Rolland <typo3(arobas)sjbr.ca>
 * All rights reserved
 *
 *
 * This script is part of the TYPO3 project. The TYPO3 project is
 * free software; you can redistribute it and/or modify
 * it under the terms of the GNU General Public License as published by
 * the Free Software Foundation; either version 2 of the License, or
 * (at your option) any later version.
 *
 * The GNU General Public License can be found at
 * http://www.gnu.org/copyleft/gpl.html.
 *
 * This script is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the
 * GNU General Public License for more details.
 *
 * This copyright notice MUST APPEAR in all copies of the script!
 ***************************************************************/
/**
 * This script loads the required environment to dispatch an extbase call
 *
 * Include this script in ext_localconf:
 * $GLOBALS['TYPO3_CONF_VARS']['FE']['eID_include']['sr_freecap_EidDispatcher'] = \TYPO3\CMS\Core\Utility\ExtensionManagementUtility::extPath('sr_freecap') . 'Resources/Private/Eid/EidDispatcher.php'
 *
 * @author Daniel Lienert <daniel@lienert.cc>
 * @author Stanislas Rolland <typo3(arobas)sjbr.ca>
 */
// Exit, if script is called directly
if (!defined('TYPO3_MODE') || TYPO3_MODE !== 'FE') {
	die('Could not access this script directly!');
}
// Hand over to the Eid Utility Object
/** @var $dispatcher SJBR\SrFreecap\Utility\EidUtility */
$dispatcher = \TYPO3\CMS\Core\Utility\GeneralUtility::makeInstance('SJBR\\SrFreecap\\Utility\\EidUtility');
echo $dispatcher->initAndDispatch();
?>
