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
<script type="text/javascript" src="/admin/js/refresh_tree.js"></script>
<script language="JavaScript">
    function wop(url, w, h) {
        w = window.open(url,'node_props','resizable=yes,toolbar=no,location=no,directories=no,status=no,menubar=no,width='+w+', height='+h+',scrollbars=yes,fullscreen=no,top=100, left=100');
		w.focus();
    }
</script>
</head>

<body leftmargin=0 topmargin=0 marginwidth=0 marginheight=0 class=main <?if ($request->getParameter("reload")!=null) {?> onLoad="refreshTree()"<? } ?>>

<table width=100% border=0 cellpadding=4 cellspacing=0>
<tr class=top4>
<td height=40 id=pad18 colspan=2 nowrap class=head><?=$AdminTrnsl["Content_tree"]?></td>
</tr>
</table>
<table width=100% border=0 cellpadding=0 cellspacing=0 class=top42><td><img src=/admin/_img/s.gif width=1 height=5></td></table>
<table width=100% border=0 cellpadding=4 cellspacing=0>
<tr><td id=pad18 class=h4>
<?if(!$restrict_edit){?><div id=pad5><b class=h3><?=$AdminTrnsl["Sub_Nodes_List"]?></b></div><?}?>

<?$shownFieldDefs = $item->shownFields?>

<table width=100% border=0 cellpadding=0 cellspacing=0 class=backtab2>
<?if(!$restrict_edit){ $nodeClass = &$item->nodeClass; ?>
<tr bgcolor=#ffffff>
<td class=td2 colspan=5 id=pad5>

<table width=100% border=0 cellpadding=0 cellspacing=0 class=nobacktab>
<td><b><?=$nodeClass->name?></b></td>
<td align=right class=small><?=$nodeClass->shortname?></td>
</table>
</td>
</tr>
<?}?>
<tr id=back2 class=header>
<td class=td2<?if (sizeof($shownFieldDefs) !=0) {?> width=15%<?}?>><?=$AdminTrnsl["Name"]?></td>
<?foreach ($shownFieldDefs as $fd) {?><td><?=$fd->name?></td>
<?}?>
<?if ($item->sort) {?><td width=1%><?=$AdminTrnsl["Sort"]?></td><?}?>
<td width=5%><?=$AdminTrnsl["Action"]?></td>
</tr>

<?$count=0; foreach ($item->nodes as $child) {$count++; ?>
<?if (!$child->hasWriteRight) { $child_disabledControlIfUserHasNoWriteRights = " disabled=true"; }?>
<tr id=back3>
<td class=td2>&nbsp;<a href="/admin/nodes.php?do=main&id=<?=$child->node->id?>"><?=$child->node->name?></a></td>
<?foreach ($shownFieldDefs as $fd) {?><td><?=$child->node->fields[$fd->shortname]->getHtmlVisualValue()?></td>
<?}?>
<?if ($item->sort) {?><td nowrap<?if ($count == sizeof($item->nodes)) {?> align=right<?}?>><?if ($count != sizeof($item->nodes)) {?><input type=button style="width:21px" class=button41><?}?><?if ($count != 1) {?><input type=button style="width:21px" class=button42><?}?></td><?}?>
<td align=center nowrap>
<?if(!$restrict_edit){?><input type=button value="<?=$AdminTrnsl["Del"]?>" class=button3 onClick="if(confirm('<?=$AdminTrnsl["This_will_delete_all_subnodes_Proceed"]?>')) location.href='/admin/nodes.php?do=deletenode&id=<?=$child->node->id?>'"><?}?></td>
</tr><?}?>
</table>

</td>
</tr>
</table>
<p>&nbsp;
</body>
</html>