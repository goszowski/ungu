<?php

require_once("prepend.php");

define('NODES_PER_PAGE', 30);
define('NODELINKS_PER_PAGE', 20);

$action = (string)$request->getParameter("do");

if ($action == null || $action=="") {
	$action = "_default";
}

$action($request);
function _default(&$request) {
	contenttree($request);
}

function contenttree(&$request) {
	global $session;
	$currentLoggedUser = $session->getAttribute(SESSION_LOGGED_USER_ATTR);
	$root = Node::findRoot();
	$nodeClass = $root->getNodeClass();

	$shownFieldDefs = array();
	$fieldDefs = $nodeClass->getFieldDefs();
	$fieldDefsKeys = array_keys($fieldDefs);
	foreach ($fieldDefsKeys as $k) {
		$fd = $fieldDefs[$k];
		if ($fd->shown) {
			$shownFieldDefs[] = &$fd;
		}
	}

	$rootMap->node = &$root;
	$ndGR = &new NodeGroupRights($currentLoggedUser->group, $root);
	$rootMap->hasWriteRight = $ndGR->hasWriteRight();
	$rootMap->hasViewRight = $ndGR->hasViewRight();

	$item->nodes = array($rootMap);
	$item->shownFields = $shownFieldDefs;
	$item->fieldCount = sizeof($fieldDefs);
	$item->nodeClass = &$nodeClass;
	$item->sort = false;

	$request->setAttribute("item", $item);
	usetemplate("nodes/contenttree");
}

function main(&$request) {
	editnode($request);
}
/*
 function addnode_moder_form(&$request) {
	global $session;
	$currentLoggedUser = $session->getAttribute(SESSION_LOGGED_USER_ATTR);

	$mid = (int)$request->getParameter("mid");
	$ma = &ModeratedAction::findById($mid);

	$data = &$ma->data;

	$nodeClass = &NodeClass::findById($data->class_id);
	$parentId = $data->parent_id;
	$parent = &Node::findById($parentId);

	if ($parent != null) {
	$gr = &new NodeGroupRights($currentLoggedUser->group, $parent);
	$request->setAttribute("userHasWriteRights", $gr->hasWriteRight());
	$pList = &$parent->getParentList();
	$pList[] = &$parent;
	$request->setAttribute("parents", $pList);
	$request->setAttribute("parent", $parent);
	$request->setAttribute("parent_id", $parent->id);
	} else {
	$request->setAttribute("userHasWriteRights", true);
	$request->setAttribute("parents", array());
	$request->setAttribute("parent_id", -1);
	}
	$request->setAttribute("nodeClass", $nodeClass);
	$fieldDefs = &$nodeClass->getFieldDefs();


	$iparams = array();

	$fieldDefsKeys = array_keys($fieldDefs);
	foreach ($fieldDefsKeys as $k) {
	$fieldDef = &$fieldDefs[$k];

	$item = &new PHPObject();

	$shortname = $fieldDef->shortname;

	$field = &$data->fieldList[$shortname];
	$params = null;
	$params = $field->getParamersForIncludeControlJSP();

	$item->jspname = $field->getJSPControlName();
	$item->fieldDef = &$fieldDef;
	$item->params = $params;
	$iparams[] = &$item;
	}

	$request->setAttribute("fieldDefs", $iparams);
	$request->setAttribute("validationErrors", $fieldErrors);

	$nodePropsMap = new phpobject();
	$nodePropsMap->NodeName = $data->NodeName;
	$nodePropsMap->NodeShortname = $data->NodeShortname;
	$nodePropsMap->NodeDynamicTemplate = $data->NodeDynamicTemplate;
	$nodePropsMap->NodeAdminURL = $data->NodeAdminURL;
	$request->setAttribute("nodeProps", $nodePropsMap);

	$request->setAttribute("moder_action", $ma);

	usetemplate("nodes/addnode");
	}
	*/
function addnode_form(&$request) {
	global $session;
	$currentLoggedUser = $session->getAttribute(SESSION_LOGGED_USER_ATTR);

	$nodeClass = NodeClass::findById($request->getParameter("class_id"));
	if ($nodeClass == null) {
		Header("Location: /admin/main.php");die();
	}
	$parentId = (int)$request->getParameter("parent_id");
	$parent = Node::findById($parentId);
	if (($parent == null) && (Node::hasRoot())) {
		Header("Location: /admin/main.php");die();
	}

	if ($parent != null) {
		$gr = &new NodeGroupRights($currentLoggedUser->group, $parent);
		$request->setAttribute("userHasWriteRights", $gr->hasWriteRight());
		$pList = $parent->getParentList();
		$pList[] = &$parent;
		$request->setAttribute("parents", $pList);
		$request->setAttribute("parent", $parent);
		$request->setAttribute("parent_id", $parent->id);
	} else {
		$request->setAttribute("userHasWriteRights", true);
		$request->setAttribute("parents", array());
		$request->setAttribute("parent_id", -1);
	}
	$request->setAttribute("nodeClass", $nodeClass);
	$fieldDefs = $nodeClass->getFieldDefs();

	$iparams = array();
	$fieldDefsKeys = array_keys($fieldDefs);
	foreach ($fieldDefsKeys as $k) {
		$fieldDef = &$fieldDefs[$k];

		$item = &new PHPObject();

		$field = $fieldDef->getFieldInstance();
		$field->setDefaultValue();
		if ($fieldDef->shortname == "secret") {
			$secert = substr(md5(uniqid(rand(), true)), 0, 10);
			$field->setValue($secert);
		}
		$params = $field->getParamersForIncludeControlJSP();

		$item->jspname = $field->getJSPControlName();
		$item->fieldDef = &$fieldDef;
		$item->params = $params;
		$iparams[] = &$item;
	}
	$fieldDefsArray = array($iparams);
	$request->setAttribute("fieldDefsArray", $fieldDefsArray);

	$nodePropsMap = new phpobject();
	$nodePropsMap->NodeDynamicTemplate = $nodeClass->default_template;
	$nodePropsArray = array($nodePropsMap);

	$formsCount = 1;

	$request->setAttribute("nodePropsArray", $nodePropsArray);
	$request->setAttribute("formsCount", $formsCount);
	$request->setAttribute("parent", $parent);

	usetemplate("nodes/addnode");
}

