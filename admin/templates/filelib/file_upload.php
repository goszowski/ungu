<html>
<head>
<meta HTTP-EQUIV="content-type" CONTENT="text/html; charset=<?=ADMIN_CHARSET?>">
<meta http-equiv="Expires" content="0">
<meta http-equiv="Cache-Control" content="must-revalidate, post-check=0, pre-check=0">
<meta http-equiv="pragma" content="no-cache">
<link rel=stylesheet href=/admin/css.css type="text/css">
	<title><?=$AdminTrnsl["DownloadLibUploadfile"]?></title>
	<?usetemplate("_res")?>
</head>

<body leftmargin=0 topmargin=0 marginwidth=0 marginheight=0 class=main>

<table width=100% border=0 cellpadding=0 cellspacing=0 class=top3><td><img src=/admin/_img/s.gif width=1 height=1></td></table>
<table width=100% border=0 cellpadding=4 cellspacing=0>
<tr class=top41>
<td height=30 id=pad18 colspan=2 nowrap class=head><b><?=$AdminTrnsl["DownloadLib"]?> : <?=$AdminTrnsl["DownloadLibUploadingFile"]?></b></td>
</tr>
</table>
<table width=100% border=0 cellpadding=0 cellspacing=0 class=top2><td><img src=/admin/_img/s.gif width=1 height=1></td></table>
<table width=100% border=0 cellpadding=4 cellspacing=0>
<tr>
<td colspan=2 id=pad18 class=h4>

<br>
<table width=100% border=0 cellpadding=0 cellspacing=0><tr><td>
<? if(strlen($cdirPath)!=0) { ?>
<b class=h3><a href="/admin/filelib.php?cname=<?=$cname?>"><?=$AdminTrnsl["DownloadLibMainFolder"]?></a></b><?foreach(explode("/", $cdirPath) as $d){if (!$d){continue;}?><?$cd=$cd."/".$d?><b> : <a href="/admin/filelib.php?cdir=<?=$cd?>&cname=<?=$cname?>"><?=$d?></a></b><?}?>
<? } else {?>
<b><a href="/admin/filelib.php?cname=<?=cname?>"><?=$AdminTrnsl["DownloadLibMainFolder"]?></a></b>
<? } ?>
</td></table>
<br>
<script>
function checkFName(fname) {
	if (fname == '') {
		alert('<?=$AdminTrnsl["DownloadLib select file"]?>');
		return false;
	}

	var i = fname.lastIndexOf("/");
	if (i == -1) {
		i = fname.lastIndexOf("\\");
	}
	fname = fname.substring(i+1);

	if (!fname.match(/^[A-Za-z0-9_\-\. ]+$/)) {
		alert('<?=$AdminTrnsl["Illegal Char in Name"]?>');
		return false;
	}
	return true;
}</script>
<table width=100% border=0 cellpadding=0 cellspacing=0 class=backtab3>
<form action="/admin/filelib.php" method=POST enctype="multipart/form-data" onSubmit="return checkFName(this.dname.value)">
<input type="hidden" name="do" value="file_upload" />
<input type="hidden" name="cname" value="<?=$cname?>" />
<input type="hidden" name="cdir" value="<?=$cdirPath?>" />
<tr id=back2>
<td class=td2 width=20%><b><?=$AdminTrnsl["DownloadLibFile"]?> <span id=red>*</span></b></td>
<td id=back3>
<div class="errmsg"><?=$errmsg?></div><input type="file" name="dname" value=""></td>
</tr>
<tr id=back2>
<td>&nbsp;</td>
<td id=pad10><input type="submit" value="<?=$AdminTrnsl["DownloadLibUploadfile"]?>" class=button5></td>
</tr>
</form>
</table>

</td>
</tr></table>

</body>
</html>
