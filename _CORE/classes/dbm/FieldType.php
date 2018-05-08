<?php

import("dbm.fields.*");

global $DBM_FIELD_TYPES;
$DBM_FIELD_TYPES = array();

class FieldParameter {
    var $shortname = "";
    var $name = "";
    var $default_value = "";
    var $values_list = null;

    function FieldParameter($shortname, $name, $default_value, $values_list=array()) {
        $this->default_value = $default_value;
        $this->shortname = $shortname;
        $this->name = $name;
        $this->values_list = $values_list;
    }
}

class FieldType {
    var $id = 0;

    var $parameterList = null;

    function& getParameterList() {
        return $this->parameterList;
    }

    function getParametersNames() {
        return array_keys($this->parameterList);
    }

    function& getParameter($shortname) {
        return $this->parameterList[$shortname];
    }

    var $phpClass = null;

    var $name = null;

	var $dbType = null;
	var $rsType = null;

    function& getAllTypes() {
    	global $DBM_FIELD_TYPES;
        return $DBM_FIELD_TYPES;
    }

    function& findTypeById($id) {
    	global $DBM_FIELD_TYPES;
        return $DBM_FIELD_TYPES[$id];
    }

    function& findTypeByphpClass($c) {
    	global $DBM_FIELD_TYPES;
        return $DBM_FIELD_TYPES[$c];
    }

    function& getFieldInstance(&$fd) {
        $field = new $this->phpClass;
        $field->fieldDefId = $fd->id;
        $field->fieldDefShortname = $fd->shortname;
        $field->nodeClassId = $fd->nodeClassId;
        return $field;
    }

    function FieldType($type_id, $name, $phpClass, $dbType, $rsType, $paramlist) {
    	global $DBM_FIELD_TYPES;
        $this->name = $name;
        $this->phpClass = $phpClass;
        $this->dbType = $dbType;
        $this->rsType = $rsType;
        $this->id = $type_id;
        if (!array_key_exists("id_type_hash", $GLOBALS)) $GLOBALS["id_type_hash"] = array();
        if (!array_key_exists("phpclass_type_hash", $GLOBALS)) $GLOBALS["phpclass_type_hash"] = array();
        $DBM_FIELD_TYPES[$type_id] = &$this;
        $DBM_FIELD_TYPES[strtolower($phpClass)] = &$this;
        $this->parameterList = array();
        foreach ($paramlist as $p) {
            $this->parameterList[$p->shortname] = $p;
        }
    }
}

//===================================================================

$DATETIME =
	new FieldType(1, "DateTime", "DateTimeField", "DATETIME", "Date",
		array(
			new FieldParameter("format", "DateTime Format",  "dd.MM.yyyy HH:mm:ss"),
    		new FieldParameter("control_type", "Type of the html control",  "normal", array("normal"=>"Normal", "readonly"=>"Readonly")),
		)
	);

//===================================================================

$DATE_PARAMLIST = array(
    new FieldParameter("format", "Date Format",  "dd.MM.yyyy"),
    new FieldParameter("control_type", "Type of the html control",  "normal", array("normal"=>"Normal", "readonly"=>"Readonly")),
);
$FIELDTYPE_DATE = new FieldType(2, "Date", "DateField", "DATE", "Date", $DATE_PARAMLIST);

//===================================================================

$STRING_PARAMLIST = array(
    new FieldParameter("max_length", "Maximum String Length",  "255"),
    new FieldParameter("min_length", "Minimum String Length",  "0"),
    new FieldParameter("regex", "Regular Expression",  ""),
    new FieldParameter("regex_error_msg", "Regular Expression Error Message",  ""),
    new FieldParameter("control_type", "Type of the html control",  "normal", array("normal"=>"Normal", "readonly"=>"Readonly")),
);
$FIELDTYPE_STRING = new FieldType(3, "String", "StringField", "TEXT", "String", $STRING_PARAMLIST);

//===================================================================//

$TEXTAREA_PARAMLIST = array(
    new FieldParameter("max_length", "Maximum String Length",  "10240"),
    new FieldParameter("control_type", "Type of the html control",  "normal", array("normal"=>"Normal", "readonly"=>"Readonly")),
    
);
$FIELDTYPE_TEXTAREA = new FieldType(4, "Textarea", "TextareaField", "TEXT", "String", $TEXTAREA_PARAMLIST);

//===================================================================//

$INTEGER_PARAMLIST = array(
    new FieldParameter("max_value", "Maximum Value",  1000000000),
    new FieldParameter("min_value", "Minimum Value",  -1000000000),
    new FieldParameter("control_type", "Type of the html control",  "normal", array("normal"=>"Normal", "readonly"=>"Readonly")),
);
$FIELDTYPE_INTEGER = new FieldType(5, "Integer", "IntegerField", "INTEGER", "Int", $INTEGER_PARAMLIST);

//===================================================================//

$FLOAT_PARAMLIST = array(
    new FieldParameter("max_value", "Maximum Value",  1000000000.0),
    new FieldParameter("min_value", "Minimum Value",  -1000000000.0),
    new FieldParameter("control_type", "Type of the html control",  "normal", array("normal"=>"Normal", "readonly"=>"Readonly")),
);
$FIELDTYPE_FLOAT = new FieldType(6, "Float", "FloatField", "FLOAT", "Float", $FLOAT_PARAMLIST);

//===================================================================//

$WYSIWYG_PARAMLIST = array(
    new FieldParameter("max_length", "Maximum String Length",  "65536")
);
$FIELDTYPE_WYSIWYG = new FieldType(7, "WYSiWYG text", "WysiwygTextField", "TEXT", "String", $WYSIWYG_PARAMLIST);

//===================================================================//