function addnode(&$request) {
	global $session, $AdminTrnsl;
	$currentLoggedUser = $session->getAttribute(SESSION_LOGGED_USER_ATTR);

	$nodeClass = & NodeClass::findById((int)$request->getParameter("class_id"));
	if ($nodeClass == null) {
		Header("Location: /admin/main.php");die();
	}
	$parentId = (int)$request->getParameter("parent_id");
	$parent = Node::findById($parentId);
	if (($parent == null) && (Node::hasRoot())) {
		Header("Location: /admin/main.php");die();
	}

	/*
	 $moder_id = (int)$request->getParameter("moder_id");
	 if ($moder_id) {
		$ma = &ModeratedAction::findById($moder_id);
		}
		*/

	if ($parent != null) {
		$gr = &new NodeGroupRights($currentLoggedUser->group, $parent);
		$request->setAttribute("userHasWriteRights", $gr->hasWriteRight());
		if (!$gr->hasWriteRight()) {
			die($AdminTrnsl["You_do_not_have_rights_to_view_this_node"]);
		}
		$pList = &$parent->getParentList();
		$pList[] = &$parent;
		$request->setAttribute("parents", $pList);
		$request->setAttribute("parent", $parent);
		$request->setAttribute("parent_id", $parent->id);
	} else {
		$request->setAttribute("userHasWriteRights", true);
		$request->setAttribute("parents", array());
		$request->setAttribute("parent_id", -1);
	}
	$request->setAttribute("nodeClass", $nodeClass);

	$fieldDefs = $nodeClass->getFieldDefs();

	$formsCount = (int)$request->getParameter("forms_count");

	$everythingWasSuccess = true;

	$fieldErrorsArray = array();
	$nodePropsArray = array();
	$fieldListArray = array();
	for ($formNumber = 0; $formNumber < $formsCount; $formNumber++) {
		$formNumberFormatted = sprintf("%02d", $formNumber);
		$fieldNamePrefix = "f" . $formNumberFormatted . "_";

		//it's a nice trick for multiple forms
		$request->parametersPrefix = $fieldNamePrefix;

		//let's validate fields
		$fieldErrors = array();
		$fieldList = array();

		$fieldDefsKeys = array_keys($fieldDefs);
		foreach ($fieldDefsKeys as $k) {
			$fieldDef = &$fieldDefs[$k];
			$field = $fieldDef->getFieldInstance();
			$verror = $field->setValue($request);
			if ($verror) {
				$fieldErrors[$fieldDef->shortname] = $verror;
			}
			$fieldList[$fieldDef->shortname] = $field;
		}

		//now validate node fields
		$NodeName = (string)$request->getParameter("NodeName");
		$NodeShortname = (string)$request->getParameter("NodeShortname");
		$NodeDynamicTemplate = (string)$request->getParameter("NodeDynamicTemplate");
		$NodeAdminURL = (string)$request->getParameter("NodeAdminURL");

		if (strlen($NodeName) == 0)
		$fieldErrors["NodeName"] = $AdminTrnsl["RequiredField"];

		if ($parent != null) {
			if(!Validator::validateShortname($NodeShortname)) {
				$fieldErrors["NodeShortname"] = $AdminTrnsl["NodeEditInvalidShortname"];
			}
		} else {
			$NodeShortname = "";
		}

		if (($parent != null) && $parent->hasChildren($NodeShortname)) {
			$fieldErrors["NodeShortname"] = $AdminTrnsl["NodeEditShortnameUsed"];
		}

		$nodePropsMap = new phpobject();
		$nodePropsMap->NodeName = $NodeName;
		$nodePropsMap->NodeShortname = $NodeShortname;
		$nodePropsMap->NodeDynamicTemplate = $NodeDynamicTemplate;
		$nodePropsMap->NodeAdminURL = $NodeAdminURL;

		$fieldErrorsArray[$formNumber] = $fieldErrors;
		$nodePropsArray[$formNumber] = $nodePropsMap;
		$fieldListArray[$formNumber] = $fieldList;

		if (sizeof($fieldErrors) > 0) {
			$everythingWasSuccess = false;
		}

		//return back empty value
		$request->parametersPrefix = "";
	}

	//if validation not failed create node:
	if ($everythingWasSuccess == true) {
		//if ($currentLoggedUser->group->isModerator) {
		for ($formNumber = 0; $formNumber < $formsCount; $formNumber++) {
			$nodePropsMap = $nodePropsArray[$formNumber];
			$fieldList = $fieldListArray[$formNumber];

			$createdNode = & Node::createWithPreparedValues($parent, $nodePropsMap->NodeName, $nodePropsMap->NodeShortname,
			$nodePropsMap->NodeDynamicTemplate, $nodePropsMap->NodeAdminURL,
			$currentLoggedUser->id, $nodeClass->id, $fieldList);
			
			
			//kazancev add from version higher
			if ($nodePropsMap->NodeShortname == '' && $nodeClass->checkFlag("NAME_FROM_SHORTNAME")) {
				$NodeShortname = name2shortname($nodePropsMap->NodeName);
				if ($nodeClass->checkFlag("NAME_FROM_SHORTNAME_WITH_ID")) {
					$NodeShortname = $NodeShortname . "_" . $createdNode->subtreeOrder;
				}
				$createdNode->setShortname($NodeShortname);
				$createdNode->store();
			}
			
			//if ($ma) {
			//	ModeratedAction::remove($ma->id);
			//}
		}
		/*} else {
			$moderatedCreateAction = new PHPObject();
			$moderatedCreateAction->parent_id = $parent->id;
			$moderatedCreateAction->NodeName = $NodeName;
			$moderatedCreateAction->NodeShortname = $NodeShortname;
			$moderatedCreateAction->NodeDynamicTemplate = $NodeDynamicTemplate;
			$moderatedCreateAction->NodeAdminURL = $NodeAdminURL;
			$moderatedCreateAction->user_id = $currentLoggedUser->id;
			$moderatedCreateAction->class_id = $nodeClass->id;
			$moderatedCreateAction->fieldList = $fieldList;

			$moderatedCreateAction->ParentPath = $parent->absolutePath;

			ModeratedAction::create(-1, $currentLoggedUser->id, "create", $moderatedCreateAction);
			$forwardUrl = "/admin/nodes.php?do=main&id=" . $parent->id . "&msg=nodes_created_moderated";
			}*/

		$forwardUrl = "/admin/nodes.php?do=main&id=";
		$forwardUrl .= $createdNode->parent_id;
		Header("Location: " . $forwardUrl . "&reload=1");
		return;
	}

	$fieldDefsArray = array();

	for ($formNumber = 0; $formNumber < $formsCount; $formNumber++) {
		$iparams = array();
		$fieldList = $fieldListArray[$formNumber];

		$fieldDefsKeys = array_keys($fieldDefs);
		foreach ($fieldDefsKeys as $k) {
			$fieldDef = &$fieldDefs[$k];

			$item = &new PHPObject();

			$shortname = $fieldDef->shortname;
			$errMsg = $fieldErrors[$shortname];

			$field = &$fieldList[$shortname];
			$params = $field->getParamersForIncludeControlJSP();

			$item->jspname = $field->getJSPControlName();
			$item->fieldDef = &$fieldDef;
			$item->params = $params;
			$iparams[] = &$item;
		}
		$fieldDefsArray[$formNumber] = $iparams;
	}

	$request->setAttribute("fieldDefsArray", $fieldDefsArray);
	$request->setAttribute("validationErrorsArray", $fieldErrorsArray);
	$request->setAttribute("nodePropsArray", $nodePropsArray);
	$request->setAttribute("formsCount", $formsCount);
	$request->setAttribute("parent", $parent);

	//if ($ma) {
	//$request->setAttribute("moder_action", $ma);
	//}

	usetemplate("nodes/addnode");
}

