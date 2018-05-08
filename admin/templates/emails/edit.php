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


<div class=msg><?=($request->getParameter("okupdated") == 'yes') ? $AdminTrnsl["Saved email data was successefully updated"]."<br><br>" : ""?></div>

<form action="" method="POST" name="node_form" enctype="multipart/form-data">
<input type="hidden" name="do" value="update">
<input type="hidden" name="id" value="<?=$node->id?>">

<input type="hidden" name="send" value="0">
<input type="hidden" name="update" value="0">

<table width=100% border=0 cellpadding=0 cellspacing=0 class=backtab3 id=back2>
<tr>
<td id=pad5><b><?=($nodeClass->nodeNameLabel ? $nodeClass->nodeNameLabel : $AdminTrnsl["Node_name"])?></b><span id=red>*</span>:&nbsp;</td>
<td width=80% id=back3><input type="text" size="45" name="NodeName" value="<?=$NodeName?>"> <span class=errmsg><?=$validationErrors['NodeName']?></span></td>
</tr>

<? if (sizeof($fieldDefs) > 0) { ?>

<? foreach ($fieldDefs as $item) {?>
<?$request->setAttribute("CM_PARAMS", $item->params);?>
<span class="small"><?$request->setAttribute("CM_CONTROL_NAME", $item->fieldDef->shortname);?></span>
<tr valign=top id=back2>
<td id=pad5><nobr><b><?=$item->fieldDef->name?></b> <?if ($item->fieldDef->required) {?><span id=red>*</span><?}?>&nbsp;</nobr><nobr><span class=small></span></nobr></td>
<td id=back3 width=80%>
<div class="errmsg"><?=$validationErrors[$item->fieldDef->shortname]?></div>
<?usetemplate("fields_controls/".$item->jspname);?></td>
</tr>
<?}?>

<tr id=back2>
<td>&nbsp;</td>
<td id=pad10>
<input type=button value="<?=$AdminTrnsl["Update email"]?>" class=button5 onClick="this.form.update.value=1;this.form.submit();">
<input type=button value="<?=$AdminTrnsl["Send email"]?>" class=button5 onClick="this.form.send.value=1;this.form.submit();">
<input type=button value="<?=$AdminTrnsl["Update and send email"]?>" class=button5 onClick="this.form.send.value=1;this.form.update.value=1;this.form.submit();">

</tr>
</form>
</table>
<? } ?>

</td>
</tr>
</table>

<p>&nbsp;

</body>
</html>
