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

<div class="mt-4">

	<table class="checkbox-groups">
		<tr>
			<td>
				<? for ($i = 0; $i < sizeof($availableValues); $i++) { $node = &$availableValues[$i]; ?>
					<?if($i%2 == 0):?>
					<? $checked = in_array($node->id, $CM_PARAMS["linked_node_ids"]); ?>
					<div>
						<label class="ui-checks">
							<input type="checkbox" name="<?=$CM_CONTROL_NAME?>[]" value="<?= $node->id ?>" <? if($checked) { ?> checked <? } ?>>
							<i></i> <?= $node->name ?><?= $node->tfields["email"] ? " (" . $node->tfields["email"] . ")" : "" ?>
						</label>
					</div>
					<?endif;?>
				<?}?>
			</td>

			<td>
				<? for ($i = 0; $i < sizeof($availableValues); $i++) { $node = &$availableValues[$i]; ?>
					<?if($i%2 != 0):?>
					<? $checked = in_array($node->id, $CM_PARAMS["linked_node_ids"]); ?>
					<div>
						<label class="ui-checks">
							<input type="checkbox" name="<?=$CM_CONTROL_NAME?>[]" value="<?= $node->id ?>" <? if($checked) { ?> checked <? } ?>>
							<i></i> <?= $node->name ?><?= $node->tfields["email"] ? " (" . $node->tfields["email"] . ")" : "" ?>
						</label>
					</div>
					<?endif;?>
				<?}?>
			</td>


		</tr>
	</table>

</div>