function deletenode(&$request) {
	global $session;
	$currentLoggedUser = $session->getAttribute(SESSION_LOGGED_USER_ATTR);

	$node_id = (int)$request->getParameter("id");
	$node = & Node::findById($node_id);
	if ($node == null) {
		Header("Location: /admin/main.php");die();
	}

	$parent_id = $node->parent_id;

	$forwardUrl = null;

	if ($parent_id != null) {
		$forwardUrl = "/admin/nodes.php?do=main&id=" . $parent_id . "&reload=1&";
	} else {
		$forwardUrl = "/admin/main.php?";
	}

	if ($currentLoggedUser->group->isModerator) {
		Node::remove($node_id);
	} else {
		$moderatedCreateAction = new PHPObject();
		$moderatedCreateAction->NodeName = $node->name;
		$moderatedCreateAction->NodePath = $node->absolutePath;
		ModeratedAction::create($node_id, $currentLoggedUser->id, "delete", $moderatedCreateAction);
		$forwardUrl .= "msg=nodes_deleted_moderated";
	}

	Header("Location: ".$forwardUrl);
}


function editnode_moder(&$request) {
	global $session, $AdminTrnsl;

	$mid = (int)$request->getParameter("mid");
	$ma = &ModeratedAction::findById($mid);

	$data = &$ma->data;

	$node_id = $ma->node_id;
	$node = &Node::findById($node_id);
	if ($node == null) {
		Header("Location: /admin/main.php");die();
	}

	$currentLoggedUser = $session->getAttribute(SESSION_LOGGED_USER_ATTR);
	$gr = &new NodeGroupRights($currentLoggedUser->group, $node);
	if (!$gr->hasViewRight()) {
		die($AdminTrnsl["You_do_not_have_rights_to_view_this_node"]);
	}

	$all_deps = &$node->findAllDepends();
	$request->setAttribute("depends", $all_deps);

	$nodeClass = $node->getNodeClass();
	$request->setAttribute("nodeClass", $nodeClass);

	$request->setAttribute("node", $node);
	$fieldDefs = &$nodeClass->getFieldDefs();

	$iparams = array();

	$fieldDefsKeys = array_keys($fieldDefs);
	foreach ($fieldDefsKeys as $k) {
		$fieldDef = &$fieldDefs[$k];
		$fieldShortname = $fieldDef->shortname;
		$field = &$data->fieldList[$fieldShortname];
		$params = $field->getParamersForIncludeControlJSP();

		$item = &new PHPObject();
		$item->jspname = $field->getJSPControlName();
		$item->fieldDef = &$fieldDef;
		$item->params = $params;
		$iparams[] = &$item;
	}

	$request->setAttribute("fieldDefs", $iparams);
	$request->setAttribute("NodeName", $data->NodeName);
	subnodeslist($request, $node, $all_deps, $currentLoggedUser);

	$request->setAttribute("moder_action", $ma);

	usetemplate("nodes/editnode");
}

