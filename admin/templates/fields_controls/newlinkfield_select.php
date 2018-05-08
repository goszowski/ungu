<?
if (strpos($CM_PARAMS["root_node_path"], "{lang}")) {
	if (!$node) {
		$_node = Node::findById($parent_id);
		$lang_pref = explode("/", $_node->absolutePath);
	} else {
		$lang_pref = explode("/", $node->absolutePath);
	}
	$CM_PARAMS["root_node_path"] = str_replace("{lang}", $lang_pref[1], $CM_PARAMS["root_node_path"]);
}
$rootNode = Node::findByPath($CM_PARAMS["root_node_path"]);

$availableValues = $rootNode->getRecursiveChildrenOfClasses(explode(",", $CM_PARAMS["allowed_classes"]));
?>
<table border=0 cellpadding=0 cellspacing=0 class=nobacktab>
<tr><td>
<select name="<?= $CM_CONTROL_NAME ?>" class="form-control input-sm">
<option value="">- - -
<? foreach ($availableValues as $avail_value) { ?>
<option value="<?=$avail_value->id?>"<? if ($CM_PARAMS["linked_node_id"] == $avail_value->id) { ?> SELECTED<? } ?>><?=$avail_value->name?>
<? } ?>
</td></tr>
</table>