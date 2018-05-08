<html>
<head>
<meta HTTP-EQUIV="content-type" CONTENT="text/html; charset=<?=ADMIN_CHARSET?>">
<meta http-equiv="Expires" content="0">
<meta http-equiv="Cache-Control" content="must-revalidate, post-check=0, pre-check=0">
<meta http-equiv="pragma" content="no-cache">
<?=$AdminTrnsl["Ok_node_has_been_moved_to_new_location"]?>
<script language="javascript">
    function _close() {
        window.opener.document.location.href="/admin/nodes.php?do=main&id=<?=$request->getParameter("node_id")?>&reload=1"
        window.close();
    }
</script>
<a href="javascript:_close()"><?=$AdminTrnsl["close_window"]?></a>