<?php
namespace SJBR\SrFreecap\Utility;
/***************************************************************
*  Copyright notice
*
*  (c) 2012-2013 Stanislas Rolland <typo3(arobas)sjbr.ca>
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
 * Utility dealing with mp3 audio content
 * See http://www.theblog.ca/merge-mp3s-php
 *
 * @author	Stanislas Rolland	<typo3(arobas)sjbr.ca>
 */
class Mp3ContentUtility {

	var $content;

	// Create a new mp3
	public function __construct($file = '') {
		if ($file != '') {
			$this->content = file_get_contents($file);
		}
	}

	// Get the mp3 content
	public function getContent() {
		return $this->content;
	}

	// Calculate where's the beginning of the sound file
	protected function getStart() {
		$strlen = strlen($this->content);
		for ($i=0; $i < $strlen; $i++) {
			$v = substr($this->content, $i, 1);
			$value = ord($v);
			if ($value == 255) {
				return $i;
			}
		}
	}

	// Calculate where's the end of the sound file 
	protected function getIdvEnd() {
		$strlen = strlen($this->content);
		$str = substr($this->content, ($strlen - 128));
		$str1 = substr($str, 0, 3);
		if (strtolower($str1) == strtolower('TAG')) {
			return $str;
		} else {
			return FALSE;
		}
	}

	// Remove the ID3 tags 
	public function striptags() {
		// Remove start stuff... 
		$newStr = '';
		$s = $start = $this->getStart();
		if ($s === FALSE) {
			return FALSE;
		} else {
			$this->content = substr($this->content, $start);
		}
		//Remove end tag stuff
		$end = $this->getIdvEnd();
		if ($end !== FALSE) {
			$this->content = substr($this->content, 0, (strlen($this->content)-129));
		}
		return $this->content;
	}
}
?>