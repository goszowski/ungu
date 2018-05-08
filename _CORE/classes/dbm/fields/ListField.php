<?php

require_once ("dbm/fields/StringField.php");

define("LINKFIELD_DB_DELIMITER", ",,,");

class ListField extends StringField {
	function haveOther() {
		$fieldDef = $this->getFieldDef();
		return strlen($fieldDef->getParameterValue("other_name")) > 0;
	}

	function setSingleValue($o) {
		return $this->setValue(LINKFIELD_DB_DELIMITER.$o.LINKFIELD_DB_DELIMITER);
	}

	function setValue($o) {
		global $AdminTrnsl;
		if (is_object($o) && getClassNameLowercase($o) == 'httprequest') {
			$values = $o->getParameter($this->fieldDefShortname);
			if ($values == null) {
				$values = array();
			}
		} elseif(is_string($o)) {
			$rValue = $o;

			if (strpos($rValue, LINKFIELD_DB_DELIMITER) === 0) {
				$this->value = $rValue;
				return null;
			}
			$values = ListField::explodeStringTrimSE(LINKFIELD_DB_DELIMITER, $rValue);
		}

		$verror = null;
		do {
			$dbValue = "";
			for ($i = 0; $i < sizeof($values); $i++) {
				$dbValue .= LINKFIELD_DB_DELIMITER;
				$dbValue .= $values[$i];
			}
			$dbValue .= LINKFIELD_DB_DELIMITER;
			$v = new LFValue();
			$v->constructFromDB($dbValue, $this->haveOther());
			$fieldDef = $this->getFieldDef();
			if ($fieldDef->required && (sizeof($v->indexes) == 0 && (!$this->haveOther() || strlen($v->other) == 0))) {
				$verror = $AdmTrnsl["RequiredField"];
				break;
			}

			$this->value = $dbValue;
		} while(false);

		return $verror;
	}

	function getParamersForIncludeControlJSP() {
		$params = array();

		$fieldDef = $this->getFieldDef();
		$avail_values = explode(",", $fieldDef->getParameterValue("available_values"));
		$params["available_values"] = $avail_values;
		$params["haveOther"] = $this->haveOther();
		$other_name = $fieldDef->getParameterValue("other_name");
		$params["other_name"] = $other_name;
		$params["values"] = array();
		$v = new LFValue();
		$v->constructFromDB($this->value, $this->haveOther());
		$params["other"] = $v->other;
		$params["values"] = $v->indexes;

		return $params;
	}

	function getValueForTemplate() {
		return $this->getValue();
	}

	function getValue() {
		if ($this->value != null && strlen($this->value) != 0) {
			$params = array();
			$fieldDef = $this->getFieldDef();
			$other_name = $fieldDef->getParameterValue("other_name");
			$avail_values = explode(",", $fieldDef->getParameterValue("available_values"));
			$v = new LFValue();
			$v->constructFromDB($this->value, $this->haveOther());
			$values = array();

			for ($i = 0; $i < sizeof($v->indexes); $i++) {
				$values[] = $avail_values[(int)$v->indexes[$i]];
			}
			$params["other"] = $v->other;
			$params["values"] = $values;
			$params["indexes"] = $v->indexes;
			return $params;
		} else {
			return null;
		}
	}

	function getDefaultValue($fd) {
		$dv = $fd->getParameterValue("default_value");

		if ($dv == null) {
			$dv = LINKFIELD_DB_DELIMITER;
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

	function getHtmlVisualValue() {
		$v = $this->getValueForTemplate();
		$values = $v["values"];
		$valuesSB = "";
		for ($i = 0; $i < sizeof($values); $i++) {
			if ($i != 0)
				$valuesSB .= ", ";
			$valuesSB .= $values[$i];
		}
		if ($this->haveOther()) {
			if (sizeof($values) > 0)
				$valuesSB .= ", ";
			$valuesSB .= $v["other"];
		}
		return $valuesSB;
	}

	function explodeStringTrim($delim, $str) {
		$result = array();
		if ($str == null)
			return $result;
		$fromIndex = 0;
		$str = trim($str);
		$strlen = strlen($str);
		while($fromIndex < $strlen) {
			$indx = strpos($str, $delim, $fromIndex);
			if ($indx === false) {
				$result[] = trim(substr($str, $fromIndex));
				break;
			}
			if ($indx != 0) {
				$substr = trim(substr($str, $fromIndex, $indx-$fromIndex));
				$result[] = $substr;
			}
			$fromIndex = $indx + strlen($delim);
		}
		return $result;
	}

	function explodeStringTrimSE($delim, $str) {
		$result = array();
		if ($str == null)
			return $result;
		$fromIndex = 0;
		$str = trim($str);
		$strlen = strlen($str);
		for (; $fromIndex < $strlen;) {
			$indx = strpos($str, $delim, $fromIndex);
			if ($indx === false) {
				$result[] = trim(substr($str, $fromIndex));
				break;
			}
			$substr = trim(substr($str, $fromIndex, $indx-$fromIndex));
			$result[] = $substr;
			$fromIndex = $indx + strlen($delim);
			if ($fromIndex == $strlen) {
				$result[] = "";
				break;
			}
		}
		return $result;
	}
}

class LFValue {
	var $indexes = array();
	var $other = "";

	function constructFromDB($dbValue, $haveOther) {
		$values = ListField::explodeStringTrim(LINKFIELD_DB_DELIMITER, $dbValue);
		if (sizeof($values) == 0) {
			$this->indexes = array();
			if ($haveOther) {
				$this->other = "";
			}
		} else if ($haveOther && sizeof($values) == 1) {
			$this->indexes = array();
			$this->other = $values[0];
		} else {
			if ($haveOther) {
				$this->other = $values[sizeof($values) - 1];
				$this->indexes = array();
				for ($i = 0; $i < sizeof($values) - 1; $i++) {
					$this->indexes[$i] = $values[$i];
				}
			} else {
				$this->indexes = $values;
			}
		}
	}

	function compileDBValue() {
		$dbValue = "";
		for ($i = 0; $i < sizeof($this->indexes); $i++) {
			$dbValue .= LINKFIELD_DB_DELIMITER;
			$dbValue .= $this->indexes[$i];
		}
		if ($this->other != null) {
			$dbValue .= LINKFIELD_DB_DELIMITER;
			$dbValue .= $this->other;
		}
		$dbValue .= LINKFIELD_DB_DELIMITER;
		return $dbValue;
	}
}

?>