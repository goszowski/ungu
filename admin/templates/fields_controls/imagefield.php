<table border=0 cellpadding=0 cellspacing=0 class=nobacktab>
<td><input type="text" size="25" style="width:245px" value="<?=$CM_PARAMS["relurl"]?>" name="<?=$CM_CONTROL_NAME?>"></td>
<td>&nbsp;</td>
<td><input type="button" value="<?=$AdminTrnsl["Select_Image_From_Library"]?>" style="width:160px" class=button1 onClick="window.cname='<?=$CM_CONTROL_NAME?>';wc=window.open('/admin/imglib/Image_main.php?cname=<?=$CM_CONTROL_NAME?>', 'wi', 'resizable=yes,toolbar=no,location=no,directories=no,status=yes,menubar=no, width=500, height=400,scrollbars=yes,fullscreen=no,top=100, left=100');wc.focus()"/></td>
<?if ($CM_PARAMS["url"]){?><td>&nbsp;</td>
<td><a href="<?=$CM_PARAMS["url"]?>" target=_blank><?=$AdminTrnsl["View_Image"]?></a></td><?}?>
</table>