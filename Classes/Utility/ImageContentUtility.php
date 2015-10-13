<?php
namespace SJBR\SrFreecap\Utility;

/*
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
 *  This script is distributed in the hope that it will be useful,
 *  but WITHOUT ANY WARRANTY; without even the implied warranty of
 *  MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 *  GNU General Public License for more details.
 *
 *  This copyright notice MUST APPEAR in all copies of the script!
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

use SJBR\SrFreecap\Utility\RandomContentUtility;
use TYPO3\CMS\Core\Utility\GeneralUtility;

/**
 * Utility dealing with image content
 */
class ImageContentUtility {
	
	const BACKGROUND_TYPE_TRANSPARENT = 'Transparent';
	const BACKGROUND_TYPE_WHITE_WITH_GRID = 'White with grid';
	const BACKGROUND_TYPE_WHITE_WITH_SQUIGGLES = 'White with squiggles';
	const BACKGROUND_TYPE_MORPHED_IMAGE_BLOCKS = 'Morphed image blocks';
	const MERGE_BACKGROUND_OVER_CAPTCHA = 0;
	const MERGE_CAPTCHA_OVER_BACKGROUND = 1;
	const TEXT_COLOR_UNIFORM = 0;
	const TEXT_COLOR_ONE_PER_CHARACTER = 1;
	const SITE_TAG_POSITION_TOP = 0;
	const SITE_TAG_POSITION_BOTTOM = 1;
	const SITE_TAG_POSITION_BOTH = 2;

