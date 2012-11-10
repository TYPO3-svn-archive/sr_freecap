<?php
/***************************************************************
*  Copyright notice
*
*  (c) 2007-2012 Stanislas Rolland <typo3(arobas)sjbr.ca>
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
class tx_srfreecap_pi3 extends tslib_pibase {
	public $cObj;							// The backReference to the mother cObj object set at call time
	public $prefixId = 'tx_srfreecap_pi3';				// Same as class name
	public $scriptRelPath = 'pi3/class.tx_srfreecap_pi3.php';	// Path to this script relative to the extension dir.
	public $extKey = 'sr_freecap';					// The extension key.
	public $conf = array();
	private $voicesDir;
	
	function main($conf) {
		$this->conf = $conf;
			// Load session data
		$this->sessionData = $GLOBALS['TSFE']->fe_user->getKey('ses','tx_'.$this->extKey);
		$this->voicesDir = $this->getVoicesDir();
		$this->output($this->getWord());
		exit();
	}
	
	/**
	 * Gets the directory of wav files based on configured language
	 *
	 * @return	void
	 */
	function getVoicesDir() {
		$voicesDir = \TYPO3\CMS\Core\Utility\ExtensionManagementUtility::extPath('sr_freecap') . '/Resources/Private/Captcha/Voices/' . $GLOBALS['TSFE']->lang . '/';
		if (!is_dir($voicesDir)) {
			$voicesDir = \TYPO3\CMS\Core\Utility\ExtensionManagementUtility::extPath('sr_freecap') . '/Resources/Private/Captcha/Voices/default/';
		}
		return $voicesDir;
	}
	
	/**
	 * Gets the word that was stored in session data
	 *
	 * @return	string		the retrieved and decoded word
	 */
	function getWord() {
		$code = $GLOBALS['TYPO3_CONF_VARS']['SYS']['encryptionKey'];
		$dec_string = $this->easy_decrypt($this->sessionData[$this->extKey . '_word_accessible'], $code);
		return implode("-", str_split($dec_string));
	}
	
	/**
	 * Decodes a string
	 *
	 * @param	array		$cyph_arr: an array as returned by easy_encrypt() (see class.tx_srfreecap_pi1.php)
	 *
	 * @return	string		the decoded string
	 */
	function easy_decrypt($cyph_arr, $key){
		$key = md5($key);
		return trim(mcrypt_decrypt(MCRYPT_BLOWFISH, $key, base64_decode($cyph_arr[0]), MCRYPT_MODE_CBC, base64_decode($cyph_arr[1])));
	}

	/**
	 * Outputs the wav file
	 *
	 * @param	string		$word: the word to be spelled and played
	 *
	 * @return	void
	 */
	function output($word) {
		$output = $this->generate($word);
		header('Expires: Mon, 26 Jul 1997 05:00:00 GMT');
		header('Last-Modified: ' . gmdate('D,d M YH:i:s') . ' GMT');
		header('Pragma: no-cache');
		header('Cache-Control: no-cache, no-store, must-revalidate');
		header('Content-Type: audio/x-wav');
		header('Content-Length: ' . strlen($output));
		echo $output;
	}
	
	/**
	 * Builds an array of letter rendering wav files
	 *
	 * @param	string		$word: the word to be spelled and played
	 *
	 * @return	array		array of wav file names
	 */
	function generate($word) {
		$letters = str_split($word);
		$letterRenderingWaveFiles = array();
		
		foreach ($letters as $letter){
			$file = $this->voicesDir . $letter . '.wav';
			if (is_file($file)) {
				$letterRenderingWaveFiles[] = $file;
			}
		}
		return $this->joinWaveFiles($letterRenderingWaveFiles);
	}
	
	/**
	 * Join multiple wav files
	 *
	 * All wave files need to have the same format and need to be uncompressed.
	 * The headers of the last file will be used (with recalculated datasize
	 * of course)
	 *
	 * @link	http://ccrma.stanford.edu/CCRMA/Courses/422/projects/WaveFormat/
	 * @link	http://www.thescripts.com/forum/thread3770.html
	 * @license	GPL 2 (http://www.gnu.org/licenses/gpl.html)
	 * @author	Andreas Gohr <gohr@cosmocode.de>
	 *
	 * @param	array		$wavs: the array of wav files
	 *
	 * @return	string		the contents of joined wav file
	 */
	
	function joinWaveFiles($wavs) {
		$fields = join('/', array(
			'H8Format',
			'H8Subchunk1ID',
			'VSubchunk1Size',
			'vAudioFormat',
			'vNumChannels',
			'VSampleRate',
			'VByteRate',
			'vBlockAlign',
			'vBitsPerSample'
		));
		$data = '';
		foreach ($wavs as $wav){
			$fp = fopen($wav, 'rb');
				// Read ChunkID
			$headerPart1 = fread($fp, 4);
				// Read ChunkSize
			$headerPart2 = fread($fp, 4);
				// Read following fields
			$headerPart3 = fread($fp, 28);
			$info = unpack($fields, $headerPart3);
				// Read optional extra stuff
				// We will not use this since AudioFormat of all our sound files is PCM
			if ($info['Subchunk1Size'] > 16) {
				$headerPart3 .= fread($fp, ($info['Subchunk1Size']-16));
			}
				// Read SubChunk2ID
			$headerPart3 .= fread($fp, 4);
				// Read Subchunk2Size
			$size = unpack('vsize', fread($fp, 4));
			$size = $size['size'];
				// Read data
			$data .= fread($fp, $size);
		}
		return $headerPart1 . pack('V', 36 + strlen($data)) . $headerPart3 . pack('V', strlen($data)) . $data;
	}
}
?>