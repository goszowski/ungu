<?
$cname = $CM_CONTROL_NAME;
?>
<table border=0 cellpadding=0 cellspacing=0 class=nobacktab>
<tr valign=top>
<td id="pad10r" nowrap>

<table border=0 cellpadding=0 cellspacing=0 class="nobacktab">
<tr>
<td colspan="2">
	<div style="position: relative;">
		<button type="button" class="btn btn-default btn-sm" onclick="$('[name=<?=$CM_CONTROL_NAME?>_file]').click()">Выбрать файл</button>
		<input type="file" size="30" name="<?=$CM_CONTROL_NAME?>_file" style="width: 0px; height: 0px; position: absolute; visibility: hidden;">
	</div>
</td>
</tr>
<input type="hidden" value="<?=$CM_PARAMS["relurl"]?>" name="<?=$CM_CONTROL_NAME?>">
</table>

</td>
<?if ($CM_PARAMS["url"]){?>
<td id=pad10r><a href="<?=$CM_PARAMS["url"]?>" target=_blank><img src="<?=$CM_PARAMS["thumburl"]?>" border=0 alt="<?=$AdminTrnsl["View_Image"]?>"></a></td>
<td id=pad10r><input type="checkbox" name="<?=$cname?>_del" value="1" onClick="if (this.checked) {this.form.<?=$CM_CONTROL_NAME?>_file.disabled = true;} else {this.form.<?=$CM_CONTROL_NAME?>_file.disabled = false;}"><span class=errmsg><?=$AdminTrnsl["Delete_file"]?></span></td>
<?}?>
</tr>
</table>