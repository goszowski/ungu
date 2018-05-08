<table border=0 cellpadding=0 cellspacing=0 class=nobacktab>
<td><input type="text" size="30" style="width:150px" value="<?=$CM_PARAMS["linked_node_path"]?>" name="<?=$CM_CONTROL_NAME?>"></td>
<td>&nbsp;</td>
<td>
<input type="button" value="<?=$AdminTrnsl["Select"]?>..."
	onClick="wc=window.open('/admin/linkfield_popup_selectnode.php?cname=<?=$CM_CONTROL_NAME?>&rn=<?=$CM_PARAMS["root_node_path"]?>&cs=<?=$CM_PARAMS["allowed_classes"]?>&d=<?=$CM_PARAMS["max_depth"]?>', 'lfp', 'resizable=yes,toolbar=no,location=no,directories=no,status=yes,menubar=no, width=400, height=500,scrollbars=yes,fullscreen=no,top=100,left=100');wc.focus()" class=button1></td>
<?if ($CM_PARAMS["linked_node_path"]) {?><td>&nbsp;</td>
<td>
    <?=$AdminTrnsl["Go_to_linked_node"]?>:&nbsp;<a href="/admin/nodes.php?do=main&id=<?=$CM_PARAMS["linked_node_id"]?>"><?=$CM_PARAMS["linked_node_name"]?></a>
</td><?}?>
</table>