function editnode(&$request) {
	global $session, $AdminTrnsl;
	$node_id = (int)$request->getParameter("id");

	$node = Node::findById($node_id);
	if ($node == null) {
		Header("Location: /admin/main.php");die();
	}

	$currentLoggedUser = $session->getAttribute(SESSION_LOGGED_USER_ATTR);
	$gr = &new NodeGroupRights($currentLoggedUser->group, $node);
	if (!$gr->hasViewRight()) {
		die($AdminTrnsl["You_do_not_have_rights_to_view_this_node"]);
	}

	$all_deps = $node->findAllDepends();
	$request->setAttribute("depends", $all_deps);

	$nodeClass = $node->getNodeClass();
	$request->setAttribute("nodeClass", $nodeClass);

	$request->setAttribute("node", $node);
	$fieldDefs = $nodeClass->getFieldDefs();

	$iparams = array();

	$fieldDefsKeys = array_keys($fieldDefs);
	foreach ($fieldDefsKeys as $k) {
		$fieldDef = &$fieldDefs[$k];
		$fieldShortname = $fieldDef->shortname;
		$field = $node->getField($fieldShortname);
		$params = $field->getParamersForIncludeControlJSP();

		$item = &new PHPObject();
		$item->jspname = $field->getJSPControlName();
		$item->fieldDef = &$fieldDef;
		$item->params = $params;
		$iparams[] = &$item;
	}
	$request->setAttribute("fieldDefs", $iparams);
	$request->setAttribute("NodeName", $node->name);
	$request->setAttribute("validationErrors", array());
	$request->setAttribute("moder_action", null);

	$parent = $node->getParent();
	$request->setAttribute("parent", $parent);


	subnodeslist($request, $node, $all_deps, $currentLoggedUser);

	usetemplate("nodes/editnode");
}

