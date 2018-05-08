<?php

//Site root dir
define('DIR_ROOT',		$_SERVER['DOCUMENT_ROOT']);
//Images dir (root relative)
define('DIR_IMAGES',	'/imglib');
//Files dir (root relative)
define('DIR_FILES',		'/files');


//Width and height of resized image
define('WIDTH_TO_LINK', 500);
define('HEIGHT_TO_LINK', 500);

//Additional attributes class and rel
define('CLASS_LINK', 'lightview');
define('REL_LINK', 'lightbox');

date_default_timezone_set('Asia/Yekaterinburg');
?>
