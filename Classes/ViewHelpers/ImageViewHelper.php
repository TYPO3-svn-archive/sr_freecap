<?php
namespace SJBR\SrFreecap\ViewHelpers;
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
class ImageViewHelper extends \TYPO3\CMS\Fluid\Core\ViewHelper\AbstractViewHelper {

	/**
	 * @var string Name of the extension this view helper belongs to
	 */
	protected $extensionName = 'SrFreecap';

	/**
	 * @var string Name of the extension this view helper belongs to
	 */
	protected $extensionKey = 'sr_freecap';

	/**
	 * @var string Name of the plugin this view helper belongs to
	 */
	protected $pluginName = 'tx_srfreecap';

	/**
	 * @var \TYPO3\CMS\Extbase\Configuration\ConfigurationManagerInterface
	 */
	protected $configurationManager;

	/**
	 * @param \TYPO3\CMS\Extbase\Configuration\ConfigurationManagerInterface $configurationManager
	 * @return void
	 */
	public function injectConfigurationManager(\TYPO3\CMS\Extbase\Configuration\ConfigurationManagerInterface $configurationManager) {
		$this->configurationManager = $configurationManager;
	}

	/**
	 * Render the captcha image html
	 *
	 * @param string suffix to be appended to the extenstion key when forming css class names
	 * @return string The html used to render the captcha image
	 */
	public function render ($suffix = '') {

		$value = '';

		// Include the required JavaScript
		$GLOBALS['TSFE']->additionalHeaderData[$this->extensionKey] .= '<script type="text/javascript" src="'. \TYPO3\CMS\Core\Utility\ExtensionManagementUtility::siteRelPath($this->extensionKey) . 'Resources/Public/JavaScript/freeCap.js"></script>';

		// Disable caching
		$GLOBALS['TSFE']->no_cache = 1;

		// Get the plugin configuration
		$settings = $this->configurationManager->getConfiguration(\TYPO3\CMS\Extbase\Configuration\ConfigurationManagerInterface::CONFIGURATION_TYPE_SETTINGS, $this->extensionName);

		// Get the translation view helper
		$objectManager = \TYPO3\CMS\Core\Utility\GeneralUtility::makeInstance('TYPO3\\CMS\\Extbase\\Object\\ObjectManager');
		$translator = $objectManager->get('SJBR\\SrFreecap\\ViewHelpers\\TranslateViewHelper');
		$translator->injectConfigurationManager($this->configurationManager);

		// Generate the image url
		$fakeId = \TYPO3\CMS\Core\Utility\GeneralUtility::shortMD5(uniqid (rand()),5);
		$siteURL = \TYPO3\CMS\Core\Utility\GeneralUtility::getIndpEnv('TYPO3_SITE_URL');
		$urlParams = array(
			'eID' => 'sr_freecap_EidDispatcher',
			'id' => $GLOBALS['TSFE']->id,
			'extensionName' => 'SrFreecap',
			'pluginName' => 'ImageGenerator',
			'controllerName' => 'ImageGenerator',
			'actionName' => 'show',
			'formatName' => 'png',
		);
		if (isset($L)) {
			$urlParams['L'] = htmlspecialchars($L);
		}
		if ($GLOBALS['TSFE']->MP) {
			$urlParams['MP'] = $GLOBALS['TSFE']->MP;
		}
		$imageUrl = $siteURL . 'index.php?' . ltrim(\TYPO3\CMS\Core\Utility\GeneralUtility::implodeArrayForUrl('', $urlParams), '&');

		// Generate the html text
		$value = '<img' . $this->getClassAttribute('image', $suffix) . ' id="tx_srfreecap_captcha_image_' . $fakeId . '"'
			. ' src="' . htmlspecialchars($imageUrl) . '"'
			. ' alt="' . $translator->render('altText') . ' "/>'
			. '<span' . $this->getClassAttribute('cant-read', $suffix) . '>' . $translator->render('cant_read1')
			. ' <a href="#" onclick="this.blur();' . $this->extensionName . '.newImage(\'' . $fakeId . '\', \'' . $translator->render('noImageMessage').'\');return false;">'
			. $translator->render('click_here') . '</a>'
			. $translator->render('cant_read2') . '</span>';
		
		return $value;
	}

	/**
	 * Returns a class attribute with a class-name prefixed with $this->pluginName and with all underscores substituted to dashes (-)
	 *
	 * @param string $class The class name (or the END of it since it will be prefixed by $this->pluginName.'-')
	 * @param string suffix to be appended to the extenstion key when forming css class names
	 * @return string the class attribute with the combined class name (with the correct prefix)
	 */
	protected function getClassAttribute ($class, $suffix = '') {
		return ' class="' . trim(str_replace('_', '-', $this->pluginName) . ($suffix ? '-' . $suffix . '-' : '-') . $class) . '"';
	}
}
?>
