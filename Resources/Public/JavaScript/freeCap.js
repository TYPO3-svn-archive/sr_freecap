/***************************************************************
*  Copyright notice
*
*  (c) 2007-2012 Stanislas Rolland <typo3(arobas)sjbr.ca>
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
*
*  This copyright notice MUST APPEAR in all copies of the script!
***************************************************************/
/*
 * Javascript functions for TYPO3 extension freeCap CAPTCHA (sr_freecap)
 *
 */
(function () {
	SrFreecap = {
		/*
		 * Loads a new freeCap image
		 *
		 * @param	string		id: identifier used to uniiquely identify the image
		 *
		 * @return	void
		 */
		newImage: function (id, noImageMessage) {
			if (document.getElementById) {
					// extract image name from image source (i.e. cut off ?randomness)
				var theImage = document.getElementById('tx_srfreecap_captcha_image_' + id);
				var parts = theImage.src.split('&set');
				theImage.src = parts[0] + '&set=' + Math.round(Math.random()*100000);
			} else {
				alert(noImageMessage ? noImageMessage : 'Sorry, we cannot autoreload a new image. Submit the form and a new image will be loaded.');
			}
		},
		
		/*
		 * Plays the audio captcha
		 *
		 * @param	string		id: identifier used to uniquely identify the wav file
		 * @param	string		wavURL: url of the wave file generating script
		 *
		 * @return	void
		 *
		 * Note: In order for this to work with IE8, [SYS][cookieDomain] must be set using the TYPO3 Install Tool
		 */
		playCaptcha: function (id, wavURL, noPlayMessage) {
			if (document.getElementById) {
				var theAudio = document.getElementById('tx_srfreecap_captcha_playAudio_' + id);
				var url = wavURL + '&nocache=' + Math.random();
				while (theAudio.firstChild) {
					theAudio.removeChild(theAudio.firstChild);
				}
				var audioElement = document.createElement('audio');
				if (audioElement.canPlayType && (audioElement.canPlayType('audio/x-wav') === 'maybe' || audioElement.canPlayType('audio/x-wav') === 'probably')) {
					audioElement.setAttribute('id', 'tx_srfreecap_captcha_playAudio_audio' + id);
					audioElement.setAttribute('autoplay', 'autoplay');
					var sourceElement = document.createElement('source');
					sourceElement.setAttribute('type', 'audio/x-wav');
					sourceElement.setAttribute('src', url);
					audioElement.appendChild(sourceElement);
					theAudio.appendChild(audioElement);
				} else {
						// In IE, Windows Media Player should be the default player for audio WAVE
					var objectElement = document.createElement('object');
					objectElement.setAttribute('id', 'tx_srfreecap_captcha_playAudio_object' + id);
					objectElement.setAttribute('type', 'audio/x-wav');
					objectElement.setAttribute('data', url);
					objectElement.style.height = 0;
					objectElement.style.width = 0;
					try {
						objectElement.innerHTML = '<a href="' + url + '">' + (noPlayMessage ? noPlayMessage : 'Sorry, we cannot play the word of the image.') + '</a>';
					} catch (e) {
							// IE8 does not allow any element other than param as child of object
						objectElement.setAttribute('altHTML', '<a href="' + url + '">' + (noPlayMessage ? noPlayMessage : 'Sorry, we cannot play the word of the image.') + '</a>');
					}
					theAudio.appendChild(objectElement);
					var parameters = {
						src: url,
						autoplay: true,
						autoStart: true,
						controller: false
					};
					for (var parameter in parameters) {
						if (parameters.hasOwnProperty(parameter)) {
							var paramElement = document.createElement('param');
							paramElement.setAttribute('value', parameters[parameter]);
							paramElement.setAttribute('name', parameter);
							paramElement = objectElement.appendChild(paramElement);
						}
					}
				}
			} else {
				alert(noPlayMessage ? noPlayMessage : 'Sorry, we cannot play the word of the image.');
			}
		}
	}
})();
