<?

require_once('prepend.php');

//$_settingsNode = Node::findByPath("/_site_settings");
// define("ADMIN_EMAIL", $_settingsNode->tfields["admin_email"]);
// define("ADMIN_EMAIL_FROM", $_settingsNode->tfields["admin_email_from"]);

define ("SAVED_EMAILS_NODE", "/_saved_emails");
define ("SUBSCRIBERS_NODE", "/_subscribers");

function send_mail($from, $to, $subj, $body, $name) {
	$body = str_replace('%name%', $name, $body);
	$subj = str_replace('%name%', $name, $subj);

	//$body = mb_convert_encoding($body, "windows-1251", "utf-8");
	//$subj = mb_convert_encoding($subj, "windows-1251", "utf-8");
	//$from = mb_convert_encoding($from, "windows-1251", "utf-8");
	sendMail($from, $to, $subj, $body/*, "windows-1251"*/);
}

function sendEmails(&$emailNodeName, &$emailNodeFields) {
	global $request, $session, $AdminTrnsl;
	$host = $_SERVER["HTTP_HOST"];
	$from = ADMIN_EMAIL_FROM;

	$body = $emailNodeFields["body"]->value;
	$subj = $emailNodeName;
	$sendToAll = ($emailNodeFields["toall"]->value === 0);
	$selectedTopicsIDs = $emailNodeFields["topics"]->getNodeIds();
	$selectedSubscribersIds = $emailNodeFields["subscribers"]->getNodeIds();

	$body = <<< EOT
<html><body>
$body
</body></html>
EOT;

	$subscribers = array();
	if ($sendToAll) {
		$r = Node::findByPath(SUBSCRIBERS_NODE);
		$allSubscribers = $r->getChildren();

		for($i = 0; $i < sizeof($allSubscribers); $i++) {
			$subscriber = $allSubscribers[$i];
			if ($subscriber->tfields["active"] === false) {
				continue;
			}
			$subscribersTopicIDs = $subscriber->fields["topics"]->getNodeIds();
			$intrsct = array_intersect($subscribersTopicIDs, $selectedTopicsIDs);
			if (sizeof($intrsct) > 0) {
				$subscribers[] = $subscriber;
			}
		}
	} else {
		foreach($selectedSubscribersIds as $subscriberID) {
			$subscriber = Node::findById($subscriberID);
			$subscribers[] = $subscriber;
		}
	}
	
	usetemplate("emails/header");

	$serverName = $_SERVER["SERVER_NAME"];
	
	$body = str_replace(" src=\"/", " src=\"http://".$serverName."/", $body);
	$body = str_replace(" src='/", " src='http://".$serverName."/", $body);
	$body = str_replace(" href=\"/", " href=\"http://".$serverName."/", $body);
	$body = str_replace(" href='/", " href='http://".$serverName."/", $body);
	$body = str_replace(" url(/", " url(http://".$serverName."/", $body);
	$body = str_replace(":url(/", ":url(http://".$serverName."/", $body);
	$body = str_replace(" url('/", " url('http://".$serverName."/", $body);
	$body = str_replace(":url('/", ":url('http://".$serverName."/", $body);

	for($i = 0; $i < sizeof($subscribers); $i++) {
		$subscriber = $subscribers[$i];
		$name = $subscriber->name;
		$email = $subscriber->tfields["email"];
		send_mail($from, $email, $subj, $body, $name);
		echo localmsg("MailerSent") . " $name ($email)<br>";
	}
	usetemplate("emails/footer");
}

