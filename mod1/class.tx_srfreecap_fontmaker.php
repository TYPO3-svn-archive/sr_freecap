<?php
/***************************************************************
*  Copyright notice
*
*  (c) 2007-2012 Stanislas Rolland <typo3(arobas)sjbr.ca>
*  All rights reserved
*
*  This script is part of the Typo3 project. The Typo3 project is
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
* Module 'GD Font Makers' for the 'sr_freecap' extension.
*
* @author Stanislas Rolland <typo3(arobas)sjbr.ca>
*/

class tx_srfreecap_fontmaker extends t3lib_SCbase {
	var $extKey = 'sr_freecap';
	var $extPrefix = 'tx_srfreecap';
	var $pageinfo;

	/**
	 * Main function of the module. Write the content to $this->content
	 *
	 * @return void
	 */
	function main() {
		
			// Access check!
			// The page will show only if there is a valid page and if this page may be viewed by the user
		$this->pageinfo = t3lib_BEfunc::readPageAccess($this->id, $this->perms_clause);
		$access = is_array($this->pageinfo) ? 1 : 0;
		
		if (($this->id && $access) || ($GLOBALS['BE_USER']->user['admin'] && !$this->id)) {
			
				// Draw the header.
			$this->doc = t3lib_div::makeInstance('template');
			$this->doc->backPath = $GLOBALS['BACK_PATH'];
			$this->doc->form = '<form action="" method="post" enctype="multipart/form-data">';
			
				// JavaScript
			$this->doc->JScode = '
				<script type="text/javascript">
					/*<![CDATA[*/
					<!--
					script_ended = 0;
					function jumpToUrl(URL) {
					document.location = URL;
					}
					// -->
					/*]]>*/
					</script>
				';
				// Render module content
			$content = $this->doc->section($GLOBALS['LANG']->getLL('title'), $this->moduleContent());
				// ShortCut
			if ($GLOBALS['BE_USER']->mayMakeShortcut()) {
				$this->content .= $this->doc->spacer(20).$this->doc->section('', $this->doc->makeShortcutIcon('id', implode(',', array_keys($this->MOD_MENU)), $this->MCONF['name']));
			}
			$this->content = $this->doc->render($GLOBALS['LANG']->getLL('title'), $content);
		} else {
				// If no access or if ID == zero
			$this->doc = t3lib_div::makeInstance('template');
			$this->doc->backPath = $GLOBALS['BACK_PATH'];
			$content = $this->doc->header($LANG->getLL('title'));
			$this->content = $this->doc->render($GLOBALS['LANG']->getLL('title'), $content);
		}
	}
	
	/**
	 * Prints out the module HTML
	 *
	 * @return void
	 */
	function printContent() {
		echo $this->content;
	}
	
