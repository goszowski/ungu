<html>
<head>
<meta HTTP-EQUIV="content-type" CONTENT="text/html; charset=<?=ADMIN_CHARSET?>">
<meta http-equiv="Expires" content="0">
<meta http-equiv="Cache-Control" content="must-revalidate, post-check=0, pre-check=0">
<meta http-equiv="pragma" content="no-cache">
<link rel=stylesheet href=/admin/css.css type="text/css">
	<title><?=$AdminTrnsl["DownloadLibRenamefileordirectory"]?></title>
	<?usetemplate("_res")?>
</head>

<body leftmargin=0 topmargin=0 marginwidth=0 marginheight=0 class=main>

<table width=100% border=0 cellpadding=0 cellspacing=0 class=top3><td><img src=/admin/_img/s.gif width=1 height=1></td></table>
<table width=100% border=0 cellpadding=4 cellspacing=0>
<tr class=top41>
<td height=30 id=pad18 colspan=2 nowrap class=head><b><?=$AdminTrnsl["DownloadLib"]?> : <?=$AdminTrnsl["DownloadLibCreatingfolder"]?></b></td>
</tr>
</table>
<table width=100% border=0 cellpadding=0 cellspacing=0 class=top2><td><img src=/admin/_img/s.gif width=1 height=1></td></table>
<table width=100% border=0 cellpadding=4 cellspacing=0>
<tr>
<td colspan=2 class=head id=pad18>
<br>
<table border=0 cellpadding=0 cellspacing=0><td>
<? if(strlen($cdirPath)!=0) { ?>
<b class=h3><a href="/admin/filelib.php?cname=<?=$cname?>"><?=$AdminTrnsl["DownloadLibMainFolder"]?></a></b><?foreach(explode("/", $cdirPath) as $d){if (!$d){continue;}?><?$cd=$cd."/".$d?><b> : <a href="/admin/filelib.php?cdir=<?=$cd?>&cname=<?=$cname?>"><?=$d?></a></b><?}?>
<? } else {?>
<b><a href="/admin/filelib.php?cname=<?=cname?>"><?=$AdminTrnsl["DownloadLibMainFolder"]?></a></b>
<? } ?>
</td></table>
<br>
<script>function checkFName(fname) {
	if (fname == '') {
		alert('<?=$AdminTrnsl["ImgLib Folder Name Required"]?>');
		return false;
	}
	if (!fname.match(/^[A-Za-z0-9_\-\. ]+$/)) {
		alert('<?=$AdminTrnsl["Illegal Char in Name"]?>');
		return false;
	}
	return true;
}</script>
<table width=100% border=0 cellpadding=0 cellspacing=0 class=backtab3>
<form action="/admin/filelib.php" method=POST onSubmit="return checkFName(this.dname.value)" enctype="multipart/form-data">
<input type="hidden" name="do" value="dir_create" >
<input type="hidden" name="cname" value="<?=$cname?>">
<input type="hidden" name="cdir" value="<?=$cdirPath?>">

<tr id=back2>
<td class=td2><?=$AdminTrnsl["ImgLibFolderName"]?></td>
<td id=back3><input type="text" name="dname" value="<?=prepareStringForHtml($dname)?>" ></td>
</tr>
<tr id=back2>
<td>&nbsp;</td>
<td id=pad10><input type="submit" name="Submit7" value="<?=$AdminTrnsl["DownloadLibCreatedirectory"]?>" class=button5></td>
</tr>
</form>
</table>

</td>
</tr></table>

</body>
</html>
