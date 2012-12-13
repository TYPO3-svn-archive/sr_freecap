<?php
namespace SJBR\SrFreecap\Domain\Repository;
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
 * Font repository
 *
 * @author Stanislas Rolland <typo3@sjbr.ca>
 */
class FontRepository extends \TYPO3\CMS\Extbase\Persistence\Repository {

	/**
	 * @var string Name of the extension this controller belongs to
	 */
	protected $extensionKey = 'sr_freecap';

	/**
	 * Writes the GD font file
	 *
	 * @param \SJBR\SrFreecap\Domain\Model\Font the object to be stored
	 * @return \SJBR\SrFreecap\Domain\Repository\FontRepository $this
	 */
	 public function writeFontFile(\SJBR\SrFreecap\Domain\Model\Font $font) {
	 	$relativeFileName = 'uploads/' . \TYPO3\CMS\Core\Utility\ExtensionManagementUtility::getCN($this->extensionKey) . '/' . $font->getGdFontFilePrefix() . '_' .  \TYPO3\CMS\Core\Utility\GeneralUtility::shortMD5($font->getGdFontData()) . '.gdf';
		if (\TYPO3\CMS\Core\Utility\GeneralUtility::writeFile(PATH_site . $relativeFileName, $font->getGdFontData())) {
			$font->setGdFontFileName($relativeFileName);
		}
		return $this;
	}
}
?>