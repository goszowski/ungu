<?usetemplate("forms/header")?>
<table width=100% border=0 cellpadding=0 cellspacing=0 class=top3><td><img src=/admin/_img/s.gif width=1 height=1></td></table>
<table width=100% border=0 cellpadding=4 cellspacing=0>
<tr class=top41>
<td height=30 id=pad18 colspan=2 nowrap class=head><a href="/admin/forms.php" id=black><?=$AdminTrnsl["Forms_Management"]?></a>: <b><?=$AdminTrnsl["Edit_Form"]?></b></td>
</tr>
</table>
<table width=100% border=0 cellpadding=0 cellspacing=0 class=top2><td><img src=/admin/_img/s.gif width=1 height=1></td></table>
<table width=100% border=0 cellpadding=4 cellspacing=0>
<?if ($MSG != null) {?><tr>
<td id=pad18><span id="red"><?=$MSG?></span></td>
</tr><?}?>
<form action=/admin/forms.php method=post>
<input type="hidden" name="do" value="update">
<input type=hidden name=form_id value='<?=$formDef->id?>'>
<tr>
<td id=pad18>
<br>
<table border=0 cellpadding=0 cellspacing=0 class=backtab3>
<tr id=back2>
<td width=250><nobr><b><?=$AdminTrnsl["Name"]?></b> <span id=red>*</span></nobr></td>
<td id=back3><input type=text name=name value="<?=prepareStringForXML($formDef->name)?>" size=45>
<?if ($ERRORS['name'] != null) {?><div class=errmsg><?= $AdminTrnsl["FORM_EDIT_ERROR_".$ERRORS['name']]?></div><?}?></td>
</tr>


<tr id=back2>
<td id=pad5><nobr><b><?=$AdminTrnsl["FormDescrText"]?></b></nobr></td>
<td id=back3 width=80%>
<textarea name=description_text style="width:495px"><?=prepareStringForXML($formDef->description_text)?></textarea>
<?if ($ERRORS['description_text'] != null) {?><div class=errmsg><?= $AdminTrnsl["FORM_EDIT_ERROR_".$ERRORS['description_text']]?></div><?}?></td>
</tr>

<tr id=back2>
<td id=pad5><nobr><b><?=$AdminTrnsl["FormSuccessText"]?></b></nobr></td>
<td id=back3 width=80%>
<textarea name=success_text style="width:495px"><?=prepareStringForXML($formDef->success_text)?></textarea>
<?if ($ERRORS['success_text'] != null) {?><div class=errmsg><?= $AdminTrnsl["FORM_EDIT_ERROR_".$ERRORS['success_text']]?></div><?}?></td>
</tr>


<tr id=back2>
<td><?=$AdminTrnsl["Forms_inc_sub"]?>:&nbsp;</td>
<td id=back3><input type=checkbox name=inc_sub value=1 <?if ($formDef->inc_sub) {?> CHECKED<?}?>></td>
</tr>

<tr id=back2>
<td>&nbsp;</td>
<td id=back3><input type=submit value="<?=$AdminTrnsl["Change"]?>" style="width:120px" class=button1></td>
</tr>
</table>

</td>
</tr>
</form>
<tr>
<td id=pad18>

<?if($fieldDefs){?>
<div id=pad5><b class=h3><?=$AdminTrnsl["Field_list"]?>:</b></div>
<table width=100% border=0 cellpadding=0 cellspacing=0 class=backtab2>
<tr align=center class=header id=back2>
<td width=5%><?=$AdminTrnsl["Form_Field_Sort"]?></td>
<td width=25%><?=$AdminTrnsl["Name"]?></td>
<td width=20%><?=$AdminTrnsl["Type"]?></td>
<td width=10%><?=$AdminTrnsl["Required"]?></td>
<td width=10%><?=$AdminTrnsl["Action"]?></td>
</tr>

<script>
function doFieldForm(id, action) {
	var f=document.forms['field_update_' + id];
	f['do'].value = action;
	f.submit();
}
</script>
<? foreach ($fieldDefs as $fd) {?>
<form action=/admin/forms.php method=post name="field_update_<?=$fd->id?>">
<input type=hidden name=do value=''>
<input type=hidden name=form_id value='<?=$formDef->id?>'>
<input type=hidden name=field_id value='<?=$fd->id?>'>
<tr id=back3>
<td nowrap>&nbsp;<input type=button value="" style="width:21px" class=button41 onClick="doFieldForm('<?=$fd->id?>', 'movedown_field')"><input type=button value="" style="width:21px" class=button42 onClick="doFieldForm('<?=$fd->id?>', 'moveup_field')">&nbsp;</td>
<td><input type=text name=name style="width:200" value="<?=prepareStringForXML($fd->name)?>">
<?if ($FERRORS[$fd->id]['name'] != null) {?><div class=errmsg><?=$AdminTrnsl["FORM_EDIT_ERROR_".$FERRORS[$fd->id]['name']]?></div><?}?></td>
<td><select name=type_shortname style="width:120"><?forEach ($fieldTypes as $ft) {?><option value="<?=$ft->shortname?>"<?if ($ft->shortname == $fd->type_shortname) {?> SELECTED<?}?>><?=$ft->name?><?}?></select></td>
<td align=center><input type=checkbox name=required value=1<?if ($fd->required) {?> CHECKED<?}?>></td>
<td align=center nowrap>
<input type=button value="<?=$AdminTrnsl["Update"]?>" class=button1 onClick="doFieldForm('<?=$fd->id?>', 'update_field')">
<input type=button value="<?=$AdminTrnsl["Del"]?>" class=button4 onClick="doFieldForm('<?=$fd->id?>', 'remove_field')"></td>
</tr>
</form><?}?>

</tr>
</table>
<?}?>
</td>
</tr>
<tr>
<td id=pad18>

<div id=pad5><b class=h3><?=$AdminTrnsl["Add_new_field"]?>:</b></div>

<table width=100% border=0 cellpadding=0 cellspacing=0 class=backtab2>
<form action=/admin/forms.php method=post>
<input type="hidden" name="do" value="add_field">
<input type=hidden name=form_id value='<?=$formDef->id?>'>
<tr align=center class=header id=back2>
<td><?=$AdminTrnsl["Name"]?></td>
<td width=20%><?=$AdminTrnsl["Type"]?></td>
<td width=10%><?=$AdminTrnsl["Required"]?></td>
</tr>
<tr id=back3>
<td>
<?if ($AFERRORS['name'] != null) {?><div class=errmsg><?=$AdminTrnsl["FORM_EDIT_ERROR_".$AFERRORS['name']]?></div><?}?>
<input type=text style="width:100%" name=name value="<?=$nfparams->name?>">
</td>
<td><select name=type_shortname style="width:100%"><?foreach ($fieldTypes as $ft) {?><option value="<?=$ft->shortname?>"<?if ($ft->shortname == $nfparams->type_shortname) {?> SELECTED<?}?>><?=$ft->name?><?}?></select></td>
<td align=center><input type=checkbox name=required value=1 <?if ($nfparams->required == 1) {?> CHECKED<?}?>></td>
</tr>

<tr id=back3>
<td colspan=5 id=pad5>
<input type=submit value="<?=$AdminTrnsl["Add"]?>" style="width:120px" class=button5>
</td>
</tr>
</form>
</table>

</td>
</tr></table>

</body>
</html>
