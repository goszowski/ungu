<?php
	//This defines language for admin area
	define("LOCALE", "ru");

	$connect_params = array(
		"url"=>"mysql://".getenv('DATABASE_HOST')."/".getenv('DATABASE_NAME'),
		"params"=>array(
			"user"=>getenv('DATABASE_USER'), 
			"password"=>getenv('DATABASE_PASSWORD')
		)
	);




	//Encoding for site pages
	define("DB_CHARSET", "utf8");
	define("SITE_CHARSET", "utf-8");
	define("ADMIN_CHARSET", "utf-8");
?>
