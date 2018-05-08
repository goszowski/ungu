<?php

require_once ("dbm/fields/IntegerField.php");

class BoolField extends IntegerField {
	function setValue($o) {
		global $AdminTrnsl;
		if (is_object($o) && getClassNameLowercase($o) == 'httprequest') {
			$rValue = (int)$o->getParameter($this->fieldDefShortname);
		} else if (is_bool($o)) {
			$rValue = (bool)$o ? 1 : 0;
		} else {
			$rValue = (int)$o;
		}

		$verror = null;
		if (($rValue == 0) || ($rValue == 1)) {
			$this->value = $rValue;
		} else {
			$verror = $AdminTrnsl["BadBoolValue"];
		}

		return $verror;
	}

	function getParamersForIncludeControlJSP() {
		$params = array();
		$params["value"] = ($this->value == 1);

		return $params;
	}

	function getHtmlVisualValue() {
		global $AdminTrnsl;

		$class = ($this->value) == 1 ? 'bg-success' : 'bg-danger';
		$content = ($this->value) == 1 ? $AdminTrnsl["Yes"] : $AdminTrnsl["No"];

		$tpl = '<span class="badge '.$class.' badge-xs">' . $content . '</span>';

		

		return $tpl;
	}

	function getValueForTemplate() {
		return ($this->value == 1);
	}
}

?>