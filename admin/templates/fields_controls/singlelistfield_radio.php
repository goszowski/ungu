<table border=0 cellpadding=0 cellspacing=15 class=nobacktab>
<tr>
<? foreach ($CM_PARAMS["available_values"] as $avail_value) { ?>
	<td><?= $avail_value ?></td>
<? } ?>
</tr>
<tr align="center">
<? $i=0; foreach ($CM_PARAMS["available_values"] as $avail_value) { ?>
	<td><input type="radio" name="<?= $CM_CONTROL_NAME ?>" value="<?= $i ?>"<? if ($CM_PARAMS["value"] == $i){?> checked<? } ?>></td>
<?$i++;}?>
</tr></table>
