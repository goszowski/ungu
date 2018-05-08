<html>
<head>
<meta HTTP-EQUIV="content-type" CONTENT="text/html; charset=<?=ADMIN_CHARSET?>">
<meta http-equiv="Expires" content="0">
<meta http-equiv="Cache-Control" content="must-revalidate, post-check=0, pre-check=0">
<meta http-equiv="pragma" content="no-cache">
	<title><?=$AdminTrnsl["DownloadLibSelectfileonserver"]?></title>
	<?usetemplate("_res")?>
	<link rel=stylesheet href=/admin/css.css type="text/css">
	<script>
		function selectFile(fileName) {
			if (window.opener == null) {
				window.open('/files' + fileName);
			} else if (window.opener.setDFileLink) {
				window.opener.setDFileLink(fileName);
				window.close();
			} else if (window.opener.document.forms["node_form"] != null) {
				var cname = window.opener.cname;
				window.opener.document.forms["node_form"].elements[cname].value = fileName;
				window.close();
			} else {
				location.href="/admin/filelib/Wysiwyg_popup_downloadlib_final.php?cdir=<?=$cdirPath?>&file="+fileName;
			}
		}
	</script>
</head>

<body leftmargin=0 topmargin=0 marginwidth=0 marginheight=0 class=main>

<table width=100% border=0 cellpadding=0 cellspacing=0 class=top3><td><img src=/admin/_img/s.gif width=1 height=1></td></table>
<table width=100% border=0 cellpadding=4 cellspacing=0>
<tr class=top41>
<td height=30 id=pad18 colspan=2 nowrap class=head><b><?=$AdminTrnsl["DownloadLib"]?></b></td>
</tr>
</table>
<table width=100% border=0 cellpadding=0 cellspacing=0 class=top2><td><img src=/admin/_img/s.gif width=1 height=1></td></table>
<table width=100% border=0 cellpadding=4 cellspacing=0>
<tr>
<td colspan=2 id=pad18>

<br>

<? if(strlen($cdirPath)!=0) { ?>
<b class=h3><a href="/admin/filelib.php?cname=<?=$cname?>"><?=$AdminTrnsl["DownloadLibMainFolder"]?></a></b><?foreach(explode("/", $cdirPath) as $d){if (!$d){continue;}?><?$cd=$cd."/".$d?><b> : <a href="/admin/filelib.php?cdir=<?=$cd?>&cname=<?=$cname?>"><?=$d?></a></b><?}?><br>
<? } else {?>
<b><?=$AdminTrnsl["DownloadLibMainFolder"]?></b><br>
<? } ?>
<br>

<table border=0 cellspacing=0 cellpadding=0>
<tr>
<td nowrap>
<input type="button" value="<?=$AdminTrnsl["DownloadLibUploadfile"]?>" class=button1 onClick="location.href='/admin/filelib.php?do=file_uploadform&cdir=<?=$cdirPath?>&cname=<?=$cname?>'">
<input type="button" value="<?=$AdminTrnsl["DownloadLibCreatefolder"]?>" class=button1 onClick="location.href='/admin/filelib.php?do=dir_createform&cdir=<?=$cdirPath?>&cname=<?=$cname?>'">
<?/* cm:if test="${CurrentAdminUser.group.canManageDownloadLib}" */?>
<? if(strlen($cdirPath)!=0) { ?><input type="button" value="<?=$AdminTrnsl["DownloadLibRename"]?>" class=button1 onClick="location.href='/admin/filelib.php?do=rename_form&cdir=<?=$cdir1?>&fname=<?=$cdirname?>&cname=<?=$cname?>'" >
<!-- <input type="button" value="<?=$AdminTrnsl["DownloadLibMove"]?>" class=button2 onClick="location.href='/admin/filelib.php?do=move_form&oldcdir=<?=$cdir1?>&fname=<?=$cdirname?>&cname=<?=$cname?>'" > -->
<!-- <input type="button" value="<?=$AdminTrnsl["DownloadLibDel"]?>" class=button3 onClick="<?if(sizeof($files)!=0) { ?>alert('<?=$AdminTrnsl["DownloadLibCantDelNEFolder"]?>.')<? } else {?>if(confirm('<?=$AdminTrnsl["DownloadLib rus del folder"]?>')) location.href='/admin/filelib.php?do=delete&cdir=<?=$cdir1?>&fname=<?=$cdirname?>&cname=<?=$cname?>'<? } ?>"> -->
<? } ?>
</* } */>
</td>
</tr>
</table>
<br>

<table width=100% cellspacing=0 cellpadding=2 border=0 class=backtab>
<tr id=back2 class=header>
<td><b><?=$AdminTrnsl["DownloadLibName"]?></b></td>
<td><b><?=$AdminTrnsl["DownloadLibLastModified"]?></b></td>
<td width="5%"><b><?=$AdminTrnsl["DownloadLibSize"]?></b></td>
<?/* cm:if test="${CurrentAdminUser.group.canManageDownloadLib}" */?>
<td width="5%"><b><?=$AdminTrnsl["DownloadLibAction"]?></b></td><?/* } */?>
</tr>

