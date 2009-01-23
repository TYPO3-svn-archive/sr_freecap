<?
if (!defined('freeCap')) die ('Access denied.');
/************************************************************\
*
*		GD Fontmaker Copyright 2005 Howard Yeend
*		www.puremango.co.uk
*
*    This file is part of GD Fontmaker.
*
*    GD Fontmaker is free software; you can redistribute it and/or modify
*    it under the terms of the GNU General Public License as published by
*    the Free Software Foundation; either version 2 of the License, or
*    (at your option) any later version.
*
*    GD Fontmaker is distributed in the hope that it will be useful,
*    but WITHOUT ANY WARRANTY; without even the implied warranty of
*    MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
*    GNU General Public License for more details.
*
*    You should have received a copy of the GNU General Public License
*    along with GD Fontmaker; if not, write to the Free Software
*    Foundation, Inc., 59 Temple Place, Suite 330, Boston, MA  02111-1307  USA
*
*
\************************************************************/

if(!empty($_POST['numchars']))
{
	// create GD image from PNG
	$im = @ImageCreateFromPNG($_FILES['pngfile']['tmp_name']);
	if(!$im)
	{
		exit("Cannot create image!");
	}

	// get user supplied data
	$numchars = $_POST['numchars'];
	$startchar = ord($_POST['startchar']);
	$pixelwidth = $_POST['pixelwidth'];
	$pixelheight = $_POST['pixelheight'];

	// encode this at start of font
	$fontdata = chr($numchars).chr(0).chr(0).chr(0).chr($startchar).chr(0).chr(0).chr(0).chr($pixelwidth).chr(0).chr(0).chr(0).chr($pixelheight).chr(0).chr(0).chr(0);

	// loop through each pixel of each character of the PNG
	// (we know the dimensions of the characters because the user told us what they were)
	$y=0;
	$x=0;
	$start_x=0;
	for($c=0; $c<$numchars*$pixelwidth ; $c+=$pixelwidth)
	{
		for($y=0 ; $y<$pixelheight ; $y++)
		{
			for($x=$c ; $x<$c+$pixelwidth ; $x++)
			{
				// get colour of this pixel
				$rgb = ImageColorAt($im, $x, $y);

				if($rgb==0)
				{
					// it's black; font data
					$fontdata .= chr(255);
				} else {
					// it's not black; background
					$fontdata .= chr(0);
				}
				$i++;
			}
		}
	}

	// remove image from memory
	ImageDestroy($im);

	// let user download font
	header("Pragma: public");
	header("Expires: 0");
	header("Cache-Control: must-revalidate, post-check=0, pre-check=0");
	header("Cache-Control: public");
	header("Content-Description: File Transfer");
	header("Content-Type: application/octet-stream");
	header("Content-Disposition: attachment; filename=font.gdf");
	header("Content-Transfer-Encoding: binary");
	header("Content-Length: ".strlen($fontdata));
	echo $fontdata;
	exit();
}
?>
<html>
<head>
<title>GD fontMaker v1 - www.puremango.co.uk</title>
<style>
body,td{
	font-family: verdana;
	font-size: 12px;
	color: black
	background: white;
}
input{
		font-family: verdana;
		font-size: 12px;
}
</style>
</head>
<body>
<b>GD fontMaker v1 - <a href="http://www.puremango.co.uk" target="_blank">www.puremango.co.uk</a></b><br /><br />
<form action="<?=$_SERVER['PHP_SELF']?>" method="post" enctype="multipart/form-data">
<table>
<tr><td>total number of characters</td><td><input type="text" name="numchars"></td></tr>
<tr><td>starting character</td><td><input type="text" name="startchar"></td></tr>
<tr><td>character width</td><td><input type="text" name="pixelwidth"></td></tr>
<tr><td>character height</td><td><input type="text" name="pixelheight"></td></tr>
<tr><td>PNG image</td><td><input type="file" name="pngfile"></td></tr>
<tr><td colspan="2" align="center"><input type="submit" value="make font"></td></tr>
</table>
This script takes a PNG image as input and creates a GD compatible font from it.<br />
<br />
Usage is best described with an example;<br />
<a href="font.png" target="_blank">This file</a> is a PNG of the Arial font.<br />
Each character is 34 pixels wide by 50 pixels high.<br />
The starting character is 'a', and there are 26 characters in total.<br />
Anything other than pure black will be treated as the background of the PNG; this is useful as it allows you to draw guidelines as in the example PNG<br />
<br />
Characters must be in ASCII code order, which means that the ideal character set is:<br />
(space)!"#$%&'()*+,-./0123456789:;&lt;=&gt;?@ABCDEFGHIJKLMNOPQRSTUVWXYZ[\]^_`abcdefghijklmnopqrstuvwxyz{|}~<br />
<br />
But if you're lazy like me, you'll be pleased to hear that GD will process any undefined character as a space;<br />
In other words, my example can be used to spell "hi there", even though there is no space defined in the font.<br />
But it can't spell "Hi There" because I didn't include capitals; what it would write is:<br />" i &nbsp;here"<br /><br />
<b>Known Bugs</b><br />
You can't create fonts with characters greater than 255 pixels in either dimension, or with more than 255 characters in total (if you need me to, I can solve this)

</form>
</body>
</html>