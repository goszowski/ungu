<?usetemplate("classes/header")?>
<?usetemplate("_res")?>
<table width=100% border=0 cellpadding=0 cellspacing=0 class=top3><td><img src=/admin/_img/s.gif width=1 height=1></td></table>
<table width=100% border=0 cellpadding=4 cellspacing=0>
<tr class=top41>
<td height=30 id=pad18 colspan=2 nowrap class=head><a href="/admin/classes.php" id=black><?=$AdminTrnsl["Classes_Management"]?></a>: <b><?=$AdminTrnsl["Creating_New_Class"]?></b></td>
</tr>
</table>
<table width=100% border=0 cellpadding=0 cellspacing=0 class=top2><td><img src=/admin/_img/s.gif width=1 height=1></td></table>
<table width=100% border=0 cellpadding=4 cellspacing=0>
<?if ($MSG != null) {?><tr>
<td id=pad18><span id="red"><?=$MSG?></span></td>
</tr><?}?>
<tr>
<td id=pad18>

<form action=/admin/classes.php method=post>
<input type="hidden" name="do" value="create">
<br>

<table width=100% border=0 cellpadding=0 cellspacing=0 class=backtab3>

<tr id=back2>
<td id=pad5><nobr><b><?=$AdminTrnsl["Name"]?></b> <span id=red>*</span></nobr></td>
<td id=back3 width=80%>
<input type=text name=name style="width:495px" value="<?=htmlrparamOut("name")?>"></textarea>
<?if ($ERRORS['name'] != null) {?><div class=errmsg><?= $AdminTrnsl["CLASS_EDIT_ERROR_".$ERRORS['name']]?></div><?}?></td>
</tr>

<tr id=back2>
<td id=pad5><b><?=$AdminTrnsl["Shortname"]?></b> <span id=red>*</span></td>
<td id=back3 width=80%><input type=text name=shortname value='<?=htmlrparamOut("shortname")?>' size=32  style="width:495px">
<?if ($ERRORS['shortname'] != null) {?><div class=errmsg><?=$AdminTrnsl["CLASS_EDIT_ERROR_".$ERRORS['shortname']]?></div><?}?></td>
</tr>

<tr id=back2>
<td id=pad5><b><?=$AdminTrnsl["Default_template"]?></b></td>
<td id=back3 width=80%><input type=text name=default_template value='<?=htmlrparamOut("default_template")?>' size=32  style="width:495px"></td>
</tr>

<tr id=back2>
<td id=pad5><b><?=$AdminTrnsl["Nodename_label"]?></b>&nbsp;</td>
<td id=back3 width=80%><input type="text"  name=nodename_label value='<?=htmlrparamOut("nodename_label ")?>' size=32  style="width:495px"></td>
</tr>

<tr id=back2>
<td id=pad5><b><?=$AdminTrnsl["Nodeclass_orderbyfield"]?></b>&nbsp;</td>
<td id=back3 width=80%><input type="text" name="orderby" value='<?=htmlrparamOut("orderby ")?>' size=32 style="width:495px"></td>
</tr>

<tr id=back2>
<td id=pad5>&nbsp;</td>
<td id=back3 width=80%><input type=checkbox name=show_at_adt value=1 <?if ($request->getParameter("show_at_adt") == 1) {?> CHECKED<?}?>>&nbsp;<?=$AdminTrnsl["ShowNodesAtAdminInterface"]?></td>
</tr>
<? global $NODECLASS_FLAG_NAMES; ?>
<? foreach ($NODECLASS_FLAG_NAMES as $flagName) { ?>
<tr id=back2>
<td id=pad5>&nbsp;</td>
<td id=back3><input type=checkbox name=flag_<?= $flagName ?> value=1 <?if ($request->getParameter("flag_" . $flagName) == 1) {?> CHECKED<?}?>>
<?= $AdminTrnsl["NODECLASS_FLAG_" . $flagName] ?></td>
</tr>
<? } ?>
<tr id=back2>
<td>&nbsp;</td>
<td id=pad10>
<input type=submit name=submit value="<?=$AdminTrnsl["Create_Class"]?>" size=32 style="width:150px" class=button5></td>
</tr>
</form>
</table>


</td>
</tr>
</table>

</body>
</html>
