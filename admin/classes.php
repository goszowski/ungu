<?php

require_once("prepend.php");

$action = (string)$request->getParameter("do"); 

if ($action == null || $action=="") {
	$action = "_default";
}

$action($request);

function checkPrivileges(&$request) {
	global $session, $AdminTrnsl;
	$currentLoggedUser = &$session->getAttribute(SESSION_LOGGED_USER_ATTR);
	if (!$currentLoggedUser->group->canManageClasses) {
		die($AdminTrnsl["You_Have_not_rights_to_manage_classes"]);
	}
}

function _default(&$request) {
	checkPrivileges($request);

	$sortby = $request->getParameter("sortby");
	$classes = array();
	if ($sortby == null) {
		$classes = &NodeClass::findAll("name");
	} else {
		$classes = &NodeClass::findAll($sortby);
	}
	$request->setAttribute("classes", $classes);

	usetemplate("classes/classes_list");
}

function create_form(&$request) {
	usetemplate("classes/create");
}

function validateClassFields($shortname, $name, $class_id) {
	$errors = array();

	if (strlen($shortname) == 0) {
		$errors["shortname"] = "sn_zerolen";
	} elseif (strlen($shortname) < 2) {
		$errors["shortname"] = "sn_short";
	} elseif(!ereg("^([A-Za-z_]{1})(.*)$", $shortname, $matches)) {
		$errors["shortname"] = "sn_badfl";
	} elseif(!ereg("^([A-Za-z_]{1})([A-Za-z0-9_]*)$", $shortname, $matches)) {
		$errors["shortname"] = "sn_bad";
	} else {
		$shClass = &NodeClass::findByShortname($shortname);
		if (($class_id == -1 && $shClass != null) || ($class_id != -1 && $shClass != null && $shClass != null && $shClass->id != $class_id)) {
			$errors["shortname"] = "sn_exists";
		}
	}

	if (strlen($name) == 0) {
		$errors["name"] = "n_required";
	}

	return $errors;
}

function create(&$request) {
	global $AdminTrnsl, $NODECLASS_FLAG_NAMES;
	$name = $request->getParameter("name");
	$shortname = $request->getParameter("shortname");
	$def_template = $request->getParameter("default_template");
	$show_at_atd = (int)$request->getParameter("show_at_adt");
	$nodename_label = $request->getParameter("nodename_label");
	$orderby = $request->getParameter("orderby");
	foreach ($NODECLASS_FLAG_NAMES as $flagName) {
		$flags[$flagName] = (int)$request->getParameter("flag_" . $flagName);
	}

	$errors = validateClassFields($shortname, $name, -1);
	$request->setAttribute("ERRORS", $errors);

	if (sizeof($errors) == 0) {
		$nc = &new NodeClass();

		$nc->name = $name;
		$nc->setShortname(strtolower($shortname));
		$nc->default_template = $def_template;
		if ($show_at_atd != null) {
			$nc->showAtAdminTree = true;
		}
		$nc->nodeNameLabel = $nodename_label;
		$nc->orderBy = $orderby;
		foreach ($NODECLASS_FLAG_NAMES as $flagName) {
			$nc->setFlag($flagName, $flags[$flagName]);
		}

		$nc->create();

		header("Location: /admin/classes.php?MSG=Create_class_success");
	} else {
		usetemplate("classes/create");
	}
}

function edit(&$request) {
	$class_id = (int)$request->getParameter("class_id");
	$nc = &NodeClass::findById($class_id);

	$fieldDefs = $nc->getFieldDefs();

	$fieldDefsBeans = array();
	foreach($fieldDefs as $fd) {
		$fdBean = & new PHPObject();
		$fdBean->id = $fd->id;

		$fdBean->shortname = $fd->shortname;
		$fdBean->name = $fd->name;
		$fdBean->field_type = $fd->fieldTypeId;
		$fdBean->required = $fd->required;
		$fdBean->shown = $fd->shown;
		$fdBean->isToDelete = false;

		$fieldDefsBeans[$fd->id] = &$fdBean;
	}

	$request->setAttribute("class", $nc);
	$request->setAttribute("fieldDefs", $fieldDefsBeans);
	$request->setAttribute("fieldTypes", FieldType::getAllTypes());

	usetemplate("classes/edit");
}

