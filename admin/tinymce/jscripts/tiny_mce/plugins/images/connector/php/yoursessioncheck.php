<?php
/**
 * User session check, for registered users
 * 
 * If you don't care about access,
 * please remove or comment following code
 * 
 */
/*
if($_SESSION['authorization'] ==1) {
	echo 'Access denied, check file '.basename(__FILE__);
	exit();
}*//*
session_start();
if(!$_SESSION["CurrentAdminUser"]){
	echo 'Access denied';
	exit();
}
*/
?>