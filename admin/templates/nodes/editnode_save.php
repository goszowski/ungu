<?
$request->setAttribute("active_tab", "editing");
$restrict_edit = $CurrentAdminUser->group->restrictNodeEdit;

$visibleDepends = array();
foreach($depends as $dep) {
	if (!$restrict_edit || $dep->checkFlag("RU_CAN_CREATE")) {
		$visibleDepends[] = $dep;
	}
}
?>

<? usetemplate("nodes/_edit_header") ?>
<?
	$disabledControlIfUserHasNoWriteRights =$request->getAttribute("disabledControlIfUserHasNoWriteRights");
?>
<? if (!$restrict_edit || $nodeClass->checkFlag("RU_CAN_DELETE") || sizeof($visibleDepends) != 0) { ?>
<table width=100% border=0 cellpadding=0 cellspacing=0>
<tr><td id=pad18>

<table width=100% border=0 cellpadding=0 cellspacing=0>
<form action="/admin/nodes.php" method="POST">
<input type="hidden" name="parent_id" value="<?=$node->id?>">
<input type="hidden" name="do" value="addnode_form">
<tr class=top4>
<td height=36 id=pad5 nowrap width=50%>
<? if (!$restrict_edit || $nodeClass->checkFlag("RU_CAN_DELETE")) { ?>
<input type=button class=button1 value="<?=$AdminTrnsl["Move"]?>" onClick="wop('/admin/nodes.php?do=move_node_form&node_id=<?=$node->id?>', 450, 600)"<?=$disabledControlIfUserHasNoWriteRights?>>
<input type=button class=button3 value="<?=$AdminTrnsl["Del"]?>" onClick="if(confirm('<?=$AdminTrnsl["This_will_delete_all_subnodes_Proceed"]?>')) location.href='/admin/nodes.php?do=deletenode&id=<?=$node->id?>'"<?=$disabledControlIfUserHasNoWriteRights?>>
<? } ?>
</td>

<? if (sizeof($visibleDepends) != 0) { ?>
<td id=pad5 nowrap>

<table border=0 cellpadding=0 cellspacing=0 width=1%>
<tr>
<td nowrap width=1%><b><?/*<?=$AdminTrnsl["New_SubNode"]?>:*/?>&nbsp;</b></td>
<td>
	<? foreach($visibleDepends as $dep) { ?>
	<input type=button class=button1 onClick="location.href='/admin/nodes.php?parent_id=<?= $node->id ?>&do=addnode_form&class_id=<?= $dep->id ?>'" value="<?=$AdminTrnsl["CreateSubnode"]?> <?= $dep->name ?>"><br>
	<? } ?>
</table>

</td>
<?}?>
</tr>
</form>
</table>

</td>
</tr>
</table>
<? } ?>

