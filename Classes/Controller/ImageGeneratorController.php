<?php
namespace SJBR\SrFreecap\Controller;

/***************************************************************
*  Copyright notice
*
*  (c) 2012-2015 Stanislas Rolland <typo3(arobas)sjbr.ca>
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
 * Renders the CAPTCHA image
 *
 * @author	Stanislas Rolland	<typo3(arobas)sjbr.ca>
 */
class ImageGeneratorController extends \TYPO3\CMS\Extbase\Mvc\Controller\ActionController {

	/**
	 * @var string Name of the extension this controller belongs to
	 */
	protected $extensionName = 'SrFreecap';

	/**
	 * @var string Name of the extension this controller belongs to
	 */
	protected $extensionKey = 'sr_freecap';

	/**
	 * @var \SJBR\SrFreecap\Domain\Repository\WordRepository
	 */
	protected $wordRepository;

	/**
	 * Initialize any action
	 *
	 * @return void
	 */
	protected function initializeAction() {
		// Get an instance of the word repository
		$this->wordRepository = $this->objectManager->get('SJBR\\SrFreecap\\Domain\\Repository\\WordRepository');
	}

	/**
	 * Show the CAPTCHA image
	 *
	 * @return string empty string (image is sent by view)
	 */
	public function showAction() {
		// Get session data
		$word = $this->wordRepository->getWord();
		// Which type of hash to use
		// Store in session so can validate in form processor
		$word->setHashFunction('md5');
		$this->view->assign('word', $word);
		// Adjust settings
		$this->processSettings();
		$this->view->assign('settings', $this->settings);
		// Render the captcha image
		$this->view->render();
		// Store the session data
		$this->wordRepository->setWord($word);
	}