<? if(strlen($cdirPath)!=0) { ?>
<tr id=back3>
<td align=left><a href="/admin/filelib.php?cdir=<?=$cdir1?>&cname=<?=$cname?>"><img src="/admin/_img/folder.gif" width="14" height="10" border="0" hspace="3"><b>..</b></a></td>
<td>&nbsp;</td>
<td noWrap align=middle>&nbsp;</td>
<td noWrap align=middle>&nbsp;</td>
</tr>
<? } ?>

<?
for ($i=0; $i<sizeof($dirs); $i++) {
	$filePath = $dirs[$i];
	$file = substr($filePath, strrpos($filePath, "/")+1);
?>
<tr id=back3>
<td align=left><a href="/admin/filelib.php?cdir=<?=($cdirPath . "/" . $file)?>&cname=<?=$cname?>"><img src="/admin/_img/folder.gif" width="14" height="10" hspace="3" border="0"><b><?=$file?></b></a></td>
<td><?=date("d.m.Y H:m:s", filemtime($filePath))?></td>
<td noWrap align=middle>&nbsp;</td>
<?/* if test="${CurrentAdminUser.group.canManageDownloadLib} */?>
<td noWrap align=middle>
<!-- <input class=button1 onClick="location.href='/admin/filelib.php?do=file_replaceform&cdir=<?=$cdirPath?>&fname=<?=$file?>&cname=<?=$cname?>'" type=button value=<?=$AdminTrnsl["DownloadLibReplace"]?> name="button"> -->
<input class=button1 onClick="location.href='/admin/filelib.php?do=rename_form&cdir=<?=$cdirPath?>&fname=<?=$file?>&cname=<?=$cname?>'" type=button value=<?=$AdminTrnsl["DownloadLibRename"]?> name="button">
<!-- <input class=button1 onClick="location.href='/admin/filelib.php?do=move_form&oldcdir=<?=$cdirPath?>&fname=<?=$file?>&cname=<?=$cname?>'" type=button value=<?=$AdminTrnsl["DownloadLibMove"]?> name="button"> -->
<input class=button4 onClick="<?if(sizeof(list_dir_contents($filePath)) !=0) { ?>alert('<?=$AdminTrnsl["DownloadLibCantDelNEFolder"]?>')<? } else {?>if(confirm('<?=$AdminTrnsl["DownloadLib rus del folder"]?>')) location.href='/admin/filelib.php?do=delete&cdir=<?=$cdirPath?>&fname=<?=$file?>&cname=<?=$cname?>'<? } ?>" type=button value=<?=$AdminTrnsl["DownloadLibDel"]?> name="button">
</td><?/* } */?>
</tr>
<?}?>

<?
for ($i=0; $i<sizeof($files); $i++) {
	$filePath = $files[$i];
	$file = substr($filePath, strrpos($filePath, "/")+1);
	$fSize = filesize($filePath);
	$fSizeStr;
	if ($fSize > 1048576) {
		$fSizeStr = floor($fSize / 1048576) . " Mb";
	} else if (fSize > 1024){
		$fSizeStr = floor($fSize / 1024) . " Kb";
	} else {
		$fSizeStr = $fSize . " b";
	}

?>
<tr id=back3>
<td align=left><a href="Select file" onClick="selectFile('<?=($cdirPath."/".$file)?>');return false;"><img src="/admin/_img/f/<?=getIconForFile($file)?>" border=0 hspace=3><?=$file?></a></td>
<td><?=date("d.m.Y H:m:s", filemtime($filePath))?></td>
<td noWrap align=middle><?=$fSizeStr?></td>
<?/* if test="${CurrentAdminUser.group.canManageDownloadLib} */?>
<td noWrap align=middle>
<!-- <input class=button1 onClick="location.href='/admin/filelib.php?do=file_replaceform&cdir=<?=$cdirPath?>&fname=<?=$file?>&cname=<?=$cname?>'" type=button value=<?=$AdminTrnsl["DownloadLibReplace"]?> name="button"> -->
<input class=button1 onClick="location.href='/admin/filelib.php?do=rename_form&cdir=<?=$cdirPath?>&fname=<?=$file?>&cname=<?=$cname?>'" type=button value=<?=$AdminTrnsl["DownloadLibRename"]?> name="button">
<!-- <input class=button1 onClick="location.href='/admin/filelib.php?do=move_form&oldcdir=<?=$cdirPath?>&fname=<?=$file?>&cname=<?=$cname?>'" type=button value=<?=$AdminTrnsl["DownloadLibMove"]?> name="button"> -->
<input class=button4 onClick="if(confirm('<?=$AdminTrnsl["DownloadLib rus del file"]?>')) location.href='/admin/filelib.php?do=delete&cdir=<?=$cdirPath?>&fname=<?=$file?>&cname=<?=$cname?>'" type=button value=<?=$AdminTrnsl["DownloadLibDel"]?> name="button">
</td><?/* } */?>
</tr>
<?}?>

</table>
<br>
<br>
</tr>

</table>

</td>
</tr>
</table>


</body>
</html>
