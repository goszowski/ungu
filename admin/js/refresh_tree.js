// ******
// ***** refresh tree
// ******
function refreshTree(node_id, parent_id) {
	var treeFrame;
	if (window.opener != null) {
		treeFrame = window.opener.top.frames["treeFrame"];
	} else {
		treeFrame = top.frames["treeFrame"];
	}

	if (parent_id == 0 || !treeFrame.reloadTreeEl('tree'+parent_id)) {
		treeFrame.location = "/admin/tree.php?selected_id=" + node_id;
	}
}

function hardRefreshTree(node_id, parent_id) {
	var treeFrame;
	if (window.opener != null) {
		treeFrame = window.opener.top.frames["treeFrame"];
	} else {
		treeFrame = top.frames["treeFrame"];
	}
	treeFrame.location = "/admin/tree.php?selected_id=" + node_id;
}
