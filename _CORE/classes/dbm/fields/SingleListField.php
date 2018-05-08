<?php

require_once ("dbm/fields/IntegerField.php");

class SingleListField extends IntegerField {

	function SingleListField() {}

	function setValue($o) {
		global $AdminTrnsl;
		if (is_object($o) && getClassNameLowercase($o) == 'httprequest') {
			$rValue = (int)$o->getParameter($this->fieldDefShortname);
		} else {
			$rValue = (int)$o;
		}

		$fieldDef = $this->getFieldDef();

		$available_values = $this->getAvailableValues();

		$verror = null;
		$this->value = 0;
		do {
			if ($rValue == -1 && $fieldDef->required) {
				$verror = $AdminTrnsl["RequiredField"];
				break;
			}
			if ($rValue >= 0 && $rValue < sizeof($available_values)) {
				$this->value = $rValue;
				break;
			} else {
				$verror = $AdminTrnsl["Value_is_not_in_range"];
				break;
			}
		} while(false);

		return $verror;
	}

	function getParamersForIncludeControlJSP() {
		$params = array();

		$fieldDef = $this->getFieldDef();
		$fieldType = $fieldDef->getFieldType();
		$classParams = $fieldType->getParametersNames();

		foreach ($classParams as $fieldParameterShortname) {
			$params[$fieldParameterShortname] = $fieldDef->getParameterValue($fieldParameterShortname);
		}

		$params["available_values"] = $this->getAvailableValues();
		$params["value"] =  $this->value;

		return $params;
	}

	function setDefaultValue() {
		$fd = $this->getFieldDef();
		$this->value = $this->getDefaultValue($fd);
	}

	function& getValueForTemplate() {
		$fieldDef = $this->getFieldDef();
		$available_values = $this->getAvailableValues();

		$value = array();

		$value["value"] = $this->value;

		$caption = "";
		if ($value != -1) {
			$caption = $available_values[$this->value];
		}
		$value["caption"] = $caption;
		$value["name"] = $caption;

		return $value;
	}

	function& getDefaultValue(&$fd) {
        $dv = $fd->getParameterValue("default_value");

		if (!$dv) {
			$dv = 0;
		}

		return $dv;
	}

	function getJSPControlName() {
		$fieldDef = $this->getFieldDef();
		$ctype = $fieldDef->getParameterValue("control_type");
		if ($ctype == null || $ctype=="") {
			$ctype = "radio";
		}

		$class = getClassNameLowercase($this);
		$cname = $class . "_" . $ctype;
		return $cname;
	}

	function getAvailableValues() {
		$fieldDef = $this->getFieldDef();
		$available_values = explode(",", $fieldDef->getParameterValue("available_values"));
		return $available_values;
	}

	function getHtmlVisualValue() {
		global $AdminTrnsl;
		if ($this->value != -1) {
			$available_values = $this->getAvailableValues();

			$caption = $available_values[$this->value];

			return "<nobr>" . $caption . "</nobr>";
		} else {
			return "&nbsp;";
		}
	}
}

?>