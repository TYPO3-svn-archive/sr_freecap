<?php
namespace SJBR\SrFreecap\Domain\Session;
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
 * Session storage
 *
 * @author Stanislas Rolland <typo3@sjbr.ca>
 */
class SessionStorage implements \TYPO3\CMS\Core\SingletonInterface {
	
	const SESSIONNAMESPACE = 'tx_srfreecap';
 
	/**
	 * Returns the object stored in the user's PHP session
	 *
	 * @return Object the stored object
	 */
	public function restoreFromSession () {
		$sessionData = $this->getFrontendUser()->getKey('ses', self::SESSIONNAMESPACE);
		return unserialize($sessionData);
	}
 
	/**
	 * Writes an object into the PHP session
	 *
	 * @param $object any serializable object to store into the session
	 * @return \SJBR\SrFreecap\Domain\Session\SessionStorage
	 */
	public function writeToSession ($object) {
		$sessionData = serialize($object);
		$this->getFrontendUser()->setKey('ses', self::SESSIONNAMESPACE, $sessionData);
		$this->getFrontendUser()->storeSessionData();
		return $this;
	}
 
	/**
	 * Cleans up the session: removes the stored object from the PHP session
	 *
	 * @return \SJBR\SrFreecap\Domain\Session\SessionStorage
	 */
	public function cleanUpSession () {
		$this->getFrontendUser()->setKey('ses', self::SESSIONNAMESPACE, NULL);
		$this->getFrontendUser()->storeSessionData();
		return $this;
	}

	/**
	 * Gets a frontend user from TSFE->fe_user
	 *
	 * @return	\TYPO3\CMS\Frontend\Authentication\FrontendUserAuthtenication	The current frontend user object
	 * @throws	LogicException
	 */
	protected function getFrontendUser () {
		if ($GLOBALS ['TSFE']->fe_user) {
			return $GLOBALS ['TSFE']->fe_user;
		}
		throw new LogicException ('No Frontentuser found in session!');
	}
}
?>