<?php

$extensionPath = t3lib_extMgm::extPath('sr_freecap');
return array(
	'tx_srfreecap_fontmaker' => $extensionPath . 'mod1/class.tx_srfreecap_fontmaker.php',
	'tx_srfreecap_gifbuilder' => $extensionPath . 'mod1/class.tx_srfreecap_fontmaker.php',
	'tx_srfreecap_pi1' => $extensionPath . 'pi1/class.tx_srfreecap_pi1.php',
	'tx_srfreecap_pi2' => $extensionPath . 'pi2/class.tx_srfreecap_pi2.php',
	'tx_srfreecap_pi3' => $extensionPath . 'pi3/class.tx_srfreecap_pi3.php',
);
unset($extensionPath);
?>