	/**
	 * Write word on image
	 *
	 * @param int $width: width of the image in pixels
	 * @param int $height: width of the image in pixels
	 * @param string $word: the captcha word
	 * @param int $textColorType (see constants)
	 * @param array $textPosition: 'horizontal' => horizontal starting position of text, 'vertical' => vertical starting position of text
	 * @param int $colorMaximum: 'darkness' => maximum color darkness along any dimension, 'lightness' => maximum color lightness along any dimension 
	 * @param string $backgroundType (see constants)
	 * @param array $fontLocations: array of font files locations
	 * @param array $fontWidths: array of font widths
	 * @param int $morphfactor: text morphing factor
	 * @return string GD image identifier of noisy background
	 */
	public static function writeWordOnImage($width, $height, $word, $textColorType, $textPosition, $colorMaximum, $backgroundType, $fontLocations, $fontWidths, $morphFactor = 1)
	{

		$image = ImageCreate($width, $height);
		$image2 = ImageCreate($width, $height);
		// Set background colour (can change to any colour not in possible $text_col range)
		// it doesn't matter as it'll be transparent or coloured over.
		// if you're using background type 'Morphed image blocks', you might want to try to ensure that the color chosen
		// below doesn't appear too much in any of your background images.
		$background = ImageColorAllocate($image, 254, 254, 254);
		$background2 = ImageColorAllocate($image2, 254, 254, 254);
		// Set transparencies
		ImageColorTransparent($image, $background);
		// $image2 transparent to allow characters to overlap slightly while morphing
		ImageColorTransparent($image2, $background2);
		// Fill backgrounds
		ImageFill($image, 0, 0, $background);
		ImageFill($image2, 0, 0, $background2);

		// Write word in random starting X position
		$word_start_x = RandomContentUtility::getRandomNumberInRange(5, $textPosition['horizontal']);
		// Y positions jiggled about later
		$word_start_y = $textPosition['vertical'];
		// Get uniform color
		if ($textColorType == self::TEXT_COLOR_UNIFORM) {
			$textColor = RandomContentUtility::getRandomColor($colorMaximum['darkness'], $colorMaximum['lightness'], $backgroundType == self::BACKGROUND_TYPE_MORPHED_IMAGE_BLOCKS);
			$textColor2 = ImageColorAllocate($image2, $textColor[0], $textColor[1], $textColor[2]);
		}
		// Write each character in different font
		$textFontWidths = array();
		$x_pos = $word_start_x;
		for ($i = 0; $i < strlen($word); $i++) {
			// Get changing color
			if ($textColorType == self::TEXT_COLOR_ONE_PER_CHARACTER) {
				$textColor = RandomContentUtility::getRandomColor($colorMaximum['darkness'], $colorMaximum['lightness'], $backgroundType == self::BACKGROUND_TYPE_MORPHED_IMAGE_BLOCKS);
				$textColor2 = ImageColorAllocate($image2, $textColor[0], $textColor[1], $textColor[2]);
			}
			$fontIndex = RandomContentUtility::getRandomNumberInRange(0, sizeof($fontLocations)-1);
			$font = ImageLoadFont($fontLocations[$fontIndex]);
			ImageString($image2, $font, $x_pos, $word_start_y, $word{$i}, $textColor2);
			$textFontWidths[$i] = $fontWidths[$fontIndex];
			$x_pos += $textFontWidths[$i];
		}

		// Morph Image
		// Firstly move each character up or down a bit:
		$x_pos = $word_start_x;
		$y_pos = 0;
		for ($i = 0; $i < strlen($word); $i++) {	
			// Move on Y axis
			// Deviate at least 4 pixels between each letter
			$prev_y = $y_pos;
			do {
				$y_pos = RandomContentUtility::getRandomNumberInRange(-5, 5);
			} while ($y_pos < $prev_y + 2 && $y_pos > $prev_y - 2);
			ImageCopy($image, $image2, $x_pos, $y_pos, $x_pos, 0, $textFontWidths[$i], $height);
			$x_pos += $textFontWidths[$i];
		}
		ImageFilledRectangle($image2, 0, 0, $width, $height, $background2);

		// Randomly morph each character individually on x-axis
		// This is where the main distortion happens
		$y_chunk = 1;
		$morph_x = 0;
		$orig_x = $word_start_x - $textFontWidths[0];
		for ($j = 0; $j < strlen($word); $j++) {
			$orig_x += $textFontWidths[$j];
			$y_pos = 0;
			for ($i = 0; $i <= $height; $i += $y_chunk) {
				// morph x += so that instead of deviating from orig x each time, we deviate from where we last deviated to
				// get it? instead of a zig zag, we get more of a sine wave.
				// I wish we could deviate more but it looks crap if we do.
				$morph_x += RandomContentUtility::getRandomNumberInRange(-$morphFactor, $morphFactor);
				// had to change this to ImageCopyMerge when starting using ImageCreateTrueColor
				// according to the manual; "when (pct is) 100 this function behaves identically to imagecopy()"
				// but this is NOT true when dealing with transparencies...
				ImageCopyMerge($image2, $image, $orig_x + $morph_x, $i + $y_pos, $orig_x, $i, $textFontWidths[$j], $y_chunk, 100);
			}
		}
		ImageFilledRectangle($image, 0, 0, $width, $height, $background);

		// Now do the same on the y-axis
		// (much easier because we can just do it across the whole image, don't have to do it char-by-char)
		$y_pos = 0;
		$x_chunk = 1;
		for ($i = 0; $i <= $width ; $i += $x_chunk) {
			// Can result in image going too far off on Y-axis;
			// not much I can do about that, apart from make image bigger
			// again, I wish I could do 1.5 pixels
			$y_pos += RandomContentUtility::getRandomNumberInRange(-1, 1);
			ImageCopy($image, $image2, $i, $y_pos, $i, 0, $x_chunk, $height);
		}

		// Cleanup
		ImageDestroy($image2);

		return $image;
	}

