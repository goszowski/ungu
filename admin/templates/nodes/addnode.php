<?
$restrict_edit = $CurrentAdminUser->group->restrictNodeEdit;
?>
<html>
<head>
<meta HTTP-EQUIV="content-type" CONTENT="text/html; charset=<?=ADMIN_CHARSET?>">
<meta http-equiv="Expires" content="0">
<meta http-equiv="Cache-Control" content="must-revalidate, post-check=0, pre-check=0">
<meta http-equiv="pragma" content="no-cache">
<title></title>
<link rel=stylesheet href=/admin/css.css type="text/css">
<?usetemplate("_res")?>
<script type="text/javascript" src="/_js/jquery.js"></script>
<script type="text/javascript" src="/_tools/autocomplete/jquery.autocomplete.js"></script>
<link rel="stylesheet" type="text/css" href="/_tools/autocomplete/jquery.autocomplete.css" />

</head>

<body leftmargin=0 topmargin=0 marginwidth=0 marginheight=0 class=main>

<?if (!$userHasWriteRights) { $disabledControlIfUserHasNoWriteRights = " disabled=true"; }?>

<table width=100% border=0 cellpadding=4 cellspacing=0>
<tr class=top4>
<td height=40 id=pad18 colspan=2 class=head><? $i=0;foreach ($parents as $parent) {$i++;?><a href="/admin/nodes.php?do=main&id=<?=$parent->id?>" id=black><?=$parent->name?></a> : <?}?><b><?=$AdminTrnsl["Creating_node_of_class"]?>:</b> <?=$nodeClass->name?><?if(!$restrict_edit){?>&nbsp;[<?=$nodeClass->shortname?>]<?}?></b></td>
</tr>
</table>

<table width=100% border=0 cellpadding=0 cellspacing=0 class=top42><td><img src=/admin/_img/s.gif width=1 height=5></td></table>

<table width=100% border=0 cellpadding=4 cellspacing=0>
<td id=pad18 class=h4>
<form action="/admin/nodes.php" method="post" name="node_form" enctype="multipart/form-data">
<input type="hidden" name="do" value="addnode">
<input type="hidden" name="class_id" value="<?=$nodeClass->id?>">
<input type="hidden" name="parent_id" value="<?=$parent_id?>">
<input type="hidden" name="forms_count" value="<?= $formsCount ?>">

<input type="hidden" name="moder_id" value="<?=$moder_action->id?>">

<script>
	var formsCount = <?= $formsCount ?>;

	function addOneForm() {
		var mf = document.getElementById("wholeForm");
		var baseForm = document.getElementById("additionalForm0");
		var newFormHTML = baseForm.innerHTML + "";
		var newFormFormatterNumber = (formsCount > 9) ? "" + formsCount : "0" + formsCount;
		newFormHTML = newFormHTML.replace(/name\=f00\_/gi, "name=f" + newFormFormatterNumber + "_");
		mf.innerHTML = mf.innerHTML + "<span id='additionalForm" + formsCount + "'><hr>" + newFormHTML + "</span>";
		formsCount++;
		document.forms["node_form"].elements["forms_count"].value = formsCount;
	}

	function removeLastForm() {
		if (formsCount > 1) {
			formsCount--;
			var lastForm = document.getElementById("additionalForm" + formsCount);
			lastForm.outerHTML = "";
			document.forms["node_form"].elements["forms_count"].value = formsCount;
		}
	}
