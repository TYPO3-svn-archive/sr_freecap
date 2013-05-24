<?php
namespace SJBR\SrFreecap\View\AudioPlayer;
/***************************************************************
*  Copyright notice
*
*  (c) 2012 Stanislas Rolland <typo3(arobas)sjbr.ca>
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
 * Renders a wav audio version of the CAPTCHA
 *
 * @author	Stanislas Rolland	<typo3(arobas)sjbr.ca>
 */
class PlayWav implements \TYPO3\CMS\Extbase\Mvc\View\ViewInterface {

	/**
	 * @var string Name of the extension this view helper belongs to
	 */
	protected $extensionName = 'SrFreecap';

	/**
	 * @var string Key of the extension this view helper belongs to
	 */
	protected $extensionKey = 'sr_freecap';

	/**
	 * @var \TYPO3\CMS\Core\Domain\Model\Word
	 */
	protected $word;

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
	 * @return \TYPO3\CMS\Extbase\Mvc\View\ViewInterface an instance of $this, to enable chaining
	 * @api
	 */
	public function assign($key, $value) {
		switch ($key) {
			case 'word':
				$this->word = $value;
				break;
		}
		return $this;
	}

	/**
	 * Add multiple variables to the view data collection
	 *
	 * @param array $values array in the format array(key1 => value1, key2 => value2)
	 * @return \TYPO3\CMS\Extbase\Mvc\View\ViewInterface an instance of $this, to enable chaining
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
	 * Renders the audio version of captcha
	 *
	 * @return string The audio output to play
	 */
	 public function render () {
	 	 // Get the catcha word
		$word = $this->getWord();
		// Get the letter rensering files
		$letterRenderingWaveFiles = $this->getLetterRenderingWaveFiles($word);
		// Join the files
		$audioContent = \SJBR\SrFreecap\Utility\WavContentUtility::joinWaveFiles($letterRenderingWaveFiles);
		// Output proper headers
		$this->sendHeaders($audioContent);
		// Return the audio content
		return $audioContent;
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
	 * Gets the word that was stored in session data
	 *
	 * @return	string		the retrieved and decoded word
	 */
	protected function getWord () {
		// Get cypher from session data
		$cypher =  $this->word->getWordCypher();
		// Decrypt the word
		$decryptedString = \SJBR\SrFreecap\Utility\EncryptionUtility::decrypt($cypher);
		return implode('-', str_split($decryptedString));
	}

	/**
	 * Builds an array of letter rendering wav files
	 *
	 * @param string $word: the word to be spelled and played
	 *
	 * @return array array of wav file names
	 */
	protected function getLetterRenderingWaveFiles ($word) {
		$letterRenderingWaveFiles = array();
		// Split the word
		$letters = str_split($word);
		// Get the directory containing the wav files
		$voicesDirectory = \SJBR\SrFreecap\Utility\LocalizationUtility::getVoicesDirectory();
		// Assemble the file names
		foreach ($letters as $letter){
			$file = $voicesDirectory . $letter . '.wav';
			if (is_file($file)) {
				$letterRenderingWaveFiles[] = $file;
			}
		}
		return $letterRenderingWaveFiles;
	}

	/**
	 * Sends headers appropriate for wav content
	 *
	 * @param string $audioContent: the audio content that will be sent
	 *
	 * @return	void
	 */
	protected function sendHeaders ($audioContent) {
		header('Content-Type: audio/x-wav');
		header('Content-Transfer-Encoding: binary');
		header('Content-Length: ' . strlen($audioContent));
		header('Expires: Mon, 26 Jul 1997 05:00:00 GMT');
		header('Last-Modified: ' . gmdate('D,d M YH:i:s') . ' GMT');
		header('Pragma: no-cache');
		header('Cache-Control: no-cache, no-store, must-revalidate');
	}
}
class_alias('SJBR\SrFreecap\View\AudioPlayer\PlayWav', 'Tx_SrFreecap_View_AudioPlayer_PlayWav');
?>