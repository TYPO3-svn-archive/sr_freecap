<?php
namespace SJBR\SrFreecap\Controller;
/***************************************************************
 *  Copyright notice
 *
 *  (c) 2012-2013 Stanislas Rolland <typo3@sjbr.ca>
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
 * Font Maker controller
 *
 * @author Stanislas Rolland <typo3@sjbr.ca>
 */
class FontMakerController  extends \TYPO3\CMS\Extbase\Mvc\Controller\ActionController {

	/**
	 * @var string Name of the extension this controller belongs to
	 */
	protected $extensionName = 'SrFreecap';

	/**
	 * Display the font maker form
	 *
	 * @param \SJBR\SrFreecap\Domain\Model\Font $font
	 * @return string An HTML form for creating a new font
	 */
	public function newAction(\SJBR\SrFreecap\Domain\Model\Font $font = NULL) {
		if (!is_object($font)) {
			$font = $this->objectManager->create('SJBR\\SrFreecap\\Domain\\Model\\Font');
		}
		$this->view->assign('font', $font);
	}	

	/**
	 * Create the font file and display the result
	 *
	 * @param \SJBR\SrFreecap\Domain\Model\Font $font
	 * @return string HTML presenting the new font that was created
	 */
	public function createAction(\SJBR\SrFreecap\Domain\Model\Font $font) {
		// Create the font data
		$font->createGdFontFile();
		// Store the GD font file
		$fontRepository = $this->objectManager->get('SJBR\\SrFreecap\\Domain\\Repository\\FontRepository');
		$fontRepository->writeFontFile($font);
		$this->view->assign('font', $font);
	}
}
class_alias('SJBR\SrFreecap\Controller\FontMakerController', 'Tx_SrFreecap_Controller_FontMakerController');
?>