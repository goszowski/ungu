<!DOCTYPE html>
<html>
<head>
	<title>Website</title>

	<meta name="viewport" content="width=device-width, initial-scale=1">
	<meta charset="utf-8">
	<meta name="keywords" content="">
	<meta name="decription" content="">
	<meta name="author" content="ActiveDesign.pl">
    
    <!-- html5shiv --> 			<script src="plugins/html5shiv/html5shiv.min.js"></script>

    <!-- jquery --> 			<script src="plugins/jquery/jquery-2.1.3.min.js"></script>
    <!-- jquery.browser --> 	<script src="plugins/jquery/jquery.browser.min.js"></script>


    <!-- bootstrap --> 			<link rel="stylesheet" href="plugins/bootstrap/css/bootstrap.min.css">
    <!-- bootstrap js --> 		<script src="plugins/bootstrap/js/bootstrap.min.js"></script>


    <!-- font-awesome --> 		<link rel="stylesheet" href="plugins/font-awesome-4.3.0/css/font-awesome.min.css">



    <!-- owl-carousel --> 		<link rel="stylesheet" href="plugins/owl-carousel/owl.carousel.css">
    <!-- owl theme --> 			<link rel="stylesheet" href="plugins/owl-carousel/owl.theme.css">
    <!-- owl transitions --> 	<link rel="stylesheet" href="plugins/owl-carousel/owl.transitions.css">
    <!-- owl js --> 			<script src="plugins/owl-carousel/owl.carousel.min.js"></script>


    <!-- global styles --> 		<link rel="stylesheet" href="css/globals.css">

    <!-- runsite.offset --> 	<link rel="stylesheet" href="plugins/runsite.offset/runsite.offset.css">
    
    <!-- master js --> 			<script src="js/master.js"></script>
    
</head>
<body>




	<header class="header ">
		<div class="container">
			<div class="row">
				<div class="col-xs-12 col-sm-12 col-md-4 text-center-xs text-center-sm">
					<!-- logo -->
					<a href="#" class="logo">
						<img src="images/logo.png" alt="...">
					</a>
					<!-- / logo -->
				</div>

				<div class="col-xs-12 col-sm-12 col-md-4 text-center">
					<!-- phone -->
					<div class="contact-row2">
						<i class="fa fa-phone-square text-primary fa-2x"></i>
						<div class="mt-4">063 42 42 623</div>
					</div>
					<!-- / phone -->
				</div>

				<div class="col-xs-12 col-sm-12 col-md-4 text-right text-center-xs text-center-sm">
					<!-- mail, skype -->
					<div class="contact-row">
						<i class="fa fa-skype text-primary"></i> <b class="text-uppercase">Скайп:</b> mihailukraine
					</div>

					<div class="contact-row">
						<i class="fa fa-envelope text-primary"></i> <b class="text-uppercase">ПОЧТА:</b> mihail@biznes.ua
					</div>
					<!-- / mail, skype -->
				</div>
			</div>
		</div>
	</header>

	<div class="container">

		<div class="text-center-xs">
			<div class="btn-group text-uppercase" role="group">
				<a href="#" class="btn btn-primary"><i class="fa fa-shopping-cart"></i> Сток</a>
				<a href="#" class="btn btn-default"><i class="fa fa-pencil"></i> Мой блог</a>
			</div>
		</div>

	</div>


	<?
	/* * * Including sub-page * * */
	if(isset($_GET['page']) and file_exists('sub/' . $_GET['page'] . '.php')) include_once 'sub/' . $_GET['page'] . '.php'; 

	elseif(file_exists('sub/main.php')) include_once 'sub/main.php';
	?>

	

	<footer class="footer">
		<div class="container">
			<div class="row">
				<div class="col-xs-12 col-sm-6 text-center-xs">
					&copy; Название сайта 2015
				</div>

				<div class="col-xs-12 col-sm-6 text-right text-center-xs">
					<a title="Runsite - Website development" class="btn btn-success btn-xs" href="https://www.facebook.com/goszowski" rel="nofollow" target="_blank"><i class="fa fa-cog"></i> Разработка сайта</a>
				</div>
			</div>
		</div>
	</footer>





</body>
</html>