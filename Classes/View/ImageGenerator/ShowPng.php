<?php
namespace SJBR\SrFreecap\View\ImageGenerator;
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
 * Integrates freeCap v1.4.1 into TYPO3 and generates the freeCap CAPTCHA image.
 *
 *
 * @author	Stanislas Rolland	<typo3(arobas)sjbr.ca>
 */
/************************************************************\
*
*		freeCap v1.4.1 Copyright 2005 Howard Yeend
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
/**
 * Renders a png image of the CAPTCHA
 *
 * @author	Stanislas Rolland	<typo3(arobas)sjbr.ca>
 */
class ShowPng implements \TYPO3\CMS\Extbase\Mvc\View\ViewInterface {

	/**
	 * @var string Name of the extension this view helper belongs to
	 */
	protected $extensionName = 'SrFreecap';

	/**
	 * @var string Name of the plugin this view helper belongs to
	 */
	protected $pluginName = 'ImageGenerator';

	/**
	 * @var string Key of the extension this view helper belongs to
	 */
	protected $extensionKey = 'sr_freecap';

	/**
	 * @var \TYPO3\CMS\Core\Domain\Model\Word
	 */
	protected $word;

	/**
	 * @var array Configuration of this view
	 */
	protected $settings;

	/**
	 * Sets the current controller context
	 *
	 * @param \TYPO3\CMS\Extbase\Mvc\Controller\ControllerContext $controllerContext
	 * @return void
	 */
	public function setControllerContext(\TYPO3\CMS\Extbase\Mvc\Controller\ControllerContext $controllerContext) {
	}

	/**
	 * Add a variable to the view data collection.
	 * Can be chained, so $this->view->assign(..., ...)->assign(..., ...); is possible
	 *
	 * @param string $key Key of variable
	 * @param mixed $value Value of object
	 * @return SJBR\SrFreecap\View\ImageGenerator\ShowPng an instance of $this, to enable chaining
	 * @api
	 */
	public function assign($key, $value) {
		switch ($key) {
			case 'word':
				$this->word = $value;
				break;
			case 'settings':
				$this->settings = $value;
				break;
		}
		return $this;
	}

	/**
	 * Add multiple variables to the view data collection
	 *
	 * @param array $values array in the format array(key1 => value1, key2 => value2)
	 * @return SJBR\SrFreecap\View\ImageGenerator\ShowPng an instance of $this, to enable chaining
	 * @api
	 */
	public function assignMultiple(array $values) {
		return $this;
	}

	/**
	 * Tells if the view implementation can render the view for the given context.
	 *
	 * @param \TYPO3\CMS\Extbase\Mvc\Controller\ControllerContext $controllerContext
	 * @return boolean TRUE if the view has something useful to display, otherwise FALSE
	 * @api
	 */
	public function canRender(\TYPO3\CMS\Extbase\Mvc\Controller\ControllerContext $controllerContext) {
	 	 return TRUE;
	}

	/**
	 * Initializes this view.
	 *
	 * @return void
	 * @api
	 */
	public function initializeView() {
	}