$BOOL_PARAMLIST = array(
);
$FIELDTYPE_BOOL = new FieldType(8, "Boolean (yes/no)", "BoolField", "TINYINT", "Int", $BOOL_PARAMLIST);

//===================================================================//

$BOOLR_PARAMLIST = array(
	new FieldParameter("max_checked", "Maximum number of checked nodes",  "1")
);
$FIELDTYPE_BOOLR = new FieldType(17, "Boolean (unique)", "BoolRField", "TINYINT", "Int", $BOOLR_PARAMLIST);

//===================================================================//

$FILE_PARAMLIST = array(
    new FieldParameter("max_size", "Maximum File Size (in bytes)",  "2097152"),
    new FieldParameter("aext", "Allowed File Extensions (separate values by ',')",  "*")
);
$FIELDTYPE_FILE = new FieldType(9, "File", "FileField", "TEXT", "String", $FILE_PARAMLIST);

//===================================================================//

$LINK_PARAMLIST = array(
    new FieldParameter("allowed_classes", "Allowed Classes ( shorntames, seperated by ',')",  "*"),
    new FieldParameter("root_node_path", "Path of relative root node",  "/"),
    new FieldParameter("max_depth", "Maximum depth of subtree under relative root node",  "10"),
    new FieldParameter("control_type", "Type of the html control",  "popup", array("popup"=>"Pop-Up Window", "readonly"=>"Readonly")),
    new FieldParameter("default_value", "Default value",  "")
);
$FIELDTYPE_Link = new FieldType(10, "Link", "LinkField", "VARCHAR(255)", "String", $LINK_PARAMLIST);

//===================================================================//

$NEW_LINK_PARAMLIST = array(
    new FieldParameter("allowed_classes", "Allowed Classes ( shorntames, seperated by ',')",  "*"),
    new FieldParameter("root_node_path", "Path of relative root node",  "/"),
    new FieldParameter("max_depth", "Maximum depth of subtree under relative root node",  "10"),
    new FieldParameter("control_type", "Type of the html control", "popup", array("popup"=>"Pop-Up Window", "select"=>"Dropdown", "readonly"=>"Readonly")),
    new FieldParameter("lazy_loading", "Lazy loading", "1", array("0"=>"No", "1"=>"Yes")),
    new FieldParameter("default_value", "Default value",  "0")
);
$FIELDTYPE_NEW_Link = new FieldType(20, "NewLink", "NewLinkField", "INTEGER", "Int", $NEW_LINK_PARAMLIST);

//===================================================================//

$CMMULTILINK_PARAMLIST = array(
    new FieldParameter("allowed_classes", "Allowed Classes (shorntames, seperated by ',')",  "*"),
    new FieldParameter("root_node_path", "Path of relative root node",  "/"),
    new FieldParameter("max_depth", "Maximum depth of subtree under relative root node",  "10"),
    new FieldParameter("control_type", "Type of the html control", "checkboxes",
			array(
				"checkboxes"=>"Checkboxes"
				)
			),
);
$FIELDTYPE_CMMultiLink = new FieldType(11, "MultiLink", "MultiLinkField", "TEXT", "String", $CMMULTILINK_PARAMLIST);
//===================================================================//

$FIELDTYPE_IMAGE = new FieldType(12, "Image", "ImageField", "TEXT", "String",
    array(
        new FieldParameter("max_width",  "Max value for width",  "65535"),
        new FieldParameter("max_height", "Max value for height", "65535"),
        new FieldParameter("min_width",  "Min value for width",  "0"),
        new FieldParameter("min_height", "Min value for height", "0")
    )
);

$FIELDTYPE_LIST = new FieldType(13, "MultiList", "ListField", "VARCHAR(255)", "String",
    array(
		new FieldParameter("available_values", "Available values ( strings, seperated by comma)", ""),
		new FieldParameter("other_name", "Name of 'other' element (if empty, element is not displayed)", ""),
		new FieldParameter("control_type", "Type of the html control", "radio",
			array(
				"radio"=>"Radio buttons",
				"checkboxes"=>"Checkboxes",
				"select1"=>"Dropdown",
				)
			),
		new FieldParameter("default_value", "Default value", "")
    )
);

$FIELDTYPE_NEWIMAGE = new FieldType(14, "NewImage", "NewImageField", "TEXT", "String",
    array(
	    new FieldParameter("max_size", "Maximum File Size (in bytes)",  "2097152"),
	    new FieldParameter("aext", "Allowed File Extensions (separate values by ',')",  "jpg,JPG,jpeg,JPEG,gif,GIF,tif,TIF,tiff,TIFF,png,PNG,bmp,BMP"),
	    new FieldParameter("thumbnail_side", "Thumbnail width/height", "70"),
	    new FieldParameter("max_side", "Maximum image width/height", "500"),
	    new FieldParameter("resize_strategy", "Image resize strategy", "bywidth", array("bywidth"=>"By Width", "bygreaterside"=>"By Larger Side", "bysmallerside"=>"By Smaller Side")),
	    new FieldParameter("control_type", "Type of the html control", "normal", array("normal"=>"Normal")),
    )
);

$FIELDTYPE_SERVERFILE = new FieldType(15, "Server File", "ServerFileField", "TEXT", "String",
    array()
);

$FIELDTYPE_LIST = new FieldType(16, "List", "SingleListField", "TINYINT", "Int",
    array(
		new FieldParameter("available_values", "Available values ( strings, seperated by comma)", ""),
		new FieldParameter("control_type", "Type of the html control", "radio",
			array(
				"radio"=>"Radio buttons",
				"select"=>"Dropdown with 'Any' element",
				"select_r"=>"Dropdown",
				)
			),
		new FieldParameter("any_el_name", "Name for 'Any' element", ""),
		new FieldParameter("default_value", "Default value", "")
    )
);



?>