	/**
	 * Generate noisy background
	 *
	 * @param int $width: width of the image in pixels
	 * @param int $height: width of the image in pixels
	 * @param string $word: the captcha word
	 * @param string $backgroundType (see constants)
	 * @param array $backgroundImages: array of background image file names
	 * @param boolean $morphBackground: if TRUE, the background will be morphed
	 * @param boolean $blurBackground: if TRUE, the background will be blurred
	 * @return string GD image identifier of noisy background
	 */
	public static function generateNoisyBackground($width, $height, $word, $backgroundType, $backgroundImages = array(), $morphBackground = TRUE, $blurBackground = TRUE)
	{
		$image = ImageCreateTrueColor($width, $height);
		if ($backgroundType != self::BACKGROUND_TYPE_TRANSPARENT) {

			// Generate noisy background, to be merged with CAPTCHA later
			// any suggestions on how best to do this much appreciated
			// sample code would be even better!
			// I'm not an OCR expert (hell, I'm not even an image expert; puremango.co.uk was designed in MsPaint)
			// so the noise models are based around my -guesswork- as to what would make it hard for an OCR prog
			// ideally, the character obfuscation would be strong enough not to need additional background noise
			// in any case, I hope at least one of the options given here provide some extra security!
			$tempBackground = ImageCreateTrueColor($width*1.5, $height*1.5);
			$background = ImageColorAllocate($image, 255, 255, 255);
			ImageFill($image, 0, 0, $background);
			$tempBackgroundColor = ImageColorAllocate($tempBackground, 255, 255, 255);
			ImageFill($tempBackground, 0, 0, $tempBackgroundColor);
			
			// We draw all noise onto tempBackground
			// then if we're morphing, merge from tempBackground to image
			// or if not, just copy a $width x $height portion of $tempBackground to $image
			// tempBackground is much larger so that when morphing, the edges retain the noise.
			switch ($backgroundType) {
				case self::BACKGROUND_TYPE_WHITE_WITH_GRID:
					// Draw grid on x
					for ($i = RandomContentUtility::getRandomNumberInRange(6, 20); $i < $width*2; $i += RandomContentUtility::getRandomNumberInRange(10, 25)) {
						ImageSetThickness($tempBackground, RandomContentUtility::getRandomNumberInRange(2, 6));
						$textColor = RandomContentUtility::getRandomColor(100, 150);
						$textColor2 = ImageColorAllocate($tempBackground, $textColor[0], $textColor[1], $textColor[2]);
						ImageLine($tempBackground, $i, 0, $i, $height*2, $textColor2);
					}
					// Draw grid on y
					for ($i = RandomContentUtility::getRandomNumberInRange(6, 20); $i < $height*2 ; $i += RandomContentUtility::getRandomNumberInRange(10, 25)) {
						ImageSetThickness($tempBackground, RandomContentUtility::getRandomNumberInRange(2, 6));
						$textColor = RandomContentUtility::getRandomColor(100, 150);
						$textColor2 = ImageColorAllocate($tempBackground, $textColor[0], $textColor[1], $textColor[2]);
						ImageLine($tempBackground, 0, $i, $width*2, $i , $textColor2);
					}
					break;
				case self::BACKGROUND_TYPE_WHITE_WITH_SQUIGGLES:
					ImageSetThickness($tempBackground, 4);
					for ($i = 0; $i < strlen($word)+1; $i++) {
						$textColor = RandomContentUtility::getRandomColor(100, 150);
						$textColor2 = ImageColorAllocate($tempBackground, $textColor[0], $textColor[1], $textColor[2]);
						$points = Array();
						// Draw random squiggle for each character
						// the longer the loop, the more complex the squiggle
						// keep random so OCR can't say "if found shape has 10 points, ignore it"
						// each squiggle will, however, be a closed shape, so OCR could try to find
						// line terminations and start from there. (I don't think they're that advanced yet..)
						for ($j = 1; $j < RandomContentUtility::getRandomNumberInRange(5, 10); $j++) {
							$points[] = RandomContentUtility::getRandomNumberInRange(1*(20*($i+1)), 1*(50*($i+1)));
							$points[] = RandomContentUtility::getRandomNumberInRange(30, $height+30);
						}
						ImagePolygon($tempBackground, $points, intval(sizeof($points)/2), $textColor2);
					}
					break;
				case self::BACKGROUND_TYPE_MORPHED_IMAGE_BLOCKS:
					// Take random chunks of $backgroundImages and paste them onto the background
					for ($i = 0; $i < sizeof($backgroundImages); $i++) {
						// Read each image and its size
						$tempImages[$i] = ImageCreateFromJPEG(GeneralUtility::getFileAbsFileName($backgroundImages[$i]));
						$tempWidths[$i] = imagesx($tempImages[$i]);
						$tempHeights[$i] = imagesy($tempImages[$i]);
					}
					$blocksize = RandomContentUtility::getRandomNumberInRange(20, 60);
					for ($i = 0; $i < $width*2; $i += $blocksize) {
						// Could randomise blocksize here... hardly matters
						for ($j = 0; $j < $height*2; $j += $blocksize) {
							$imageIndex = RandomContentUtility::getRandomNumberInRange(0, sizeof($tempImages)-1);
							$cut_x = RandomContentUtility::getRandomNumberInRange(0, $tempWidths[$imageIndex]-$blocksize);
							$cut_y = RandomContentUtility::getRandomNumberInRange(0, $tempHeights[$imageIndex]-$blocksize);
							ImageCopy($tempBackground, $tempImages[$imageIndex], $i, $j, $cut_x, $cut_y, $blocksize, $blocksize);
						}
					}
					// Cleanup
					for ($i = 0; $i < sizeof($tempImages); $i++) {
						ImageDestroy($tempImages[$i]);
					}
					break;
			}

			if ($morphBackground) {
				// Morph background
				// We do this separately to the main text morph because:
				// a) the main text morph is done char-by-char, this is done across whole image
				// b) if an attacker could un-morph the background, it would un-morph the CAPTCHA
				// hence background is morphed differently to text
				// why do we morph it at all? it might make it harder for an attacker to remove the background
				// morph_chunk 1 looks better but takes longer
				// this is a different and less perfect morph than the one we do on the CAPTCHA
				// occasonally you get some dark background showing through around the edges
				// it doesn't need to be perfect as it's only the background.
				$morph_chunk = RandomContentUtility::getRandomNumberInRange(1, 5);
				$morph_y = 0;
				for ($x = 0; $x < $width; $x += $morph_chunk) {
					$morph_chunk = RandomContentUtility::getRandomNumberInRange(1, 5);
					$morph_y += RandomContentUtility::getRandomNumberInRange(-1, 1);
					ImageCopy($image, $tempBackground, $x, 0, $x+30, 30+$morph_y, $morph_chunk, $height*2);
				}
				
				ImageCopy($tempBackground, $image, 0, 0, 0, 0, $width, $height);
				
				$morph_x = 0;
				for ($y = 0; $y <= $height; $y += $morph_chunk) {
					$morph_chunk = RandomContentUtility::getRandomNumberInRange(1, 5);
					$morph_x += RandomContentUtility::getRandomNumberInRange(-1, 1);
					ImageCopy($image, $tempBackground, $morph_x, $y, 0, $y, $width, $morph_chunk);
				
				}
			} else {
				// Just copy tempBackground onto $image
				ImageCopy($image, $tempBackground, 0, 0, 30, 30, $width, $height);
			}
			// Cleanup
			ImageDestroy($tempBackground);
			
			if ($blurBackground) {
				$image = self::blurImage($image);
			}
		}
		return $image;
	}
	