function update(&$request) {
	global $AdminTrnsl, $NODECLASS_FLAG_NAMES;
	$class_id = (int)$request->getParameter("class_id");

	//without '&' !!!
	$nc = NodeClass::findById($class_id);

	if ($nc == null) {
		die("invalid class_id");
	}

	$name = $request->getParameter("name");
	$shortname = $request->getParameter("shortname");
	$def_template = $request->getParameter("default_template");
	$show_at_atd = (int)$request->getParameter("show_at_adt");
	$nodename_label = $request->getParameter("nodename_label");
	$orderby = $request->getParameter("orderby");
	$flags = array();
	foreach ($NODECLASS_FLAG_NAMES as $flagName) {
		$flags[$flagName] = (int)$request->getParameter("flag_" . $flagName);
	}

	$nc->name = $name;
	$nc->setShortname(strtolower($shortname));
	$nc->default_template = $def_template;
	$nc->showAtAdminTree = ($show_at_atd == 1);
	$nc->nodeNameLabel = $nodename_label;
	$nc->orderBy = $orderby;

	foreach ($NODECLASS_FLAG_NAMES as $flagName) {
		$nc->setFlag($flagName, $flags[$flagName]);
	}

	$fieldDefs = $nc->getFieldDefs();

	$FERRORS = array();

	$fieldDefsBeans = array();
	$thereAreFieldErrors = false;
	foreach($fieldDefs as $fd) {
		$fdBean = new PHPObject();
		$fdBean->id = $fd->id;

		$fdBean->shortname = $request->getParameter("df_shortnames_" . $fd->id);
		$fdBean->name = $request->getParameter("df_names_" . $fd->id);
		$fdBean->field_type = $request->getParameter("df_types_" . $fd->id);
		$fdBean->required = ($request->getParameter("df_required_" . $fd->id) == 1);
		$fdBean->shown = ($request->getParameter("df_shown_" . $fd->id) == 1);
		$fdBean->isToDelete = ($request->getParameter("df_delete_" . $fd->id) == 1);

		$fieldDefsBeans[$fd->id] = $fdBean;

		if ($fdBean->isToDelete) {
			continue;
		}

		$errors = array();
		if(!Validator::validateShortname($fdBean->shortname)) {
			$errors["shortname"] = "fbad";
		} elseif (strlen($fdBean->shortname) == 0) {
			$errors["shortname"] = "fsmall";
		} else {
			$efd = $fieldDefs[$fdBean->shortname];
			if ($efd != null && $efd->id != $fd->id) {
				$errors["shortname"] = "fdup";
			}
		}

		if (strlen($fdBean->name) == 0) {
			$errors["name"] = "fn_required";
		}

		$FERRORS[$fd->id] = $errors;
		if (sizeof($errors) > 0) {
			$thereAreFieldErrors = true;
		}
	}

	$errors = validateClassFields($shortname, $name, $class_id);

	if (!$thereAreFieldErrors && (sizeof($errors) == 0)) {
		$nc->store();
		foreach($fieldDefsBeans as $fdBean) {
			if ($fdBean->isToDelete) {
				$nc->delFieldDef($fdBean->shortname);
			} else {
				$nc->updateFieldDef($fdBean->id, $fdBean->shortname, $fdBean->name, $fdBean->field_type, $fdBean->required, $fdBean->shown);
			}
		}
		header("Location: /admin/classes.php?do=edit&class_id=" . $class_id . "&MSG=Class_update_success");
		return;
	} else {
		_dump($error);
		_dump($FERRORS);
	}

	$request->setAttribute("ERRORS", $errors);
	$request->setAttribute("FERRORS", $FERRORS);
	$request->setAttribute("class", $nc);
	$request->setAttribute("fieldDefs", $fieldDefsBeans);

	usetemplate("classes/edit");
}

function delete(&$request) {
	global $AdminTrnsl;
	$class_id = (int)$request->getParameter("class_id");
	$nc = &NodeClass::findById($class_id);
	$nc->remove();

	$request->setAttribute("MSG", $AdminTrnsl["Class_delete_success"]);
	header("Location: /admin/classes.php?MSG=Class_delete_success");
}

