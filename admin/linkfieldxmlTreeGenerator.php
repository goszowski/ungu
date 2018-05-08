<?php
include("prepend.php");

function getNodeXML($node, $depth, $page) {
	global $allowed_classes,$node_path,$allowed_classes_str;
	$strBuffer = "";

	if ($page >= 0) {
		$rpp = 10;

		$childrenCount = $node->getChildrenCount();
		$pagesCount = floor($childrenCount / $rpp) + ($childrenCount % $rpp != 0 ? 1 : 0);
		$offset = $page*$rpp;
		$children = $node->getChildren("subtree_order", $offset, $rpp);
		
		$pagingUrl = "/admin/linkfieldxmlTreeGenerator.php?nodepath=".$node->absolutePath."&amp;allowed_classes=".$allowed_classes_str . "&amp;depth=" . ($depth-1) ."";
	} else {
		$children = $node->getChildren();
	}

	if ($page > 0) {
		//paging prev button
		$strBuffer .= "<PagingArrow imgSrc='/admin/_img/arrow-up.gif'" .
			" onClick=\"top.reloadTreeElForPrevOrNext(" . $node->id . ", '" .$pagingUrl . "', " . ($page - 1) . ");\"/>";
	}

	foreach ($children as $child) {
		$title = $child->name;

		if ($allowed_classes_str != "*") {
			$hasAllowedChildren = $child->hasChildrenOfClasses($allowed_classes, $depth);
			$cnc = $child->getNodeClass();
			$isOfOneOfAllowedClasses = in_array($cnc->id, $allowed_classes);
		} else {
			$hasAllowedChildren = ($child->getChildrenCount() > 0);
			$isOfOneOfAllowedClasses = true;
		}
		if ($hasAllowedChildren || $isOfOneOfAllowedClasses) {
			$src = "";
			if ($hasAllowedChildren) {
				$src = " NodeXmlSrc=\"/admin/linkfieldxmlTreeGenerator.php?nodepath=".$child->absolutePath."&amp;allowed_classes=".$allowed_classes_str . "&amp;depth=" . ($depth-1) ."\"";
			}

			$strBuffer .= "<TreeNode NodeId=\"tree" . $child->id . "\" Title=\"" . prepareStringForXML($title) . "\" " .
				$src;
			if ($isOfOneOfAllowedClasses) {
				$strBuffer .= " Href=\"javascript:selectNode('" . $child->absolutePath . "')\" Target=\"linkfieldwindow\"";
			}

			$childStr = "";
			$strBuffer .= ">";
			$strBuffer .= $childStr;
			$strBuffer .= "</TreeNode>";
		}
	}

	if ($page != -1 && ($page+1) != $pagesCount) {
		//paging next button
		$strBuffer .= "<PagingArrow imgSrc='/admin/_img/arrow-down.gif'" .
			" onClick=\"top.reloadTreeElForPrevOrNext(" . $node->id . ", '" .$pagingUrl . "', " . ($page + 1) . ");\"/>";
	}

	return $strBuffer;
}

Header("Content-type: text/xml;charset=".ADMIN_CHARSET);
echo "<?xml version='1.0' encoding='".ADMIN_CHARSET."' ?>";
echo "<TreeNode>";

$node_path = $request->getParameter("nodepath");
$root = $request->getParameter("root");
$allowed_classes_str = $request->getParameter("allowed_classes");
if ($allowed_classes_str != "*") {
	$allowed_classes_shortnames = explode(",", $allowed_classes_str);
	$allowed_classes = array();
	foreach($allowed_classes_shortnames as $classShortname) {
		$nc = NodeClass::findByShortname($classShortname);
		$allowed_classes[] = $nc->id;
	}
}
$depth = $request->getParameter("depth");

$current = Node::findByPath($node_path);

if ($root == 'true') {
	if ($allowed_classes_str != "*") {
		$hasAllowedChildren = $current->hasChildrenOfClasses($allowed_classes, $depth);
		$cnc = $current->getNodeClass();
		$isOfOneOfAllowedClasses = in_array($cnc->id, $allowed_classes);
	} else {
		$hasAllowedChildren = ($current->getChildrenCount() > 0);
		$isOfOneOfAllowedClasses = true;
	}

	echo "<TreeNode NodeId=\"tree";
	echo $current->id;
	echo "\" Title=\"";
	echo prepareStringForXML($current->name);
	echo "\"";
	if ($isOfOneOfAllowedClasses) {
		echo " Href=\"javascript: selectNode('";
		echo $current->absolutePath;
		echo "')\" Target=\"linkfieldwindow\"";
	}

	if ($hasAllowedChildren) {
		echo " expanded=\"true\"";
		echo '>';

		echo getNodeXML($current, $depth-1, -1);

		echo "</TreeNode>";
	} else {
		echo "/>";
	}
} else {
	if ($current != null) {
		echo getNodeXML($current, $depth, (int)$request->getParameter("page"));
	}
}
echo "</TreeNode>";

?>