<?php
$extensionPath = t3lib_extMgm::extPath('sr_freecap');
$extensionClassesPath = \TYPO3\CMS\Core\Utility\ExtensionManagementUtility::extPath('sr_freecap') . 'Classes/';
return array(
	'SJBR\SrFreecap\Configuration\ConfigurationHelper' => $extensionClassesPath . 'Configuration/ConfigurationHelper.php',
	'SJBR\SrFreecap\Controller\AudioPlayerController' => $extensionClassesPath . 'Controller/AudioPlayerController.php',
	'SJBR\SrFreecap\Controller\FontMakerController' => $extensionClassesPath . 'Controller/FontMakerController.php',
	'SJBR\SrFreecap\Controller\ImageGeneratorController' => $extensionClassesPath . 'Controller/ImageGeneratorController.php',
	'SJBR\SrFreecap\Domain\Model\Font' => $extensionClassesPath . 'Domain/Model/Font.php',
	'SJBR\SrFreecap\Domain\Model\Word' => $extensionClassesPath . 'Domain/Model/Word.php',
	'SJBR\SrFreecap\Domain\Repository\FontRepository' => $extensionClassesPath . 'Domain/Repository/FontRepository.php',
	'SJBR\SrFreecap\Domain\Repository\WordRepository' => $extensionClassesPath . 'Domain/Repository/WordRepository.php',
	'SJBR\SrFreecap\Domain\Session\SessionStorage' => $extensionClassesPath . 'Domain/Session/SessionStorage.php',
	'TYPO3\CMS\Extbase\MVC\Web\Request' => \TYPO3\CMS\Core\Utility\ExtensionManagementUtility::extPath('extbase') . 'Classes/Mvc/Web/Request.php',
	'TYPO3\CMS\Extbase\MVC\Web\Response' => \TYPO3\CMS\Core\Utility\ExtensionManagementUtility::extPath('extbase') . 'Classes/Mvc/Web/Response.php',
	'SJBR\SrFreecap\Utility\EidUtility' => $extensionClassesPath . 'Utility/EidUtility.php',
	'SJBR\SrFreecap\Utility\EncryptionUtility' => $extensionClassesPath . 'Utility/EncryptionUtility.php',
	'SJBR\SrFreecap\Utility\FontMakingUtility' => $extensionClassesPath . 'Utility/FontMakingUtility.php',
	'SJBR\SrFreecap\Utility\GifBuilderUtility' => $extensionClassesPath . 'Utility/GifBuilderUtility.php',
	'SJBR\SrFreecap\Utility\ImageContentUtility' => $extensionClassesPath . 'Utility/ImageContentUtility.php',
	'SJBR\SrFreecap\Utility\RandomContentUtility' => $extensionClassesPath . 'Utility/RandomContentUtility.php',
	'SJBR\SrFreecap\Utility\WavContentUtility' => $extensionClassesPath . 'Utility/WavContentUtility.php',
	'SJBR\SrFreecap\Validation\Validator\TtfFileValidator' => $extensionClassesPath . 'Validation/Validator/TtfFileValidator.php',
	'TYPO3\CMS\Extbase\MVC\View\ViewInterface' => \TYPO3\CMS\Core\Utility\ExtensionManagementUtility::extPath('extbase') . 'Classes/Mvc/View/ViewInterface.php',
	'SJBR\SrFreecap\View\AudioPlayer\PlayWav' => $extensionClassesPath . 'View/AudioPlayer/PlayWav.php',
	'SJBR\SrFreecap\View\ImageGenerator\ShowPng' => $extensionClassesPath . 'View/ImageGenerator/ShowPng.php',
	'SJBR\SrFreecap\Validation\Validator\CaptchaValidator' => $extensionClassesPath . 'Validation/Validator/CaptchaValidator.php',
	'SJBR\SrFreecap\ViewHelpers\AudioViewHelper' => $extensionClassesPath . 'ViewHelpers/AudioViewHelper.php',
	'SJBR\SrFreecap\ViewHelpers\ImageViewHelper' => $extensionClassesPath . 'ViewHelpers/ImageViewHelper.php',
	'SJBR\SrFreecap\ViewHelpers\TranslateViewHelper' => $extensionClassesPath . 'ViewHelpers/TranslateViewHelper.php',
	'SJBR\SrFreecap\PiBaseApi' => $extensionClassesPath . 'PiBaseApi.php',
);
unset($extensionPath);       
?>