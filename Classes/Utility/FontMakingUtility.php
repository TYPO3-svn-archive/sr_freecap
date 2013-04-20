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
 * Utility for making GD fonts
 *
 * @author	Stanislas Rolland	<typo3(arobas)sjbr.ca>
 */
class FontMakingUtility {

	/**
	 * Returns an image displaying a list of characters with specified font file and character size
	 *
	 * @param string $characters: text to display on the image
	 * @param string $font: specified True Type font file name
	 * @param integer $width: width of each character
	 * @param string $height: height of the image
	 * @return array image file info array
	 */
	public static function makeFontImage ($characters, $font, $width = 34, $height = 50) {
		
		$size = intval($height * .8);
		$vertOffset = intval($height * .7);
		$color = '#000000';
		$bgColor = 'white';
		$align = 'left';
		
		$charactersArray = explode(',', $characters);
		$charactersCount = count($charactersArray);
		
		$gifObjArray = array();
		$gifObjArray['backColor'] = $bgColor;
		$gifObjArray['transparentBackground'] = 0;
		$gifObjArray['reduceColors'] = '';
		$gifObjArray['maxWidth'] = ($charactersCount * $width) + 1;	
		$gifObjArray['XY'] = ($charactersCount * $width) . ',' . $height;
		
		for ($ic = 1; $ic < $charactersCount+1; $ic++) {
			$gifObjArray[$ic . '0'] = 'TEXT';
			$gifObjArray[$ic . '0.']['text'] = $charactersArray[$ic-1];
			
			$bbox = imagettfbbox($size, 0, $font, $charactersArray[$ic-1]);
			$hOffset = intval(($width - ($bbox[4] - $bbox[6]))/2);
			$vOffset = intval(($width - ($bbox[7] - $bbox[1]))/2);
			
			$gifObjArray[$ic . '0.']['niceText'] = 0;
			$gifObjArray[$ic . '0.']['antiAlias'] = 1;
			$gifObjArray[$ic . '0.']['align'] = $align;
			$gifObjArray[$ic . '0.']['fontSize'] = $size;
			$gifObjArray[$ic . '0.']['fontFile'] = $font;
			$gifObjArray[$ic . '0.']['fontColor'] = $color;
			$gifObjArray[$ic . '0.']['maxWidth'] = $width;
			$gifObjArray[$ic . '0.']['offset'] = (($ic-1) * $width + $hOffset) . ',' . $vertOffset;
		}
		$objectManager = \TYPO3\CMS\Core\Utility\GeneralUtility::makeInstance('TYPO3\\CMS\\Extbase\\Object\\ObjectManager');
		$gifCreator = $objectManager->create('SJBR\\SrFreecap\\Utility\\GifBuilderUtility');
		$gifCreator->init();
		if ($GLOBALS['TYPO3_CONF_VARS']['GFX']['gdlib']) {
			$gifCreator->start($gifObjArray, array());
			return $gifCreator->gifBuild();
		} else {
			return FALSE;
		}
	}

	/************************************************************\
	*
	*		GD Fontmaker Copyright 2005 Howard Yeend
	*		www.puremango.co.uk
	*
	*    This file is part of GD Fontmaker.
	*
	*    GD Fontmaker is free software; you can redistribute it and/or modify
	*    it under the terms of the GNU General Public License as published by
	*    the Free Software Foundation; either version 2 of the License, or
	*    (at your option) any later version.
	*
	*    GD Fontmaker is distributed in the hope that it will be useful,
	*    but WITHOUT ANY WARRANTY; without even the implied warranty of
	*    MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
	*    GNU General Public License for more details.
	*
	*    You should have received a copy of the GNU General Public License
	*    along with GD Fontmaker; if not, write to the Free Software
	*    Foundation, Inc., 59 Temple Place, Suite 330, Boston, MA  02111-1307  USA
	*
	*
	\************************************************************/
	public static function makeFont ($image, $numchars, $startchar, $pixelwidth, $pixelheight, $endianness = 0) {
		$startchar = ord($startchar);
		// encode this at start of font
		if ($endianness) {
			// big-endian
			$fontdata = chr(0).chr(0).chr(0).chr($numchars).chr(0).chr(0).chr(0).chr($startchar).chr(0).chr(0).chr(0).chr($pixelwidth).chr(0).chr(0).chr(0).chr($pixelheight);
		} else {
			// little-endian
			$fontdata = chr($numchars).chr(0).chr(0).chr(0).chr($startchar).chr(0).chr(0).chr(0).chr($pixelwidth).chr(0).chr(0).chr(0).chr($pixelheight).chr(0).chr(0).chr(0);
		}
		// loop through each pixel of each character of the PNG
		// (we know the dimensions of the characters because the user told us what they were)
		$y = 0;
		$x = 0;
		$start_x = 0;
		for ($c = 0; $c < $numchars*$pixelwidth; $c += $pixelwidth) {
			for ($y = 0; $y < $pixelheight; $y++) {
				for ($x = $c; $x < $c+$pixelwidth; $x++) {
					// get colour of this pixel
					$rgb = ImageColorAt($image, $x, $y);
					if ($rgb == 0) {
						// it's black; font data
						$fontdata .= chr(255);
					} else {
						// it's not black; background
						$fontdata .= chr(0);
					}
					$i++;
				}
			}
		}
		return $fontdata;
	}
}
?>