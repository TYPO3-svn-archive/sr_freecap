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
 * Encryption utility
 *
 * @author	Stanislas Rolland	<typo3(arobas)sjbr.ca>
 */
class EncryptionUtility {

	/**
	 * Encrypts a string
	 *
	 * @param array $string: the string to be encrypted
	 *
	 * @return array an array with the string as the first element and the initialization vector as the second element
	 */
	public static function encrypt($string) {
		if (in_array('mcrypt', get_loaded_extensions())) {
			$module = mcrypt_module_open($GLOBALS['TYPO3_CONF_VARS']['EXTCONF']['sr_freecap']['encryptionAlgorithm'], '', MCRYPT_MODE_CBC, '');
			if ($module !== FALSE) {
				$key = md5($GLOBALS['TYPO3_CONF_VARS']['SYS']['encryptionKey']);
				$iv = mcrypt_create_iv(mcrypt_enc_get_iv_size($module), MCRYPT_RAND);
				$string = mcrypt_encrypt($GLOBALS['TYPO3_CONF_VARS']['EXTCONF']['sr_freecap']['encryptionAlgorithm'], $key, $string, MCRYPT_MODE_CBC, $iv);
				$cypher = array(base64_encode($string), base64_encode($iv));
				mcrypt_module_close($module);
			} else {
				$cypher = array(base64_encode($string));
			}
		} else {
			$cypher = array(base64_encode($string));			
		}
		return $cypher;
	}

	/**
	 * Decrypts a string
	 *
	 * @param array $cypher: an array as returned by encrypt()
	 *
	 * @return string the decrypted string
	 */
	public static function decrypt ($cypher){
		if (in_array('mcrypt', get_loaded_extensions())) {
			$key = md5($GLOBALS['TYPO3_CONF_VARS']['SYS']['encryptionKey']);
			$string = trim(mcrypt_decrypt($GLOBALS['TYPO3_CONF_VARS']['EXTCONF']['sr_freecap']['encryptionAlgorithm'], $key, base64_decode($cypher[0]), MCRYPT_MODE_CBC, base64_decode($cypher[1])));
		} else {
			$string = base64_decode($cypher[0]);
		}
		return $string;
	}
}
?>