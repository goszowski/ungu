<?include("prepend.php")?>
<html>
<meta HTTP-EQUIV="content-type" CONTENT="text/html; charset=<?=ADMIN_CHARSET?>">
<meta http-equiv="Expires" content="0">
<meta http-equiv="Cache-Control" content="must-revalidate, post-check=0, pre-check=0">
<meta http-equiv="pragma" content="no-cache">

<title>Deeptree</title>
<link rel="stylesheet" type="text/css" href="/admin/css.css">
<link rel="stylesheet" type="text/css" href="/extjs/resources/css/ext-all.css" />
<link rel="stylesheet" type="text/css" href="/admin/app.min.css">
</head>

<!-- GC -->
<!-- LIBS -->
<script type="text/javascript" src="/extjs/adapter/ext/ext-base.js"></script>
<!-- ENDLIBS -->

<script type="text/javascript" src="/extjs/ext-all.js"></script>

<script>
if (top.location == self.location) {
	top.location.href = "/";
}
function selectstart() {
	window.event.cancelBubble = true;
	window.event.returnValue = false;
	return false;
}

function reloadEl(imgObj, containerObj) {
	if (imgObj.innerHTML.indexOf('-') != -1) {
		imgObj.click();
		imgObj.click();
	}
}

function reloadTreeEl(nodeID) {
	var el = tree.getNodeById('treenode' + nodeID);
	if (!el) {
		return false;
	}

	if (imgObj.innerHTML.indexOf('.') == -1) {
		var containerObj = eval('cnt'+nodeID);
		var labelObj = eval(nodeID);
		//alert(labelObj.xmlsrc);
		containerObj.innerHTML ='';
		reloadEl(imgObj, containerObj);
	}
	return true;
}

Ext.onReady(function(){
    // shorthand
    var Tree = Ext.tree;
    
    var treeLoader = new Tree.TreeLoader({
            dataUrl:'/admin/jsonTreeGenerator.php?node=treenode-1'
	});
	<?if ($request->getParameter("selected_id")!=null) {?>
	treeLoader.baseParams.selected_id = <?=$request->getParameter("selected_id")?>;
	<?}?>
    
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
        text: 'Runsite.CMS',
        draggable:false,
        id:'treenode-1'
    });
    tree.setRootNode(root);

    // render the tree
    tree.render();
    root.expand();
});

</script>

<body onselectstart="selectstart();" leftmargin=0 topmargin=0 marginwidth=0 marginheight=0 class=tree>

<nobr>
<?$root = Node::findRoot();?>


<div id="cmtree" class="deeptree" CfgXMLSrc="/admin/mstree/deeptreeconfig_xml.php<?if ($request->getParameter("selected_id")!=null) {?>?selected_id=<?=$request->getParameter("selected_id")?><?}?>">
</div>


<div id="tree-div" style="overflow:auto; height:100%;width:100%;"></div>
</nobr>
</body>
</html>
