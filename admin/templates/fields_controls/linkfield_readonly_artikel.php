<?
$linkedNode = & Node::findById($CM_PARAMS["linked_node_id"]);
?>
<input type="hidden" name="<?= $CM_CONTROL_NAME ?>" value="<?= $CM_PARAMS["db_value"] ?>">

<? if ($CM_PARAMS["db_value"]) { ?>
<a href="<?= $CM_PARAMS["linked_node_admin_href"] ?>"><?= $CM_PARAMS["linked_node_name"] ?></a>
&nbsp;&nbsp;&nbsp;
ArtikelNr: <?= $linkedNode->tfields["artikel"] ?>
<? } ?>