	/**
	 * Merge captcha image with background
	 *
	 * @param int $width: width of the image in pixels
	 * @param int $height: width of the image in pixels
	 * @param string $captchaImage: a GD image identifier
	 * @param string $backgroundImage: a GD image identifier
	 * @param string $backgroundType (see constants)
	 * @param int $mergeType: 0 - Background over captcha, 1 - Captcha over background (see constants)
	 * @param int $backgroundFadePercentage: fading factor for background	 
	 * @return string GD image identifier of merged image
	 */
	public static function mergeCaptchaWithBackground($width, $height, $captchaImage, $backgroundImage, $backgroundType, $mergeType)
	{
		if ($backgroundType != self::BACKGROUND_TYPE_TRANSPARENT) {
			// How faded should the background be? (100=totally gone, 0=bright as the day)
			// to test how much protection the bg noise gives, take a screenshot of the freeCap image
			// and take it into a photo editor. play with contrast and brightness.
			// If you can remove most of the background, then it's not a good enough percentage
			switch ($backgroundType) {
				case self::BACKGROUND_TYPE_WHITE_WITH_GRID:
				case self::BACKGROUND_TYPE_WHITE_WITH_SQUIGGLES:
					$backgroundFadePercentage = 65;
					break;
				case self::BACKGROUND_TYPE_MORPHED_IMAGE_BLOCKS:
					$backgroundFadePercentage = 50;
					break;
			}
			// Slightly randomize the background fade
			$backgroundFadePercentage += RandomContentUtility::getRandomNumberInRange(-2, 2);
			// Fade background
			if ($backgroundType != self::BACKGROUND_TYPE_MORPHED_IMAGE_BLOCKS) {
				$tempImage = ImageCreateTrueColor($width, $height);
				$white = ImageColorAllocate($tempImage, 255, 255, 255);
				ImageFill($tempImage, 0, 0, $white);
				ImageCopyMerge($backgroundImage, $tempImage, 0, 0, 0, 0, $width, $height, $backgroundFadePercentage);
				ImageDestroy($tempImage);
				$colorFadePercentage = 50;
			} else {
				$colorFadePercentage = $backgroundFadePercentage;
			}
			// Merge background image with CAPTCHA image to create smooth background
			if ($mergeType == self::MERGE_CAPTCHA_OVER_BACKGROUND) {
				// Might want to not blur if using this method, otherwise leaves white-ish border around each letter
				ImageCopyMerge($backgroundImage, $captchaImage, 0, 0, 0, 0, $width, $height, 100);
				ImageCopy($captchaImage, $backgroundImage, 0, 0, 0, 0, $width, $height);
			} else {
				// Background over captcha
				ImageCopyMerge($captchaImage, $backgroundImage, 0, 0, 0, 0, $width, $height, $colorFadePercentage);
			}
		}
		return $captchaImage;	
	}

