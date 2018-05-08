<?
	$label = $parent->tfields[$CM_FIELD_SHORTNAME . "label"];
?>
<? if ($label) { ?>
<strong><?= $label ?> :</strong> <input type="<?=$CM_PARAMS['inputtype']?>" size="40" style="width:495px" value="<?=prepareStringForXML($CM_PARAMS['strvalue'])?>" name="<?=$CM_CONTROL_NAME?>">
<? } else { ?>
Не используется
<? } ?>