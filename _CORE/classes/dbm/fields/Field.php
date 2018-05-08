<?php

define("FIELD_DATE_FORMAT", "d.m.Y");
class Field {
	var $nodeId = -1;
	var $fieldDefId = -1;
	var $nodeClassId = -1;

	var $value = null;

	function getFieldDef() {
		$nc = NodeClass::findById($this->nodeClassId);
		$fd = $nc->getFieldDef($this->fieldDefId);
		return $fd;
	}

	function load() {
		die("Deprecated method Field::load()");
	}

	function store() {
		die("Deprecated method Field::store()");
	}

	/**
	 * Init from database value
	 */
	function init($fieldRS, $tableAlias, $shortname) {
		if ($tableAlias != "") {
			$fieldNamesPrefix = $tableAlias . "___";
		} else {
			$fieldNamesPrefix = "";
		}

		$fieldType = $this->getType();

		$falias = $fieldNamesPrefix . $shortname;
		$rsGetMethod = "get" . $fieldType->rsType;
		$dbValue = $fieldRS->$rsGetMethod($falias);

		$this->value = $dbValue;
	}

	/**
	 * post-database create event
	 * node, fielddef and value must be set before
	 */
	function create() {
		if ($this->nodeId == null) {
			die("Invalid call to Field::create() - nodeId hasn't been set up.");
		}
	}

	/**
	 * post-database update event
	 */
	function update() {}

	/**
	 * post-database delete event
	 */
	function remove() {}

	function setDefaultValue() {
		$fd = $this->getFieldDef();
		$this->value = $fd->getDefaultValue();
	}

	function getDefaultValue($fd) {
		$ft = $fd->getFieldType();
		die("Abstract method Field::getDefaultValue() not implemented in " . $ft->phpClass);
	}

	function getType() {
		global $DBM_FIELD_TYPES;
		return $DBM_FIELD_TYPES[getClassNameLowercase($this)];
		//$fd = $this->getFieldDef();
		//return $fd->getFieldType;
	}

	function getJSPControlName() {
		$class = getClassNameLowercase($this);
		$cname = $class;
		return $cname;
	}

	function getParamersForIncludeControlJSP() {
		die("Abstract method Field::getParamersForIncludeControlJSP() not implemented in " . getClassNameLowercase($this));
	}

	function getDefaultParamersForIncludeControlJSP() {
		die("Deprecated method Field::getDefaultParamersForIncludeControlJSP");
	}

	function getValueForTemplate() {
		return $this->value;
	}

	function getHtmlVisualValue() {
		return $this->value;
	}
}

?>