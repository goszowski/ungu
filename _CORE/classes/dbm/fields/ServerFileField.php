<?

require_once ("dbm/fields/Field.php");

class ServerFileField extends Field {
	var $dbColumnName = "value_str";

	function ServerFileField() {}

	function setValue($o) {
		global $AdminTrnsl, $request;
		$fieldDef = $this->getFieldDef();
		if (is_object($o) && getClassNameLowercase($o) == 'httprequest') {
			$del = $request->getParameter($fieldDef->shortname . "_del");
			$v = $o->getParameter($fieldDef->shortname);
		} elseif(is_string($o)) {
			$this->value = $o;
			return;
		}

		$verror = null;
		do {
			if ($del == "1") {
				$this->value = "";
			} else {
				if (strlen($v) == 0 && $fieldDef->required) {
					$verror = $AdminTrnsl["RequiredField"];
					break;
				}
				$this->value = $v;
			}
		} while(false);
		return $verror;
	}

	function getParamersForIncludeControlJSP() {
		$params = array();

		$fname = $this->value;
		if ($fname) {
			$serverFilesDirPath = FIELD_SERVERFILES_DIR;
			$f = $serverFilesDirPath . $fname;
			if (!file_exists($f)) {
				return $this->getDefaultParamersForIncludeControlJSP();
			} else {
				$params["filename"] = substr($fname, strpos($fname, '/'));
				$params["name"] = $params["filename"];

				$params["fileext"] = getFileExt($params["filename"]);
				$params["ext"] = $params["fileext"];

				$fsize = filesize($f);

				$params["filesize"] = $fsize;
				$params["size"] = $fsize;

				if ($fsize > 1024 * 1024) {
					$params["sizeFormatted"] = sprintf("%3.2f", $fsize / 1024 / 1024) . " Mb";
				} else if ($fsize > 1024) {
					$params["sizeFormatted"] = sprintf("%3.2f", $fsize / 1024) . " Kb";
				} else {
					$params["sizeFormatted"] = ($fsize) . " b";
				}

				$params["fileurl"] = "/files" . $fname;
				$params["url"] = "/files" . $fname;
			}
		} else {
			return $this->getDefaultParamersForIncludeControlJSP();
		}
		return $params;
	}

	function getDefaultParamersForIncludeControlJSP() {
		$params = array();

		$params["filename"] = "";
		$params["name"] = "";

		$params["fileext"] = "";
		$params["ext"] = "";

		$params["filesize"] = 0;
		$params["size"] = 0;

		$params["fileurl"] = "";
		$params["url"] = "";

		return $params;
	}

	function getHtmlVisualValue() {
		$fname = $this->value;
		$file = FIELD_SERVERFILES_DIR . $fname;
		if (file_exists($file)) {
			return "<a href='/files" . $fname . "'>" . $fname . "</a>";
		} else {
			return "";
		}
	}

	function getValueForTemplate() {
		return $this->getParamersForIncludeControlJSP();
	}

	function& getDefaultValue($fd) {
		$value = "";
		return $value;
	}
}

?>