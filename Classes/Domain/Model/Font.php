<?php
declare(encoding='ISO-8859-2');
namespace SJBR\SrFreecap\Domain\Model;
/***************************************************************
 *  Copyright notice
 *
 *  (c) 2012 Stanislas Rolland <typo3@sjbr.ca>
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
 * Font object
 *
 * This file must be iso-8859-2-encoded!
 *
 * @author Stanislas Rolland <typo3@sjbr.ca>
 */
class Font extends \TYPO3\CMS\Extbase\DomainObject\AbstractEntity {
	/**
	 * @var int
	 */
	protected $charactersIncludedInFont;

	/**
	 * @var int
	 * *@validate NumberRange(minimum=5, maximum=255)*
	 */
	protected $characterWidth;

	/**
	 * @var int
	 * *@validate NumberRange(minimum=5, maximum=255)*
	 */
	protected $characterHeight;

	/**
	 * @var int
	 **/
	protected $endianness;

	/**
	 * @var string
	 * *@validate StringLength(minimum=1, maximum=255)*
	 * *@validate \SJBR\SrFreecap\Validation\Validator\TtfFileValidator*
	 **/
	protected $ttfFontFileName = '';

	/**
	 * @var string
	 **/
	protected $gdFontFilePrefix = 'font';

	/**
	 * @var string
	 **/
	protected $pngImageFileName = '';

	/**
	 * @var string
	 **/
	protected $gdFontData = '';

	/**
	 * @var string
	 **/
	protected $gdFontFileName = '';

	public function __construct(
			$charactersIncludedInFont = 0,
			$characterWidth = 34,
			$characterHeight = 50,
			$endianness = 0,
			$ttfFontFileName = '',
			$gdFontFilePrefix = '',
			$pngImageFileName = '',
			$gdFontData = '',
			$gdFontFileName = ''
		) {
		$this->setCharactersIncludedInFont($charactersIncludedInFont);
		$this->setCharacterWidth($characterWidth);
		$this->setCharacterHeight($characterHeight);
		$this->setEndianness($endianness);
		$this->setTtfFontFileName($ttfFontFileName);
		$this->setGdFontFilePrefix($gdFontFilePrefix);
		$this->setPngImageFileName($pngImageFileName);
		$this->setGdFontData($gdFontData);
		$this->setGdFontFileName($gdFontFileName);
	}

	public function setCharactersIncludedInFont($charactersIncludedInFont) {
		$this->charactersIncludedInFont = (int)$charactersIncludedInFont;
	}

	public function getCharactersIncludedInFont() {
		return $this->charactersIncludedInFont;
	}
                                                                                                    
	public function setCharacterWidth($characterWidth) {
		$this->characterWidth = (int)$characterWidth;
	}
                                                                                                                   
	public function getCharacterWidth() {
		return $this->characterWidth;
	}

	public function setCharacterHeight($characterHeight) {                                      
		$this->characterHeight = (int)$characterHeight;
	}

	public function getCharacterHeight() {                                                   
		return $this->characterHeight;
	}

	public function setEndianness($endianness) {
		$this->endianness = (int)$endianness;
	}

	public function getEndianness() {
		return $this->endianness;
	}

	public function setTtfFontFileName($ttfFontFileName) {
		$this->ttfFontFileName = (string)$ttfFontFileName;
	}

	public function getTtfFontFileName() {
		return $this->ttfFontFileName;
	}

	public function setGdFontFilePrefix($gdFontFilePrefix = 'font') {
		$this->gdFontFilePrefix = (string)$gdFontFilePrefix;
	}

	public function getGdFontFilePrefix() {
		return $this->gdFontFilePrefix;
	}


	public function setPngImageFileName($pngImageFileName) {
		$this->pngImageFileName = (string)$pngImageFileName;		
	}

	public function getPngImageFileName() {
		return $this->pngImageFileName;
	}

	public function setGdFontData($gdFontData) {
		$this->gdFontData = (string)$gdFontData;
	}

	public function getGdFontdata() {
		return $this->gdFontData;
	}

	public function setGdFontFileName($gdFontFileName) {
		$this->gdFontFileName = (string)$gdFontFileName;
	}

	public function getGdFontFileName() {
		return $this->gdFontFileName;
	}

	/**
	 * Creates teh GD font file
	 */
	public function createGdFontFile() {
		switch ($this->charactersIncludedInFont) {
			case 1:
				$characters = 'a,b,c,d,e,f,g,h,i,j,k,l,m,n,o,p,q,r,s,t,u,v,w,x,y,z';
				$startCharacter = 'a';
				break;
			case 2:
				// Note: This script must be iso-8859-1-encoded
				$characters = 'a,b,c,d,e,f,g,h,i,j,k,l,m,n,o'
					.',p,q,r,s,t,u,v,w,x,y,z,-,-,-,-,-'
					.',-,-,-,-,-,-,-,-,-,-,-,-,-,-,-,-'
					.',-,-,-,-,-,-,-,-,-,-,-,-,-,-,-,-'
					.',-,-,-,-,-,-,-,-,-,-,-,-,-,-,-,-'
					.',-,-,-,-,-,-,-,-,-,-,-,-,-,-,-,-'
					.',-,-,-,-,-,-,-,-,-,-,-,-,-,-,-,-'
					.',-,-,-,-,-,-,-,-,-,-,-,-,-,-,-,ß'
					.',à,á,â,ã,ä,å,æ,ç,è,é,ê,ë,ì,í,î,ï'
					.',ð,ñ,ò,ó,ô,õ,ö,-,ø,ù,ú,û,ü,ý,þ,ÿ';
				$startCharacter = 'a';
				break;
			case 0:
			default:
				$characters = '0,1,2,3,4,5,6,7,8,9';
				$startCharacter = '0';
				break;
		}
		$numberOfCharacters = count(explode(',', $characters));
		$this->setPngImageFileName(\SJBR\SrFreecap\Utility\FontMakingUtility::makeFontImage($characters, '../' . $this->ttfFontFileName, $this->characterWidth, $this->characterHeight));
		if ($GLOBALS['TYPO3_CONF_VARS']['GFX']['gdlib_png']) {
			$image = @ImageCreateFromPNG(PATH_site . $this->pngImageFileName);
		} else {
			$image = @ImageCreateFromGIF(PATH_site . $this->pngImageFileName);
		}
		if ($image !== FALSE) {
			$this->setGdFontdata(\SJBR\SrFreecap\Utility\FontMakingUtility::makeFont($image, $numberOfCharacters, $startCharacter, $this->characterWidth, $this->characterHeight, $this->endianness));
			ImageDestroy($image);
		}
	}
}
?>