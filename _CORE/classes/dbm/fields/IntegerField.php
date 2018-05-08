<?php

require_once ("dbm/fields/NumberField.php");

class IntegerField extends NumberField {
	function setValue($o) {
		if (is_object($o) && getClassNameLowercase($o) == 'httprequest') {
			$rValue = (int)$o->getParameter($this->fieldDefShortname);
		} else {
			$rValue = (int)$o;
		}
		$fieldDef = $this->getFieldDef();
		$max_value = (int)$fieldDef->getParameterValue("max_value");
		$min_value = (int)$fieldDef->getParameterValue("min_value");

		$verror = null;
		if (($rValue <= $max_value) && ($rValue >= $min_value)) {
			$this->value = $rValue;
		} else {
			$verror = $AdminTrnsl["FloadtValueNotInRange"] . " [" . $min_value . "," . $max_value . "].";
		}

		return $verror;
	}
}

?>