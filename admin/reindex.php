<?
require_once('prepend.php');
require_once('../WEB-INF/prepend.php');

$reindex = $request->getParameter("reindex");

if ($reindex == 'all') {
	Node::buildFullTextIndex();
}
?>

<?usetemplate("emails/header");?>

<table width=100% border=0 cellpadding=4 cellspacing=0>
<tr class=top4>
<td height=40 id=pad18 colspan=2 nowrap class=head>Поисковый индекс</td>
</tr>
</table>
<table width=100% border=0 cellpadding=0 cellspacing=0 class=top42><td><img src=/admin/_img/s.gif width=1 height=5></td></table>
<table width=100% border=0 cellpadding=4 cellspacing=0>
<tr><td id=pad18 class=h4>
<div id=pad5><b class=h3>Поисковый индекс</b></div>

<table width=100% border=0 cellpadding=0 cellspacing=0 class=backtab2>
<tr bgcolor=#ffffff>
<td class=td2 colspan=5 id=pad5>

<?if ($reindex) {?>
	Поисковый индекс обновлен успешно.<br>
<?}?>

</td>
</tr>
<tr id=back2 class=header>
<td class=td2>Название</td>
<td width=5%>Действие</td>
</tr>

<tr id=back3>
<td class=td2>&nbsp;Поисковый индекс</td>
<td align=center nowrap>
<input type=button value="Обновить" class=button3 onClick="if(confirm('Вы уверены, что следует обновить весь поисковый индекс? Продолжить?')) location.href='?reindex=all'"></td>
</tr>

</table>

</td>
</tr>
</table>

<?usetemplate("emails/footer");?>