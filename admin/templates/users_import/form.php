<html>
<head>
<meta HTTP-EQUIV="content-type" CONTENT="text/html; charset=<?=ADMIN_CHARSET?>">
<meta http-equiv="Expires" content="0">
<meta http-equiv="Cache-Control" content="must-revalidate, post-check=0, pre-check=0">
<meta http-equiv="pragma" content="no-cache">
<link rel=stylesheet href=/admin/css.css type="text/css">
	<title>Шины - Импорт</title>
</head>

<body leftmargin=0 topmargin=0 marginwidth=0 marginheight=0 class=main>

<table width=100% border=0 cellpadding=0 cellspacing=0 class=top3><td><img src=/admin/_img/s.gif width=1 height=1></td></table>
<table width=100% border=0 cellpadding=4 cellspacing=0>
<tr class=top41>
<td height=30 id=pad18 colspan=2 nowrap class=head><b>Данные пользователей - Импорт</b></td>
</tr>
</table>
<table width=100% border=0 cellpadding=0 cellspacing=0 class=top2><td><img src=/admin/_img/s.gif width=1 height=1></td></table>
<table width=100% border=0 cellpadding=4 cellspacing=0>
<tr>
<td colspan=2 id=pad18 class=h4>

<table width=100% border=0 cellpadding=0 cellspacing=0 class=backtab3>
<form action="/admin/users_import.php" method=POST enctype="multipart/form-data">
<input type="hidden" name="do" value="import_file" />
<tr id=back2>
<td class=td2 width=20%><b>Файл импорта <span id=red>*</span></b></td>
<td id=back3>
<div class="errmsg"><?= $msg ?></div><input type="file" name="import_file" value=""></td>
</tr>
<tr id=back2>
<td>&nbsp;</td>
<td id=pad10><input type="submit" value="Импортировать" class=button5></td>
</tr>
</form>
</table>

</td>
</tr></table>

</body>
</html>
