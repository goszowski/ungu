<?php

require_once ("dbm/fields/Field.php");

define("DATETIME_FORMAT", "d.m.Y H:i:s");

class DateTimeField extends Field {
	var $dbColumnName = "value_timestamp";
	function DateTimeField() {}

	function getDefaultValue($fd) {
		$value = new Date();
		return $value;
	}

	function getParamersForIncludeControlJSP() {
		$params = array();

		$date = $this->value;

		$params["day"] = $date->getDay();
		$params["month"] = $date->getMonth();

		$params["year"] = $date->getYear();
		$params["hour"] = $date->getHour();
		$params["minute"] =$date->getMinute();
		$params["second"] = $date->getSecond();

		$params["formatted_date"] = $date->format(DATE_FORMAT);
		$params["formatted_datetime"] = $date->format(DATETIME_FORMAT);

		return $params;
	}

	function setValue($o) {
		if (is_object($o) && getClassNameLowercase($o) == 'httprequest') {
			$rValue = $o->getParameter($this->fieldDefShortname);
		} elseif(is_string($o)) {
			$rValue = $o;
		}

        /** Godjatsky Edit **/
		$this->value->time = strtotime($rValue);
        //$this->value = new Date($rValue);
        //$this->value->time = strtotime('21.09.2011 12:35:45');
        //_dump($this->value);        
        /** END **/
	}

	function getValueForTemplate() {
		$v = $this->getParamersForIncludeControlJSP();
		return $v;
	}

	function getHtmlVisualValue() {
		return $this->value->format(DATETIME_FORMAT);
	}

	function getJSPControlName() {
		$class = getClassNameLowercase($this);

		$fieldDef = $this->getFieldDef();
		$ctype = $fieldDef->getParameterValue("control_type");

		if ($ctype == null || $ctype=="") {
			$cname = $class;
			return $cname;
		}

		$cname = $class . "_" . $ctype;

		return $cname;
	}
}

?>