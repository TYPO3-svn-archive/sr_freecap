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
 * Utility dealing with audio content
 *
 * @author	Stanislas Rolland	<typo3(arobas)sjbr.ca>
 */
class AudioContentUtility {

	/**
	 * Joins multiple audio files
	 *
	 * @param array $files: the array of audio files
	 * @param string $format: the audio format	 
	 *
	 * @return string the contents of joined audio file
	 */
	public static function joinAudioFiles($files, $format = 'wav') {
		switch ($format) {
			case 'mp3':
				return self::joinMp3Files($files);
				break;
			case 'wav':
			default:
				return self::joinWavFiles($files);
				break;
		}
	}

	/**
	 * Joins multiple wav files
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
	protected static function joinWavFiles ($wavs) {
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
			$size = unpack('VSubChunk2Size', fread($fp, 4));
			$size = $size['SubChunk2Size'];
				// Read data
			$data .= fread($fp, $size);
			fclose($fp);
		}
		return $headerPart1 . pack('V', 36 + strlen($data)) . $headerPart3 . pack('V', strlen($data)) . $data;
	}

	/**
	 * Joins multiple wav files
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
	protected static function joinMp3Files ($files) {
		$data = '';
		foreach ($files as $file) {
			$mp3 = new Mp3ContentUtility($file);
			$data .= $mp3->striptags();
		}
		return $data;
	}

}
?>