function main() {
	global $request, $session, $AdminTrnsl;
	$action = $request->getparameter("do");

	$currentLoggedUser = $session->getAttribute(SESSION_LOGGED_USER_ATTR);
	$request->setAttribute("currentLoggedUser", $currentLoggedUser);

switch($action) {
case "delete":
	$id = (int)$request->getParameter("id");
	Node::remove($id);
	header("Location: /admin/emails.php");
	break;
case "edit":
	$id = (int)$request->getParameter("id");
	$node = Node::findById($id);
	$saved_email = $node;
	$nodeClass = $node->getNodeClass();
	$request->setAttribute("nodeClass", $nodeClass);

	$request->setAttribute("node", $node);
	$fieldDefs = $nodeClass->getFieldDefs();

	$iparams = array();
	foreach ($fieldDefs as $fieldDef) {
		$fieldShortname = $fieldDef->shortname;
		$field = $node->getField($fieldShortname);
		$params = $field->getParamersForIncludeControlJSP();

		$item = new stdClass;

		$item->jspname = $field->getJSPControlName();
		$item->fieldDef = $fieldDef;
		$item->params = $params;
		$iparams[] = $item;
	}
	$request->setAttribute("fieldDefs", $iparams);
	$request->setAttribute("NodeName", $node->name);

	$request->setAttribute("saved_email", $saved_email);
	usetemplate("emails/edit");
	break;

case "update":
	$update = (int)$request->getParameter("update");
	$send = (int)$request->getParameter("send");

	$node_id = (int)$request->getParameter("id");

	$node = Node::findById($node_id);
	if ($node == null) {
		Header("Location: /admin/emails.php");die();
	}

	$nodeClass = $node->getNodeClass();

	//let's validate fields
	$fieldErrors = array();
	$fieldList = array();

	$_keys = array_keys($node->fields);
	foreach ($_keys as $fieldShortname) {
		$field = $node->fields[$fieldShortname];
		$fieldDef = $field->getFieldDef();
		$verror = $field->setValue($request);
		if ($verror != null) {
			$fieldErrors[$fieldDef->shortname] = $verror;
		}
		$fieldList[$fieldDef->shortname] = $field;
	}
	$NodeName = (string)$request->getParameter("NodeName");
	if (strlen($NodeName) == 0)
		$fieldErrors["NodeName"] = $AdminTrnsl["RequiredField"];

	//if validation not failed update fields data
	if (sizeof($fieldErrors) == 0) {
		if ($update == 1) {
			$node->name = $NodeName;
			$node->store();
			$forwardUrl = "/admin/emails.php?do=edit&okupdated=yes&id=" . $node->id;
			if ($send != 1) {
				Header("Location: " . $forwardUrl);
				return;
			}
		}
		if ($send == 1) {
			sendEmails($NodeName, $fieldList);
		}
		return;
	}

	$pList = $node->getParentList();
	$request->setAttribute("parents", $pList);
	$request->setAttribute("node", $node);
	$request->setAttribute("saved_email", $saved_email);

	$iparams = array();
	$fieldDefs = $nodeClass->getFieldDefs();
	$_keys = array_keys($fieldDefs);
	foreach ($_keys as $_k) {
		$fieldDef = $fieldDefs[$_k];

		$fieldShortname = $fieldDef->shortname;
		$field = $fieldList[$fieldShortname];
		$params = $field->getParamersForIncludeControlJSP();

		$item = new stdClass;
		$item->jspname = $field->getJSPControlName();
		$item->fieldDef = $fieldDef;
		$item->params = $params;
		$iparams[] = $item;
	}
	$request->setAttribute("fieldDefs", $iparams);
	$request->setAttribute("validationErrors", $fieldErrors);

	$request->setAttribute("NodeName", $NodeName);

	usetemplate("emails/edit");

	break;

case "addform":
	$nodeClass = NodeClass::findByShortname("saved_email");
	$savedEmailsNode = Node::findByPath(SAVED_EMAILS_NODE);
	$parentId = $savedEmailsNode->id;
	$parent = Node::findById($parentId);
	$request->setAttribute("parent_id", $parent->id);
	$request->setAttribute("parent", $parent);

	$request->setAttribute("nodeClass", $nodeClass);
	$fieldDefs = $nodeClass->getFieldDefs();
	$iparams = array();
	$fieldDefsKeys = array_keys($fieldDefs);
	foreach ($fieldDefsKeys as $k) {
		$fieldDef = $fieldDefs[$k];
	
		$item = new stdClass;

		$field = $fieldDef->getFieldInstance();
		$field->setDefaultValue();
		$params = $field->getParamersForIncludeControlJSP();

		$item->jspname = $field->getJSPControlName();
		$item->fieldDef = $fieldDef;
		$item->params = $params;
		$iparams[] = $item;
	}
	$request->setAttribute("fieldDefs", $iparams);

	$nodePropsMap = new stdClass;
	$nodePropsMap->NodeDynamicTemplate = $nodeClass->default_template;
	$request->setAttribute("nodeProps", $nodePropsMap);

	usetemplate("emails/add");
	break;

case "create":
	$create = (int)$request->getParameter("create");
	$send = (int)$request->getParameter("send");

	$nodeClass = NodeClass::findByShortname("saved_email");
	$parentId = (int)$request->getParameter("parent_id");
	$parent = Node::findById($parentId);
	$request->setAttribute("parent", $parent);
	$request->setAttribute("parent_id", $parent->id);

	$request->setAttribute("nodeClass", $nodeClass);

	$fieldDefs = $nodeClass->getFieldDefs();

	//let's validate fields
	$fieldErrors = array();
	$fieldList = array();
	foreach ($fieldDefs as $fieldDef) {
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

	//if validation not failed create node:
	if (sizeof($fieldErrors) == 0) {
		if ($create == 1) {
			$createdNode = Node::createWithPreparedValues($parent, $NodeName, $NodeShortname, $NodeDynamicTemplate, $NodeAdminURL, $currentLoggedUser->id, $nodeClass->id, $fieldList);
			$forwardUrl = "/admin/emails.php";
	
			if ($send != 1) {
				Header("Location: ".$forwardUrl."?reload=1");
				return;
			}
		}
		if ($send == 1) {
			sendEmails($NodeName, $fieldList);
		}
		return;
	}

	$iparams = array();
	foreach ($fieldDefs as $fieldDef) {
		$item = new stdClass;

		$shortname = $fieldDef->shortname;
		$errMsg = $fieldErrors[$shortname];

		$field = $fieldList[$shortname];
		$params = null;
		if (strlen($errMsg) == 0) {
			$params = $field->getParamersForIncludeControlJSP();
		} else {
			$params = $field->getDefaultParamersForIncludeControlJSP();
		}

		$item->jspname = $field->getJSPControlName();
		$item->fieldDef = $fieldDef;
		$item->params = $params;
		$iparams[] = $item;
	}

	$request->setAttribute("fieldDefs", $iparams);
	$request->setAttribute("validationErrors", $fieldErrors);

	$nodePropsMap = new stdClass;
	$nodePropsMap->NodeName = $NodeName;
	$nodePropsMap->NodeShortname = $NodeShortname;
	$nodePropsMap->NodeDynamicTemplate = $NodeDynamicTemplate;
	$nodePropsMap->NodeAdminURL = $NodeAdminURL;
	$request->setAttribute("nodeProps", $nodePropsMap);

	usetemplate("emails/add");

	break;

case "send":

	break;

default :
	// $saved_emailsNode = Node::findByPath(SAVED_EMAILS_NODE);
	// $saved_emails = $saved_emailsNode->getChildren();

	// $request->setAttribute("saved_emails", $saved_emails);
	usetemplate("emails/list");
	break;
}
}
main();

?>