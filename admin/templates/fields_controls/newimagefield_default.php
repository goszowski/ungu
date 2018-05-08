<?
$cname = $CM_CONTROL_NAME;
?>
<table border=0 cellpadding=0 cellspacing=0 class=nobacktab>
<?if ($CM_PARAMS["url"]){?>
<tr>
<td><a href="<?=$CM_PARAMS["url"]?>" target=_blank><img src="<?=$CM_PARAMS["thumburl"]?>" border=0 alt="<?=$AdminTrnsl["View_Image"]?>"></a></td>
<td id=pad5><input type="checkbox" name="<?=$cname?>_del" value="1"><span class=errmsg><?=$AdminTrnsl["Delete_file"]?></span></td>
<?}?>
<td class=top2><img src=/admin/_img/s.gif width=1 height=1></td>
<td id=pad5 nowrap>
<?=$AdminTrnsl["NewImageFieldUploadFile"]?>:<br>
<input type="file" size="30" name="<?=$CM_CONTROL_NAME?>_file" style="width:200px"><br>
<?=$AdminTrnsl["NewImageField_Select_From_Lib1"]?>:<br>
<input type="text" size="25" style="width:117px" value="<?=$CM_PARAMS["relurl"]?>" name="<?=$CM_CONTROL_NAME?>">
<input type="button" value="<?=$AdminTrnsl["NewImageField_Select_From_Lib2"]?>" class=button onClick="window.cname='<?=$CM_CONTROL_NAME?>';wc=window.open('/admin/imglib.php?cname=<?=$CM_CONTROL_NAME?>', 'wi', 'resizable=yes,toolbar=no,location=no,directories=no,status=yes,menubar=no, width=500, height=400,scrollbars=yes,fullscreen=no,top=100, left=100');wc.focus()"/>
</td>
</tr>
</table>
