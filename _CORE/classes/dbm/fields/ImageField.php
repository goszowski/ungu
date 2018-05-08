<?

require_once ("dbm/fields/StringField.php");
require_once ("dbm/ImgLib.php");

class ImageField extends StringField {
	function ImageField() {
	}

	function setValue($o) {
		if (is_object($o) && getClassNameLowercase($o) == 'httprequest') {
			$rValue = $o->getParameter($this->fieldDefShortname);
		} elseif(is_string($o)) {
			$rValue = $o;
		}

		$verror = null;
		do {
			$fieldDef = $this->getFieldDef();
			if ((strlen($rValue) == 0) && $fieldDef->required) {
				$verror = RequiredField;
				break;
			}
			if (strlen($rValue) != 0) {
				$img = ImgLibImage::findByUrl($rValue);
				if ($img == null) {
					$verror = "Image not found!";
					break;
				}
			}
		} while(false);

		$this->value = $rValue;
		return $verror;
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
				$fsize = $img->getFileSize();
				$params["size"] = $fsize;
				$params["sizeKB"] = round($fsize / 1024);
				$params["sizeMB"] = round($fsize / 1024 / 1024);
			} else {
				return $this->getDefaultParamersForIncludeControlJSP();
			}
		}

		return $params;
	}

	function getHtmlVisualValue() {
		return ($this->value) ? "<img src=\"/imglib_thumbnails/".$this->value."\" border='0'>" : "&nbsp;";
	}
}

?>