<?php
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
 * Word object
 *
 * @author Stanislas Rolland <typo3@sjbr.ca>
 */
class Word extends \TYPO3\CMS\Extbase\DomainObject\AbstractEntity {

	/**
	 * @var string
	 */
	protected $wordHash;

	/**
	 * @var string
	 */
	protected $hashFunction;

	/**
	 * @var array
	 */
	protected $wordCypher;

	/**
	 * @var int
	 */
	protected $attempts;

	public function __construct(
			$wordHash = '',
			$hashFunction = 'md5',
			$wordCypher = array(),
			$attempts = 0
		) {
		$this->setWordHash($wordHash);
		$this->setHashFunction($hashFunction);
		$this->setWordCypher($wordCypher);
		$this->setAttempts($attempts);
	}

	public function setWordHash($wordHash) {
		$this->wordHash = (string)$wordHash;
	}

	public function getWordHash() {
		return $this->wordHash;
	}
                                                                                                    
	public function setHashFunction($hashFunction) {
		$this->hashFunction = (string)$hashFunction;
	}
                                                                                                                   
	public function getHashFunction() {
		return $this->hashFunction;
	}

	public function setWordCypher($wordCypher) {                                      
		$this->wordCypher = (array)$wordCypher;
	}

	public function getWordCypher() {                                                   
		return $this->wordCypher;
	}

	public function setAttempts($attempts) {
		$this->attempts = (int)$attempts;
	}

	public function getAttempts() {
		return $this->attempts;
	}
}
?>