<?

require_once ("dbm/fields/StringField.php");
require_once ("dbm/ImgLib.php");

class NewImageField extends Field {
	var $dbColumnName = "value_str";

	var $tmpFilePath = "";
	var $tmpFileName = "";
	var $deleteOnStore = false;

	var $oldValue = "";

	function NewImageField() {}

	function computeImglibDir($node_id=0) {
		$apath = null;

		if ($node_id!=0) {
			$apath = Node::findAbsolutePathById($node_id);
		} else {
			if ($this->nodeId != -1) {
				$apath = Node::findAbsolutePathById($this->nodeId);
			}
			if ($apath == null) {
				die("can't create NewImageField");
			}
		}

		return "img" . $apath;
	}

	function setValue($o) {
		global $AdminTrnsl;
		$del = null;
		if (is_object($o) and getClassNameLowercase($o) == 'httprequest') {
			$request = $o;
			$fieldDef = $this->getFieldDef();
			$rValue = $o->getParameter($fieldDef->shortname);
			$del = $request->getParameter($fieldDef->shortname . "_del");
			$file = $request->getParameter($fieldDef->shortname . "_file");
			//$this->tmpFileName = $file->name; //original
			if ($file->name) { //kazancev
				$pathinfo = pathinfo($file->name);
				$this->tmpFileName = $this->nodeId . "_" . $this->fieldDefId . '_' . md5(microtime()) . ".".$pathinfo["extension"]; //goszowski
			}
			$this->tmpFilePath = $file->tmp_name;
		} elseif(is_string($o)) {
			$rValue = $o;
		}

		$verror = "";
		do {
			$fieldDef = $this->getFieldDef();
			if ($del != null && $del == "1") {
				$this->oldValue = $this->value;
				if ($fieldDef->required) {
					$verror = $AdminTrnsl["CannotDeleteFileRequiredField"];
					$this->value = $rValue;
					break;
				}
				$this->value = "";
				$this->deleteOnStore = true;
			} else {
				if (strlen($this->tmpFileName) == 0) {
					if (!$rValue) {
						if ($fieldDef->required) {
							$verror = $AdminTrnsl["Required Field"];
							break;
						}
					} else {
						
						$image = ImgLibImage::findByUrl($rValue);
						if ($image == null) {
							$verror = $AdminTrnsl["ImageNotFoundInImgLib"];
							break;
						}
					}


					
					if (strpos($rValue, "/tmp/") !== false) {
						$this->oldValue = $this->value;
					}
					$this->value = $rValue;
				} else {
					if ($fieldDef->required && (@filesize($this->tmpFilePath) == 0) && !file_exists($this->tmpFilePath)) {
						$verror = $AdminTrnsl["Required Field"];
						break;
					}

					$maxLength = (int)$fieldDef->getParameterValue("max_size");
					if (@filesize($this->tmpFilePath) > $maxLength) {
						$verror = $AdminTrnsl["UploadFileIsTooLarge"] . $maxLength;
						break;
					}
					$ext = getFileExt($this->tmpFileName);
					$aext = $fieldDef->getParameterValue("aext");
					if ($aext != "*") {
						if (strpos("," . $aext . ",", $ext) === false) {
							$verror = $AdminTrnsl["ExtensionNotAllowed1"] . " '" . $ext . "' " . $AdminTrnsl["ExtensionNotAllowed2"];
							break;
						}
					}
					$this->createTmpValue();
				}
			}
		} while(false);

		return $verror;
	}

	function createTmpValue() {
		$this->oldValue = $this->value;

		$dirRelUrl = "_newimage/tmp/" . md5(uniqid(rand(), true));
		$fieldDef = $this->getFieldDef();
		if ($this->tmpFileName) {
			if (filesize($this->tmpFilePath) != 0) {
				$cdir = &ImgLibFolder::findByRelDir($dirRelUrl);
				$thumbnailSide = $fieldDef->getParameterValue("thumbnail_side");
				$maxSide = $fieldDef->getParameterValue("max_side");
				$resizeStrategy = $fieldDef->getParameterValue("resize_strategy");
				$img = $cdir->createImage($this->tmpFilePath, $this->tmpFileName, $thumbnailSide, $maxSide, constant("_IMG_RESIZE_STRATEGY_".strtoupper($resizeStrategy)));
				$this->value = $img->getUrl();
			}
		}
	}

	function create() {
		$this->update();
	}

