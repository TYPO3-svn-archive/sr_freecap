<?php
namespace SJBR\SrFreecap\Configuration;
/***************************************************************
*  Copyright notice
*
*  (c) 2013 Stanislas Rolland <typo3(arobas)sjbr.ca>
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
 * Class providing configuration help for extension SrFreecap
 */
class ConfigurationHelper {

	/**
	 * Renders a select element that allows to choose the encryption algoritm to be used by the extension
	 *
	 * @param array $params: Field information to be rendered
	 * @param \TYPO3\CMS\Extensionmanager\ViewHelpers\Form\TypoScriptConstantsViewHelper $pObj: The calling parent object.
	 * @return string The HTML select field
	 */
	public function buildEncryptionAlgorithmSelector (array $params, \TYPO3\CMS\Extensionmanager\ViewHelpers\Form\TypoScriptConstantsViewHelper $pObj) {
		if (in_array('mcrypt', get_loaded_extensions())) {
			$encryptionAlgorithms = mcrypt_list_algorithms();
			if (!empty($encryptionAlgorithms)) {
				$field = '<br /><select id="' . $params['propertyName'] . '" name="' . $params['fieldName'] . '" />' . LF;
				foreach ($encryptionAlgorithms as $encryptionAlgorithm) {
					$selected = $params['fieldValue'] == $encryptionAlgorithm ? 'selected="selected"' : '';
					$field .= '<option name="' . $encryptionAlgorithm . '" value="' . $encryptionAlgorithm . '" ' . $selected . '>' . $encryptionAlgorithm . '</option>' . LF;
				}
				$field .= '</select><br /><br />' . LF;
			} else {
				$field = '<br />Available encryption algorithms could not be found. Algorithm blowfish will be used.<br />';
			}
		} else {
			$field = '<br />PHP mcrypt extension is not available.<br />';
		}
		return $field;
	}
}
?>