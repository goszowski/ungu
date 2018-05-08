<?include("prepend.php");?><html>
<head>
	<title></title>
	<meta HTTP-EQUIV="content-type" CONTENT="text/html; charset=<?=ADMIN_CHARSET?>">
	<meta http-equiv="Expires" content="0">
	<meta http-equiv="Cache-Control" content="must-revalidate, post-check=0, pre-check=0">
	<meta http-equiv="pragma" content="no-cache">
	<link rel="stylesheet" href="/admin/css.css" type="text/css">
	<?usetemplate('_res');?>
</head>
<body leftmargin="0" topmargin="0" marginwidth="0" marginheight="0">

<div class="navbar-header bg-white box-shadow-inset dk">
	<a class="navbar-brand text-lt">
        <i class="glyphicon glyphicon-th-large text-md"></i><span class="hidden-folded m-l-xs">runsite.CMS<sup class="text-xs font-thin">1.0</sup></span>
    </a>
</div>

<table width=100% border=0 cellpadding="0" cellspacing="0">
<tr class="top11">
<td height=20 style="padding-left:7px"><a href="/" id=white target=_blank style="text-decoration:none"><?= $request->getServerName() ?></a></td>
<td align=right><a href="javascript:{top.main.location.reload();}"><img src="/admin/_img/reload.gif" width="13" height="16" alt="Reload Frame" border="0"></a> &nbsp;&nbsp;</td></tr>
<tr class=top2>
<td colspan=2 align=right class=small nowrap height=20><?=$AdminTrnsl["user"]?> <b id=red><?=$CurrentAdminUser->login?></b>&nbsp;|&nbsp;<a href="/admin/logout.php" target="_top" id=black><?=$AdminTrnsl["logout"]?></a>&nbsp;&nbsp;</td>
</tr>
</table>
</body>
</html>
