<?php
namespace SJBR\SrFreecap\Utility;
/***************************************************************
 *  Copyright notice
 *
 *  (c) 2009 Sebastian Kurfürst <sebastian@typo3.org>
 *  (c) 2013 Stanislas Rolland <typo3@sjbr.ca>
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
 *
 *  This script is distributed in the hope that it will be useful,
 *  but WITHOUT ANY WARRANTY; without even the implied warranty of
 *  MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 *  GNU General Public License for more details.
 *
 *  This copyright notice MUST APPEAR in all copies of the script!
 ***************************************************************/
/**
 * Localization helper which should be used to fetch appropriate words list or voice rendering language
 *
 */
class LocalizationUtility {

	/**
	 * Key of the extension to which this class belongs
	 *
	 * @var string
	 */
	protected static $extensionKey = 'sr_freecap';

	/**
	 * Key of the language to use
	 *
	 * @var string
	 */
	protected static $languageKey = 'default';

	/**
	 * Pointer to alternative fall-back language to use
	 *
	 * @var array
	 */
	protected static $alternativeLanguageKeys = array();

	/**
	 * Gets the location of the words list based on configured language
	 *
	 * @param string $defaultWordsList: location of the default words list
	 * @return string the location of the words list to be used
	 */
	public static function getWordsListLocation($defaultWordsList = '') {
		self::setLanguageKeys();
		$initialWordsList = $defaultWordsList;
		if (!trim($initialWordsList)) {
			$initialWordsList = 'EXT:' . self::$extensionKey . '/Resources/Private/Captcha/Words/default_freecap_words';
		}
		$path = dirname(\TYPO3\CMS\Core\Utility\GeneralUtility::getFileAbsFileName($initialWordsList)) . '/';
		$wordsListLocation = $path . self::$languageKey . '_freecap_words';
		if (!is_file($wordsListLocation)) {
			foreach (self::$alternativeLanguageKeys as $language) {
				$wordsListLocation = $path . $language . '_freecap_words';
				if (is_file($wordsListLocation)) {
					break;
				}
			}
		}
		if (!is_file($wordsListLocation)) {
			$wordsListLocation = $path . 'default_freecap_words';
			if (!is_file($wordsListLocation)) {
				$wordsListLocation = '';
			}
			
		}
		return $wordsListLocation;
	}

	/**
	 * Gets the directory of wav files based on configured language
	 *
	 * @return string name of the directory containing the wav files to be used
	 */
	public static function getVoicesDirectory() {
		self::setLanguageKeys();
		$path = \TYPO3\CMS\Core\Utility\ExtensionManagementUtility::extPath(self::$extensionKey) . '/Resources/Private/Captcha/Voices/';
		$voicesDirectory = $path . self::$languageKey . '/';
		if (!is_dir($voicesDirectory)) {
			foreach (self::$alternativeLanguageKeys as $language) {
				$voicesDirectory = $path . $language . '/';
				if (is_dir($voicesDirectory)) {
					break;
				}
			}
		}
		if (!is_dir($voicesDirectory)) {
			$voicesDirectory = $path . 'default/';
		}
		return $voicesDirectory;
	}

	/**
	 * Sets the currently active language/language_alt keys.
	 * Default values are "default" for language key and "" for language_alt key.
	 *
	 * @return void
	 */
	protected static function setLanguageKeys() {
		self::$languageKey = 'default';
		self::$alternativeLanguageKeys = array();
		if (TYPO3_MODE === 'FE') {
			if (isset($GLOBALS['TSFE']->config['config']['language'])) {
				self::$languageKey = $GLOBALS['TSFE']->config['config']['language'];
				if (isset($GLOBALS['TSFE']->config['config']['language_alt'])) {
					self::$alternativeLanguageKeys[] = $GLOBALS['TSFE']->config['config']['language_alt'];
				} else {
					/** @var $locales \TYPO3\CMS\Core\Localization\Locales */
					$locales = \TYPO3\CMS\Core\Utility\GeneralUtility::makeInstance('TYPO3\\CMS\\Core\\Localization\\Locales');
					if (in_array(self::$languageKey, $locales->getLocales())) {
						foreach ($locales->getLocaleDependencies(self::$languageKey) as $language) {
							self::$alternativeLanguageKeys[] = $language;
						}
					}
				}
			}
		} elseif (strlen($GLOBALS['BE_USER']->uc['lang']) > 0) {
			self::$languageKey = $GLOBALS['BE_USER']->uc['lang'];
			// Get standard locale dependencies for the backend
			/** @var $locales \TYPO3\CMS\Core\Localization\Locales */
			$locales = \TYPO3\CMS\Core\Utility\GeneralUtility::makeInstance('TYPO3\\CMS\\Core\\Localization\\Locales');
			if (in_array(self::$languageKey, $locales->getLocales())) {
				foreach ($locales->getLocaleDependencies(self::$languageKey) as $language) {
					self::$alternativeLanguageKeys[] = $language;
				}
			}
		}
	}
}
?>