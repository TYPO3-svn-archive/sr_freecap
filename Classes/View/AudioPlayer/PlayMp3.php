<?php
namespace SJBR\SrFreecap\View\AudioPlayer;
/***************************************************************
*  Copyright notice
*
*  (c) 2013 Stanislas Rolland <typo3(arobas)sjbr.ca>
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
 * Renders a mp3 audio version of the CAPTCHA
 *
 * @author	Stanislas Rolland	<typo3(arobas)sjbr.ca>
 */
class PlayMp3 extends AbstractPlayFormat {

	/**
	 * Renders the audio version of captcha
	 *
	 * @return string The audio output to play
	 */
	 public function render () {
	 	 // Get the catcha word
		$word = $this->getWord();
		// Get the letter rensering files
		$letterRenderingFiles = $this->getLetterRenderingFiles($word, 'mp3');
		// Join the files
		$audioContent = \SJBR\SrFreecap\Utility\AudioContentUtility::joinAudioFiles($letterRenderingFiles, 'mp3');
		// Output proper headers
		$this->sendHeaders($audioContent, 'mpeg');
		// Return the audio content
		return $audioContent;
	}
}