function subnodeslist(&$request, &$node, &$all_deps, &$currentLoggedUser) {
	$pageNumMap = $request->getParameter("pagenum");
	$childrenByClasses = array();
	$childrenCount = 0;

	$all_deps_keys = array_keys($all_deps);
	foreach ($all_deps_keys as $k) {
		$nodeClass = &$all_deps[$k];

		$item = &new PHPObject();

		$item->nodeClass = &$nodeClass;
		$classChildsCount = $node->getChildrenCountOfClassForGroup($currentLoggedUser->group->id, $nodeClass->id);
		$orderBy = $nodeClass->orderBy;
		$orderByDesc = false;

		if (!$orderBy) {
			$orderBy = "n.subtree_order";
		}/* else {
		list($orderBy, $_descStr) = explode(" ", $orderBy);
		if (strtoupper(trim($_descStr)) == 'DESC') {
		$orderByDesc = true;
		}
		}*/

		if ($nodeClass->shortname == "subscriber") {
			$nodesPerPage = 100000;
		} else {
			$nodesPerPage = NODES_PER_PAGE;
		}
		if ($classChildsCount <= $nodesPerPage) {
			$classChilds = $node->getChildrenOfClassForGroup($currentLoggedUser->group->id, $nodeClass, $orderBy);
			$item->nodespagesCount = 0;
		} else {
			$page = $pageNumMap[$nodeClass->shortname];
			$pagesCount = floor($classChildsCount / $nodesPerPage) + ($classChildsCount % $nodesPerPage != 0 ? 1 : 0);
			$offset = $page*$nodesPerPage;
			$classChilds = $node->getChildrenOfClassForGroup($currentLoggedUser->group->id, $nodeClass, $orderBy, $offset, $nodesPerPage);

			$item->nodespagesCount = $pagesCount;
			$item->nodesOffset = $offset;
			$item->nodesPage = $page;
		}
		$classChildsP = array();
		$classChildsKeys = array_keys($classChilds);

		foreach ($classChildsKeys as $cck) {
			$nd = &$classChilds[$cck];

			$ndMap = &new PHPObject();
			$ndMap->node = &$nd;
			$ndGR = &new NodeGroupRights($currentLoggedUser->group, $nd);
			$ndMap->hasWriteRight = $ndGR->hasWriteRight();
			$ndMap->hasViewRight = $ndGR->hasViewRight();

			$classChildsP[] = &$ndMap;
		}

		$item->nodes = $classChildsP;
		$item->nodesCount = $classChildsCount;
		$childrenCount += sizeof($classChildsP);
		if (sizeof($classChildsP) > 1) {
			$item->sort = false; //Temporary!!!
		} else {
			$item->sort = false;
		}

		$shownFieldDefs = array();
		$fieldDefs = $nodeClass->getFieldDefs();
		$fieldDefsKeys = array_keys($fieldDefs);
		foreach ($fieldDefsKeys as $fdk) {
			$fieldDef = &$fieldDefs[$fdk];
			if ($fieldDef->shown) {
				$shownFieldDefs[] = &$fieldDef;
			}
		}

		$item->shownFields = $shownFieldDefs;
		$item->fieldCount = sizeof($fieldDefs);
		$childrenByClasses[] = &$item;
	}

	$request->setAttribute("childrenByClasses", $childrenByClasses);
	$request->setAttribute("childrenCount", $childrenCount);
}

function updatenode(&$request) {
	global $session, $AdminTrnsl;
	$currentLoggedUser = $session->getAttribute(SESSION_LOGGED_USER_ATTR);
	$node_id = (int)$request->getParameter("id");

	$node = Node::findById($node_id);
	if ($node == null) {
		Header("Location: /admin/main.php");die();
	}

	$mid = (int)$request->getParameter("moder_id");
	$ma = null;
	if ($mid != 0) {
		$ma = ModeratedAction::findById($mid);
	}

	$nodeClass = $node->getNodeClass();
	$request->setAttribute("nodeClass", $nodeClass);

	$cuGroupRights = &new NodeGroupRights($currentLoggedUser->group, $node);
	if (!$cuGroupRights->hasWriteRight()) {
		die($AdminTrnsl["You_do_not_have_rights_to_update_data_of_this_node"]);
	}

	$all_deps = $node->findAllDepends();
	$request->setAttribute("depends", $all_deps);

	//let's validate fields
	$fieldErrors = array();
	$fieldList = array();

	$_keys = array_keys($node->fields);
	foreach ($_keys as $fieldShortname) {
		$field = &$node->fields[$fieldShortname];
		$fieldDef = $field->getFieldDef();
		$verror = $field->setValue($request);
		if ($verror != null) {
			$fieldErrors[$fieldDef->shortname] = $verror;
		}
		$fieldList[$fieldDef->shortname] = &$field;
	}

	$NodeName = (string)$request->getParameter("NodeName");
	if (strlen($NodeName) == 0)
	$fieldErrors["NodeName"] = $AdminTrnsl["RequiredField"];

	//if validation not failed update fields data
	if (sizeof($fieldErrors) == 0) {
		$forwardUrl = "/admin/nodes.php?do=main&reload=yes&id=" . $node->id;

		if ($currentLoggedUser->group->isModerator) {
			
			//kazancev add
			/*
			if (($node->name != $NodeName) && $nodeClass->checkFlag("NAME_FROM_SHORTNAME")) {
				$NodeShortname = name2shortname($NodeName) . "_" . $node->id;
				$typeFD = $nodeClass->getFieldDef("type");
				if ($typeFD != null) {
					$ft = $typeFD->getFieldType();
					if ($ft->name == "NewLink") {
						$typeNode = $node->getField("type")->getLinkedNode();
						$NodeShortname = name2shortname($typeNode->name) . "_" . $NodeShortname;
					}
				}
				$node->setShortname($NodeShortname);
			}
			*/
			$node->name = $NodeName;
			$node->store();
			$forwardUrl .= "&msg=Node_update_success";
			if ($ma) {
				ModeratedAction::remove($ma->id);
			}
		} else {
			$moderatedCreateAction = new PHPObject();
			$moderatedCreateAction->NodeName = $NodeName;
			$moderatedCreateAction->NodePath = $node->absolutePath;
			$moderatedCreateAction->fieldList = $fieldList;
			ModeratedAction::create($node->id, $currentLoggedUser->id, "update", $moderatedCreateAction);
			$forwardUrl .= "&msg=nodes_updated_moderated";
		}

		Header("Location: ".$forwardUrl);
		return;
	}

	$request->setAttribute("node", $node);

	$iparams = array();
	$fieldDefs = $nodeClass->getFieldDefs();
	$_keys = array_keys($fieldDefs);
	foreach ($_keys as $_k) {
		$fieldDef = &$fieldDefs[$_k];
		$item = &new phpobject();

		$fieldShortname = $fieldDef->shortname;
		$field = &$fieldList[$fieldShortname];
		$params = $field->getParamersForIncludeControlJSP();

		$item->jspname = $field->getJSPControlName();
		$item->fieldDef = &$fieldDef;
		$item->params = $params;
		$iparams[] = &$item;
	}
	$request->setAttribute("fieldDefs", $iparams);
	$request->setAttribute("validationErrors", $fieldErrors);

	$request->setAttribute("NodeName", $NodeName);
	subnodeslist($request, $node, $all_deps, $currentLoggedUser);

	if ($ma) {
		$request->setAttribute("moder_action", $ma);
	} else {
		$request->setAttribute("moder_action", null);
	}

	$parent = $node->getParent();
	$request->setAttribute("parent", $parent);

	usetemplate("nodes/editnode");
}

