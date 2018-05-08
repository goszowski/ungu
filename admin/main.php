<?include("prepend.php")?>
<html>
<head><meta HTTP-EQUIV="content-type" CONTENT="text/html;charset=<?=ADMIN_CHARSET?>">
<title></title>
<link rel=stylesheet href=/admin/css.css type="text/css">
<link rel="stylesheet" type="text/css" href="/admin/app.min.css">
</head>

<body leftmargin=0 topmargin=0 marginwidth=0 marginheight=0 class=main>
<table width=100% border=0 cellpadding=4 cellspacing=0>
<tr class=top1>
<td height=20 class=small id=pad18></td>
<td align=right class=small id=pad18>&nbsp;</td>
</tr>
<tr class=top2>
<td height=20 id=pad18 class=small></td>
<td align=right class=small id=pad18>User <b id=red><?= $CurrentAdminUser->login ?></b>&nbsp;|&nbsp;<a href="/admin/logout.php" target="_top">logout</a></td>
</tr>
</table>

<table width=100% border=0 cellpadding=4 cellspacing=0>
<tr>
<td id=pad18>
<!-- head -->
<img src=/_img/s.gif width=1 height=5><br>
<br><br>
<form name="goForm" action="test" method=POST><input type="hidden" name="_login" value="<?=$CurrentAdminUser->login?>"><input type="hidden" name="_password" value="<?=$CurrentAdminUser->password?>"></form>
<div class=head>
</div>
</tr>
</table>

</body>
</html>
