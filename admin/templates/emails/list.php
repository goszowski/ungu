<html>
<head><meta HTTP-EQUIV="content-type" CONTENT="text/html; charset=<?=ADMIN_CHARSET?>">
<title></title>
<link rel=stylesheet href=/admin/css.css type="text/css">
<script type="text/javascript" src="/admin/js/refresh_tree.js"></script>
<script type="text/javascript" src="/admin/js/wysiwyg.js"></script>
<script language="JavaScript">
    function wop(url, w, h) {
        w = window.open(url,'node_props','resizable=yes,toolbar=no,location=no,directories=no,status=no,menubar=no,width='+w+', height='+h+',scrollbars=yes,fullscreen=no,top=100, left=100');
		w.focus();
    }
</script>
</head>

<body leftmargin=0 topmargin=0 marginwidth=0 marginheight=0 class=main>

<table width=100% border=0 cellpadding=0 cellspacing=0 class=top3><td><img src=/admin/_img/s.gif width=1 height=1></td></table>

<table width=100% border=0 cellpadding=4 cellspacing=0>
<tr class=top41>
<td height=30 id=pad18 colspan=2 nowrap class=head><b><?=$AdminTrnsl["Saved emails"]?></b></td>
</tr>
</table>

<table width=100% border=0 cellpadding=4 cellspacing=0>
<tr><td id=pad18>
<div id=pad5><b class=h3><?=$AdminTrnsl["Saved emails list"]?>:</b></div>

<br>
<a href="?do=addform">Создать рассылку</a>
<br><br><br>

<table width=100% border=0 cellpadding=0 cellspacing=0 class=backtab2>
<tr id=back2 class=header>
<td width=45%><?=$AdminTrnsl["Subject"]?></td>
<td width=10%>Дата создания</td>
<td width=10%>Дата посл. модификации</td>
<td width=10%><?=$AdminTrnsl["Action"]?></td>
</tr>
<? foreach($saved_emails as $email) {
?>
<tr id=back3>
<td><a href="?do=edit&id=<?=$email->id?>"><?=$email->name?></a></td>
<td><?=$email->timeCreated->format("d.m.Y h:i") ?></td>
<td><?=$email->timeUpdated->format("d.m.Y h:i") ?></td>
<td><input type=button value="<?=$AdminTrnsl["Delete"]?>" onClick="location.href='?do=delete&id=<?=$email->id?>'" class=button4></td>
</tr>
<? } ?>
</table>
</td>
</tr>
</table>

<p>&nbsp;

</body>
</html>
