<table border=0 cellpadding=0 cellspacing=0 class=nobacktab>
<tr><td>
<select name="<?= $CM_CONTROL_NAME ?>">
<option value="-1"><?= $CM_PARAMS["any_el_name"] ?></option>
<? $i=0; foreach ($CM_PARAMS["available_values"] as $avail_value) { ?>
<option value="<?= $i ?>"<? if($CM_PARAMS["value"] == $i) { ?> SELECTED<? } ?>><?= $avail_value ?></option>
<? $i++; }?>
</select>
</td>
</tr></table>
