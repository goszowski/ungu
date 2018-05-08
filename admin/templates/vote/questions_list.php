<html>
<head><meta HTTP-EQUIV="content-type" CONTENT="text/html; charset=windows-1251">
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
<td height=30 id=pad18 colspan=2 nowrap class=head><b>Голосование</b></td>
</tr>
</table>

<div id=pad5>
<?$msg = $request->getParameter("msg"); if ($msg != null) {?><span id="red"><?=$AdminTrnsl[$msg]?></span><?}?>&nbsp;</div>

<table width=100% border=0 cellpadding=4 cellspacing=0>
<tr><td id=pad18>
<form action="/admin/vote.php" method=POST>
<input type=hidden name=do value=createquestion>

<div id=pad5><b class=h3>Новый опрос:</b></div>

<table width=100% border=0 cellpadding=0 cellspacing=0 class=backtab3 id=back2>
<td><nobr><b>Вопрос:</nobr>&nbsp;</td>
<td width=80% id=back3 height=28><input type=text name=question value="<?= prepareStringForHtml($request->getParameter("question")) ?>" style="width:400px"></td>
</tr>
<tr id=back2>
<td>&nbsp;</td>
<td id=pad10>
<input type=submit class=button5 value="Создать"></td>
</tr>
</table>

</form>

<table width=100% border=0 cellpadding=0 cellspacing=0 class=top3><td><img src=/admin/_img/s.gif width=1 height=1></td></table>
<br>
<div id=pad5><b class=h3>Список опросов:</b></div>

<?
	$questions = Question::findAll();
?>

<?if (sizeof($questions)>0){?>

<table width=100% border=0 cellpadding=0 cellspacing=0 class=backtab2>
<tr id=back2 class=header>
<td width=40% class=td2 nowrap>Вопрос</td>
<td width=1% nowrap>Кол-во ответов</td>
<td width=1% nowrap>Кол-во ответов (реальное)</td>
<td width=1% nowrap>Активный</td>
<td width=1% nowrap>Действие</td>
</tr>
<?
for ($i=0; $i < sizeof($questions); $i++) { $question = &$questions[$i];
?>
<tr id=back3>
<td><a href="/admin/vote.php?do=edit_question&id=<?=$question->id?>"><?=$question->question?></a></td>
<td><?= $question->getTotalVotesCount() ?></td>
<td><?= $question->getTotalRealVotesCount() ?></td>
<td nowrap><?= $question->isActive ? "АКТИВНЫЙ" : "&nbsp;" ?></td>
<td nowrap><input type=button value="Удалить" onClick="location.href='/admin/vote.php?do=remove&id=<?=$question->id?>'" class=button4>
<? if (!$question->isActive) { ?><input type=button value="Сделать активным" onClick="location.href='/admin/vote.php?do=set_active&id=<?=$question->id?>'" class=button1><? } else {?>&nbsp;<? } ?>
</td>
</tr>
<? } ?>
</table>
<?}else{?>
Нет опросов
<?}?>
</td>
</tr>
</table>

<p>&nbsp;

</body>
</html>
