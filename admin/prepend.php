<?php
require_once ($_SERVER["DOCUMENT_ROOT"]. "/vendor/autoload.php");
$dotenv = new Dotenv\Dotenv(__DIR__.'/../');
$dotenv->load();

require_once($_SERVER["DOCUMENT_ROOT"]. "/_CORE/prepend.php");

Header("Content-Type: text/html; charset=" . ADMIN_CHARSET);

define('SESSION_LOGGED_USER_ATTR', 'CurrentAdminUser');

function htmlrparamOut($pname) {
	global $request;
	return prepareStringForXML($request->getParameter($pname));
}

function usetemplate($template) {
	global $request, $CurrentAdminUser, $AdminTrnsl;
	foreach($request->attributes as $key=>$value) {
		$$key = $value;
	}
	include ("admin/templates/" . $template . ".php");
}

function localmsg($key) {
	return $GLOBALS["AdminTrnsl"][$key];
}

$loginPage = "loginform";
$loggedUser = $session->getAttribute(SESSION_LOGGED_USER_ATTR);
$GLOBALS["CurrentAdminUser"] = $session->getAttribute(SESSION_LOGGED_USER_ATTR);

if ($loggedUser == null) {

	
	$login = $request->getParameter("_login");
	$password = $request->getParameter("_password");

	if ($login != null) {
		$loggedUser = User::findByLogin($login);
		if ($loggedUser == null) {
			$request->setAttribute("error_message", "login is not valid");
			usetemplate($loginPage);
			die();
		} else if ($loggedUser->password != $password) {
			$request->setAttribute("error_message", "password does not match");
			usetemplate($loginPage);
			die();
		} else {
			//Node::buildFullTextIndex();
			$session->setAttribute(SESSION_LOGGED_USER_ATTR, $loggedUser);
			Header("Location: /admin");
			die();
		}
	} else {
		$request->setAttribute("error_message", null);
		usetemplate($loginPage);
		die();
	}

}

$fileExtensionIconMap = array(
	"doc"=>"icon_word.gif"
	,"rtf"=>"icon_word.gif"
	,"txt"=>"icon_word.gif"
	,"jpg"=>"icon_img.gif"
	,"gif"=>"icon_img.gif"
	,"bmp"=>"icon_img.gif"
	,"png"=>"icon_img.gif"
	,"avi"=>"icon_media.gif"
	,"mpg"=>"icon_media.gif"
	,"wav"=>"icon_media.gif"
	,"mp3"=>"icon_mp3.gif"
	,"pdf"=>"icon_pdf.gif"
	,"ppt"=>"icon_ppt.gif"
	,"xls"=>"icon_xls.gif"
	,"mov"=>"icon_mov.gif"
	,"zip"=>"icon_zip.gif"
);
function getIconForFile($fileName) {
	global $fileExtensionIconMap;
	$fileExtension = getFileExt($fileName);
	$icon = $fileExtensionIconMap[strtolower($fileExtension)];

	if ($icon == null) {
		$icon = "icons_rest.gif";
	}
	return $icon;
}

?>
