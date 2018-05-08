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
<? foreach ($availableValues as $avail_value) { ?>
	<? if ($CM_PARAMS["linked_node_id"] == $avail_value->id) { ?> 
	<?//=$avail_value->name?> 
	<a href="/admin/nodes.php?do=main&amp;id=<?=$CM_PARAMS["linked_node_id"]?>"><?=$CM_PARAMS["linked_node_name"]?></a>
	<? } ?>
<? } ?>