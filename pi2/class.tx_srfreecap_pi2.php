<?php
/***************************************************************
*  Copyright notice
*
*  (c) 2005-2013 Stanislas Rolland <typo3(arobas)sjbr.ca>
*  All rights reserved
*
*  This script is part of the TYPO3 project. The TYPO3 project is
*  free software; you can redistribute it and/or modify
*  it under the terms of the GNU General Public License as published by
*  the Free Software Foundation; either version 2 of the License, or
*  (at your option) any later version.
*
*  The GNU General Public License can be found at
*  http://www.gnu.org/copyleft/gpl.html.
*  A copy is found in the textfile GPL.txt and important notices to the license
*  from the author is found in LICENSE.txt distributed with these scripts.
*
*
*  This script is distributed in the hope that it will be useful,
*  but WITHOUT ANY WARRANTY; without even the implied warranty of
*  MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
*  GNU General Public License for more details.
*
*  This copyright notice MUST APPEAR in all copies of the script!
***************************************************************/
/**
 * Integrates freeCap v1.4 into TYPO3 and checks the freeCap CAPTCHA word.
 *
 *
 * @author	Stanislas Rolland	<typo3(arobas)sjbr.ca>
 */
/************************************************************\
*
*		freeCap v1.4 Copyright 2005 Howard Yeend
*		www.puremango.co.uk
*
*    This file is part of freeCap.
*
*    freeCap is free software; you can redistribute it and/or modify
*    it under the terms of the GNU General Public License as published by
*    the Free Software Foundation; either version 2 of the License, or
*    (at your option) any later version.
*
*    freeCap is distributed in the hope that it will be useful,
*    but WITHOUT ANY WARRANTY; without even the implied warranty of
*    MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
*    GNU General Public License for more details.
*
*    You should have received a copy of the GNU General Public License
*    along with freeCap; if not, write to the Free Software
*    Foundation, Inc., 59 Temple Place, Suite 330, Boston, MA  02111-1307  USA
*
*
\************************************************************/
class tx_srfreecap_pi2 extends tslib_pibase {

	/**
	 * @var string Name of this plugin
	 */
	public $prefixId = 'tx_srfreecap_pi2';

	/**
	 * @var string Path to this script relative to the extension directory
	 */
	public $scriptRelPath = 'pi2/class.tx_srfreecap_pi2.php';

	/**
	 * @var string The extension key
	 */
	public $extKey = 'sr_freecap';

	/**
	 * @var array The TypoScript configuration for this plugin
	 */
	public $conf = array();

