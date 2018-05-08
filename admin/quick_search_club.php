<html>
<head>
<meta HTTP-EQUIV="content-type" CONTENT="text/html; charset=<?=ADMIN_CHARSET?>">
<meta http-equiv="Expires" content="0">
<meta http-equiv="Cache-Control" content="must-revalidate, post-check=0, pre-check=0">
<meta http-equiv="pragma" content="no-cache">
<title></title>
<link rel=stylesheet href=/admin/css.css type="text/css">

<script type="text/javascript" src="/_js/jquery.js"></script>
<script type="text/javascript" src="/_tools/autocomplete/jquery.autocomplete.js"></script>
<link rel="stylesheet" type="text/css" href="/_tools/autocomplete/jquery.autocomplete.css" />

</head>

<body leftmargin=0 topmargin=0 marginwidth=0 marginheight=0 class=main>

<table width=100% border=0 cellpadding=4 cellspacing=0>
<tr class=top4>
<td height=40 id=pad18 colspan=2 class=head>
Quick Search Club
</td>
</tr>
</table>

<table width=100% border=0 cellpadding=0 cellspacing=0 class=top42><td><img src=/admin/_img/s.gif width=1 height=5></td></table>

<script>
$().ready(function() {

	$("#search_club").autocomplete("/request.php?do=clubs_countries_admin", {
		width: 360,
		minChars: 2,
		selectFirst: true
	}).result(function(event, item) {
		location.href = item[1];
	});
	
	
});
</script>

<table width=100% border=0 cellpadding=4 cellspacing=0>
<td id=pad18 class=h4>
<br>
<input type="text" id="search_club">
</td>
</table>

</body>
</html>