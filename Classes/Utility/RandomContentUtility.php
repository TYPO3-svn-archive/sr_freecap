<?php
namespace SJBR\SrFreecap\Utility;
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
 * Random content utility
 *
 * @author Stanislas Rolland <typo3(arobas)sjbr.ca>
 */
class RandomContentUtility {

	/**
	 * Returns a random number within the given range
	 *
	 * @param int $min: the lower boundary of the range
	 * @param int $max: the upper boundary of the range
	 * @return int the generated number
	 */
	public static function getRandomNumberInRange ($min, $max) {
		if ($min > $max) {
			$newMin = $max;
			$newMax = $min;
		} else {
			$newMin = $min;
			$newMax = $max;
		}
		return mt_rand($newMin, $newMax);
	}

	/**
	 * Returns a random color in a the form of an array of 3 numbers
	 *
	 * @param int $colorMaximumDarkness: maximum darkness along any dimension
	 * @param int $colorMaximumLightness: maximum lightness along any dimension
	 * @param boolean $darker: if TRUE, produce a possibly darker image by default
	 *
	 * @return array the random color
	 */
	public static function getRandomColor ($colorMaximumDarkness, $colorMaximumLightness, $darker = FALSE) {
		$color = array();
		if ($darker) {
			// Needs darker colour..
			$minimum = isset($colorMaximumDarkness) ? $colorMaximumDarkness : 10;
			$maximum = isset($colorMaximumLightness) ? $colorMaximumLightness : 100;
		} else {
			$minimum = isset($colorMaximumDarkness) ? $colorMaximumDarkness : 30;
			$maximum = isset($colorMaximumLightness) ? $colorMaximumLightness : 140;
		}
		for ($i = 0; $i < 3 ; $i++) {
			$color[] = self::getRandomNumberInRange($minimum, $maximum);
		}
		return $color;
	}

	/**
	 * Returns a random word
	 *
	 * @param boolean $useWordsList: if TRUE, a word dictionary is used
	 * @param string $wordsList: dictionary file location
	 * @param boolean $numbersOnly: if TRUE, use only numbers
	 *
	 * @return string random word
	 */
	public static function getRandomWord($useWordsList = FALSE, $wordsList = '', $numbersOnly = FALSE, $maxWordLength = 6) {
		if ($useWordsList && is_file($wordsList)) {
			// Load dictionary and choose random word
			// Keep dictionary in non-web accessible folder, or htaccess it
			// or modify so word comes from a database; SELECT word FROM words ORDER BY rand() LIMIT 1
			$words = @file($wordsList);
			$word = strtolower($words[self::getRandomNumberInRange(0, sizeof($words)-1)]);
			// Cut off line breaks
			$word = preg_replace('/['.preg_quote(chr(10).chr(13)).']+/', '', $word);
			unset($words);
		} else {
			// Based on code originally by breakzero at hotmail dot com
			// (http://uk.php.net/manual/en/function.rand.php)
			// generate pseudo-random string
			// doesn't use ijtf as are easily mistaken
				
			// I'm not using numbers because the custom fonts I've created don't support anything other than
			// lowercase or space (but you can download new fonts or create your own using my GD fontmaker script)
			if ($numbersOnly) {
				$consonants = '123456789';
				$vowels = '123456789';
			} else {
				$consonants = 'bcdghklmnpqrsvwxyz';
				$vowels = 'aeuo';
			}
			$word = '';
			$wordlen = self::getRandomNumberInRange(5, $maxWordLength);
			for ($i = 0; $i < $wordlen; $i++) {
				// Don't allow to start with 'vowel'
				if (self::getRandomNumberInRange(0, 20) >= 10 && $i != 0) {
					$word .= $vowels{self::getRandomNumberInRange(0, strlen($vowels)-1)};
				} else {
					$word .= $consonants{self::getRandomNumberInRange(0, strlen($consonants)-1)};
				}
			}
		}
		return $word;
	}
}
?>