	/**
	 * Generates the module content
	 *
	 * @return void
	 */
	function moduleContent() {
		$content = '';
			// Get user supplied data
		$charactersToIncludeInFont = intval(t3lib_div::_GP('charactersToIncludeInFont'));
		
		$pixelwidth = intval(t3lib_div::_GP('pixelwidth'));
		$pixelheight = intval(t3lib_div::_GP('pixelheight'));
		if (!$pixelheight) $pixelheight = 50;
		
		$endianness = intval(t3lib_div::_GP('endianness'));
		
		$gdFontFileName = t3lib_div::_GP('gdfontfilename');
		if (!trim($gdFontFileName)) {
			$gdFontFileName = 'font';
		}
		
		$fontFileName = t3lib_div::_GP('fontfilename');
		if (!trim($fontFileName)) {
			$fontFileName = 't3lib/fonts/nimbus.ttf';
		}
		$ttfFontFileName = t3lib_div::getFileAbsFileName($fontFileName);
		
		if (!is_file($ttfFontFileName)) {
			$content .= $GLOBALS['LANG']->getLL('ttfFontFileNotFound') . ' '. $fontFileName;
		} elseif (!empty($pixelwidth)) {
			if ($charactersToIncludeInFont == 1) {
				$characters = 'a,b,c,d,e,f,g,h,i,j,k,l,m,n,o,p,q,r,s,t,u,v,w,x,y,z';
				$numchars = 26;
				$startCharacter = 'a';
			} else if ($charactersToIncludeInFont == 2) {
				$characters = 'a,b,c,d,e,f,g,h,i,j,k,l,m,n,o'
					.',p,q,r,s,t,u,v,w,x,y,z,-,-,-,-,-'
					.',-,-,-,-,-,-,-,-,-,-,-,-,-,-,-,-'
					.',-,-,-,-,-,-,-,-,-,-,-,-,-,-,-,-'
					.',-,-,-,-,-,-,-,-,-,-,-,-,-,-,-,-'
					.',-,-,-,-,-,-,-,-,-,-,-,-,-,-,-,-'
					.',-,-,-,-,-,-,-,-,-,-,-,-,-,-,-,-'
					.',-,-,-,-,-,-,-,-,-,-,-,-,-,-,-,�'
					.',�,�,�,�,�,�,�,�,�,�,�,�,�,�,�,�'
					.',�,�,�,�,�,�,�,-,�,�,�,�,�,�,�,�';
					$numchars = 159;
					$startCharacter = 'a';
			} else {
				$characters = '0,1,2,3,4,5,6,7,8,9';
				$numchars = 10;
				$startCharacter = '0';
			}
			
			$PNGImageFile = $this->makeFontImage($characters, $ttfFontFileName, $pixelwidth, $pixelheight);
			$content .= $GLOBALS['LANG']->getLL('usingFontFile') . ' ' . $fontFileName . $this->doc->spacer(5);
			$content .= $GLOBALS['LANG']->getLL('pngImageCreated') . ' ' . $PNGImageFile . $this->doc->spacer(5) . '<img src="' . $GLOBALS['BACK_PATH'] . '../' . $PNGImageFile . '" />' . $this->doc->spacer(20);
			
			if ($GLOBALS['TYPO3_CONF_VARS']['GFX']['gdlib_png']) {
				$image = @ImageCreateFromPNG(PATH_site.$PNGImageFile);
			} else {
				$image = @ImageCreateFromGIF(PATH_site.$PNGImageFile);
			}
			$fontdata = $this->makeFont($image, $numchars, $startCharacter, $pixelwidth, $pixelheight, $endianness);
			$gdfFontFileName = $this->buildFontFile($gdFontFileName,$fontdata);
			ImageDestroy($image);
			
			if ($gdfFontFileName) {
				$content .= $GLOBALS['LANG']->getLL('gdFontFileCreated') . ' ' . $gdfFontFileName;
			} else {
				$content .= $GLOBALS['LANG']->getLL('gdFontFileNotCreated') . ' ' . $gdfFontFileName;
			}
		}
		$content .= $this->doc->spacer(20);
		$content .= '
			<table cellspacing="5">
				<tr><td>' . $GLOBALS['LANG']->getLL('charactersToIncludeInFont') . '</td><td>
					<input id="numbers-only" type="radio" name="charactersToIncludeInFont" value="0" checked="checked" style="margin-right: 3px;" /><label for="numbers-only">' . $GLOBALS['LANG']->getLL('numbers-only') . '</label>
					<br /><input id="ASCII-lowercase-letters" type="radio" name="charactersToIncludeInFont" value="1" style="margin-right: 3px;" /><label for="ASCII-lowercase-letters">' . $GLOBALS['LANG']->getLL('ASCII-lowercase-letters') . '</label>
					<br /><input id="ANSI-extended-ASCII-lowercase-letters" type="radio" name="charactersToIncludeInFont" value="2" style="margin-right: 3px;" /><label for="ANSI-extended-ASCII-lowercase-letters">' . $GLOBALS['LANG']->getLL('ANSI-extended-ASCII-lowercase-letters') . '</label>
				</td></tr>
				<tr><td><label for="pixelwidth">' . $GLOBALS['LANG']->getLL('characterWidth') . '</label></td><td><input id="pixelwidth" type="text" name="pixelwidth" size="5" /></td></tr>
				<tr><td><label for="pixelheight">' . $GLOBALS['LANG']->getLL('characterHeight') . '</label></td><td><input id="pixelheight" type="text" name="pixelheight" size="5" /></td></tr>
				<tr><td>' . $GLOBALS['LANG']->getLL('endianness') . '</td><td>
					<input id="little-endian" type="radio" name="endianness" value="0" checked="checked" style="margin-right: 3px;" /><label for="little-endian">' . $GLOBALS['LANG']->getLL('littleEndian') . '</label>
					<br /><input id="big-endian" type="radio" name="endianness" value="1" style="margin-right: 3px;" /><label for="big-endian">' . $GLOBALS['LANG']->getLL('bigEndian') . '</label>
				</td></tr>
				<tr><td><label for="fontfilename">' . $GLOBALS['LANG']->getLL('pathToTTFFile') . '</td><td><input id="fontfilename" type="text" name="fontfilename" size="50" /></td></tr>
				<tr><td><label for="gdfontfilename">' . $GLOBALS['LANG']->getLL('gdFontFilePrefix') . '</td><td><input id="gdfontfilename" type="text" name="gdfontfilename" size="25"></td></tr>
				<tr><td colspan="2"><input type="submit" value="' . htmlspecialchars($GLOBALS['LANG']->getLL('makeFont')) . '" /></td></tr>
				</table>
			';
		return $content;
	}
	
	/**
	 * Return a file name built with the label and containing the specified contents
	 *
	 * @return string		filename
	 */
	 
