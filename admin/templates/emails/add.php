<html>
<head><meta HTTP-EQUIV="content-type" CONTENT="text/html; charset=<?=ADMIN_CHARSET?>">
<title></title>
<link rel=stylesheet href=/admin/css.css type="text/css">
<script type="text/javascript" src="/admin/js/refresh_tree.js"></script>
<script type="text/javascript" src="/admin/js/wysiwyg.js"></script>
<script language="JavaScript">
    function wop(url, w, h) {
        w = window.open(url,'node_props','resizable=yes,toolbar=no,location=no,directories=no,status=no,menubar=no,width='+w+', height='+h+',scrollbars=yes,fullscreen=no,top=100, left=100');
		w.focus();
    }
</script>
</head>

<body leftmargin=0 topmargin=0 marginwidth=0 marginheight=0 class=main<?if ($request->getParameter('reload') != null) {?> onLoad="refreshTree(<?=$node->id?>, <?=$node->parent_id+0?>)"<?}?>>

<table width=100% border=0 cellpadding=0 cellspacing=0 class=top3><td><img src=/admin/_img/s.gif width=1 height=1></td></table>

<table width=100% border=0 cellpadding=4 cellspacing=0>
<tr>
<td id=pad18>

<form action="" method="POST" name="node_form" enctype="multipart/form-data">
<input type="hidden" name="do" value="create">
<input type="hidden" name="send" value="0">
<input type="hidden" name="create" value="0">
<input type="hidden" name="parent_id" value="<?=$parent_id?>">

<table width=100% border=0 cellpadding=0 cellspacing=0 class=backtab3 id=back2>
<tr>
<td><b><?=($nodeClass->nodeNameLabel ? $nodeClass->nodeNameLabel : $AdminTrnsl["Node_name"])?></b><span id=red>*</span>:&nbsp;</td>
<td width=80% id=back3><input type="text" size="45" name="NodeName" value="<?=$nodeProps->NodeName?>"> <span class=errmsg><?=$validationErrors['NodeName']?></span></td>
</tr>
<input type="hidden" name="NodeDynamicTemplate" value="<?=$nodeProps->NodeDynamicTemplate?>">
<? foreach ($fieldDefs as $item) { ?>
<?$request->setAttribute("CM_PARAMS", $item->params);?>
<?$request->setAttribute("CM_CONTROL_NAME", $item->fieldDef->shortname);?>
<tr valign=top id=back2>
<td id=pad5><nobr><b><?=$item->fieldDef->name?></b> <?if ($item->fieldDef->required) {?><sup><font color=red>*</font></sup><?}?>&nbsp;</nobr><nobr><span class=small><?if(!$restrict_edit){?>[<?=$item->fieldDef->shortname?>]<?}?></td>
<td id=back3 width=80%>
<div class="errmsg"><?= $validationErrors[$item->fieldDef->shortname] ?></div>
<? usetemplate("fields_controls/".$item->jspname); ?>
</td></tr>
<? } ?>
<tr valign=top id=back2>
<td>&nbsp;</td>
<td id=pad10>
<input type=button value="<?=$AdminTrnsl["Create email"]?>" class=button5 onClick="this.form.create.value=1;this.form.submit();">
<input type=button value="<?=$AdminTrnsl["Send email"]?>" class=button5 onClick="this.form.send.value=1;this.form.submit();">
<input type=button value="<?=$AdminTrnsl["Create and send email"]?>" class=button5 onClick="this.form.send.value=1;this.form.create.value=1;this.form.submit();">
</td></tr>
</table>
</form>

</td>
</tr>
</table>

</body>
</html>
