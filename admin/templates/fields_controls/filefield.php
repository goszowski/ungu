<? $cname = $CM_CONTROL_NAME;?>
<? $fname = $CM_PARAMS['filename'];?>
<? $fsize = $CM_PARAMS['filesize'];?>
<? $furl = $CM_PARAMS['fileurl'];?>
<table border=0 cellpadding=0 cellspacing=0 class=nobacktab>
<td><input type="file" size="30" name="<?=$cname?>" style="width:170px"></td>
<? if ($fname) {?><td>&nbsp;&nbsp;<a href="<?=$furl?>" target="_blank"><?=$AdminTrnsl["Link_to_file"]?></a> (size: <?=$fsize?>&nbsp;b)&nbsp;&nbsp;</td><?}?>
<? if ($fname) {?><td><input type="checkbox" name="<?=$cname?>_del" value="1"><span class=errmsg><?=$AdminTrnsl["Delete_file"]?></span></td><?}?>
</table>