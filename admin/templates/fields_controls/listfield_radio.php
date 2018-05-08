<table border=0 cellpadding=0 cellspacing=5>
<tr>
<? foreach ($CM_PARAMS["available_values"] as $avail_value) {?>
	<td class="pr-5"><?=$avail_value?></td>
<?}?>
</tr><?if ($CM_PARAMS["haveOther"]){?><td align=center><?=$CM_PARAMS["other_name"]?></td><?}?>
<tr align="center">
<? $i=0;foreach ($CM_PARAMS["available_values"] as $avail_value) {?>
	<td class="pr-5">
		<label class="ui-checks">
			<input type="radio" name="<?=$CM_CONTROL_NAME?>" value="<?=$i?>"<?if(sizeof($CM_PARAMS["values"])!=0 && $CM_PARAMS["values"][0] == $i){?> checked<?}?>>
			<i></i>
		</label>
	</td>
<?$i++;}?>
<?if ($CM_PARAMS["haveOther"]){?><td><input type=text name=<?=$CM_CONTROL_NAME?> value="<?=$CM_PARAMS["other"]?>"></td><?}?>
</tr></table>
