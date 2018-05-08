<?php

$GLOBALS["_CLEAR_NODES_CACHE_BY_ID"] = true;
$GLOBALS["_CLEAR_NODES_CACHE_BY_PATH"] = true;

require_once("prepend.php");

$action = (string)$request->getParameter("do");

if ($action == null || $action=="") {
	$action = "_default";
}

checkPrivileges($request);
$action($request);

function checkPrivileges(&$request) {
	global $session;
	$currentLoggedUser = $session->getAttribute(SESSION_LOGGED_USER_ATTR);
	if (!$currentLoggedUser->group->isModerator) {
		die("You need to have moderator privileges");
	}
}

function _default(&$request) {
	global $session;

	$actions = ModeratedAction::findAll();
	$request->setAttribute("actions", $actions);

	usetemplate("moderation/list");
}

function process(&$request) {
	global $session;
	$id = (int)$request->getParameter("id");
	$a = ModeratedAction::findById($id);

	switch($a->type) {
		case "create":
			header("Location: /admin/nodes.php?do=addnode_moder_form&mid=" . $id);
			break;
		case "update":
			header("Location: /admin/nodes.php?do=editnode_moder&mid=" . $id);
			break;
		case "delete":
			Node::remove($a->node_id);
			ModeratedAction::remove($a->id);
			header("Location: /admin/moderation.php");
			break;
	}
}

function delete(&$request) {
	global $session;
	$id = (int)$request->getParameter("id");
	$a = ModeratedAction::remove($id);
	header("Location: /admin/moderation.php");
}

?>