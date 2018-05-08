<?
$rootNode = &Node::findByPath($CM_PARAMS["root_node_path"]);
$availableValues = $rootNode->getRecursiveChildrenOfClasses(explode(",", $CM_PARAMS["allowed_classes"]));
?>
<table border=0 cellpadding=0 cellspacing=0 class=nobacktab>
<tr><td>
<select name="<?= $CM_CONTROL_NAME ?>" class="form-control input-sm">
<option value="">- - -
<? foreach ($availableValues as $avail_value) { ?>
<option value="<?=$avail_value->absolutePath?>"<? if ($CM_PARAMS["linked_node_path"] == $avail_value->absolutePath) { ?> SELECTED<? } ?>><?=$avail_value->name?>
<? } ?>
</td></tr>
</table>