<?php

/**
 * Ptitcaptcha : simple php captcha system
 * 
 * @author Jean-Pierre Morfin
 * @license Creative Commons By
 * @license http://creativecommons.org/licenses/by/2.0/fr/
 */
 
/* Change it to have a specific encoding ! */
define("PTITCAPTCHA_ENTROPY","sourceml ptitcaptcha entropy");
 
/* Choose length (max 32) */
define("PTITCAPTCHA_LENGTH",5);
 
$GLOBALS["ptitcaptcha_akey"] = md5(uniqid(rand(), true));
 
/**
 * Helper to generate html form tags
 *
 */
class PtitCaptchaHelper
{
	/**
	 * Generate IMG Tag
	 *
	 * @param string $baseuri : relative or absolute path to folder containing this file on web
	 * @return IMG Tag
	 */
	function generateImgTags($baseuri)
	{
		return "<a class=\"ptitcaptcha\" href=\"#\"><img alt=\"???\" title=\"?\"".
			" src=\"".$baseuri."ptitcaptcha.php?pck=".$GLOBALS['ptitcaptcha_akey']."\"".
			" id=\"ptitcaptcha\"".
			" onclick=\"javascript:this.src='".$baseuri."ptitcaptcha.php?pck=".
			$GLOBALS['ptitcaptcha_akey'].
			"&amp;z='+Math.random();return false;\" /></a>\n";
	}
 
	/**
	 * Generate hidden tag (must be in a form)
	 *
	 * @return input hidden tag
	 */
	function generateHiddenTags()
	{
		return "<input type=\"hidden\" name=\"ptitcaptcha_key\" value=\"".$GLOBALS['ptitcaptcha_akey']."\"/>";
	}
 
	/**
	 * Generate input tag (must be in a form)
	 *
	 * @return input tag
	 */
	function generateInputTags()
	{
		return "<input type=\"text\" name=\"ptitcaptcha_entry\" id=\"ptitcaptcha_entry\" value=\"\"/>";
	}
 
	/**
	 * Check if user input is correct
	 *
	 * @return boolean (true=correct, false=incorrect)
	 */
	function checkCaptcha()
	{
		if(	isset($_POST['ptitcaptcha_entry']) && 
			$_POST['ptitcaptcha_entry'] == PtitCaptchaHelper::_getDisplayText($_POST['ptitcaptcha_key']))
		{
			return true;
		}
		return false;
	}
 
	/**
	 * Internal function
	 *
	 * @param string $pck
	 * @return string
	 */
	function _getDisplayText($pck)	// internal function
	{
		$src=md5(PTITCAPTCHA_ENTROPY.$pck);
		$txt="";
		for($i=0;$i<PTITCAPTCHA_LENGTH;$i++)
			$txt.=substr($src,$i*32/PTITCAPTCHA_LENGTH,1);
		return $txt;
	}
}	
 
 
// If script called directly : generate image
if(basename($_SERVER["SCRIPT_NAME"])=="ptitcaptcha.php" && isset($_GET["pck"]))
{
	$width = PTITCAPTCHA_LENGTH*10+10;
	$height = 30;
 
	$image = imagecreatetruecolor($width, $height);
	$bgCol = imagecolorallocate($image, rand(128,255), rand(128,255), rand(128,255));
	imagefilledrectangle($image,0,0,$width,$height,$bgCol);
 
	$txt = PtitCaptchaHelper::_getDisplayText($_GET["pck"]);
 
	for($c=0;$c<PTITCAPTCHA_LENGTH*2;$c++)
	{
		$bgCol = imagecolorallocate($image, rand(100,255), rand(100,255), rand(100,255));
		$x=rand(0,$width);
		$y=rand(0,$height);
		$w=rand(5,$width/2);
		$h=rand(5,$height/2);
		imagefilledrectangle($image,$x,$y,$x+$w,$y+$h,$bgCol);
		imagecolordeallocate($image,$bgCol);
	}
	for($c=0;$c<PTITCAPTCHA_LENGTH;$c++)
	{
		$txtCol = imagecolorallocate($image, rand(0,128) , rand(0,128), rand(0,128));
		imagestring($image,5,5+10*$c,rand(0,10),substr($txt,$c,1),$txtCol);
		imagecolordeallocate($image,$txtCol);
	}
 
	header("Content-type: image/png");
	imagepng($image);
	imagedestroy($image);
}

?>