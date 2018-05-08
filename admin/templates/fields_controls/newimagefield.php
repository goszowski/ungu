<?
$cname = $CM_CONTROL_NAME;
$fname = $CM_PARAMS['filename'];
$fsize = $CM_PARAMS['filesize'];
$furl = $CM_PARAMS['fileurl'];
$thumburl = $CM_PARAMS['thumburl'];
?>
<table border=0 cellpadding=0 cellspacing=0 class=nobacktab>
<td>
	<div style="position: relative;">
		<button type="button" class="btn btn-default btn-sm">Выбрать файл</button>
		<input type="file" size="30" name="<?=$cname?>" style="width: 0px; height: 0px; position: absolute; visibility: hidden;">
	</div>
</td>
<? if ($fname) {?><td>&nbsp;&nbsp;<a href="<?=$furl?>" target="_blank"><img src="<?=$thumburl?>" border=0></a>&nbsp;&nbsp;</td><?}?>
<? if ($fname) {?><td><input type="checkbox" name="<?=$cname?>_del" value="1"><span class=errmsg><?=$AdminTrnsl["Delete_file"]?></span></td><?}?>
</table>

