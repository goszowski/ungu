<?
$cname = $CM_CONTROL_NAME;
$fname =$CM_PARAMS["filename"];
$fsize =$CM_PARAMS["filesize"];
$furl = $CM_PARAMS["fileurl"];
?>
<table border=0 cellpadding=0 cellspacing=0 class=nobacktab>
<td><?if($fname) {?><a href="<?=$furl?>" target="_blank"><?=$AdminTrnsl["Link_to_file"]?></a> (size: <?=$fsize?>&nbsp;b)&nbsp;&nbsp;<?}?></td>
<td>
	<div class="input-group">
		<input type="text" size="40" class="form-control input-sm" style="width:245px" value="<?=$fname?>" name="<?=$cname?>">

		<?if(!$fname):?>
		<span class="input-group-btn">
			<button class="btn btn-sm btn-default" type="button" onClick="window.cname='<?=$cname?>';wc=window.open('/admin/filelib.php?cname=<?=$CM_CONTROL_NAME?>', 'wi', 'resizable=yes,toolbar=no,location=no,directories=no,status=yes,menubar=no, width=500, height=400,scrollbars=yes,fullscreen=no,top=100, left=100');wc.focus()"><?=$AdminTrnsl["Select"]?>...</button>
		</span>
		<?else:?>
		<input type="hidden" name="<?=$cname?>_del" value="0">

		<span class="input-group-btn">
			<button class="btn btn-sm btn-default dropdown-toggle" data-toggle="dropdown" type="button">Action <span class="caret"></span></button>
			<ul class="dropdown-menu">
				<li><a onClick="window.cname='<?=$cname?>';wc=window.open('/admin/filelib.php?cname=<?=$CM_CONTROL_NAME?>', 'wi', 'resizable=yes,toolbar=no,location=no,directories=no,status=yes,menubar=no, width=500, height=400,scrollbars=yes,fullscreen=no,top=100, left=100');wc.focus(); return false;"><?=$AdminTrnsl["Select"]?>...</a></li>
				<li><a onClick="$('[name=<?=$cname?>_del]'); return false;"><?=$AdminTrnsl["Delete_file"]?></a></li>
			</ul>
		</span>
		<?endif;?>

		
	</div>
</td>

</table>