	/**
	 * Renders the captcha image
	 *
	 * @return string empty string (the image is sent here)
	 */
	public function render () {

		// Avoid Brute Force Attacks:
		if (!$this->word->getAttempts()) {
			$this->word->setAttempts(1);
		} else {
			$this->word->setAttempts($this->word->getAttempts() + 1);
			// if more than ($this->settings['maxAttempts']) refreshes, block further refreshes
			// can be negated by connecting with new session id
			// could get round this by storing num attempts in database against IP
			// could get round that by connecting with different IP (eg, using proxy servers)
			// in short, there's little point trying to avoid brute forcing
			// the best way to protect against BF attacks is to ensure the dictionary is not
			// accessible via the web or use random string option
			if ($this->word->getAttempts() > $this->settings['maxAttempts']) {
				$this->word->setWordHash('');
				$this->word->setWordCypher(array());
				$this->word->setHashFunction('');
				// Get an instance of the word repository
				$objectManager = \TYPO3\CMS\Core\Utility\GeneralUtility::makeInstance('TYPO3\\CMS\\Extbase\\Object\\ObjectManager');
				$wordRepository = $objectManager->get('SJBR\\SrFreecap\\Domain\\Repository\\WordRepository');
				// Reset the word
				$wordRepository->setWord($this->word);
				$string = \TYPO3\CMS\Extbase\Utility\LocalizationUtility::translate('max_attempts', $this->extensionName);
				$font = 5;
				$width  = imagefontwidth($font) * strlen($string);
				$height = imagefontheight($font);
				$image = ImageCreate($width+2, $height+20);
				$background = ImageColorAllocate($image, 255,255,255);
				ImageColorTransparent($image, $background);
				$red = ImageColorAllocate($image, 255, 0, 0);
				ImageString($image, $font, 1, 10, $string, $red);
				\SJBR\SrFreecap\Utility\ImageContentUtility::sendImage($image, $this->settings['imageFormat']);
				ImageDestroy($image);
				// Return an empty string
				return '';
			}
		}

		// Get random word
		$word = \SJBR\SrFreecap\Utility\RandomContentUtility::getRandomWord($this->settings['useWordsList'], $this->settings['wordsListLocation'], $this->settings['generateNumbers'], $this->settings['maxWordLength']);

		// Save hash of word for comparison
		// using hash so that if there's an insecurity elsewhere (eg on the form processor),
		// an attacker could only get the hash
		// also, shared servers usually give all users access to the session files
		// echo `ls /tmp`; and echo `more /tmp/someone_elses_session_file`; usually work
		// so even if your site is 100% secure, someone else's site on your server might not be
		// hence, even if attackers can read the session file, they can't get the freeCap word
		// (though most hashes are easy to brute force for simple strings)
		$this->word->setWordHash(md5($word));

		// We use a simple encrypt to prevent the session from being exposed
		if ($this->settings['accessibleOutput']) {
			$this->word->setWordCypher(\SJBR\SrFreecap\Utility\EncryptionUtility::encrypt($word));
		}

		// Build the image
		$image = $this->buildImage($word, $this->settings['imageWidth'], $this->settings['imageHeight'], $this->settings['backgroundType']);

		// Send the image
		\SJBR\SrFreecap\Utility\ImageContentUtility::sendImage($image, $this->settings['imageFormat']);

		// Cleanup
		ImageDestroy($image);

		// Return an empty string
		return '';
	}

	/**
	 * Builds the CAPTCHA image
	 *
	 * @return string GD image identifier of image
	 */
	protected function buildImage ($word, $width, $height, $backgroundType) {
		
		$image = ImageCreate($width, $height);
		$background = ImageColorAllocate($image, 254, 254, 254);
		
		// Write word on image
		$image = \SJBR\SrFreecap\Utility\ImageContentUtility::writeWordOnImage($width, $height, $word, $this->settings['textColor'], $this->settings['textPosition'], $this->settings['colorMaximum'], $backgroundType, $this->settings['fontLocations'], $this->settings['fontWidths'], $this->settings['morphFactor']);

		// Blur edges
		// Doesn't really add any security, but looks a lot nicer, and renders text a little easier to read
		// for humans (hopefully not for OCRs, but if you know better, feel free to disable this function)
		// (and if you do, let me know why)
		$image = \SJBR\SrFreecap\Utility\ImageContentUtility::blurImage($image);

		if ($this->settings['imageFormat'] != 'jpg' && $backgroundType == \SJBR\SrFreecap\Utility\ImageContentUtility::BACKGROUND_TYPE_TRANSPARENT) {
			// Make background transparent
			ImageColorTransparent($image, $background);
		}

		if ($backgroundType != \SJBR\SrFreecap\Utility\ImageContentUtility::BACKGROUND_TYPE_TRANSPARENT) {
			// Get noisy background
			$image3 = \SJBR\SrFreecap\Utility\ImageContentUtility::generateNoisyBackground($width, $height, $word, $backgroundType, $this->settings['backgroundImages'], $this->settings['backgroundMorph'], $this->settings['backgroundBlur']);
			// Merge with obfuscated background
			$image = \SJBR\SrFreecap\Utility\ImageContentUtility::mergeCaptchaWithBackground($width, $height, $image, $image3, $backgroundType, $this->settings['mergeWithBackground']);
			ImageDestroy($image3);
		}

		return $image;
	}
}
