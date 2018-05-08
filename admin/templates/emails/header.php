<html>
<head><meta HTTP-EQUIV="content-type" CONTENT="text/html; charset=<?=ADMIN_CHARSET?>">
<title></title>
<link rel=stylesheet href=/admin/css.css type="text/css">
<script type="text/javascript" src="/admin/js/refresh_tree.js"></script>
<script type="text/javascript" src="/admin/js/wysiwyg.js"></script>
<script language="JavaScript">
    function wop(url, w, h) {
        w = window.open(url,'node_props','resizable=yes,toolbar=no,location=no,directories=no,status=no,menubar=no,width='+w+', height='+h+',scrollbars=yes,fullscreen=no,top=100, left=100');
		w.focus();
    }
</script>
</head>

<body leftmargin=0 topmargin=0 marginwidth=0 marginheight=0 class=main<?if ($request->getParameter('reload') != null) {?> onLoad="refreshTree(<?=$node->id?>, <?=$node->parent_id+0?>)"<?}?>>

<table width=100% border=0 cellpadding=0 cellspacing=0 class=top3><td><img src=/admin/_img/s.gif width=1 height=1></td></table>

<table width=100% border=0 cellpadding=4 cellspacing=0>
<tr>
<td id=pad18>