	function buildFontFile($label,$contents,$ext='gdf') {
		$relFilename = 'uploads/' . $this->extPrefix . '/' . $label . '_' .  t3lib_div::shortMD5($contents) . '.' . $ext;
		$absFilename = PATH_site . $relFilename;
		if (t3lib_div::writeFile($absFilename,$contents))	{
			return $relFilename;
		} else {
			return FALSE;
		}
	}
	
	/**
	 * Outputs an image of a specified background color displaying a list of characters with specified font size, file and color
	 *
	 * @param	string		$text: text to display on he image
	 * @param	integer		$size: specified font size
	 * @param	string		$font: specified font file name
	 * @param	string		$color: specified font color
	 * @param	string		$bgColor: specified background color
	 * @param	string		$align: left or right alignment of text
	 * @param	string		$width: width of each character
	 * @param	string		$height: height of the image
	 * @return	array		image file info array
	 */
	function makeFontImage($characters, $font, $width = 35, $height = 50) {
		
		$size = intval($height*.8);
		$vertOffset = intval($height*.7);
		$color = '#000000';
		$bgColor = 'white';
		$align = 'left';
		
		$charactersArray = explode(',', $characters);
		$charactersCount = count($charactersArray);
		
		$gifObjArray = array();
		$gifObjArray['backColor'] = $bgColor;
		$gifObjArray['transparentBackground'] = 0;
		$gifObjArray['reduceColors'] = '';
		$gifObjArray['maxWidth'] = ($charactersCount*$width)+1;	
		$gifObjArray['XY'] = ($charactersCount*$width) .','.$height;
		
		for ($ic = 1; $ic < $charactersCount+1; $ic++) {
			$gifObjArray[$ic.'0'] = 'TEXT';
			$gifObjArray[$ic.'0.']['text'] = $charactersArray[$ic-1];
			
			$bbox = imagettfbbox($size, 0, $font, $charactersArray[$ic-1]);
			$hOffset = intval(($width - ($bbox[4] - $bbox[6]))/2);
			$vOffset = intval(($width - ($bbox[7] - $bbox[1]))/2);
			
			$gifObjArray[$ic.'0.']['niceText'] = 0;
			$gifObjArray[$ic.'0.']['antiAlias'] = 1;
			$gifObjArray[$ic.'0.']['align'] = $align;
			$gifObjArray[$ic.'0.']['fontSize'] = $size;
			$gifObjArray[$ic.'0.']['fontFile'] = $font;
			$gifObjArray[$ic.'0.']['fontColor'] = $color;
			$gifObjArray[$ic.'0.']['maxWidth'] = $width;
			$gifObjArray[$ic.'0.']['offset'] = (($ic-1)*$width+$hOffset).','.$vertOffset;
		}
		
		$gifCreator = t3lib_div::makeInstance('tx_srfreecap_gifbuilder');
		$theImage='';
		$gifCreator->init();
		if ($GLOBALS['TYPO3_CONF_VARS']['GFX']['gdlib'])	{
			$gifCreator->start($gifObjArray,array());
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
	
	function makeFont($image,$numchars,$startchar,$pixelwidth,$pixelheight,$endianness=0) {
		
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
		for ($c=0; $c<$numchars*$pixelwidth ; $c+=$pixelwidth)	{
			for ($y=0 ; $y<$pixelheight ; $y++)	{
				for($x=$c ; $x<$c+$pixelwidth ; $x++)	{
						// get colour of this pixel
					$rgb = ImageColorAt($image, $x, $y);

					if ($rgb==0)	{
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

class tx_srfreecap_gifbuilder extends tslib_gifbuilder {
	
	/**
	 * Returns the reference to a "resource" in TypoScript.
	 *
	 * @param	string		The resource value.
	 * @return	string		Returns the relative filepath
	 * @access private
	 * @see t3lib_TStemplate::getFileName()
	 */
	function checkFile($file)	{
		return $file;
	}
	
	/**
	 * Writes the input GDlib image pointer to file
	 *
	 * @param	pointer		The GDlib image resource pointer
	 * @param	string		The filename to write to
	 * @return	mixed		The output of either imageGif, imagePng or imageJpeg based on the filename to write
	 * @see maskImageOntoImage(), scale(), output()
	 */
	function ImageWrite($destImg, $theImage)	{
		return parent::ImageWrite($destImg, PATH_site.$theImage);
 	}
}

if (defined('TYPO3_MODE') && $GLOBALS['TYPO3_CONF_VARS'][TYPO3_MODE]['XCLASS']['ext/sr_freecap/mod1/class.tx_srfreecap_fontmaker.php']) {
	include_once($GLOBALS['TYPO3_CONF_VARS'][TYPO3_MODE]['XCLASS']['ext/sr_freecap/mod1/class.tx_srfreecap_fontmaker.php']);
}
?>