	/**
	 * This function generates an array of markers used to render the captcha element
	 *
	 * @return array marker array containing the captcha markers to be sustituted in the html template
	 */	
	public function makeCaptcha() {

		parent::__construct();

		// Get the translation view helper
		$configurationManager = \TYPO3\CMS\Core\Utility\GeneralUtility::makeInstance('TYPO3\\CMS\\Extbase\\Configuration\\ConfigurationManager');
		$objectManager = \TYPO3\CMS\Core\Utility\GeneralUtility::makeInstance('TYPO3\\CMS\\Extbase\\Object\\ObjectManager');
		$configurationManager->injectObjectManager($objectManager);
		$translator = \TYPO3\CMS\Core\Utility\GeneralUtility::makeInstance('SJBR\\SrFreecap\\ViewHelpers\\TranslateViewHelper');
		$translator->injectConfigurationManager($configurationManager);

		//Get the TypoScript configuration
		$this->conf = $GLOBALS['TSFE']->tmpl->setup['plugin.'][$this->prefixId.'.'];

		// Disable caching
		$this->pi_USER_INT_obj = 1;
		$siteURL = \TYPO3\CMS\Core\Utility\GeneralUtility::getIndpEnv('TYPO3_SITE_URL');

		$fakeId = \TYPO3\CMS\Core\Utility\GeneralUtility::shortMD5(uniqid (rand()),5);
		$GLOBALS['TSFE']->additionalHeaderData[$this->extKey] .= '<script type="text/javascript" src="'. \TYPO3\CMS\Core\Utility\ExtensionManagementUtility::siteRelPath($this->extKey) . 'Resources/Public/JavaScript/freeCap.js"></script>';

		$urlParams = array(
			'eID' => 'sr_freecap_EidDispatcher',
			'id' => $GLOBALS['TSFE']->id,
			'extensionName' => 'SrFreecap',
			'pluginName' => 'AudioPlayer',
			'controllerName' => 'AudioPlayer',
			'actionName' => 'play',
			'formatName' => 'wav',
		);
		$L = \TYPO3\CMS\Core\Utility\GeneralUtility::_GP('L');
		if (isset($L)) {
			$urlParams['L'] = htmlspecialchars($L);
		}
		if ($GLOBALS['TSFE']->MP) {
			$urlParams['MP'] = $GLOBALS['TSFE']->MP;
		}
		$audioURL = $siteURL . 'index.php?' . ltrim(\TYPO3\CMS\Core\Utility\GeneralUtility::implodeArrayForUrl('', $urlParams), '&');

		$urlParams = array(
			'eID' => 'sr_freecap_EidDispatcher',
			'id' => $GLOBALS['TSFE']->id,
			'extensionName' => 'SrFreecap',
			'pluginName' => 'ImageGenerator',
			'controllerName' => 'ImageGenerator',
			'actionName' => 'show',
			'formatName' => 'png',
		);
		if (isset($L)) {
			$urlParams['L'] = htmlspecialchars($L);
		}
		if ($GLOBALS['TSFE']->MP) {
			$urlParams['MP'] = $GLOBALS['TSFE']->MP;
		}
		$imgUrl = $siteURL . 'index.php?' . ltrim(\TYPO3\CMS\Core\Utility\GeneralUtility::implodeArrayForUrl('', $urlParams), '&');

		$markerArray = array();
		$markerArray['###'. strtoupper($this->extKey) . '_IMAGE###'] = '<img' . $this->pi_classParam('image') . ' id="tx_srfreecap_pi2_captcha_image_'.$fakeId.'" src="' . htmlspecialchars($imgUrl) . '" alt="' . $translator->render('altText') . ' "/>';
		$markerArray['###'. strtoupper($this->extKey) . '_NOTICE###'] = $translator->render('notice') . ' ' . $translator->render('explain');
		$markerArray['###'. strtoupper($this->extKey) . '_CANT_READ###'] = '<span' . $this->pi_classParam('cant-read') . '>' . $translator->render('cant_read1');
		$markerArray['###'. strtoupper($this->extKey) . '_CANT_READ###'] .= ' <a href="#" onclick="this.blur();newFreeCap(\''.$fakeId.'\', \'' . $translator->render('noImageMessage').'\');return false;">' . $translator->render('click_here') . '</a>';
		$markerArray['###'. strtoupper($this->extKey) . '_CANT_READ###'] .= $translator->render('cant_read2') . '</span>';
		if ($this->conf['accessibleOutput'] && in_array('mcrypt', get_loaded_extensions())) {
			if ($this->conf['accessibleOutputImage']) {
				$markerArray['###'. strtoupper($this->extKey) . '_ACCESSIBLE###'] .= '<input type="image" alt="' . $translator->render('click_here_accessible') . '" title="' . $translator->render('click_here_accessible') . '" src="' . $siteURL . str_replace(PATH_site, '', \TYPO3\CMS\Core\Utility\GeneralUtility::getFileAbsFileName($this->conf['accessibleOutputImage'])) . '" onclick="playCaptcha(\''.$fakeId.'\', \''.$audioURL.'\', \'' . $translator->render('noPlayMessage').'\');return false;" style="cursor: pointer;"' . $this->pi_classParam('image-accessible') . ' /><span'.$this->pi_classParam('accessible').' id="tx_srfreecap_pi2_captcha_playAudio_'.$fakeId.'"></span>';
			} else {
				$markerArray['###'. strtoupper($this->extKey) . '_ACCESSIBLE###'] .= '<span id="tx_srfreecap_pi2_captcha_playLink_'.$fakeId.'"' . $this->pi_classParam('accessible-link') . '>' . $translator->render('click_here_accessible_before_link');
				$markerArray['###'. strtoupper($this->extKey) . '_ACCESSIBLE###'] .= '<a onClick="playCaptcha(\''.$fakeId.'\', \''.$audioURL.'\', \'' . $translator->render('noPlayMessage').'\');" style="cursor: pointer;" title="' . $translator->render('click_here_accessible') . '">' . $translator->render('click_here_accessible_link').'</a>';
				$markerArray['###'. strtoupper($this->extKey) . '_ACCESSIBLE###'] .= $translator->render('click_here_accessible_after_link').'</span>';
				$markerArray['###'. strtoupper($this->extKey) . '_ACCESSIBLE###'] .= '<span ' . $this->pi_classParam('accessible').' id="tx_srfreecap_pi2_captcha_playAudio_'.$fakeId.'"></span>';
			}
		} else {
			$markerArray['###'. strtoupper($this->extKey) . '_ACCESSIBLE###'] .= '';
		}
		return $markerArray;
	}
	
	/**
	 * Check the word that was entered against the hashed value
	 *
	 * @param	string		$word: hte word that was entered
	 * @return	boolean		true, if the word entered matches the hashes value
	 */
	public function checkWord ($word) {
		// Get validator
		$validator = \TYPO3\CMS\Core\Utility\GeneralUtility::makeInstance('SJBR\\SrFreecap\\Validation\\Validator\\CaptchaValidator');
		// Check word
		return $validator->isValid($word);
	}
}
?>