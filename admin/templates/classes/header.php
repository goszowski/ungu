<html>
<head>
<meta HTTP-EQUIV="content-type" CONTENT="text/html;charset=<?=ADMIN_CHARSET?>">
<meta http-equiv="Expires" content="0">
<meta http-equiv="Cache-Control" content="must-revalidate, post-check=0, pre-check=0">
<meta http-equiv="pragma" content="no-cache">
<title></title>
<link rel=stylesheet href=/admin/css.css type="text/css">
<script type="text/javascript" src="/admin/js/refresh_tree.js"></script>
</head>
<body leftmargin=0 topmargin=0 marginwidth=0 marginheight=0 class=main <?if ($request->getParameter("reload") != null) {?> onLoad="refreshTree()"<?}?>>
<!--
<table width=100% border=0 cellpadding=4 cellspacing=0>
<tr class=top1>
<td height=20 class=small id=pad18><b><?=$AdminTrnsl["Classes"]?></b></td>
</tr>
<tr class=top2>
<td height=20 class=small id=pad18><?=$class->name?></td>
</tr>
</table>
-->