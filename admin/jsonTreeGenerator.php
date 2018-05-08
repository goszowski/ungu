<?php
include("prepend.php");

function getNodes($node, $selectedNode, $currentLoggedUser, $page = -1) {
	$nodes = array();
	$children = $node->getChildrenForAdminTree($currentLoggedUser);

	foreach ($children as $child) {
		$title = $child->name;
		if ($child->absolutePath=="/_subscribers") {
			$title .= " (".$child->getChildrenCount().")";
		}

		$metaChildrenCount = $child->getChildrenCountForAdminTree($currentLoggedUser);

		$href = null;
		if (($child->adminUrl != null) && (strlen($child->adminUrl) != 0)) {
			$href = prepareStringForXML($child->adminUrl);
		} else {
			$href = "/admin/nodes.php?do=main&amp;id=" . $child->id;
		}
		$strBuffer .= "<TreeNode NodeId=\"tree" . $child->id . "\" Title=\"" . prepareStringForXML($title) . "\" " .
			$src .
			" Href=\"" . $href . "\" Target=\"main\"";
		
		$node = array();
		$node['id'] = 'treenode' . $child->id;
		$node['text'] = prepareStringForXML($title);
		//$node['cls'] = 'clsLabel';
		$node['href'] = $href;
		$node['hrefTarget'] = 'main';
		$node['allowDrag'] = false;

		if ($metaChildrenCount != 0) {
			$node['expandable'] = true;
			$node['leaf'] = false;
		} else {
			$node['leaf'] = true;
		}

		if ($selectedNode != null && $selectedNode->isSubNodeOf($child) && $metaChildrenCount > 0) {
			$node['expanded'] = true;

			$chilChildren = getNodes($child, $selectedNode, $currentLoggedUser);
			$node['children'] = $chilChildren;
		}
		$nodes[] = $node;
	}

	return $nodes;
}

$currentLoggedUser = $session->getAttribute(SESSION_LOGGED_USER_ATTR);

Header("Content-type: text/plain;charset=".ADMIN_CHARSET);

$nodes = array();

$node = $request->getParameter("node");

$node_id = (int)substr($node, 8);
$selected_id = (int)$request->getParameter("selected_id");

if ($node_id == -1) {
	$root = Node::findRoot();

	$showSiteSettings = false; //temporary
	$node = array();
	if ($currentLoggedUser->group->canManageUsers) {
		//echo "<TreeNode NodeId=\"userstree0\" Title=\"".$AdminTrnsl["Users_Management"]."\" Href=\"/admin/users.php\" Target=\"main\"/>";
		$node['id'] = 'userstree0';
		$node['text'] = $AdminTrnsl["Users_Management"];
		$node['leaf'] = true;
		//$node['cls'] = 'clsLabel';
		$node['href'] = '/admin/users.php';
		$node['hrefTarget'] = 'main';
		$node['allowDrag'] = false;
		$nodes[] = $node;
	} else {
		$showSiteSettings = false;
	}
	if ($currentLoggedUser->group->canManageClasses) {
		//echo "<TreeNode NodeId=\"classestree0\" Title=\"".$AdminTrnsl["Classes_Management"]."\" Href=\"/admin/classes.php\" Target=\"main\"/>";
		$node['id'] = 'classestree0';
		$node['text'] = $AdminTrnsl["Classes_Management"];
		$node['leaf'] = true;
		//$node['cls'] = 'clsLabel';
		$node['href'] = '/admin/classes.php';
		$node['hrefTarget'] = 'main';
		$node['allowDrag'] = false;
		$nodes[] = $node;
	} else {
		$showSiteSettings = false;
	}
	if (_MODERATION_FEATURE_ === true && $CurrentAdminUser->group->isModerator) {
		//echo "<TreeNode NodeId=\"moderation0\" Title=\"".$AdminTrnsl["Nodes_Moderation"]."\" Href=\"/admin/moderation.php\" Target=\"main\"/>";
	}
	if ($showSiteSettings) {
		//echo "<TreeNode NodeId=\"settingstree0\" Title=\"Site Settings\" Href=\"/admin/settings.php\" Target=\"main\"/>";
	}
	if ($currentLoggedUser->group->canManageImgLib) {

		//$AdminTrnsl["Download_Library"]
		//echo "<TreeNode NodeId=\"imglib0\" Title=\"".$AdminTrnsl["DownloadLib"]."\" Href=\"/admin/filelib.php\" Target=\"main\"/>";
		//echo "<TreeNode NodeId=\"imglib0\" Title=\"".$AdminTrnsl["Image_Library"]."\" Href=\"/admin/imglib.php\" Target=\"main\"/>";
		//echo "<TreeNode NodeId=\"post0\" Title=\"".$AdminTrnsl["Post_Management"]."\" Href=\"/admin/post0.php\" Target=\"main\"/>";
		$node['id'] = 'filelib0';
		$node['text'] = $AdminTrnsl["DownloadLib"];
		$node['leaf'] = true;
		//$node['cls'] = 'clsLabel';
		$node['href'] = '/admin/filelib.php';
		$node['hrefTarget'] = 'main';
		$node['allowDrag'] = false;
		$nodes[] = $node;
		
		$node['id'] = 'imglib0';
		$node['text'] = $AdminTrnsl["Image_Library"];
		$node['leaf'] = true;
		//$node['cls'] = 'clsLabel';
		$node['href'] = '/admin/imglib.php';
		$node['hrefTarget'] = 'main';
		$node['allowDrag'] = false;
		$nodes[] = $node;
	}

	if (_FORMS_FEATURE_ === true) {
		//echo "<TreeNode NodeId=\"forms0\" Title=\"".$AdminTrnsl["Forms"]."\" Href=\"/admin/forms.php\" Target=\"main\"/>";
	}

	//echo "<TreeNode NodeId=\"help0\" Title=\"".$AdminTrnsl["Help"]."\" Href=\"/admin/help.php\" Target=\"main\"/>";
	//echo "<Separator/>";

	$node['id'] = 'tree0';
	$node['text'] = $AdminTrnsl["Content_tree"];
	$node['hrefTarget'] = 'main';
	$node['allowDrag'] = false;

	if ($root == null) {
		//$node['cls'] = 'clsLabel';
		$node['leaf'] = true;
		$node['href'] = '/admin/nodes.php?do=addroot_form';
	} else {
		//$node['cls'] = 'clsLabel';
		$node['href'] = '/admin/nodes.php?do=contenttree';
		$node['leaf'] = false;
		$node['expanded'] = true;
	}

	//$nodes[] = $node;
	if ($root != null) {
		$node['id'] = 'treenode' . $root->id;
		$node['text'] = prepareStringForXML($root->name);
		//$node['cls'] = 'clsLabel';
		$node['href'] = '/admin/nodes.php?do=main&amp;id=' . $root->id;
		$node['hrefTarget'] = 'main';
		$node['allowDrag'] = false;

		if ($root->getChildrenCountForAdminTree($currentLoggedUser) > 0) {
			$node['leaf'] = false;
			$node['expanded'] = true;
			$node['expandable'] = true;

			$selectedNode = Node::findById($selected_id);
			$node['children'] = getNodes($root, $selectedNode, $currentLoggedUser);

		} else {
			$node['leaf'] = true;
		}
		$nodes[] = $node;
	}
} else {
	$current = Node::findById($node_id);
	if ($current != null) {
		$selectedNode = Node::findById($selected_id);
		$nodes = array_merge($nodes, getNodes($current, $selectedNode, $currentLoggedUser));
	}
}


echo json_encode($nodes);
?>