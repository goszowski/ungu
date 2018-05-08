<?include("prepend.php")?>
<html>
<head><meta HTTP-EQUIV="content-type" CONTENT="text/html; charset=<?=ADMIN_CHARSET?>">
<META NAME="Robots" CONTENT="noindex">
  <title>Deeptree</title>
  <link rel="stylesheet" type="text/css" href="/admin/css.css">
  <link rel="stylesheet" type="text/css" href="/admin/mstree/deeptree.css">
</head>

<script>
function selectstart() {
	window.event.cancelBubble = true;
	window.event.returnValue = false;
	return false;
}
function reloadTreeEl(nodeID) {
	//alert('reloadTreeEl('+nodeID+')')
	var imgObj = eval('imgtree'+nodeID);
	if (!imgObj) return false;
	if (imgObj.innerHTML.indexOf('.') == -1) {
		var containerObj = eval('cnttree'+nodeID);
		containerObj.innerHTML ='';
		if (imgObj.innerHTML.indexOf('-') != -1) {
			imgObj.click();
			imgObj.click();
		}
	}
	return true;
}

function reloadTreeElForPrevOrNext(nodeID, url, page) {
	var imgObj = eval('imgtree'+nodeID);
	var containerObj = eval('cnttree'+nodeID);
	var labelObj = eval('tree'+nodeID);
	labelObj.xmlsrc = url + "&page="+page
	containerObj.innerHTML ='';
	if (imgObj.innerHTML.indexOf('-') != -1) {
		imgObj.click();
		imgObj.click();
	}
}

window.name = 'linkfieldwindow';
<?
$rn = $request->getParameter("rn");
$cs = $request->getParameter("cs");
$d = $request->getParameter("d");
$cname = $request->getParameter("cname");
?>
function selectNode(nodePath) {
    opener.document.forms["node_form"].elements["<?=$cname?>"].value = nodePath;
    window.close();
}
</script>

<body onselectstart="selectstart();" leftmargin=0 topmargin=0 marginwidth=0 marginheight=0 class=main>
<nobr>
<div id="cmtree" class="deeptree" CfgXMLSrc="/admin/linkfield_popup_treeconfig.php?&rn=<?=$rn?>&cs=<?=$cs?>&d=<?=$d?>"></div>
</nobr>

</body>
</html>
