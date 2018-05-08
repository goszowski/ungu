<?php

require_once ("dbm/FormDef.php");
require_once ("dbm/fields/NumberField.php");

class FormField extends NumberField {
	function setValue($o) {
		$fieldDef = $this->getFieldDef();
		if (is_object($o) && getClassNameLowercase($o) == 'httprequest') {
			$rValue = (int)$o->getParameter($fieldDef->shortname);
		} else {
			$rValue = (int)$o;
		}

		$verror = null;

		if ($rValue == 0 && $fieldDef->required) {
			$verror = $AdminTrnsl["RequiredField"];
		} else {
			if($rValue != 0) {
				$formDef = &FormDef::findById($rValue);
				if ($formDef == null) {
					$verror = $AdminTrnsl["FormFieldFormWasDeleted"];
				}
			}
		}
		$this->value = $rValue;

		return $verror;
	}

	function getParamersForIncludeControlJSP() {
		$params = array();
		$params["form_id"] = (int)$this->value;
		$params["all_forms"] = &FormDef::findAll();

		return $params;
	}

	function getDefaultParamersForIncludeControlJSP() {
		$params = array();
		$params["form_id"] = 0;
		$params["all_forms"] = &FormDef::findAll();

		return $params;
	}

	function getValueForTemplate() {
		if ($this->value != 0) {
			$formDef = &FormDef::findById((int)$this->value);
			return $formDef;
		} else {
			return null;
		}
	}

	function getHtmlVisualValue() {
		global $AdminTrnsl;
		if ($this->value != 0) {
			$formDef = &FormDef::findById((int)$this->value);
			return $formDef->name;
		} else {
			return "&nbsp;";
		}
	}
}

?>