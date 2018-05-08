<?php

require_once ("dbm/fields/Field.php");

class FileField extends Field {
	var $dbColumnName = "value_str";

	var $tmpFilePath = "";
	var $tmpFileName = "";
	var $filePath = "";
	var $fileName = "";
	var $deleteOnStore = false;

	function FileField() {}

	function computeFileName($node_id=0, $field_id=0, $ext="") {
		if ($node_id != 0) {
			return $node_id . "_" . $field_id . (strlen($ext) != 0 ? "." . $ext : "");
		} else {
			if ($this->nodeId != 0) {
				//$node = &Node::findById($this->nodeId);
				//if ($node != null) {
					return $this->nodeId . "_" . $this->fieldDefId . (strlen((string)$this->value) != 0 ? "." . $this->value : "");
				//}
			}
			return "";
		}
	}

	function init($fieldRS, $tableAlias, $shortname) {
		Field::init($fieldRS, $tableAlias, $shortname);
		$this->fileName = $this->computeFileName();
		$this->filePath = FILE_FIELD_DIR . FILE_SEPARATOR . $this->fileName;
	}

	function setValue($o) {
		$del = null;
		if (is_object($o) && getClassNameLowercase($o) == 'httprequest') {
			$request = $o;
			$del = $request->getParameter($this->fieldDefShortname . "_del");
			$file = $request->getParameter($this->fieldDefShortname);
			$this->tmpFileName = $file->name;
			$this->tmpFilePath = $file->tmp_name;
		} elseif(is_string($o)) {
			$els = explode(",", $rValue);
			$this->tmpFileName = (sizeof($els) != 2) ? "" : $els[0];
			$this->tmpFilePath = (sizeof($els) != 2) ? "" : $els[1];
		}

		$verror = "";
		do {
			$fieldDef = $this->getFieldDef();
			if ($del != null && $del == "1") {
				if ($fieldDef->required) {
					$verror = "�� ���� ������� ����. ������������ ����.";
					break;
				}
				$this->deleteOnStore = true;
			} else {
				if (strlen($this->tmpFileName) == 0) {
					if ($fieldDef->required && (!$this->filePath || !file_exists($this->filePath))) {
						$verror = "������������ ����.";
						break;
					}
				} else {
					if ((@filesize($this->tmpFilePath) == 0) && $fieldDef->required && !file_exists($this->tmpFilePath)) {
						$verror = "������������ ����.";
						break;
					}

					$maxLength = (int)$fieldDef->getParameterValue("max_size");
					if (@filesize($this->tmpFilePath) > $maxLength) {
						$verror = "Uploaded file length exceed maximum length of " . $maxLength;
						break;
					}
					$ext = getFileExt($this->tmpFileName);
					$aext = $fieldDef->getParameterValue("aext");
					if (!$aext == "*") {
						if (strpos("," . $aext . ",", $ext) === false) {
							$verror = "Extension '" . $ext . "' not allowed";
							break;
						}
					}
				}
			}
		} while(false);

		return $verror;
	}

	function copyFromTmpFile() {
		$this->value = getFileExt($this->tmpFileName);
		$this->fileName = $this->computeFileName();
		$this->filePath = FILE_FIELD_DIR . DIR_SEPARATOR . $this->fileName;
		copy($this->tmpFilePath, $this->filePath);
	}

	function create() {
		$this->fileName = $this->computeFileName($node_id, $this->fieldDefId, "");
		$this->filePath = FILE_FIELD_DIR . DIR_SEPARATOR . $this->fileName;
		$this->value = "";
		if ($this->tmpFileName) {
			$ext = getFileExt($this->tmpFileName);
			$_fileName = $this->computeFileName($node_id, $this->fieldDefId, $ext);
			$_filePath = FILE_FIELD_DIR . DIR_SEPARATOR . $_fileName;

			if (filesize($this->tmpFilePath) != 0) {
				copy($this->tmpFilePath, $_filePath);
				$this->filePath = $_filePath;
				$this->fileName = $_fileName;
				$this->value = $ext;
			}
		}
		Field::create($node_id);
	}

	function update() {
		if ($this->deleteOnStore && file_exists($this->filePath)) {
			unlink($this->filePath);
			$this->value = "";
			Field::update();
			return;
		}

		if ($this->tmpFileName) {
			if (file_exists($this->filePath)) {
				unlink($this->filePath);
			}
			$this->copyFromTmpFile();
			Field::update();
		}
	}

	function remove() {
		if (file_exists($this->filePath)) {
			unlink($this->filePath);
		}
	}

	/**
	 * filename, filesize, fileurl
	 */
	function getParamersForIncludeControlJSP() {
		$params = array();
		if (file_exists($this->filePath)) {
			$fname = $this->computeFileName();
			$params["filename"] = $fname;
			$params["filesize"] = filesize($this->filePath);
			$params["fileurl"] = "/_files/" . $fname;
		} else {
			return $this->getDefaultParamersForIncludeControlJSP();
		}

		return $params;
	}

	function getDefaultParamersForIncludeControlJSP() {
		$params = array();
		$params["filename"] = "";
		$params["filesize"] = 0;
		$params["fileurl"] = "";

		return $params;
	}

	function getValueForTemplate() {
		$params = array();
		if (file_exists($this->filePath)) {
			$params["name"] = $this->fileName;
			$ftime = filemtime($this->filePath);
			$params["time"] = date("Y-m-d H:i",$ftime);
			$fsize = filesize($this->filePath);
			$params["size"] = $fsize;
			$params["sizeKB"] = $fsize / 1024;
			if ($fsize / 1024 < 1) {
				$params["sizeKB"] = sprintf("%3.2f", $fsize / 1024);
			}
			$params["sizeMB"] = $fsize / 1024 / 1024;

			$params["url"] = "/_files/" . $this->computeFileName();
		} else {
			$params["size"] = 0;
			$params["name"] = "";
			$params["time"] = "";
			$params["sizeKB"] = 0;
			$params["sizeMB"] = 0;
			$params["url"] = "";
		}

		return $params;
	}

	function getValue() {
		return "/_files/" . $this->computeFileName();
	}

	function getHtmlVisualValue() {
		global $AdminTrnsl;
		$ext = getFileExt($this->value);
		return ($this->value) ? $AdminTrnsl["DownloadLibFile"] . ": " . $ext . ". <a href=\"/_files/".$this->computeFileName()."\" target=_blank>".$AdminTrnsl["Link_to_file"]."</a>" : "&nbsp;";
	}

	function getDefaultValue($fd) {
		$value = "";
		return $value;
	}
}

?>