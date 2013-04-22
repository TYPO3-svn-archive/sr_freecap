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
class AudioViewHelper extends \TYPO3\CMS\Fluid\Core\ViewHelper\AbstractViewHelper {

	/**
	 * @var string Name of the extension this view helper belongs to
	 */
	protected $extensionName = 'SrFreecap';

	/**
	 * @var string Name of the extension this view helper belongs to
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
	 * Render the captcha audio rendering request icon
	 *
	 * @param string suffix to be appended to the extenstion key when forming css class names
	 * @return string The html used to render the captcha audio rendering request icon
	 */
	public function render ($suffix = '') {
		$value = '';
		// Get the plugin configuration
		$settings = $this->configurationManager->getConfiguration(\TYPO3\CMS\Extbase\Configuration\ConfigurationManager::CONFIGURATION_TYPE_SETTINGS, $this->extensionName, $this->pluginName);
		// Get the translation view helper
		$objectManager = \TYPO3\CMS\Core\Utility\GeneralUtility::makeInstance('TYPO3\\CMS\\Extbase\\Object\\ObjectManager');
		$translator = $objectManager->get('SJBR\\SrFreecap\\ViewHelpers\\TranslateViewHelper');
		$translator->injectConfigurationManager($this->configurationManager);
		// Get browser info (as of iOS 6, audio rendering does not work)
		$browserInfo = \TYPO3\CMS\Core\Utility\ClientUtility::getBrowserInfo(\TYPO3\CMS\Core\Utility\GeneralUtility::getIndpEnv('HTTP_USER_AGENT'));
		// Generate the icon
		if ($settings['accessibleOutput'] && in_array('mcrypt', get_loaded_extensions()) && intval($GLOBALS['TYPO3_CONF_VARS']['SYS']['UTF8filesystem']) && !in_array('iOS', $browserInfo['all_systems'])) {
			$fakeId = \TYPO3\CMS\Core\Utility\GeneralUtility::shortMD5(uniqid (rand()),5);
			$siteURL = \TYPO3\CMS\Core\Utility\GeneralUtility::getIndpEnv('TYPO3_SITE_URL');
			$urlParams = array(
				'eID' => 'sr_freecap_EidDispatcher',
				'id' => $GLOBALS['TSFE']->id,
				'extensionName' => $this->extensionName,
				'pluginName' => 'AudioPlayer',
				'controllerName' => 'AudioPlayer',
				'actionName' => 'play',
				'formatName' => 'wav',
			);
			$L = \TYPO3\CMS\Core\Utility\GeneralUtility::_GP('L');
			if (isset($L)) {
				$urlParams['L'] = htmlspecialchars($L);
			}
			if ($GLOBALS['TSFE']->MP) {
				$urlParams['MP'] = $GLOBALS['TSFE']->MP;
			}
			$audioURL = $siteURL . 'index.php?' . ltrim(\TYPO3\CMS\Core\Utility\GeneralUtility::implodeArrayForUrl('', $urlParams), '&');
			if ($settings['accessibleOutputImage']) {
				$value = '<input type="image" alt="' . $translator->render('click_here_accessible') . '"'
					. ' title="' . $translator->render('click_here_accessible') . '"'
					. ' src="' . $siteURL . str_replace(PATH_site, '', \TYPO3\CMS\Core\Utility\GeneralUtility::getFileAbsFileName($settings['accessibleOutputImage'])) . '"'
					. ' onclick="' . $this->extensionName . '.playCaptcha(\'' . $fakeId . '\', \'' . $audioURL . '\', \'' . $translator->render('noPlayMessage') . '\');return false;" style="cursor: pointer;"'
					. $this->getClassAttribute('image-accessible', $suffix) . ' />'
					. '<span' . $this->getClassAttribute('accessible') . ' id="tx_srfreecap_captcha_playAudio_' . $fakeId . '"></span>';
			} else {
				$value = '<span id="tx_srfreecap_captcha_playLink_' . $fakeId . '"'
					. $this->getClassAttribute('accessible-link', $suffix) . '>' . $translator->render('click_here_accessible_before_link') 
					. '<a onClick="' . $this->extensionName . '.playCaptcha(\'' . $fakeId.'\', \'' . $audioURL . '\', \'' . $translator->render('noPlayMessage') . '\');" style="cursor: pointer;" title="' . $translator->render('click_here_accessible') . '">'
					. $translator->render('click_here_accessible_link') . '</a>'
					. $translator->render('click_here_accessible_after_link') . '</span>'
					. '<span ' . $this->getClassAttribute('accessible', $suffix) . ' id="tx_srfreecap_captcha_playAudio_'  . $fakeId . '"></span>';
			}
		}
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
