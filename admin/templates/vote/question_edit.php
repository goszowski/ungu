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
<td height=30 id=pad18 colspan=2 nowrap class=head><b><a href="/admin/vote.php">Голосование</a></b></td>
</tr>
</table>

<div id=pad5>
<?$msg = $request->getParameter("msg"); if ($msg != null) {?><span id="red"><?=$AdminTrnsl[$msg]?></span><?}?>&nbsp;</div>

<table width=100% border=0 cellpadding=4 cellspacing=0>
<tr><td id=pad18>
<form action="/admin/vote.php" method=POST>
<input type=hidden name=do value=update_question>
<input type=hidden name=id value=<?= $question->id ?>>

<table width=100% border=0 cellpadding=0 cellspacing=0 class=backtab3 id=back2>
<tr><td><nobr><b>Вопрос:</nobr>&nbsp;</td>
<td width=90% id=back3 height=28><input type=text name=question value="<?= prepareStringForHtml($question->question) ?>" style="width:400px"></td>
</tr>

<tr><td><nobr><b>Ответы:</nobr>&nbsp;</td>
<td width=90% id=back3>

<?
$totalVotesCount = $question->getTotalVotesCount();
$totalRealVotesCount = $question->getTotalRealVotesCount();
?>
Кол-во ответов: <?= $question->getAnswersCount() ?><br>
Общее кол-во голосов: <?= $totalVotesCount ?><br>
Общее кол-во реально проголосовавших: <?= $totalRealVotesCount ?><br>
<br>
<? if (sizeof($question->answers) > 0) { ?>
<table width=100% border=0 cellpadding=0 cellspacing=0 class=backtab2>
<tr id=back2 class=header>
<td class=td2 nowrap>Ответ</td>
<td width=1% nowrap>Кол-во ответов</td>
<td width=1% nowrap>Кол-во ответов (реальное)</td>
<td width=1% nowrap>Действие</td>
</tr><?
for ($i=0; $i < sizeof($question->answers); $i++) {
	$answer = $question->answers[$i];
?>
<tr id=back3>
<td><input type=text name="answers[]" value="<?= prepareStringForHtml($answer) ?>" style="width:300px"></td>
<td><input type=text name="answer_vote_counts[]" value="<?= $question->answer_vote_counts[$i] ?>" style="width:30px"></td>
<td><?= $question->answer_vote_real_counts[$i] ?></td>
<td nowrap><input type=button value="Удалить" onClick="location.href='/admin/vote.php?do=removeanswer&id=<?=$question->id?>&number=<?=$i?>'" class=button4></td>
</tr>
<? } ?>
</table>
<? } ?>

</td>
</tr>


<tr id=back2>
<td>&nbsp;</td>
<td id=pad10>
<input type=submit class=button5 value="Сохранить изменения"></td>
</tr>
</table>

</form>

<form action="/admin/vote.php" method=POST>
<input type=hidden name=do value=addanswer>
<input type=hidden name=id value=<?= $question->id ?>>

<div id=pad5><b class=h3>Новый ответ:</b></div>

<table width=100% border=0 cellpadding=0 cellspacing=0 class=backtab3 id=back2>
<td><nobr><b>Ответ:</nobr>&nbsp;</td>
<td width=80% id=back3 height=28><input type=text name=answer value="<?= prepareStringForHtml($request->getParameter("answer")) ?>" style="width:400px"></td>
</tr>
<tr id=back2>
<td>&nbsp;</td>
<td id=pad10>
<input type=submit class=button5 value="Создать"></td>
</tr>
</table>

</form>

</td>
</tr>
</table>

<p>&nbsp;

</body>
</html>
