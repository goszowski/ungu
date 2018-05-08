<table border=0 cellpadding=0 cellspacing=0 class=nobacktab>
<tr><td><select class="form-control input-sm" name="<?=$CM_CONTROL_NAME?>">
<? $i=0;foreach ($CM_PARAMS["available_values"] as $avail_value) {?>
<option value="<?=$i?>"<?if(sizeof($CM_PARAMS["values"])!=0 && $CM_PARAMS["values"][0] == $i){?> SELECTED<?}?>><?=$avail_value?><?$i++;}?>
</select></td>
<?if ($CM_PARAMS["haveOther"]){?><td align=center><?=$CM_PARAMS["other_name"]?></td><?}?>
<?if ($CM_PARAMS["haveOther"]){?><td><input type=text name=<?=$CM_CONTROL_NAME?> value="<?=$CM_PARAMS["other"]?>"></td><?}?>
</tr></table>
