<?php
/*
 * Extension Manager configuration file for ext "sr_freecap".
 *
 */
$EM_CONF[$_EXTKEY] = array(
	'title' => 'freeCap CAPTCHA',
	'description' => 'A TYPO3 integration of freeCap CAPTCHA.',
	'category' => 'plugin',
	'version' => '2.2.0',
	'state' => 'stable',
	'uploadfolder' => 1,
	'createDirs' => '',
	'clearcacheonload' => 0,
	'author' => 'Stanislas Rolland',
	'author_email' => 'typo3(arobas)sjbr.ca',
	'author_company' => 'SJBR',
	'constraints' => 
	array(
		'depends' => array(
			'php' => '5.3.0-0.0.0',
			'typo3' => '6.2.0-7.99.99'
		),
		'conflicts' => array(),
		'suggests' => array()
	)
);