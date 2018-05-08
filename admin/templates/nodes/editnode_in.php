<? if ($nodeClass->checkFlag("USE_NODENAME") || sizeof($fieldDefs) != 0) { ?>
<table width=100% border=0 cellpadding=0 cellspacing=0 class=backtab3 id=back2>
<form action="/admin/nodes.php" method="POST" name="node_form" enctype="multipart/form-data">
<input type="hidden" name="do" value="updatenode">
<input type="hidden" name="id" value="<?=$node->id?>">
<input type="hidden" name="moder_id" value="<?=$moder_action->id?>">

<? if(!$restrict_edit) { ?>
<tr>
<td><nobr><b><?=$AdminTrnsl["Node_path"]?></b></nobr>&nbsp;</td>
<td width=80% id=back3 height=28><?=$node->absolutePath?></td>
</tr>
<? } ?>

<? if ($nodeClass->checkFlag("USE_NODENAME")) { ?>
<tr>
<td><nobr><b><?=($nodeClass->nodeNameLabel ? $nodeClass->nodeNameLabel : $AdminTrnsl["Node_name"])?></b><span id=red>*</span></nobr></td>
<td width="80%" id="back3">
<? if (!$restrict_edit || !$nodeClass->checkFlag("NODENAME_READONLY")) { ?>
	<input class="form-control input-sm" style="width: 495px;" type="text" size="45" name="NodeName" value="<?=prepareStringForHtml($NodeName)?>">
	<? if(array_key_exists("NodeName", $validationErrors)) { ?><span class=errmsg><?= $validationErrors['NodeName'] ?></span><? } ?>
<? } else { ?>
	<?=prepareStringForHtml($NodeName)?>
	<input type="hidden" name="<?= $fieldNamePrefix ?>NodeName" value="<?= prepareStringForHtml($NodeName) ?>">
<? } ?>
</td>
</tr>
<? } else {?>
<input type="hidden" name="<?= $fieldNamePrefix ?>NodeName" value="<?= prepareStringForHtml($NodeName) ?>">
<? } ?>

<?
foreach($fieldDefs as $item):?>

<?
$request->setAttribute("CM_PARAMS", $item->params);
$request->setAttribute("CM_CONTROL_NAME", $item->fieldDef->shortname);
$request->setAttribute("CM_FIELD_SHORTNAME", $item->fieldDef->shortname);
?>

<tr valign="top" id="back2">
	<td id="pad5">
		<nobr>
			<?=$item->fieldDef->name?> <?if($item->fieldDef->required):?><span class="text-danger">*</span><?endif;?>
		</nobr>

		<?if(!$restrict_edit):?>
			<small>[<?=$item->fieldDef->shortname?>]</small>
		<?endif;?>
	</td>

	<td id="back3" width="80%">
	<?if($validationErrors[$item->fieldDef->shortname]):?>
	<div class="errmsg"><?=$validationErrors[$item->fieldDef->shortname]?></div>
	<?endif;?>

	<?usetemplate("fields_controls/".$item->jspname);?>

	</td>
</tr>
<?endforeach;?>

<tr id=back2 class=date>
<td align=right id=pad5><nobr><?=$AdminTrnsl["Node_Time_Created"]?></nobr></td>
<td id=back3 width=80%><?= $node->timeCreated != null ? $node->timeCreated->format(DATETIME_FORMAT) : "Unknown" ?></td>
</tr>
<tr id=back2 class=date>
<td align=right id=pad5><nobr><?=$AdminTrnsl["Node_Time_Updated"]?></nobr></td>
<td id=back3 width=80%><?= $node->timeUpdated != null ? $node->timeUpdated->format(DATETIME_FORMAT) : "Unknown" ?></td>
</tr>

<tr id=back2>
<td>&nbsp;</td>
<td id="pad10">
	<button class="btn btn-sm btn-primary" <?=$disabledControlIfUserHasNoWriteRights?> type="submit"><?=($moder_action ? $AdminTrnsl["Submit_changes_from_moderation"] : $AdminTrnsl["Submit_changes"])?></button>
</tr>
</form>
</table>
<? } ?>