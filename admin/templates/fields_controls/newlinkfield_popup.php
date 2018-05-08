<script>
$().ready(function() {

	$("#<?=$CM_CONTROL_NAME?>_show_admin").autocomplete("/admin/request_ajax.php?do=show&class_name=<?=$CM_PARAMS["allowed_classes"]?>", {
		width: 360,
		minChars: 2,
		selectFirst: true
	}).result(function(event, item) {		
		$("#<?=$CM_CONTROL_NAME?>").val(item[1]);
	});
	
});
</script>

<table border=0 cellpadding=0 cellspacing=0 class=nobacktab>
<td>
	<div class="input-group">
		<input class="form-control input-sm" type="text" size="50" style="width:250px" id="<?=$CM_CONTROL_NAME?>_show_admin" value="<?=$CM_PARAMS["linked_node_name"]?>">
		<div class="input-group-btn">
			<button class="btn btn-sm btn-default" type="button" onclick="$('#<?=$CM_CONTROL_NAME?>').val(''); $('#<?=$CM_CONTROL_NAME?>_show_admin').val('');"><?=$AdminTrnsl["Clear"]?></button>
		</div>
	</div>
	<input type="hidden" value="<?=$CM_PARAMS["linked_node_path"]?>" name="<?=$CM_CONTROL_NAME?>" id="<?=$CM_CONTROL_NAME?>">

	<?if($CM_PARAMS["linked_node_path"]):?>
		<div class="pt-5 pb-5">
		<?=$AdminTrnsl["Go_to_linked_node"]?>:&nbsp;<a href="/admin/nodes.php?do=main&amp;id=<?=$CM_PARAMS["linked_node_id"]?>"><?=$CM_PARAMS["linked_node_name"]?></a>
		</div>
	<?endif;?>
</td>


</table>