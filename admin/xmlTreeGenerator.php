<?php
include("prepend.php");

function getNodeXML($node, $selectedNode, $currentLoggedUser, $page = -1) {
	$strBuffer = "";

	if ($page >= 0) {
		$rpp = 30;

		$childrenCount = $node->getChildrenCountForAdminTree($currentLoggedUser);
		$pagesCount = floor($childrenCount / $rpp) + ($childrenCount % $rpp != 0 ? 1 : 0);
		$offset = $page*$rpp;
		$children = $node->getChildrenForAdminTree($currentLoggedUser, $offset, $rpp);
	} else {
		$children = $node->getChildrenForAdminTree($currentLoggedUser);
	}

	if ($page > 0) {
		//paging prev button
		$strBuffer .= "<PagingArrow imgSrc='/admin/_img/arrow-up.gif'" .
			" onClick=\"top.frames.treeFrame.reloadTreeElForPrevOrNext(" . $node->id . ", " . ($page - 1) . ");\"/>";
	}

	foreach ($children as $child) {
		$title = $child->name;
		if ($child->absolutePath=="/_subscribers") {
			$title .= " (".$child->getChildrenCount().")";
		}
		$src = "";
		$metaChildrenCount = $child->getChildrenCountForAdminTree($currentLoggedUser);
		if ($metaChildrenCount != 0) {
			$src = " NodeXmlSrc=\"/admin/xmlTreeGenerator.php?nodeid=" . $child->id . "\"";
		}

		$href = null;
		if (($child->adminUrl != null) && (strlen($child->adminUrl) != 0)) {
			$href = prepareStringForXML($child->adminUrl);
		} else {
			$href = "/admin/nodes.php?do=main&amp;id=" . $child->id;
		}
		$strBuffer .= "<TreeNode NodeId=\"tree" . $child->id . "\" Title=\"" . prepareStringForXML($title) . "\" " .
			$src .
			" Href=\"" . $href . "\" Target=\"main\"";

		$childStr = "";
		if ($selectedNode != null && $selectedNode->isSubNodeOf($child) && $metaChildrenCount > 0) {
			$strBuffer .= " expanded=\"true\"";
			$childStr = getNodeXML($child, $selectedNode, $currentLoggedUser);
		}
		$strBuffer .= ">";
		$strBuffer .= $childStr;
		$strBuffer .= "</TreeNode>";
	}

	if ($page != -1 && ($page+1) != $pagesCount) {
		//paging next button
		$strBuffer .= "<PagingArrow imgSrc='/admin/_img/arrow-down.gif'" .
			" onClick=\"top.frames.treeFrame.reloadTreeElForPrevOrNext(" . $node->id . ", " . ($page + 1) . ");\"/>";
	}

	return $strBuffer;
}

$currentLoggedUser = $session->getAttribute(SESSION_LOGGED_USER_ATTR);

Header("Content-type: text/xml;charset=".ADMIN_CHARSET);
echo "<?xml version='1.0' encoding='".ADMIN_CHARSET."'?>";
echo "<TreeNode>";

$node_id = (int)$request->getParameter("nodeid");
$selected_id = (int)$request->getParameter("selected_id");

if ($node_id == -1) {
	$root = Node::findRoot();
/*
	echo "<TreeNode NodeId=\"helptree0\" Title=\"Help\" Href=\"/admin/help/index.php\""
			. " Target=\"main\" NodeXmlSrc=\"/admin/mstree/help_tree_xml.php\">";
	echo "</TreeNode>";
*/
	$showSiteSettings = false; //temporary
	if ($currentLoggedUser->group->canManageUsers) {
		echo "<TreeNode NodeId=\"userstree0\" Title=\"".$AdminTrnsl["Users_Management"]."\" Href=\"/admin/users.php\" Target=\"main\"/>";
	} else {
		$showSiteSettings = false;
	}
	if ($currentLoggedUser->group->canManageClasses) {
		echo "<TreeNode NodeId=\"classestree0\" Title=\"".$AdminTrnsl["Classes_Management"]."\" Href=\"/admin/classes.php\" Target=\"main\"/>";
	} else {
		$showSiteSettings = false;
	}
	if (_MODERATION_FEATURE_ === true && $CurrentAdminUser->group->isModerator) {
		echo "<TreeNode NodeId=\"moderation0\" Title=\"".$AdminTrnsl["Nodes_Moderation"]."\" Href=\"/admin/moderation.php\" Target=\"main\"/>";
	}
	if ($showSiteSettings) {
		echo "<TreeNode NodeId=\"settingstree0\" Title=\"Site Settings\" Href=\"/admin/settings.php\" Target=\"main\"/>";
	}
	if ($currentLoggedUser->group->canManageImgLib) {

		//$AdminTrnsl["Download_Library"]
		echo "<TreeNode NodeId=\"imglib0\" Title=\"".$AdminTrnsl["DownloadLib"]."\" Href=\"/admin/filelib.php\" Target=\"main\"/>";
		echo "<TreeNode NodeId=\"imglib0\" Title=\"".$AdminTrnsl["Image_Library"]."\" Href=\"/admin/imglib.php\" Target=\"main\"/>";
		//echo "<TreeNode NodeId=\"post0\" Title=\"".$AdminTrnsl["Post_Management"]."\" Href=\"/admin/post.php\" Target=\"main\"/>";
	}

	if (_FORMS_FEATURE_ === true) {
		echo "<TreeNode NodeId=\"forms0\" Title=\"".$AdminTrnsl["Forms"]."\" Href=\"/admin/forms.php\" Target=\"main\"/>";
	}

	//echo "<TreeNode NodeId=\"help0\" Title=\"".$AdminTrnsl["Help"]."\" Href=\"/admin/help.php\" Target=\"main\"/>";
	echo "<Separator/>";

	$ctreeString = "<TreeNode NodeId=\"tree0\" Title=\"".$AdminTrnsl["Content_tree"]."\"";

	if ($root == null) {
		$ctreeString .= " Href=\"/admin/nodes.php?do=addroot_form\" Target=\"main\"";
	} else {
		$ctreeString .= " Href=\"/admin/nodes.php?do=contenttree\" Target=\"main\" expanded=\"true\"";
	}

	$ctreeString .= '>';
	//echo $ctreeString;

	if ($root != null) {
		echo "<TreeNode NodeId=\"tree";
		echo $root->id;
		echo "\" Title=\"";
		echo prepareStringForXML($root->name);
		echo "\" Href=\"/admin/nodes.php?do=main&amp;id=";
		echo $root->id;
		echo "\" Target=\"main\"";

		if ($root->getChildrenCountForAdminTree($currentLoggedUser) > 0) {
			echo " NodeXmlSrc=\"/admin/xmlTreeGenerator.php?nodeid=";
			echo $root->id;
			echo '"';
			echo " expanded=\"true\"";
			echo '>';

			$selectedNode = Node::findById($selected_id);
			echo getNodeXML($root, $selectedNode, $currentLoggedUser);

			echo "</TreeNode>";
		} else {
			echo "/>";
		}
	}
	//echo "</TreeNode>";
} else {
	$current = Node::findById($node_id);
	if ($current != null) {
		$selectedNode = Node::findById($selected_id);
		echo getNodeXML($current, $selectedNode, $currentLoggedUser, (int)$request->getParameter("page"));
	}
}
echo "</TreeNode>";
?>
