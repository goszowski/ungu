<?php

require_once ("dbm/fields/DateTimeField.php");

define("DATE_FORMAT", "d.m.Y");

class DateField extends DateTimeField {
	function DateField() {}

	function getParamersForIncludeControlJSP() {
		$params = array();

		if ($this->value != null) {
			$date = $this->value;

			$params["day"] = $date->getDay1();
			$params["month"] = $date->getMonth();
			$params["year"] = $date->getYear();

			$params["formatted_date"] = $date->format(DATE_FORMAT);
		}

		return $params;
	}

	function setValue($o) {
		if (is_object($o) && getClassNameLowercase($o) == 'httprequest') {
			$rValue = $o->getParameter($this->fieldDefShortname);
			if (strlen($rValue) > 0) {
				$this->value = new Date($rValue);
			} else {
				$this->value = null;
			}
		} elseif(is_string($o)) {
			$rValue = $o;
			if (strlen($rValue) > 0) {
				$this->value = new Date($rValue);
			} else {
				$this->value = null;
			}
		} elseif (is_object($o) && getClassNameLowercase($o) == 'date') {
			$this->value = $o;
		} elseif (is_null($o)) {
			$this->value = null;
		}
	}

	function getHtmlVisualValue() {
		if ($this->value != null) {
			$str = $this->value->format(DATE_FORMAT);
			$fieldDef = $this->getFieldDef();
			if ($fieldDef->shortname == "birthdate") {
				$today = new Date();
				if ($this->value->getDay1() == $today->getDay1() && $this->value->getMonth() == $today->getMonth()) {
					$str = "<span style='color:red'>$str</span>";
				}
			}
			return $str;
		} else {
			return "&nbsp;";
		}
	}
}

?>