	/**
	 * Blurs an image
	 *
	 * @param string $image: a GD image identifier
	 * @return string GD image identifier of blurred image
	 */
	public static function blurImage($image)
	{
		// w00t. my very own blur function
		// in GD2, there's a gaussian blur function. bunch of bloody show-offs... :-)

		$width = imagesx($image);
		$height = imagesy($image);

		$temp_im = ImageCreateTrueColor($width, $height);
		$bg = ImageColorAllocate($temp_im, 150, 150, 150);

		// preserves transparency if in orig image
		ImageColorTransparent($temp_im, $bg);

		// fill bg
		ImageFill($temp_im, 0, 0, $bg);

		// anything higher than 3 makes it totally unreadable
		// might be useful in a 'real' blur function, though (ie blurring pictures not text)
		$distance = 1;
		// use $distance=30 to have multiple copies of the word. not sure if this is useful.

		// blur by merging with itself at different x/y offsets:
		ImageCopyMerge($temp_im, $image, 0, 0, 0, $distance, $width, $height-$distance, 70);
		ImageCopyMerge($image, $temp_im, 0, 0, $distance, 0, $width-$distance, $height, 70);
		ImageCopyMerge($temp_im, $image, 0, $distance, 0, 0, $width, $height, 70);
		ImageCopyMerge($image, $temp_im, $distance, 0, 0, 0, $width, $height, 70);
		// remove temp image
		ImageDestroy($temp_im);

		return $image;
	}

	/**
	 * Outputs the image with appropriate headers
	 *
	 * @param string $image: a GD image identifier
	 * @param string $imageType: type of image (jpg, gif or png)
	 * @return	void
	 */
	public static function sendImage($image, $imageType)
	{
		header('Expires: Mon, 26 Jul 1997 05:00:00 GMT');
		header('Last-Modified: ' . gmdate('D, d M Y H:i:s') . ' GMT');
		header('Pragma: no-cache');
		header('Cache-Control: no-cache, no-store, must-revalidate');
		switch ($imageType) {
			case 'jpg':
				header('Content-Type: image/jpeg');
				ImageJPEG($image);
				break;
			case 'gif':
				header('Content-Type: image/gif');
				ImageGIF($image);
				break;
			case 'png':
			default:
				header('Content-Type: image/png');
				ImagePNG($image);
				break;
		}
	}
}