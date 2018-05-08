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

<div class="p-md pt-clear">
	<div class="p b-a no-b-t bg-white m-b">
<?
	$disabledControlIfUserHasNoWriteRights =$request->getAttribute("disabledControlIfUserHasNoWriteRights");
?>
<? if (!$restrict_edit || $nodeClass->checkFlag("RU_CAN_DELETE") || sizeof($visibleDepends) != 0) { ?>




	<form action="/admin/nodes.php" method="POST" class="form-horizontal">
		
			<input type="hidden" name="parent_id" value="<?=$node->id?>">
			<input type="hidden" name="do" value="addnode_form">

			<div class="clearfix">
				<?if(!$restrict_edit or $nodeClass->checkFlag("RU_CAN_DELETE")):?>
				<div class="pull-right">
					<input type=button class="btn btn-xs btn-primary" value="<?=$AdminTrnsl["Move"]?>" onClick="wop('/admin/nodes.php?do=move_node_form&node_id=<?=$node->id?>', 450, 600)"<?=$disabledControlIfUserHasNoWriteRights?>>
					<input type=button class="btn btn-xs btn-danger" value="<?=$AdminTrnsl["Del"]?>" onClick="if(confirm('<?=$AdminTrnsl["This_will_delete_all_subnodes_Proceed"]?>')) location.href='/admin/nodes.php?do=deletenode&id=<?=$node->id?>'"<?=$disabledControlIfUserHasNoWriteRights?>>
				</div>
				<?endif;?>

				<?if(sizeof($visibleDepends) != 0):?>
				<div class="pull-left">
					<div class="btn-group" role="group">
						<button type="button" class="btn btn-xs btn-primary dropdown-toggle" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false"><i class="fa fa-plus"></i> <?=$AdminTrnsl["CreateSubnode"]?> <span class="caret"></span></button>
						<ul class="dropdown-menu">
							<?foreach($visibleDepends as $dep):?>
							<li><a href="/admin/nodes.php?parent_id=<?=$node->id?>&amp;do=addnode_form&amp;class_id=<?=$dep->id?>" ><?= $dep->name ?></a></li>
							<?endforeach;?>
						</ul>
					</div>
					
				</div>
				<?endif;?>
			</div>



	</form>

<? } ?>

<? usetemplate("nodes/editnode_in") ?>

	</div>
</div>



<?if ($childrenCount > 0):?>