<table width=100% border=0 cellpadding=4 cellspacing=0>
<tr>
<td id=pad18 class=back2>
<? usetemplate("nodes/editnode_in") ?>
</td>
</tr>
</table>
<? if ($childrenCount > 0) { ?>
<table width=100% border=0 cellpadding=4 cellspacing=0>
<tr>
<td id=pad18 class=back2>
<div id="loadingIndicator"><strong>Загрузка...</strong></div>
</td>
</tr>
</table>

<table width=100% border=0 cellpadding=4 cellspacing=0>
<tr><td id=pad18>
<?
$pageNumMap = $request->getParameter("pagenum");
if ($pageNumMap == null) {
	$pageNumMap = array();
}

foreach($childrenByClasses as $item) {
	if (sizeof($item->nodes) == 0) {
		continue;
	}

	$showShortname = !$restrict_edit || $item->nodeClass->checkFlag("USE_SHORTNAME");
	$showNodeName = $item->nodeClass->checkFlag("USE_NODENAME");
	$nodeNameReadonly = $item->nodeClass->checkFlag("NODENAME_READONLY");
	$shownFieldDefs = $item->shownFields; 
?>
<table width=100% border=0 cellpadding=0 cellspacing=0 class=backtab2>
<tr bgcolor=#ffffff>
<td class=td2 colspan=10 id=pad5>

<a name="<?="nodeslist".$item->nodeClass->shortname?>"></a>

<table width=100% border=0 cellpadding=0 cellspacing=0 class=nobacktab>
<td><b><?=$AdminTrnsl["CClass"]?>: <?=$item->nodeClass->name?></b></td>
<? if (!$restrict_edit) { ?>
<td align=right class=small><?=$item->nodeClass->shortname?></td>
<? } ?>
</table>

</td>
</tr>
</table>

<table width=100% border=0 cellpadding=0 cellspacing=0 class=backtab2>
<tr id=back2 class=header>
<? if ($showShortname) { ?>
<td width=1% class=td2><?=$AdminTrnsl["Shortname"]?></td>
<? } ?>
<? if ($showNodeName) { ?>
<td<?if (sizeof($shownFieldDefs) !=0 ) {?> width=40%<?}?>><?=($item->nodeClass->nodeNameLabel ? $item->nodeClass->nodeNameLabel : $AdminTrnsl["Node_name"])?></td>
<? } ?>
<? foreach($shownFieldDefs as $fd) { $fdt = $fd->getFieldType(); ?>
<? if ($fdt->name == "MultiLink") { ?>
<?
$params = array();
$classParams = $fdt->getParametersNames();
foreach ($classParams as $fieldParameterShortname) {
	$params[$fieldParameterShortname] = $fd->getParameterValue($fieldParameterShortname);
}
$rootNode = Node::findByPath($params["root_node_path"]);
$mlfavailableValues = $rootNode->getRecursiveChildrenOfClasses(explode(",", $params["allowed_classes"]));
$mlfCounts = array();
foreach($mlfavailableValues as $mlfNode) {
	$mlfCount = Node::findCountByQuery("SELECT COUNT(n.*) FROM {" . $item->nodeClass->shortname . "} n WHERE n.parent_id=? AND n.".$fd->shortname."=?", array($node->id, $mlfNode->id));
	$mlfCounts[] = $mlfCount;
}
?>
<? for ($z = 0; $z < sizeof($mlfavailableValues); $z++) { $mlfNode = &$mlfavailableValues[$z]; ?>
<td align="center"><?= $mlfNode->name ?>(<?= $mlfCounts[$z] ?>)</td>
<? } ?>
<? } else {?>
<td><?= $fd->name ?></td>
<? } ?>
<? } ?>
<?/* <?if ($item->sort){?><td width=1%><?=$AdminTrnsl["Sort"]?></td><?}?> */?>
<td width=1%><?=$AdminTrnsl["Action"]?></td>
</tr>

<?
$c=0;
$tot=sizeof($item->nodes);
foreach($item->nodes as $child) {
	$c++;
	if (!$child->hasWriteRight) {
		$child_disabledControlIfUserHasNoWriteRights = " disabled=true";
	} else {
		$child_disabledControlIfUserHasNoWriteRights = "";
	}
?>
<tr id=back3>
<? if ($showShortname) { ?>
<td class=td2><?=$child->node->shortname?></td>
<? } ?>
<? if ($showNodeName) { ?>
<td>
<?/* <a href="/admin/nodes.php?do=main&id=<?=$child->node->id?>"> */?>
<?=$child->node->name?>
<?/* </a> */?>
</td>
<? } ?>
<? foreach($shownFieldDefs as $fd) { $fdt = $fd->getFieldType(); ?>
<? if ($fdt->name == "MultiLink") { ?>
<?
$params = array();
$classParams = $fdt->getParametersNames();
foreach ($classParams as $fieldParameterShortname) {
	$params[$fieldParameterShortname] = $fd->getParameterValue($fieldParameterShortname);
}
$linked_node_ids = $child->node->fields[$fd->shortname]->getNodeIds();
$rootNode = Node::findByPath($params["root_node_path"]);
$mlfavailableValues = $rootNode->getRecursiveChildrenOfClasses(explode(",", $params["allowed_classes"]));
?>
<? for ($z = 0; $z < sizeof($mlfavailableValues); $z++) { $mlfNode = &$mlfavailableValues[$z]; $mlfchecked = in_array($mlfNode->id, $linked_node_ids); ?>
<td align="center"><?= $mlfchecked ? "+" : "-" ?></td>
<? } ?>
<? } else {?>
<td><?=$child->node->fields[$fd->shortname]->getHtmlVisualValue() ?></td>
<? } ?>
<? } ?>
<?/* <?if ($item->sort) {?><td nowrap<? if ($c == $tot) { ?> align=right<? } else { ?><input type=button style="width:21px" class=button41><?}?><?if ($c!=1) {?><input type=button style="width:21px" class=button42><?}?></td><?}?> */?>
<td nowrap width=1%>
<input type=button value="<?= $AdminTrnsl["Edit"] ?>" onClick="location.href='/admin/nodes.php?do=editnode&id=<?=$child->node->id?>'" class=button1>
<? if (!$restrict_edit || $item->nodeClass->checkFlag("RU_CAN_DELETE")) { ?>
<input type=button value="<?=$AdminTrnsl["Del"]?>" class=button4 onClick="if(confirm('<?=$AdminTrnsl["This_will_delete_all_subnodes_Proceed"]?>')) location.href='/admin/nodes.php?do=deletenode&id=<?=$child->node->id?>'"<?=$child_disabledControlIfUserHasNoWriteRights?>>
<? } ?>

</td>
<!--<?if(!$restrict_edit){?><input type=button value="<?=$AdminTrnsl["Properties"]?>" class=button1 onclick="wop('/admin/nodes.php?do=props_main&id=<?=$child->node->id?>&return=<?=urlencode("main&id=".$node->id)?>', 500, 400)"<?=$child_disabledControlIfUserHasNoWriteRights?>><?}?> -->
</tr>
<? } ?>
</table>

<? if($item->nodespagesCount > 1) { ?>
<table width=100% border=0 cellpadding=0 cellspacing=0 class=nobacktab>
<tr id=back3>
<td class=td2 colspan=10 id=pad5>
<?
$hiddenNames = array();
$hiddenValues = array();
foreach($pageNumMap as $_k=>$_v) {
	if($_k == $item->nodeClass->shortname) continue;
	$hiddenNames[] = "pagenum[".$_k."]";
	$hiddenValues[] = $_v;
}
$hiddenNames[] = "do";
$hiddenValues[] = "main";
$hiddenNames[] = "id";
$hiddenValues[] = $node->id;

HTMLWriter::draw_paging_links($item->nodesOffset, NODES_PER_PAGE, NODELINKS_PER_PAGE, $item->nodesCount,
	$hiddenNames,
	$hiddenValues,
	"_default_draw_paging_link",
	"#nodeslist".$item->nodeClass->shortname,	
	"pagenum[".$item->nodeClass->shortname."]"
	)?>
</td>
</tr>
</table>
<? } ?>

<? } ?>
<script>
var loadingIndicator = document.getElementById('loadingIndicator');
loadingIndicator.outerHTML = "";
</script>
</td>
</tr>
<? } ?>
</table>

<p>&nbsp;

</body>
</html>
