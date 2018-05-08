<?usetemplate("classes/header")?>
<?usetemplate("_res")?>
<table width=100% border=0 cellpadding=0 cellspacing=0 class=top3><td><img src=/admin/_img/s.gif width=1 height=1></td></table>
<table width=100% border=0 cellpadding=4 cellspacing=0>
<tr class=top41>
<td height=30 id=pad18 colspan=2 nowrap class=head><a href="/admin/classes.php" id=black><?=$AdminTrnsl["Classes_Management"]?></a>: <a href="/admin/classes.php?do=edit&class_id=<?=$class->id?>" id=black><?=$AdminTrnsl["CClass"]?> <?=$class->name?> (<?=$class->shortname?>)</a> : <b><?=$AdminTrnsl["Field"]?> <?=$fieldDef->name?> (<?=$fieldDef->shortname?>): <?=$AdminTrnsl["parameters"]?></b></td>
</tr>
</table>
<table width=100% border=0 cellpadding=0 cellspacing=0 class=top2><td><img src=/admin/_img/s.gif width=1 height=1></td></table>

<table width=100% border=0 cellpadding=4 cellspacing=0>
<tr>
<td id=pad18>

<form action=/admin/classes.php method=post>
<input type="hidden" name="do" value="update_field_params">
<input type=hidden name=class_id value='<?=$class->id?>'>
<input type=hidden name=field_id value='<?=$fieldDef->id?>'>
<table width=100% border=0 cellpadding=2 cellspacing=1 class=backtab>
<tr align=center class=back2>
    <td width=30% height=22><?=$AdminTrnsl["Name"]?></td>
    <td><?=$AdminTrnsl["Value"]?></td>
</tr>
    <?
    	$fdFieldType = $fieldDef->getFieldType();
    	foreach ($fdFieldType->parameterList as $fp) {?><tr class=back3>
    <td align=left><?=$fp->name?></td>
    <td align=left>
    <?if($fp->values_list){?>
    	<select name="p_<?=$fp->shortname?>_value[]">
    		<?foreach($fp->values_list as $fpv=>$fpn){?><option value="<?=$fpv?>"<?if($fpv == $parameterValues[$fp->shortname]){?> SELECTED<?}?>><?=$fpn?></option><?}?>
    	</select>
    <?}else{?>
    	<input type=text name='p_<?=$fp->shortname?>_value[]' value='<?=$parameterValues[$fp->shortname]?>' style="width:100%"></td>
    <?}?>
 </tr><?}?>
<tr class=back2>
<td colspan=2>
<img src=/admin/_img/s.gif width=1 height=8><br>
<input type=submit value="<?=$AdminTrnsl["Update"]?>" style="width:120px" class=but>
</td>
</tr>
</table>

</td>
</tr></table>
</form>
</body>
</html>