	function update() {
		
		if ($this->deleteOnStore) {
			if ($this->oldValue and strlen($this->oldValue) > 0) {
				$image = ImgLibImage::findByUrl($this->oldValue);
				if ($image) {
					$image->remove();
				}
			}
			Field::update();
			return;
		}

		if (strpos($this->value, "/tmp/") !== false) {
			$tmp_old_value = $this->value;//kazancev
			if ($this->oldValue && ($this->oldValue != $this->value)) {
				$image = ImgLibImage::findByUrl($this->oldValue);
				
				if ($image) {
					$image->remove();
				}
			}

			$tmpImage = ImgLibImage::findByUrl($this->value);

			$dirRelUrl = $this->computeImglibDir();
			$cdir = ImgLibFolder::findByRelDir($dirRelUrl);

			$tmpImage->moveTo($cdir);
			$this->value = $tmpImage->getUrl();
			
			//kazancev start
			$path_parts = pathinfo($tmp_old_value);
			//_dump($path_parts);
			$tmpdir = ImgLibFolder::findByRelDir($path_parts['dirname']);
			//$tmpdir = str_replace('//', '/', $tmpdir);
			//$tmpdir = str_replace("\", "/", $tmpdir);
			//_dump(is_dir($tmpdir));
			//var_dump(is_dir($tmpdir->dir));
			
			if (is_dir($tmpdir->dir)) {
				$tmpdir->remove();
			}
			//kazancev end
			
		}
		Field::update();
	}

	function remove() {
		$image = ImgLibImage::findByUrl($this->value);
		if ($image != null) {
			$image->remove();
		}
	}

	function getParamersForIncludeControlJSP() {
		$params = array();

		$fieldDef = $this->getFieldDef();
		$fieldType = $fieldDef->getFieldType();
		$classParams = $fieldType->getParametersNames();

		foreach ($classParams as $fieldParameterShortname) {
			$params[$fieldParameterShortname] = $fieldDef->getParameterValue($fieldParameterShortname);
		}

		$size_tag = "";
		$url = $this->value;
		if ($url != null && strlen($url) != 0) {
			$img = ImgLibImage::findByUrl($url);

			if ($img != null) {
				$params["size_tag"] = " width=" . $img->getWidth() . " height=" . $img->getHeight();
				$params["width"] = $img->getWidth();
				$params["height"] = $img->getHeight();
				$params["url"] = "/imglib" . $img->getUrl();
				$params["relurl"] = $img->getUrl();

				$params["thumburl"] = "/imglib_thumbnails" . $img->getUrl();
				$params["thumbwidth"] = $img->getThumbnailWidth();
				$params["thumbheight"] = $img->getThumbnailHeight();

				$fsize = $img->getFileSize();
				$params["size"] = $fsize;
				$params["sizeKB"] = round($fsize / 1024);
				$params["sizeMB"] = round($fsize / 1024 / 1024);
			} else {
				$params["size_tag"] = "";
				$params["width"] = -1;
				$params["height"] = -1;
				$params["url"] = "";
				$params["relurl"] = "";

				$params["thumburl"] = "";
				$params["thumbwidth"] = -1;
				$params["thumbheight"] = -1;

				$fsize = -1;
				$params["size"] = -1;
				$params["sizeKB"] = -1;
				$params["sizeMB"] = -1;
			}
		}

		return $params;
	}

	function getValueForTemplate() {
		return $this->getParamersForIncludeControlJSP();
	}

	function getValue() {
		$url = $this->value;
		if ($url != null && strlen($url) != 0) {
			$img = ImgLibImage::findByUrl($url);
			if ($img != null) {
				return "/imglib" . $img->getUrl();
			} else {
				return null;
			}
		} else {
			return null;
		}
	}

	function getHtmlVisualValue() {
		return ($this->value) ? "<img src=\"/imglib_thumbnails/".$this->value."\" border=0 style='max-width: 50px;'>" : "&nbsp;";
	}

	function getJSPControlName() {
		$fieldDef = $this->getFieldDef();
		$ctype = $fieldDef->getParameterValue("control_type");
		if ($ctype == null || $ctype=="") {
			$ctype = "normal";
		}

		$class = getClassNameLowercase($this);
		$cname = $class . "_" . $ctype;
		return $cname;
	}

	function& getDefaultValue($fd) {
		$value = "";
		return $value;
	}
}

?>