function add_field(&$request) {
	$class_id = (int)$request->getParameter("class_id");

	$name = $request->getParameter("nfname");
	$shortname = $request->getParameter("nfshortname");
	$field_type = (int)$request->getParameter("nffieldtype");
	$rRequired = (int)$request->getParameter("nfrequired");

	$required = ($rRequired == 1);

	$rShown = (int)$request->getParameter("nfshown");
	$shown = ($rShown == 1);

	$nc = &NodeClass::findById($class_id);

	$errors = array();
	if (strlen($name) == 0) {
		$errors["name"] = "fn_required";
	}

	if(!Validator::validateShortname($shortname)) {
		$errors["shortname"] = "fbad";
	} elseif (strlen($shortname) == 0) {
		$errors["shortname"] = "fsmall";
	} elseif ($nc->getFieldDef($shortname) != null) {
		$errors["shortname"] = "fdup";
	}

	$request->setAttribute("AFERRORS", $errors);

	if (sizeof($errors) == 0) {
		$nc->addFieldDef($shortname, $name, $field_type, $required, $shown);
	} else {
		$nfParams = array();
		$nfParams["shortname"] = shortname;
		$nfParams["name"] = $name;
		$nfParams["fieldtype"] = $field_type;
		$nfParams["required"] = $rRequired;
		$nfParams["shown"] = $rShown;

		$request->setAttribute("nfparams", $nfParams);
	}
	edit($request);
}

function field_params(&$request) {
	$class_id = (int)$request->getParameter("class_id");
	$nc = &NodeClass::findById($class_id);
	if ($nc == null) {
		_default($request);
		return;
	}

	$request->setAttribute("class", $nc);

	$field_id = (int)$request->getParameter("field_id");

	$fd = $nc->getFieldDef($field_id);
	$request->setAttribute("fieldDef", $fd);

	$fpValues = array();
	$fdFieldType = $fd->getFieldType();
	$field_params = $fdFieldType->getParameterList();
	foreach ($field_params as $fp) {
		$fpValues[$fp->shortname] = $fd->getParameterValue($fp->shortname);
	}
	$request->setAttribute("parameterValues", $fpValues);

	usetemplate("classes/field_params");
}

function update_field_params(&$request) {
	$class_id = (int)$request->getParameter("class_id");
	$nc = &NodeClass::findById($class_id);
	if ($nc == null) {
		_default($request);
		return;
	}

	$request->setAttribute("class", $nc);

	$field_id = (int)$request->getParameter("field_id");
	$fd = $nc->getFieldDef($field_id);

	$fdFieldType = $fd->getFieldType();
	$field_params = $fdFieldType->parameterList;

	foreach ($field_params as $fp) {
		$param_value = $request->getParameter("p_" . $fp->shortname . "_value");
		$fd->setParameterValue($fp->shortname, $param_value[0]);
	}

	//field_params($request);
	header("Location: /admin/classes.php?do=field_params&class_id=".$class_id."&field_id=".$field_id);
}

function depends(&$request) {
	$class_id = (int)$request->getParameter("class_id");
	$nc = &NodeClass::findById($class_id);

	$depends = &$nc->getDependentClasses();
	$request->setAttribute("allDepends", $depends);
	$request->setAttribute("class", $nc);

	$classes = &NodeClass::findAll();
	$request->setAttribute("allClasses", $classes);

	usetemplate("classes/depends");
}

function update_depends(&$request) {
	$class_id = (int)$request->getParameter("class_id");

	$dep_classes = $request->getParameter("class_deps");
	$nc = &NodeClass::findById($class_id);
	$nc->removeDepend();

	if ($dep_classes !== null) {
		foreach ($dep_classes as $dep_class_id) {
			$dep_class = &NodeClass::findById($dep_class_id);
			$nc->addDepend($dep_class);
		}
	}

	depends($request);
}

function moveup_field(&$request) {
	$class_id = (int)$request->getParameter("class_id");

	$fid = (int)$request->getParameter("field_id");

	$nc = &NodeClass::findById($class_id);
	$fd = $nc->getFieldDef($fid);

	$fd->moveUpInOrder();
	edit($request);
}

function movedown_field(&$request) {
	$class_id = (int)$request->getParameter("class_id");

	$fid = (int)$request->getParameter("field_id");

	$nc = &NodeClass::findById($class_id);
	$fd = $nc->getFieldDef($fid);

	$fd->moveDownInOrder();
	edit($request);
}

?>