<?include("prepend.php")?>
<html>
<head>
<link rel="icon" type="image/png" href="/favicon.png" />
<meta HTTP-EQUIV="content-type" CONTENT="text/html; charset=<?=ADMIN_CHARSET?>">
<meta http-equiv="Expires" content="0">
<meta http-equiv="Cache-Control" content="must-revalidate, post-check=0, pre-check=0">
<meta http-equiv="pragma" content="no-cache">
<title>Admin Module</title>
</head>
<frameset cols="240,*" border=0>
  <frameset rows="50,*" border=0>
    <frame name="logoFrame" src="/admin/top.php" marginwidth=0 marginheight=0 scrolling=no frameborder=0>
    <frame name="treeFrame" src="/admin/tree.php" marginwidth=0 marginheight=0 scrolling=no frameborder=0>
  </frameset>
  <frame name="main" src="/admin/nodes.php" marginwidth=0 marginheight=0 scrolling="auto" frameborder=0>
</frameset>
</html>
