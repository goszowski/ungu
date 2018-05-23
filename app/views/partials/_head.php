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

	<link href='https://fonts.googleapis.com/css?family=Roboto+Condensed:400,700&amp;subset=latin,cyrillic' rel='stylesheet' type='text/css'>

	<link rel="stylesheet" type="text/css" href="/asset/css/globals.css?21">
</head>
<body>
<?=$website_data['ga_code'];?>
<!-- Facebook Pixel Code -->
<script>
  !function(f,b,e,v,n,t,s)
  {if(f.fbq)return;n=f.fbq=function(){n.callMethod?
  n.callMethod.apply(n,arguments):n.queue.push(arguments)};
  if(!f._fbq)f._fbq=n;n.push=n;n.loaded=!0;n.version='2.0';
  n.queue=[];t=b.createElement(e);t.async=!0;
  t.src=v;s=b.getElementsByTagName(e)[0];
  s.parentNode.insertBefore(t,s)}(window, document,'script',
  'https://connect.facebook.net/en_US/fbevents.js');
  fbq('init', '789810367790996');
  fbq('track', 'PageView');
</script>
<noscript><img height="1" width="1" style="display:none"
  src="https://www.facebook.com/tr?id=789810367790996&ev=PageView&noscript=1"
/></noscript>
<!-- End Facebook Pixel Code -->