function properties(&$request) {
	global $session;
	$node_id = (int)$request->getParameter("node_id");

	$node = & Node::findById($node_id);
	if ($node == null) {
		die("Invalid request argument");
	}

	$nodeMap = new phpobject();
	$nodeMap->NodeShortname = $node->shortname;
	$nodeMap->NodeDynamicTemplate = $node->dynamicTemplate;
	$nodeMap->NodeAdminURL = $node->adminUrl;
	$nodeMap->NodeID = $node->id;
	$nodeMap->id = $node->id;
	$nodeMap->parent_id = $node->parent_id;

	$request->setAttribute("nodeMap", $nodeMap);
	$request->setAttribute("nodeIsRoot", $node->isRoot());
	$request->setAttribute("node", $node);

	usetemplate("nodes/properties");
}

function properties_update(&$request) {
	global $session;
	$node_id = (int)$request->getParameter("node_id");
	$node = & Node::findById($node_id);
	if ($node == null) {
		die("Invalid request argument");
	}

	$nodeClass = $node->getNodeClass();

	$parent = $node->getParent();

	//now validate node fields
	$fieldErrors = array();
	$NodeShortname = $request->getParameter("NodeShortname");
	$NodeDynamicTemplate = $request->getParameter("NodeDynamicTemplate");
	$NodeAdminURL = $request->getParameter("NodeAdminURL");

	if ($parent != null) {
		if ($NodeShortname == null) {
			$NodeShortname = "";
		}
		if(!Validator::validateShortname($NodeShortname)) {
			$fieldErrors["NodeShortname"] = "Bad shorname";
		}
	} else {
		$NodeShortname = "";
	}

	if ($parent != null) {
		if ($NodeShortname == null)
		$NodeShortname = "";
		if (strlen($NodeShortname) == 0)
		$fieldErrors["NodeShortname"] = "Required field";
	} else {
		$NodeShortname = "";
	}

	if (!($NodeShortname == $node->shortname) && ($parent != null) && $parent->hasChildren($NodeShortname)) {
		$fieldErrors["NodeShortname"] = "Duplicate shortname in this subtree";
	}

	if ($NodeDynamicTemplate == null)
	$NodeDynamicTemplate = "";
	if ($NodeAdminURL == null)
	$NodeAdminURL = "";

	if (sizeof($fieldErrors) == 0) {
		$node_id = $node->id;
		$node->setShortname($NodeShortname);
		$node->dynamicTemplate = $NodeDynamicTemplate;
		$node->adminUrl = $NodeAdminURL;
		$node->store();

		header("Location: /admin/nodes.php?do=properties&msg=Node_properties_update_success&reload=true&node_id=".$node_id);
	}

	$nodeMap = new phpobject();
	$nodeMap->NodeShortname = $NodeShortname;
	$nodeMap->NodeDynamicTemplate = $NodeDynamicTemplate;
	$nodeMap->NodeAdminURL = $NodeAdminURL;
	$nodeMap->NodeID = $node->id;
	$nodeMap->id = $node->id;
	$nodeMap->parent_id = $node->parent_id;

	$request->setAttribute("nodeMap", $nodeMap);
	$request->setAttribute("node", $node);
	$request->setAttribute("nodeIsRoot", $node->isRoot());
	$request->setAttribute("validationErrors", $fieldErrors);

	usetemplate("nodes/properties");
}