<div class="p-md pt-clear">

	<ul class="nav nav-sm nav-tabs" role="tablist">
	<?foreach($childrenByClasses as $k=>$item):?>
		<li class="<?if($k==0):?>active<?endif;?>"><a href="#tab_<?=$item->nodeClass->shortname?>" aria-controls="tab_<?=$item->nodeClass->shortname?>" role="tab" data-toggle="tab"><?=$item->nodeClass->name?></a></li>
	<?endforeach;?>
	</ul>

	<div class="p b-a no-b-t bg-white m-b tab-content">

	<?php 
	$pageNumMap = $request->getParameter("pagenum");
	if($pageNumMap == null) $pageNumMap = array();
	?>

	<?foreach($childrenByClasses as $k=>$item):?>
		<?
		$showShortname = !$restrict_edit || $item->nodeClass->checkFlag("USE_SHORTNAME");
		$showNodeName = $item->nodeClass->checkFlag("USE_NODENAME");
		$nodeNameReadonly = $item->nodeClass->checkFlag("NODENAME_READONLY");
		$shownFieldDefs = $item->shownFields; 
		?>

		<div class="tab-pane <?if($k==0):?>active<?endif;?>" role="tabpanel" id="tab_<?=$item->nodeClass->shortname?>">

			<?if(!$restrict_edit):?>
			<div class="text-danger text-right"><small>system name: <b><?=$item->nodeClass->shortname?></b></small></div>
			<?endif;?>

			<table class="default-table">
				<tr>
					<?if($showShortname):?>
					<td>
						<?=$AdminTrnsl["Shortname"]?>
					</td>
					<?endif;?>

					<?if($showNodeName):?>
					<td>
						<?=($item->nodeClass->nodeNameLabel ? $item->nodeClass->nodeNameLabel : $AdminTrnsl["Node_name"])?>
					</td>
					<?endif;?>



					<? foreach($shownFieldDefs as $fd) { $fdt = $fd->getFieldType(); ?>
					<? if ($fdt->name == "MultiLink") { ?>
					<?
					$params = array();
					$classParams = $fdt->getParametersNames();
					foreach ($classParams as $fieldParameterShortname) {
						$params[$fieldParameterShortname] = $fd->getParameterValue($fieldParameterShortname);
					}
					/*kazancev begin*/
					if (strpos($params["root_node_path"], "{lang}")) {
						$lang_pref = explode("/", $node->absolutePath);
						$params["root_node_path"] = str_replace("{lang}", $lang_pref[1], $params["root_node_path"]);
					}
					/*kazancev end*/
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
					<td><?=$fd->name?></td>
					<? } ?>
					<? } ?>


					<td width=1%><?=$AdminTrnsl["Action"]?></td>

				</tr>

				<?php 
				$c=0;
				$tot=sizeof($item->nodes);
				?>

				<?foreach($item->nodes as $child):?>
					<?
					$c++;
					if (!$child->hasWriteRight) $child_disabledControlIfUserHasNoWriteRights = " disabled=true";
					else $child_disabledControlIfUserHasNoWriteRights = "";
					?>
					<tr>
						<?if($showShortname):?>
						<td>
							<?=$child->node->shortname?>
						</td>
						<?endif;?>

						<?if($showNodeName):?>
						<td>
							<a href="/admin/nodes.php?do=main&amp;id=<?=$child->node->id?>"><?=$child->node->name?></a>
						</td>
						<?endif;?>


						<? foreach($shownFieldDefs as $fd) { $fdt = $fd->getFieldType(); ?>
						<? if ($fdt->name == "MultiLink") { ?>
						<?
						$params = array();
						$classParams = $fdt->getParametersNames();
						foreach ($classParams as $fieldParameterShortname) {
							$params[$fieldParameterShortname] = $fd->getParameterValue($fieldParameterShortname);
						}
						$linked_node_ids = $child->node->fields[$fd->shortname]->getNodeIds();
						/*kazancev begin*/
						if (strpos($params["root_node_path"], "{lang}")) {
							$lang_pref = explode("/", $node->absolutePath);
							$params["root_node_path"] = str_replace("{lang}", $lang_pref[1], $params["root_node_path"]);
						}
						/*kazancev end*/
						$rootNode = Node::findByPath($params["root_node_path"]);
						$mlfavailableValues = $rootNode->getRecursiveChildrenOfClasses(explode(",", $params["allowed_classes"]));
						?>
						<? for ($z = 0; $z < sizeof($mlfavailableValues); $z++) { $mlfNode = &$mlfavailableValues[$z]; $mlfchecked = in_array($mlfNode->id, $linked_node_ids); ?>
						<td align="center"><?= $mlfchecked ? "+" : "-" ?></td>
						<? } ?>
						<? } else {?>
						<td<?if($_GET["id"]==39249){?> style="width: 5%;"<?}?>><?=$child->node->fields[$fd->shortname]->getHtmlVisualValue() ?></td>
						<? } ?>
						<? } ?>

						<td>
							<a href="/admin/nodes.php?do=editnode&amp;id=<?=$child->node->id?>" class="btn btn-sm btn-primary"><?=$AdminTrnsl["Edit"];?></a>
							<?if(!$restrict_edit || $item->nodeClass->checkFlag("RU_CAN_DELETE")):?>
							<a href="/admin/nodes.php?do=editnode&amp;id=<?=$child->node->id?>" onClick="if(!confirm('<?=$AdminTrnsl["This_will_delete_all_subnodes_Proceed"]?>')) return false;" class="btn btn-sm btn-danger"><?=$AdminTrnsl["Del"];?></a>
							<?endif;?>
						</td>
					</tr>

				<?endforeach;?>



			</table>
		</div>
	<?endforeach;?>
<?endif;?>

</div></div>







</body>
</html>
