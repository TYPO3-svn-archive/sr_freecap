<?php

$extensionPath = t3lib_extMgm::extPath('sr_freecap');
$extensionClassesPath = \TYPO3\CMS\Core\Utility\ExtensionManagementUtility::extPath('sr_freecap') . 'Classes/';
return array(
	'SJBR\SrFreecap\Controller\AudioPlayerController' => $extensionClassesPath . 'Controller/AudioPlayerController.php',
	'SJBR\SrFreecap\Controller\ImageGeneratorController' => $extensionClassesPath . 'Controller/ImageGeneratorController.php',
	'SJBR\SrFreecap\Domain\Model\Word' => $extensionClassesPath . 'Domain/Model/Word.php',
	'SJBR\SrFreecap\Domain\Repository\WordRepository' => $extensionClassesPath . 'Domain/Repository/WordRepository.php',
	'SJBR\SrFreecap\Domain\Session\SessionStorage' => $extensionClassesPath . 'Domain/Session/SessionStorage.php',
	'TYPO3\CMS\Extbase\MVC\Web\Request' => \TYPO3\CMS\Core\Utility\ExtensionManagementUtility::extPath('extbase') . 'Classes/Mvc/Web/Request.php',
	'TYPO3\CMS\Extbase\MVC\Web\Response' => \TYPO3\CMS\Core\Utility\ExtensionManagementUtility::extPath('extbase') . 'Classes/Mvc/Web/Response.php',
	'SJBR\SrFreecap\Utility\EidUtility' => $extensionClassesPath . 'Utility/EidUtility.php',
	'SJBR\SrFreecap\Utility\EncryptionUtility' => $extensionClassesPath . 'Utility/EncryptionUtility.php',
	'SJBR\SrFreecap\Utility\ImageContentUtility' => $extensionClassesPath . 'Utility/ImageContentUtility.php',
	'SJBR\SrFreecap\Utility\RandomContentUtility' => $extensionClassesPath . 'Utility/RandomContentUtility.php',
	'SJBR\SrFreecap\Utility\WavContentUtility' => $extensionClassesPath . 'Utility/WavContentUtility.php',
	'TYPO3\CMS\Extbase\MVC\View\ViewInterface' => \TYPO3\CMS\Core\Utility\ExtensionManagementUtility::extPath('extbase') . 'Classes/Mvc/View/ViewInterface.php',
	'SJBR\SrFreecap\View\AudioPlayer\PlayWav' => $extensionClassesPath . 'View/AudioPlayer/PlayWav.php',
	'SJBR\SrFreecap\View\ImageGenerator\ShowPng' => $extensionClassesPath . 'View/ImageGenerator/ShowPng.php',
	'tx_srfreecap_fontmaker' => $extensionPath . 'mod1/class.tx_srfreecap_fontmaker.php',
	'tx_srfreecap_gifbuilder' => $extensionPath . 'mod1/class.tx_srfreecap_fontmaker.php',
	'tx_srfreecap_pi2' => $extensionPath . 'pi2/class.tx_srfreecap_pi2.php',
);
unset($extensionPath);
?>