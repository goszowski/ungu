<!DOCTYPE html>
<html>
<head>
	<?

	$title = $CurrentNode->tfields["title"] ? $CurrentNode->tfields["title"] : $CurrentNode->name . ' - UNGU';
	$keywords = $CurrentNode->tfields["keywords"] ? $CurrentNode->tfields["keywords"] : $CurrentNode->name;
	$description = $CurrentNode->tfields["description"] ? $CurrentNode->tfields["description"] : $CurrentNode->name;

	?>
	<title><?=$title?></title>
	<meta name="keywords" content="<?=$keywords?>" >
	<meta name="description" content="<?=$description?>" >
	<meta name="viewport" content="width=device-width, initial-scale=1">
	<meta charset="utf-8">
	<link rel="shortcut icon" type="image/png" href="/asset/images/favicon.png">
	<link rel="stylesheet" type="text/css" href="/asset/plugins/bootstrap/css/bootstrap.min.css">
	<link rel="stylesheet" type="text/css" href="/asset/plugins/runsite.offset/runsite.offset.css">

	<script type="text/javascript" src="/asset/plugins/jquery/jquery-2.1.3.min.js"></script>

	<link rel="stylesheet" type="text/css" href="/asset/plugins/magnific-popup/magnific-popup.css">
	<script src="/asset/plugins/magnific-popup/jquery.magnific-popup.min.js"></script>

	<link rel="stylesheet" type="text/css" href="/asset/plugins/font-awesome-4.3.0/css/font-awesome.min.css">

	<link rel="stylesheet" type="text/css" href="/asset/plugins/owl.carousel.2.0.0-beta.2.4/assets/owl.carousel.css">
	<script src="/asset/plugins/owl.carousel.2.0.0-beta.2.4/owl.carousel.min.js"></script>
	<script src="/asset/plugins/owl.carousel2.thumbs.min.js"></script>

	<link href="https://fonts.googleapis.com/css?family=Lato:300,400,700" rel="stylesheet">

	<link rel="stylesheet" type="text/css" href="/asset/css/globals.css?19">
</head>
<body>
<?=$website_data['ga_code'];?>
