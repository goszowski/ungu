<?php 

require_once ("vendor/autoload.php");

$dotenv = new Dotenv\Dotenv(__DIR__);
$dotenv->load();

require_once('config.php');

$uri = $_SERVER['REQUEST_URI'];
$path = $uri;
//$path = str_replace("-", "/", $path);
$p = strpos($path, "?");
if ($p !== false) {
	$path = substr($path, 0, $p);
}

if (strpos($path, ".html")) {
	$path = substr($path, 0, strlen($path)-5);
}

if ($path=='') {
	$path = '/';
} else if (strlen($path) != 1 && $path{strlen($path)-1} == '/') {
	$path = substr($path, 0, strlen($path) - 1);
}


if(substr($path, 0, strpos($path, "/", 1)))
	{$base = substr($path, 0, strpos($path, "/", 1));}
else{$base = $path;}


require_once('_CORE/prepend.php');
Header("Content-Type: text/html; charset=" . SITE_CHARSET);
$CurrentNode = Node::findByPath($path);
$request->setAttribute("CurrentNode", $CurrentNode);

require_once('_site_settings.php');

list($website_data) = getSimpleList('index', array(
		'currency',
		'facebook_link',
		'instagram_link',
		'ga_code'
	));

use_controller($CurrentNode->dynamicTemplate);

_logTimeFromStart("script end");
?>
