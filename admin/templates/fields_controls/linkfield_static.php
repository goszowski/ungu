<table border=0 cellpadding=0 cellspacing=0 class=nobacktab>
<tr><td>
<input type="hidden" name="<?= $CM_CONTROL_NAME ?>" value="<?= $CM_PARAMS["db_value"] ?>">
<? if ($CM_PARAMS["db_value"]) { ?>
<a href="<?= $CM_PARAMS["linked_node_admin_href"] ?>"><?= $CM_PARAMS["linked_node_name"] ?></a>
<? } ?>
<? /*
<a href="<?= $CM_PARAMS["linked_node_id"] ?>">
*/ ?>
</td></tr>
</table>