</script>
<? if (!$restrict_edit || $nodeClass->checkFlag("USE_MULTIFORMS")) { ?>
<div id="pad5">

<button type="button" class="btn btn-xs btn-primary" onClick="addOneForm()">Add form</button>
<button type="button" class="btn btn-xs btn-primary" onClick="removeLastForm()">Remove form</button>
</div>
<? } ?>
<span id="wholeForm">
<?
for ($formNumber = 0; $formNumber < $formsCount; $formNumber++) {
	$nodeProps = $nodePropsArray[$formNumber];
	$validationErrors = $validationErrorsArray[$formNumber];
	$fieldDefs = $fieldDefsArray[$formNumber];
	$formNumberFormatted = sprintf("%02d", $formNumber);
	$fieldNamePrefix = "f" . $formNumberFormatted . "_";
?>
<span id="additionalForm<?= $formNumber ?>">
<table width=100% border=0 cellpadding=0 cellspacing=0 class=backtab3 id=back2>
<? if ($parent != null && $nodeClass->checkFlag("USE_SHORTNAME")) { ?>
<tr>
<td><?=$AdminTrnsl["Shortname"]?>:&nbsp;</td>
<td width=80% id=back3><input type="text" size="45" class="form-control input-sm" style="width: 495px;" name="<?= $fieldNamePrefix ?>NodeShortname" value="<?=$nodeProps->NodeShortname?>"> <span class=errmsg><?=$validationErrors['NodeShortname']?></span></td>
</tr>
<? } ?>
<? if (!$restrict_edit && $nodeClass->checkFlag("USE_ADMINURL_AND_TEMPLATE")) { ?>
<tr>
<td><?=$AdminTrnsl["Dynamic_Template"]?>:&nbsp;</td>
<td width=80% id=back3><input type="text" class="form-control input-sm" style="width: 495px;" size="45" name="<?= $fieldNamePrefix ?>NodeDynamicTemplate" value="<?=$nodeProps->NodeDynamicTemplate?>"> <span class=errmsg><?=$validationErrors['NodeDynamicTemplate']?></span></td>
</tr>
<tr>
<td><?=$AdminTrnsl["Admin_URL"]?>:&nbsp;</td>
<td width=80% id=back3><input type="text" class="form-control input-sm" style="width: 495px;" size="45" name="<?= $fieldNamePrefix ?>NodeAdminURL" value="<?=$nodeProps->NodeAdminURL?>"> <span class=errmsg><?=$validationErrors['NodeAdminURL']?></span></td>
</tr>
<? } else { ?>
<input type="hidden" name="<?= $fieldNamePrefix ?>NodeDynamicTemplate" value="<?=$nodeProps->NodeDynamicTemplate?>">
<input type="hidden" name="<?= $fieldNamePrefix ?>NodeAdminURL" value="<?=$nodeProps->NodeAdminURL?>">
<? } ?>
</table>
<table width=100% border=0 cellpadding=0 cellspacing=0 class=backtab3>
<? if ($nodeClass->checkFlag("USE_NODENAME")) { ?>
<tr id=back2>
<td><b><?=($nodeClass->nodeNameLabel ? $nodeClass->nodeNameLabel : $AdminTrnsl["Node_name"])?></b><span id=red>*</span>&nbsp;</td>
<td width=80% id=back3><input type="text" class="form-control input-sm" style="width: 495px;" size="45" name="<?= $fieldNamePrefix ?>NodeName" value="<?=prepareStringForHtml($nodeProps->NodeName)?>"> <span class=errmsg><?=$validationErrors['NodeName']?></span></td>
</tr>
<? } else {?>
<input type="hidden" name="<?= $fieldNamePrefix ?>NodeName" value="<?= prepareStringForHtml($nodeClass->name) ?>">
<? } ?>
<? foreach ($fieldDefs as $item) { ?>
<?$request->setAttribute("CM_PARAMS", $item->params);?>
<?$request->setAttribute("CM_CONTROL_NAME", $fieldNamePrefix . $item->fieldDef->shortname);?>
<?$request->setAttribute("CM_FIELD_SHORTNAME", $item->fieldDef->shortname);?>
<tr valign=top id=back2>
<td id=pad5><nobr><b><?=$item->fieldDef->name?></b> <?if ($item->fieldDef->required) {?><sup><font color=red>*</font></sup><?}?>&nbsp;</nobr><nobr><span class=small><?if(!$restrict_edit){?>[<?=$item->fieldDef->shortname?>]<?}?></td>
<td id=back3 width=80%>
<div class="errmsg"><?=$validationErrors[$item->fieldDef->shortname]?></div>
<?usetemplate("fields_controls/".$item->jspname);?>
</td></tr>
<? } ?>
</table>
</span>
<? } ?>
</span>

<table width=100% border=0 cellpadding=0 cellspacing=0 class=backtab3>
<tr valign=top id=back2>
<td>&nbsp;</td>
<td id="pad10">
<button type="submit" class="btn btn-sm btn-success" <?=$disabledControlIfUserHasNoWriteRights?>><?=($moder_action ? $AdminTrnsl["Create_node_from_moderation"] : $AdminTrnsl["Create_node"])?></button>
</td></tr>
</table>

</td></tr>
</form>
</table>

</body>
</html>