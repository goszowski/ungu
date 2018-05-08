<?php

require_once ("dbm/fields/NumberField.php");

class FloatField extends NumberField {
	function setValue($o) {
		if (is_object($o) && getClassNameLowercase($o) == 'httprequest') {
			$rValue = (float)$o->getParameter($this->fieldDefShortname);
		} else {
			$rValue = (float)$o;
		}
		$fieldDef = $this->getFieldDef();
		$max_value = (float)$fieldDef->getParameterValue("max_value");
		$min_value = (float)$fieldDef->getParameterValue("min_value");

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