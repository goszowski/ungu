<?php
function addNodeReg($classname, $shortname, $parent_id, $name, $filds)
{
	list($classData) = getSimpleListSQL("SELECT * FROM dbm_classes WHERE shortname='".$classname."' LIMIT 1");
	list($sequencesData) = getSimpleListSQL("SELECT value FROM dbm_sequences WHERE name='nodes' LIMIT 1");
	list($subtreeOrderData) = getSimpleListSQL("SELECT subtree_order FROM dbm_nodes WHERE parent_id=".$parent_id." ORDER BY subtree_order DESC LIMIT 1");
	list($parentNodeData) = getSimpleListSQL("SELECT absolute_path FROM dbm_nodes WHERE id=".$parent_id." LIMIT 1");

	// dbm_nodes

	$class_id = $classData["id"]; // ID класу
	$node_id = 1 + (int)$sequencesData["value"]; // NODE ID
	$dynamic_template = $classData["default_template"];
	$subtree_order = 1 + (int)$subtreeOrderData["subtree_order"];
	$shortname = $shortname;
	$absolute_path = $parentNodeData["absolute_path"]."/".$shortname;
	$owner = 1;
	$time_created = date("Y-m-d H:i:s");
	$time_updated = $time_created;

	// insert into dbm_nodes

	$sql_1 = "
		INSERT INTO dbm_nodes (id, shortname, name, dynamic_template, subtree_order, class_id, parent_id, absolute_path, owner, time_created, time_updated)
		VALUES ('{$node_id}', '{$shortname}', '{$name}', '{$dynamic_template}', '{$subtree_order}', '{$class_id}', '{$parent_id}', '{$absolute_path}', '{$owner}', '{$time_created}', '{$time_updated}')
	";

	// insert into dbm_nfv

	$sql_2 = "INSERT INTO dbm_nfv_".$classname." (node_id";
		foreach($filds as $key=>$fild)
		{
			$sql_2 .= ", " . $key;
		}
	$sql_2 .= ") VALUES('".$node_id."'";
		foreach($filds as $key=>$fild)
		{
			$sql_2 .= ", '" . $fild . "'";
		}
	$sql_2 .= ")";

	// update counter

	$sql_3 = "UPDATE dbm_sequences SET value='{$node_id}' WHERE name='nodes'";

	// add group rights 1

	$sql_4 = "INSERT INTO dbm_group_rights (group_id, node_id, rights) VALUES ('1', '{$node_id}', '3')";

	// add group rights 2

	$sql_5 = "INSERT INTO dbm_group_rights (group_id, node_id, rights) VALUES ('2', '{$node_id}', '3')";

	// submission

	$sql_6 = "INSERT INTO dbm_nodes_submission (child_id, parent_id, level_diff) VALUES ('{$node_id}', '{$parent_id}', '1')";

	//submission

	$sql_7 = "INSERT INTO dbm_nodes_submission (child_id, parent_id, level_diff) VALUES ('{$node_id}', '1', '2')";

	mysql_query($sql_1) or die("addNode_1: ".mysql_error());
	mysql_query($sql_2) or die("addNode_2: ".mysql_error());
	mysql_query($sql_3) or die("addNode_3: ".mysql_error());
	mysql_query($sql_4) or die("addNode_4: ".mysql_error());
	mysql_query($sql_5) or die("addNode_5: ".mysql_error());
	mysql_query($sql_6) or die("addNode_6: ".mysql_error());
	mysql_query($sql_7) or die("addNode_7: ".mysql_error());

	return $node_id;
}
?>