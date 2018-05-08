<html>
<head>
  <title>Deeptree</title>

	<meta HTTP-EQUIV="content-type" CONTENT="text/html; charset=<?=ADMIN_CHARSET?>">
	<meta http-equiv="Expires" content="0">
	<meta http-equiv="Cache-Control" content="must-revalidate, post-check=0, pre-check=0">
	<meta http-equiv="pragma" content="no-cache">

	<link rel="stylesheet" type="text/css" href="/admin/css.css">
	<link rel="stylesheet" type="text/css" href="/extjs/resources/css/ext-all.css" />

	<script type="text/javascript" src="/extjs/adapter/ext/ext-base.js"></script>
	<script type="text/javascript" src="/extjs/ext-all.js"></script>

    <?usetemplate("_res")?>
</head>

<script>
function selectstart() {
	window.event.cancelBubble = true;
	window.event.returnValue = false;
	return false;
}

function selectNode(np_id) {
    window.document.location.href = '/admin/nodes.php?do=move_node&node_id=<?=$request->getParameter("node_id")?>&np_id='+np_id;
}

Ext.onReady(function(){
    // shorthand
    var Tree = Ext.tree;

    var treeLoader = new Tree.TreeLoader({
			dataUrl:'/admin/nodes.php?do=move_node_jsontree&node=treenode-1'
		});
		treeLoader.baseParams.node_id = <?=$request->getParameter("node_id")?>;

    var tree = new Tree.TreePanel({
        el:'tree-div',
        autoScroll:true,
        animate:true,
        enableDD:true,
        containerScroll: true,
        rootVisible:false,
        loader: treeLoader
    });

    // set the root node
    var root = new Tree.AsyncTreeNode({
        text: 'Ext JS',
        draggable:false,
        id:'treenode-1'
    });
    tree.setRootNode(root);

    // render the tree
    tree.render();
    root.expand();
});

</script>

<body onselectstart="selectstart();" leftmargin=0 topmargin=0 marginwidth=0 marginheight=0 class=main>

<?if (!$NoValidNodes) {?>
<?=$AdminTrnsl["NodeMoveSelectTargetNode"]?>
<nobr>
<div id="tree-div" style="overflow:auto; height:100%;width:100%;border:1px solid #c3daf9;"></div>
</nobr>
<?}else{?>
<table width=100% border=0 cellpadding=4 cellspacing=0>
<tr>
<td id=pad18 width=10%>
<?=$AdminTrnsl["NodeMoveNoValidNodes"]?>
</TD>
</TR>
</table>
<?}?>
</body>
</html>