function dependencies(&$request) {
	global $session;
	$node_id = (int)$request->getParameter("node_id");
	$node = &Node::findById($node_id);
	if ($node == null) {
		Header("Location: /admin/main.php");die();
		return;
	}
	$action = $request->getParameter("action");

	if ($action == "adddep") {
		$class_id = (int)$request->getParameter("class_id");
		$nc = & NodeClass::findById($class_id);
		$node->addDependentClass($nc);
		header("Location: /admin/nodes.php?do=dependencies&node_id=".$node->id);
	} else if ($action == "removedep") {
		$class_id = (int)$request->getParameter("class_id");
		$node->removeDepend(NodeClass::findById($class_id));
		header("Location: /admin/nodes.php?do=dependencies&node_id=".$node->id);
	}

	$deps = $node->findDepends();
	$nodeClass = $node->getNodeClass();
	$classDeps = $nodeClass->getDependentClasses();
	$allClasses = & NodeClass::findAll();
	$availClasses = array();
	foreach ($allClasses as $nodeClass)
	if (!in_array($nodeClass->id, array_keys ($classDeps)) && !in_array($nodeClass->id, array_keys ($deps))) {
		$availClasses[] = $nodeClass;
	}

	$request->setAttribute("availClasses", $availClasses);
	$request->setAttribute("nodeDeps", $deps);

	$request->setAttribute("node", $node);
	usetemplate("nodes/dependencies");
}

function permissions(&$request) {
	global $session;
	$currentLoggedUser = $session->getAttribute(SESSION_LOGGED_USER_ATTR);

	$node_id = (int)$request->getParameter("node_id");
	$node =  &Node::findById($node_id);
	if ($node == null) {
		Header("Location: /admin/main.php");die();
		return;
	}

	$action = $request->getParameter("action");
	$recurs = $request->getParameter("recursive");
	if ($action != null) {
		$cg = &UserGroup::findAll();
		if (sizeof($cg) != 0) {
			foreach ($cg as $ug) {
				if ($ug->id == $currentLoggedUser->group->id || $ug->id == 1)
				continue;
				$ngr = &new NodeGroupRights($ug, $node);
				if ($request->getParameter($ug->id . "_r") == null) {
					$ngr->unsetViewRight();
				} else {
					$ngr->setViewRight();
				}
				if ($request->getParameter($ug->id . "_w") == null) {
					$ngr->unsetWriteRight();
				} else {
					$ngr->setWriteRight();
				}
				$ngr->store();
				if ($recurs != null) {
					$node->setRightsToChildren($ug, $ngr->getRights());
				}
			}
		}
		header("Location: /admin/nodes.php?do=permissions&node_id=".$node->id."&msg=Node_permissions_update_successefull&reload=true");
	}

	$groups = &UserGroup::findAll();
	$rights = array();
	foreach ($groups as $g) {
		$rights[] = &new NodeGroupRights($g, $node);
	}
	$request->setAttribute("groups", $groups);
	$request->setAttribute("rights", $rights);
	$request->setAttribute("node", $node);
	usetemplate("nodes/permissions");
}

function children_sort(&$request) {
	global $session;
	$currentLoggedUser = $session->getAttribute(SESSION_LOGGED_USER_ATTR);

	$node_id = (int)$request->getParameter("node_id");
	$node = &Node::findById($node_id);
	if ($node == null) {
		Header("Location: /admin/main.php");die();
		return;
	}

	$action = $request->getParameter("action");
	if ($action != null) {
		$kid_action = $request->getParameter("kid_act");
		$kid_id = $request->getParameter("kid_id");

		$kid = &Node::findById($kid_id);

		if ($kid_action=="up") {
			$kid->moveUpInSubtreeOrder();
		} else if ($kid_action == "down") {
			$kid->moveDownInSubtreeOrder();
		} else if ($kid_action == "top") {
			$kid->moveHomeInSubtreeOrder();
		} else if ($kid_action == "bottom") {
			$kid->moveEndInSubtreeOrder();
		}

	}

	$c = $node->getChildrenForGroup($currentLoggedUser->group->id);
	$request->setAttribute("children", $c);
	$request->setAttribute("parents", $node->getParentList());
	$request->setAttribute("node", $node);

	usetemplate("nodes/children_sort");
}


function move_node_jsontree(&$request) {
	global $session;

	$currentLoggedUser = $session->getAttribute(SESSION_LOGGED_USER_ATTR);

	$node = $request->getParameter("node");

	$target_id = (int)$request->getParameter("node_id");
	$generate_from_id = (int)substr($node, 8);

	$target = Node::findById($target_id);
	$current = null;
	$nodes = array();
	if ($generate_from_id == -1) {
		$current = Node::findRoot();

		$hasAllowedChildren = $current->hasChildrenForMoveNodeTree($currentLoggedUser, $target);
		$cdeps = $current->findAllDepends();
		$isValid = $currentLoggedUser->canEditNode($current) && !$current->equals($target) && !$current->equals($target->getParent()) && $cdeps[$target->nodeClassId]!=null;

		$node = array();
		$node['id'] = 'tree' . $current->id;
		$node['text'] = prepareStringForXML($current->name);

		if ($isValid) {
			$node['href'] = 'javascript: selectNode(' . $current->id . ')';
		}

		if ($hasAllowedChildren) {
			$node['leaf'] = false;
			$node['expanded'] = true;

			$node['children'] = move_node_xmltree_getNodes($current, $target, $currentLoggedUser);
		} else {
			$node['leaf'] = true;
		}
		$nodes[] = $node;
	} else {
		$current = Node::findById($generate_from_id);
		if ($current != null) {
			$nodes = move_node_xmltree_getNodes($current, $target, $currentLoggedUser);
		}
	}

	echo json_encode($nodes);
}

