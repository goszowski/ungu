<?
$MSG = null;
$rmsg = $request->getParameter("MSG");
if ($rmsg) {
	$MSG = $AdminTrnsl[$rmsg];
}
?>
<? usetemplate("classes/header") ?>
<? usetemplate("_res") ?>
<??>
<table width=100% border=0 cellpadding=0 cellspacing=0 class=top3><td><img src=/admin/_img/s.gif width=1 height=1></td></table>
<table width=100% border=0 cellpadding=4 cellspacing=0>
<tr class=top41>
<td height=30 id=pad18 colspan=2 nowrap class=head><b><?=$AdminTrnsl["Classes_Management"]?></b></td>
</tr>
<?if ($MSG != null) {?><tr>
<td id=pad18><span id="red"><?=$MSG?></span></td>
</tr><?}?>
</table>
<table width=100% border=0 cellpadding=0 cellspacing=0 class=top2><td><img src=/admin/_img/s.gif width=1 height=1></td></table>
<table width=100% border=0 cellpadding=4 cellspacing=0>
<tr class=top4>
<td height=36 id=pad18 nowrap><input type=submit value="<?=$AdminTrnsl["Create_New_Class"]?>" title="" style="width:150px" class=button1 onClick="location.href='/admin/classes.php?do=create_form';">
</tr>
<tr>
<td id=pad18>

<br>
<table width=100% border=0 cellpadding=0 cellspacing=0 class=backtab2>
<tr id=back2 class=header>
<td width=1%>ID</td>
<td width=70%><a href="/admin/classes.php?sortby=name"><?=$AdminTrnsl["CClass"]?></a></td>
<td width=1%><?=$AdminTrnsl["NODECLASS_FLAG_CACHE"]?></td>
<td width=1%><a href="/admin/classes.php?sortby=shortname"><?=$AdminTrnsl["Shortname"]?></a></td>
<td><?=$AdminTrnsl["Actions"]?></td>
</tr>
<? foreach ($classes as $nc) { ?>
<tr id=back3>
<td><?= $nc->id ?></td>
<td ><a href=/admin/classes.php?do=edit&class_id=<?=$nc->id?>><?= $nc->name ?></a></td>
<td><?= $nc->checkFlag("CACHE") ? $AdminTrnsl["Yes"] : $AdminTrnsl["No"] ?></td>
<td><?= $nc->shortname ?></td>
<td nowrap align=center>
	<input type=button value="<?=$AdminTrnsl["Class_Dependences"]?>" onclick="document.location.href='/admin/classes.php?do=depends&class_id=<?=$nc->id?>'" class=button1>
	<input type=button value="<?=$AdminTrnsl["Delete"]?>" onclick='if(confirm("<?=$AdminTrnsl[Warning_All_children_of_this_class_will_be_removed_to]?>")) document.location.href="/admin/classes.php?do=delete&class_id=<?=$nc->id?>"' class=button3>
</td>
</tr><?}?>
</table>
<br>
</td>
</tr>
<form action="/admin/classes.php" method=POST>
<input type="hidden" name="do" value="create_form">
<tr class=top4>
<td height=36 id=pad18 nowrap><input type=submit value="<?=$AdminTrnsl["Create_New_Class"]?>" title="" style="width:150px" class=button1>
</tr>
</form>
</table>
</body>
</html>