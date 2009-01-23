<?php
/***************************************************************
*  Copyright notice
*
*  (c) 2005-2008 Stanislas Rolland <typo3(arobas)sjbr.ca>
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

require_once(PATH_tslib.'class.tslib_pibase.php');

class tx_srfreecap_pi2 extends tslib_pibase {
	var $prefixId = 'tx_srfreecap_pi2';
	var $scriptRelPath = 'pi2/class.tx_srfreecap_pi2.php';  // Path to this script relative to the extension dir.
	var $extKey = 'sr_freecap';		// The extension key.
	var $conf = array();
	
	function makeCaptcha() {
		global $TSFE;
		
		$this->tslib_pibase();
		
			//Make sure that labels in locallang.php may be overridden
		$this->conf = $TSFE->tmpl->setup['plugin.'][$this->prefixId.'.'];
		$this->pi_loadLL();
		$this->pi_USER_INT_obj = 1;  // Disable caching
		
		$L = t3lib_div::_GP('L');
		if (isset($L)) {
			$L = htmlspecialchars($L);
		}
		$fakeId = t3lib_div::shortMD5(uniqid (rand()),5);
		$TSFE->additionalHeaderData[$this->extKey] .= '<script type="text/javascript" src="'. t3lib_extMgm::siteRelPath($this->extKey) . 'pi2/freeCap.js"></script>';		
		$audioURL = t3lib_extMgm::siteRelPath($this->extKey).'pi3/audioCaptcha.php?id=' . $GLOBALS['TSFE']->id . (isset($L)?'&amp;L='.$L:'');
		
		$markerArray = array();
		$markerArray['###'. strtoupper($this->extKey) . '_IMAGE###'] = '<img ' . $this->pi_classParam('image') . ' id="tx_srfreecap_pi2_captcha_image_'.$fakeId.'" src="'.t3lib_extMgm::siteRelPath($this->extKey).'pi1/captcha.php?id=' . $TSFE->id . (isset($L)?'&amp;L='.$L:'') . '" alt="' . $this->pi_getLL('altText') . '" style="vertical-align: middle; "/>';
		$markerArray['###'. strtoupper($this->extKey) . '_NOTICE###'] = $this->pi_getLL('notice') . ' ' . $this->pi_getLL('explain');
		$markerArray['###'. strtoupper($this->extKey) . '_CANT_READ###'] = '<span ' . $this->pi_classParam('cant-read') . '>' . $this->pi_getLL('cant_read1');
		$markerArray['###'. strtoupper($this->extKey) . '_CANT_READ###'] .= ' <a href="#" onclick="this.blur();newFreeCap(\''.$fakeId.'\', \''.$this->pi_getLL('noImageMessage').'\');return false;">' . $this->pi_getLL('click_here') . '</a>';
		$markerArray['###'. strtoupper($this->extKey) . '_CANT_READ###'] .= $this->pi_getLL('cant_read2') . '</span>';
		if ($this->conf['accessibleOutput']  && in_array('mcrypt', get_loaded_extensions())) {
			$markerArray['###'. strtoupper($this->extKey) . '_ACCESSIBLE###'] .= '<img alt="' . $this->pi_getLL('click_here_accessible') . '" title="' . $this->pi_getLL('click_here_accessible') . '" src="'.t3lib_extMgm::siteRelPath($this->extKey).'res/images/audio.png" onClick="playCaptcha(\''.$fakeId.'\', \''.$audioURL.'\', \''.$this->pi_getLL('noPlayMessage').'\');" style="cursor: pointer;"'.$this->pi_classParam('image-accessible').' /><div '.$this->pi_classParam('accessible').' id="tx_srfreecap_pi2_captcha_playAudio_'.$fakeId.'"></div>';
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
	function checkWord($word) {
			// Load session data
		$this->sessionData = $GLOBALS['TSFE']->fe_user->getKey('ses','tx_'.$this->extKey);
		if (!empty($this->sessionData[$this->extKey . '_word_hash']) && !empty($word)) {
			// all freeCap words are lowercase.
			// font #4 looks uppercase, but trust me, it's not...
			if ($this->sessionData[$this->extKey . '_hash_func'] == 'md5') {
				if (md5(strtolower(utf8_decode($word))) == $this->sessionData[$this->extKey . '_word_hash']) {
					// reset freeCap session vars
					// cannot stress enough how important it is to do this
					// defeats re-use of known image with spoofed session id
					$this->sessionData[$this->extKey . '_attempts'] = 0;
					$this->sessionData[$this->extKey . '_word_hash'] = false;
					return true;
				}
			}
		}
		return false;
	}

	/**
	 * From the 'salutationswitcher' extension.
	 *
	 * @author	Oliver Klee <typo-coding@oliverklee.de>
	 */
	    // list of allowed suffixes
	var $allowedSuffixes = array('formal', 'informal');
	
	/**
	 * Returns the localized label of the LOCAL_LANG key, $key
	 * In $this->conf['salutation'], a suffix to the key may be set (which may be either 'formal' or 'informal').
	 * If a corresponding key exists, the formal/informal localized string is used instead.
	 * If the key doesn't exist, we just use the normal string.
	 *
	 * Example: key = 'greeting', suffix = 'informal'. If the key 'greeting_informal' exists, that string is used.
	 * If it doesn't exist, we'll try to use the string with the key 'greeting'.
	 *
	 * Notice that for debugging purposes prefixes for the output values can be set with the internal vars ->LLtestPrefixAlt and ->LLtestPrefix
	 *
	 * @param    string        The key from the LOCAL_LANG array for which to return the value.
	 * @param    string        Alternative string to return IF no value is found set for the key, neither for the local language nor the default.
	 * @param    boolean        If true, the output label is passed through htmlspecialchars()
	 * @return    string        The value from LOCAL_LANG.
	 */
	function pi_getLL($key, $alt = '', $hsc = FALSE) {
			// If the suffix is allowed and we have a localized string for the desired salutation, we'll take that.
		if (isset($this->conf['salutation']) && in_array($this->conf['salutation'], $this->allowedSuffixes, 1)) {
			$expandedKey = $key.'_'.$this->conf['salutation'];
			if (isset($this->LOCAL_LANG[$this->LLkey][$expandedKey])) {
				$key = $expandedKey;
			}
		}
		return parent::pi_getLL($key, $alt, $hsc);
	}

}

if (defined('TYPO3_MODE') && $TYPO3_CONF_VARS[TYPO3_MODE]['XCLASS']['ext/sr_freecap/pi2/class.tx_srfreecap_pi2.php']) {
	include_once($TYPO3_CONF_VARS[TYPO3_MODE]['XCLASS']['ext/sr_freecap/pi2/class.tx_srfreecap_pi2.php']);
}

?>