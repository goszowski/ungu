<?php

require_once ("dbm/fields/TextareaField.php");

class WysiwygTextField extends TextareaField {
	function WysiwygTextField () {}

	function& getDefaultParamersForIncludeControlJSP() {
		$params = TextareaField::getDefaultParamersForIncludeControlJSP();
		if ($this->nodeId != -1) {
			$params["node_id"] = $this->nodeId;
			$params["field_shortname"] = $this->fieldDefShortname;
		}
		return $params;
	}

	function getParamersForIncludeControlJSP() {
		$params = TextareaField::getParamersForIncludeControlJSP();
		if ($this->nodeId != -1) {
			$params["node_id"] = $this->nodeId;
			$params["field_shortname"] = $this->fieldDefShortname;
		}
		return $params;
	}

	function getValueForTemplate() {
		return $this->value;
	}
}

?>