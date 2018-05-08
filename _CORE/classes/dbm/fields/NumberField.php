<?php

require_once ("dbm/fields/Field.php");

class NumberField extends Field {
	var $dbColumnName = "value_float";
	function NumberField () {}

	function getJSPControlName() {
		$class = getClassNameLowercase($this);

		$fieldDef = $this->getFieldDef();
		$ctype = $fieldDef->getParameterValue("control_type");

		if ($ctype == null) {
			$cname = $class;
			return $cname;
		}

		$cname = $class . "_" . $ctype;

		return $cname;
	}

	function getParamersForIncludeControlJSP() {
		$params = array();
		$params["strvalue"] = $this->value;

		return $params;
	}

	function getDefaultValue($fd) {
		$value = 0;
		return $value;
	}
}

?>