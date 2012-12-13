<?php
namespace SJBR\SrFreecap\Validation\Validator;
/***************************************************************
*  Copyright notice
*
*  (c) 2012 Stanislas Rolland <typo3@sjbr.ca>
*  All rights reserved
*
*  This class is a backport of the corresponding class of FLOW3. 
*  All credits go to the v5 team.
*
*  This script is part of the TYPO3 project. The TYPO3 project is
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
 * Validator for True Type Font file existence
 *
 */
class TtfFileValidator extends \TYPO3\CMS\Extbase\Validation\Validator\AbstractValidator {

	/**
	 * Returns TRUE, if the given property ($propertyValue) is a valid number in the given range.
	 *
	 * If at least one error occurred, the result is FALSE.
	 *
	 * @param mixed $value The value that should be validated
	 * @return boolean TRUE if the value is within the range, otherwise FALSE
	 */
	public function isValid ($value) {
		$isValid = TRUE;
		$this->errors = array();

		$absoluteFileName = \TYPO3\CMS\Core\Utility\GeneralUtility::getFileAbsFileName($value);

		// Check file existence
		if (!is_file($absoluteFileName)) {
			$this->addError('A file with the given name could not be found.', 9221561046);
			$isValid = FALSE;
		} else {
			// Check file extension
			$pathInfo = pathinfo($absoluteFileName);
			if (strtolower($pathInfo['extension']) != 'ttf') {
				$this->addError('The specified file is not a True Type Font file.', 9221561047);
				$isValid = FALSE;				
			}
		}
		return $isValid;
	}
}
?>