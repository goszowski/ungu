<?php

require_once("prepend.php");
require_once("dbm/forms/FormDef.php");

$action = (string)$request->getParameter("do"); 

if ($action == null || $action=="") {
	$action = "_default";
}

$action($request);

function _default(&$request) {
//	$sortby = $request->getParameter("sortby");
//	$classes = array();
//	if ($sortby == null) {
//		$classes = &NodeClass::findAll("name");
//	} else {
//		$classes = &NodeClass::findAll($sortby);
//	}

	$formDefs = &FormDef::findAll();
	$request->setAttribute("formDefs", $formDefs);

	usetemplate("forms/list");
}

function create_form(&$request) {
	usetemplate("forms/create");
}

function validateClassFields($name) {
	$errors = array();

	if (strlen($name) == 0) {
		$errors["name"] = "n_required";
	}

	return $errors;
}

function create(&$request) {
	global $AdminTrnsl;
	$name = $request->getParameter("name");
	$description_text = $request->getParameter("description_text");
	$success_text = $request->getParameter("success_text");
	$inc_sub = (int)$request->getParameter("inc_sub");

	$errors = validateClassFields($name);
	$request->setAttribute("ERRORS", $errors);

	if (sizeof($errors) == 0) {
		$formDef = &FormDef::create($name, $description_text, $success_text, $inc_sub);

		$request->setAttribute("MSG", $AdminTrnsl["Create_form_success"]);
		_default($request);
	} else {
		usetemplate("forms/create");
	}
}

function edit(&$request) {
	$form_id = (int)$request->getParameter("form_id");
	$formDef = &FormDef::findById($form_id);

	$request->setAttribute("formDef", $formDef);
	$request->setAttribute("fieldDefs", $formDef->fieldDefs);
	$request->setAttribute("fieldTypes", FormFieldType::getAllTypes());

	usetemplate("forms/edit");
}

function update(&$request) {
	global $AdminTrnsl;
	$form_id = (int)$request->getParameter("form_id");
	$name = $request->getParameter("name");
	$description_text = $request->getParameter("description_text");
	$success_text = $request->getParameter("success_text");
	$inc_sub = (int)$request->getParameter("inc_sub");

	$formDef = &FormDef::findById($form_id);

	if ($formDef == null) {
		_default($request);
		return;
	}

	$errors = validateClassFields($name);
	$request->setAttribute("ERRORS", $errors);

	if (sizeof($errors) == 0) {
		$formDef->name = $name;
		$formDef->description_text = $description_text;
		$formDef->success_text = $success_text;
		$formDef->inc_sub = $inc_sub;

		$formDef->store();
		$request->setAttribute("MSG", $AdminTrnsl["Form_update_success"]);
	}

	edit($request);
}

function delete(&$request) {
	global $AdminTrnsl;
	$form_id = (int)$request->getParameter("form_id");
	$formDef = &FormDef::findById($form_id);
	$formDef->remove();

	$request->setAttribute("MSG", $AdminTrnsl["Form_delete_success"]);
	_default($request);
}

function add_field(&$request) {
	$form_id = (int)$request->getParameter("form_id");

	$name = $request->getParameter("name");
	$type_shortname = $request->getParameter("type_shortname");
	$required = (int)$request->getParameter("required");

	$formDef = &FormDef::findById($form_id);

	$errors = array();
	if (strlen($name) == 0) {
		$errors["name"] = "fn_required";
	}

	$request->setAttribute("AFERRORS", $errors);

	if (sizeof($errors) == 0) {
		$formDef->addFieldDef($name, $type_shortname, $required);
	} else {
		$nfParams = array();
		$nfParams["name"] = $name;
		$nfParams["type_shortname"] = $type_shortname;
		$nfParams["required"] = $required;

		$request->setAttribute("nfparams", $nfParams);
	}
	edit($request);
}

function update_field(&$request) {
	global $AdminTrnsl;
	$form_id = (int)$request->getParameter("form_id");

	$id = $request->getParameter("field_id");
	$name = $request->getParameter("name");
	$type_shortname = $request->getParameter("type_shortname");
	$required = (int)$request->getParameter("required");

	$formDef = &FormDef::findById($form_id);

	$errors = array();
	if (strlen($name) == 0) {
		$errors["name"] = "fn_required";
	}

	$FERRORS = array();
	$FERRORS[$id] = $errors;
	$request->setAttribute("FERRORS", $FERRORS);

	if (sizeof($errors) == 0) {
		$formDef->updateFieldDef($id, $name, $type_shortname, $required);
		$request->setAttribute("MSG", $AdminTrnsl["Form_update_success"]);
	}
	edit($request);
}

function remove_field(&$request) {
	$form_id = (int)$request->getParameter("form_id");
	$field_id = $request->getParameter("field_id");

	$formDef = &FormDef::findById($form_id);
	$formDef->deleteFieldDef($field_id);
	edit($request);
}

function moveup_field(&$request) {
	$form_id = (int)$request->getParameter("form_id");
	$field_id = $request->getParameter("field_id");

	$formDef = &FormDef::findById($form_id);
	$fd = $formDef->fieldDefs[$field_id];

	$fd->moveUpInOrder();
	edit($request);
}

function movedown_field(&$request) {
	$form_id = (int)$request->getParameter("form_id");
	$field_id = $request->getParameter("field_id");

	$formDef = &FormDef::findById($form_id);
	$fd = $formDef->fieldDefs[$field_id];

	$fd->moveDownInOrder();
	edit($request);
}

?>