function move_node_xmltree_getNodes(&$parentNode, &$target, &$currentLoggedUser) {
	$nodes = array();
	$children = $parentNode->getChildrenForUser($currentLoggedUser);
	for ($i = 0; $i < sizeof($children); $i++) {
		$child = $children[$i];

		$hasAllowedChildren = $child->hasChildrenForMoveNodeTree($currentLoggedUser, $target);
		$tparent = $target->getParent();
		$cdeps = $child->findAllDepends();
		$isValid = $child->id!=$target->id && $child->id != $tparent->id && $currentLoggedUser->canEditNode($child) && $cdeps[$target->nodeClassId]!= null;

		$node = array();

		if ($hasAllowedChildren || $isValid) {
			if ($hasAllowedChildren) {
				$node['expandable'] = true;
				$node['leaf'] = false;
			} else {
				$node['leaf'] = true;
			}

			$node['id'] = 'treenode' . $child->id;
			$node['text'] = prepareStringForXML($child->name);

			if ($isValid) {
				$node['href'] = "javascript:selectNode(" . $child->id . ")";
			}

			$nodes[] = $node;
		}
	}

	return $nodes;
}

function move_node_form(&$request) {
	global $session;
	$currentLoggedUser = $session->getAttribute(SESSION_LOGGED_USER_ATTR);
	$node_id = (int)$request->getParameter("node_id");

	$target = Node::findById($node_id);
	$root = Node::findRoot();

	$hasAllowedChildren = $root->hasChildrenForMoveNodeTree($currentLoggedUser, $target);
	$rdeps = $root->findAllDepends();
	$isValid = $root->id != $target->id && !$root->equals($target->getParent()) && $currentLoggedUser->canEditNode($root) && $rdeps[$target->nodeClassId] != null;

	if (!$hasAllowedChildren && !$isValid) {
		$request->setAttribute("NoValidNodes", true);
	}
	usetemplate("nodes/move_node");
}

function move_node(&$request) {
	global $session;
	$node = &Node::findById((int)$request->getParameter("node_id"));
	$node->moveToNewParent((int)$request->getParameter("np_id"));
	usetemplate("nodes/move_node_ok");
}


/*
 function importxmlform(&$request) {
	global $session;
	$node_id = (int)$request->getParameter("id");

	$node = Node::findById($node_id);
	if (node == null) {
	$request->setAttribute("error_msg", "Invalid request argument");
	}
	$request->setAttribute("node", node);

	usetemplate(request, response, "nodes/importxml");
	} catch (ServletException e) {
	throw e;
	} catch (Exception e) {
	e.printStackTrace(System.err);
	}
	}
	/*
	function importxml(&$request) {
	global $session;
	$currentLoggedUser = $session->getAttribute(SESSION_LOGGED_USER_ATTR);
	$node_id = (int)$request->getParameter("id");

	$node = Node::findById($node_id);
	if (node == null) {
	$request->setAttribute("error_msg", "Invalid request argument");
	}

	Validator validationErrors = new Validator();

	String xmlFilePath = $request->getParameter("xmlfile.file");
	FileReader fr = new FileReader(xmlFilePath);
	Node.importFromXML(fr, node, currentLoggedUser);
	} catch(Throwable e) {
	validationErrors.setErrorMsg("xmlfile", e.getMessage());
	}

	$request->setAttribute("validationErrors", validationErrors.getValidationErrors());
	$request->setAttribute("node", node);

	usetemplate(request, response, "nodes/importxml");
	} catch (ServletException e) {
	throw e;
	} catch (Exception e) {
	e.printStackTrace(System.err);
	}
	}
	/*
	function exportxmlform(&$request) {
	global $session;
	$node_id = (int)$request->getParameter("id");

	$node = Node::findById($node_id);
	if (node == null) {
	$request->setAttribute("error_msg", "Invalid request argument");
	}
	$request->setAttribute("node", node);

	usetemplate(request, response, "nodes/exportxml");
	} catch (ServletException e) {
	throw e;
	} catch (Exception e) {
	e.printStackTrace(System.err);
	}
	}
	/*
	function exportxml(&$request) {
	global $session;
	$currentLoggedUser = $session->getAttribute(SESSION_LOGGED_USER_ATTR);
	$node_id = (int)$request->getParameter("id");

	$node = Node::findById($node_id);
	if (node == null) {
	$request->setAttribute("error_msg", "Invalid request argument");
	}

	boolean exportNode = $request->getParameter("exportnode") != null;
	Reader xmlReader;
	if (exportNode == false) {
	xmlReader = node.exportChildrenToXML();
	} else {
	xmlReader = node.exportToXML();
	}
	response.addHeader("Content-disposition", "filename=" + node.shortname + ".xmld");
	response.addHeader("Pragma", "no-cache");
	response.addHeader("Expires", "0");
	response.setContentType("application/octetstream");
	int i;
	Writer out = response.getWriter();
	while ((i=xmlReader.read()) != -1) {
	out.write(i);
	}
	out.flush();
	} catch (Exception e) {
	e.printStackTrace(System.err);
	}
	}
	*/

?>