	/**
	 * Reviews and adjusts plugin settings
	 *
	 * @return void
	 * @api
	 */
	protected function processSettings () {

		// Image type:
		// possible values: "jpg", "png", "gif"
		// jpg doesn't support transparency (transparent bg option ends up white)
		// png isn't supported by old browsers (see http://www.libpng.org/pub/png/pngstatus.html)
		// gif may not be supported by your GD Lib.
		$this->settings['imageFormat'] = $this->settings['imageFormat'] ? $this->settings['imageFormat'] : 'png';
		
		// true = generate pseudo-random string, false = use dictionary
		// dictionary is easier to recognise
		// - both for humans and computers, so use random string if you're paranoid.
		$this->settings['useWordsList'] = $this->settings['useWordsList'] ? TRUE : FALSE;
		
		// if your server is NOT set up to deny web access to files beginning ".ht"
		// then you should ensure the dictionary file is kept outside the web directory
		// eg: if www.foo.com/index.html points to c:\website\www\index.html
		// then the dictionary should be placed in c:\website\dict.txt
		// test your server's config by trying to access the dictionary through a web browser
		// you should NOT be able to view the contents.
		// can leave this blank if not using dictionary
		$this->settings['wordsListLocation'] = \SJBR\SrFreecap\Utility\LocalizationUtility::getWordsListLocation($this->settings['defaultWordsList']);
		
		// Used for non-dictionary word generation and to calculate image width
		$this->settings['maxWordLength'] = $this->settings['maxWordLength'] ? $this->settings['maxWordLength'] : 6;

		// Maximum times a user can refresh the image
		// on a 6500 word dictionary, I think 15-50 is enough to not annoy users and make BF unfeasble.
		// further notes re: BF attacks in "avoid brute force attacks" section, below
		// on the other hand, those attempting OCR will find the ability to request new images
		// very useful; if they can't crack one, just grab an easier target...
		// for the ultra-paranoid, setting it to <5 will still work for most users
		$this->settings['maxAttempts'] = $this->settings['maxAttempts'] ? $this->settings['maxAttempts'] : 50;
		
		// List of fonts to use
		// font size should be around 35 pixels wide for each character.
		// you can use my GD fontmaker script at www.puremango.co.uk to create your own fonts
		// There are other programs to can create GD fonts, but my script allows a greater
		// degree of control over exactly how wide each character is, and is therefore
		// recommended for 'special' uses. For normal use of GD fonts,
		// the GDFontGenerator @ http://www.philiplb.de is excellent for convering ttf to GD
		// the fonts included with freeCap *only* include lowercase alphabetic characters
		// so are not suitable for most other uses
		// to increase security, you really should add other fonts
		if ($this->settings['generateNumbers']) {
			$this->settings['fontLocations'] = Array('EXT:' . $this->extensionKey . '/Resources/Private/Captcha/Fonts/anonymous.gdf');
		} else {
			$this->settings['fontLocations'] = Array(
				'EXT:' . $this->extensionKey . '/Resources/Private/Captcha/Fonts/freecap_font1.gdf',
				'EXT:' . $this->extensionKey . '/Resources/Private/Captcha/Fonts/freecap_font2.gdf',
				'EXT:' . $this->extensionKey . '/Resources/Private/Captcha/Fonts/freecap_font3.gdf',
				'EXT:' . $this->extensionKey . '/Resources/Private/Captcha/Fonts/freecap_font4.gdf',
				'EXT:' . $this->extensionKey . '/Resources/Private/Captcha/Fonts/freecap_font5.gdf'
				);
		}
		if ($this->settings['fontFiles']) {
			$this->settings['fontLocations'] = \TYPO3\CMS\Core\Utility\GeneralUtility::trimExplode(',', $this->settings['fontFiles'], 1);
		}
		for ($i = 0; $i < sizeof($this->settings['fontLocations']); $i++) {
			if (substr($this->settings['fontLocations'][$i],0,4) == 'EXT:') {
				$this->settings['fontLocations'][$i] = \TYPO3\CMS\Core\Utility\GeneralUtility::getFileAbsFileName($this->settings['fontLocations'][$i]);
			} else {
				$this->settings['fontLocations'][$i] = PATH_site . 'uploads/' . \TYPO3\CMS\Core\Utility\ExtensionManagementUtility::getCN($this->extensionKey) . '/' . $this->settings['fontLocations'][$i];
			}
		}

		// Text color
		// 0 = one random color for all letters
		// 1 = different random color for each letter
		if ($this->settings['textColor']) {
			$this->settings['textColor'] = 1;
		} else {
			$this->settings['textColor'] = 0;
		}

		// Text position
		$this->settings['textPosition'] = array();
		$this->settings['textPosition']['horizontal'] = $this->settings['textHorizontalPosition'] ? intval($this->settings['textHorizontalPosition']) : 32;
		$this->settings['textPosition']['vertical'] = $this->settings['textVerticalPosition'] ? intval($this->settings['textVerticalPosition']) : 15;
		// Text morphing factor
		$this->settings['morphFactor'] = $this->settings['morphFactor'] ? $this->settings['morphFactor'] : 0;
		// Limits for text color
		$this->settings['colorMaximum'] = array();
		if (isset($this->settings['colorMaximumDarkness'])) {
			$this->settings['colorMaximum']['darkness'] = intval($this->settings['colorMaximumDarkness']);
		}
		if (isset($this->settings['colorMaximumLightness'])) {
			$this->settings['colorMaximum']['lightness'] = intval($this->settings['colorMaximumLightness']);
		}

		// Background
		// Many thanks to http://ocr-research.org.ua and http://sam.zoy.org/pwntcha/ for testing
		// for jpgs, 'transparent' is white
		if (!in_array($this->settings['backgroundType'], array('Transparent', 'White with grid', 'White with squiggles', 'Morphed image blocks'))) {
			$this->settings['backgroundType'] = 'White with grid';
		}
		// Should we blur the background? (looks nicer, makes text easier to read, takes longer)
		$this->settings['backgroundBlur'] = ($this->settings['backgroundBlur'] || !isset($this->settings['backgroundBlur'])) ? TRUE : FALSE;
		// For background type 'Morphed image blocks', which images should we use?
		// If you add your own, make sure they're fairly 'busy' images (ie a lot of shapes in them)
		$this->settings['backgroundImages'] = Array(
			'EXT:' . $this->extensionKey . '/Resources/Private/Captcha/Images/freecap_im1.jpg',
			'EXT:' . $this->extensionKey . '/Resources/Private/Captcha/Images/freecap_im2.jpg',
			'EXT:' . $this->extensionKey . '/Resources/Private/Captcha/Images/freecap_im3.jpg',
			'EXT:' . $this->extensionKey . '/Resources/Private/Captcha/Images/freecap_im4.jpg',
			'EXT:' . $this->extensionKey . '/Resources/Private/Captcha/Images/freecap_im5.jpg'
			);
		// For non-transparent backgrounds only:
		// if 0, merges CAPTCHA with background
		// if 1, write CAPTCHA over background
		$this->settings['mergeWithBackground'] = $this->settings['mergeWithBackground'] ? 0 : 1;
		// Should we morph the background? (recommend yes, but takes a little longer to compute)
		$this->settings['backgroundMorph'] = $this->settings['backgroundMorph'] ? TRUE : FALSE;
		
		// Read each font and get font character widths
		$this->settings['fontWidths'] = Array();
		for ($i=0 ; $i < sizeof($this->settings['fontLocations']); $i++)	{
			$handle = fopen($this->settings['fontLocations'][$i],"r");
			// Read header of GD font, up to char width
			$c_wid = fread($handle,12);
			$this->settings['fontWidths'][$i] = ord($c_wid{8})+ord($c_wid{9})+ord($c_wid{10})+ord($c_wid{11});
			fclose($handle);
		}
		// Modify image width depending on maximum possible length of word
		// you shouldn't need to use words > 6 chars in length really.
		$this->settings['imageWidth'] = ($this->settings['maxWordLength'] * (array_sum($this->settings['fontWidths'])/sizeof($this->settings['fontWidths']))) + (isset($this->settings['imageAdditionalWidth']) ? intval($this->settings['imageAdditionalWidth']) : 40);
		$this->settings['imageHeight'] = $this->settings['imageHeight'] ? $this->settings['imageHeight'] : 90;

		// Try to avoid the 'free p*rn' method of CAPTCHA circumvention
		// see www.wikipedia.com/captcha for more info
		// "To avoid spam, please do NOT enter the text if this site is not example.org";
		// or more simply:
		// "for use only on example.org";
		// reword or add lines as you please
		$this->settings['siteTag'] = $this->settings['siteTag'] ? explode('|', \TYPO3\CMS\Extbase\Utility\LocalizationUtility::translate('site_tag', $this->extensionName, isset($this->settings['siteTagDomain']) ? $this->settings['siteTagDomain'] : 'example.org')) : array();
		
		// where to write the above:
		// 0=top
		// 1=bottom
		// 2=both
		$this->settings['siteTagPosition'] = isset($this->settings['siteTagPosition']) ? $this->settings['siteTagPosition'] : 1;
	}
}