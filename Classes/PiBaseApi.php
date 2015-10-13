<?php
namespace SJBR\SrFreecap;

/*
 *  Copyright notice
 *
 *  (c) 2005-2015 Stanislas Rolland <typo3(arobas)sjbr.ca>
 *  All rights reserved
 *
 *  This script is part of the TYPO3 project. The TYPO3 project is
 *  free software; you can redistribute it and/or modify
 *  it under the terms of the GNU General Public License as published by
 *  the Free Software Foundation; either version 2 of the License, or
 *  (at your option) any later version.
 *
 *  The GNU General Public License can be found at
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
 */
/**
 * Integrates freeCap v1.4 into TYPO3 and checks the freeCap CAPTCHA word.
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
class PiBaseApi
{

	/**
	 * @var string The extension key
	 */
	public $extKey = 'sr_freecap';

	/**
	 * @var \TYPO3\CMS\Extbase\Object\ObjectManagerInterface $objectManager
	 */
	protected $objectManager = NULL;

	/**
	 * This function generates an array of markers used to render the captcha element
	 *
	 * @return array marker array containing the captcha markers to be sustituted in the html template
	 */	
	public function makeCaptcha()
	{

		// Get the object manager
		if ($this->objectManager === NULL) {
			$this->objectManager = \TYPO3\CMS\Core\Utility\GeneralUtility::makeInstance('TYPO3\\CMS\\Extbase\\Object\\ObjectManager');
		}
		
		// Get the configuration manager
		$configurationManager = $this->objectManager->get('TYPO3\\CMS\\Extbase\\Configuration\\ConfigurationManager');
		
		// Get translation view helper
		$translator = $this->objectManager->get('SJBR\\SrFreecap\\ViewHelpers\\TranslateViewHelper');
		$translator->injectConfigurationManager($configurationManager);

		$markerArray = array();
		$markerArray['###'. strtoupper($this->extKey) . '_NOTICE###'] = $translator->render('notice') . ' ' . $translator->render('explain');

		// Get the captcha image view helper
		$imageViewHelper = $this->objectManager->get('SJBR\\SrFreecap\\ViewHelpers\\ImageViewHelper');
		$imageViewHelper->injectConfigurationManager($configurationManager);
		$markerArray['###'. strtoupper($this->extKey) . '_IMAGE###'] = $imageViewHelper->render('pi1');
		$markerArray['###'. strtoupper($this->extKey) . '_CANT_READ###'] = '';

		// Get the audio icon view helper
		$audioViewHelper = $this->objectManager->get('SJBR\\SrFreecap\\ViewHelpers\\AudioViewHelper');
		$audioViewHelper->injectConfigurationManager($configurationManager);
		$markerArray['###'. strtoupper($this->extKey) . '_ACCESSIBLE###'] = $audioViewHelper->render('pi1');

		return $markerArray;
	}
	
	/**
	 * Check the word that was entered against the hashed value
	 *
	 * @param	string		$word: hte word that was entered
	 * @return	boolean		true, if the word entered matches the hashes value
	 */
	public function checkWord($word)
	{
		// Get the object manager
		if ($this->objectManager === NULL) {
			$this->objectManager = new \TYPO3\CMS\Extbase\Object\ObjectManager();
		}
		// Get the validator
		$validator = $this->objectManager->get('SJBR\\SrFreecap\\Validation\\Validator\\CaptchaValidator');
		// Check word
		return !$validator->validate($word)->hasErrors();
	}
}
class_alias('SJBR\\SrFreecap\\PiBaseApi', 'tx_srfreecap_pi2');