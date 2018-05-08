<?
ini_set ("memory_limit", "40M");
set_time_limit (10*60*3600);

require_once("prepend.php");

$action = (string)$request->getParameter("do");

if ($action == null || $action=="") {
	$action = "_default";
}

$action($request);
function _default(&$request) {
	import_form($request);
}

function import_form(&$request) {
	global $session;
	usetemplate("users_import/form");
}

function import_file(&$request) {
	global $session;
	$file = $request->getParameter("import_file");

	$tmpFilePath = $file->tmp_name;

	$tmpFile = file($tmpFilePath);

	if (!$tmpFile) {
		$request->setAttribute("msg", "Некорректный файл");
		usetemplate("users_import/form");
		die();
	}

	class XmlImportReader {
		var $parser = null;
		var $userObj = null;
		var $cdata = "";

		function XmlImportReader() {
			//$this->super();
			$this->parser = xml_parser_create();
			xml_parser_set_option($this->parser, XML_OPTION_CASE_FOLDING, 0);
			xml_parser_set_option($this->parser, XML_OPTION_SKIP_WHITE , 1);

			xml_set_object ( $this->parser, &$this );
			xml_set_element_handler ( $this->parser, "startElement", "endElement" );
			xml_set_character_data_handler ( $this->parser, "characterData" );
		}

		function parse($xmlcode) {
			global $_queue, $_depth;
			$_queue = array();
			$_depth = 0;

			if (!xml_parse($this->parser, $xmlcode, true)) {
				echo "\n\n<br>Xml parser error :<br>";
				echo  "Error string: ".xml_error_string( xml_get_error_code ($this->parser))."<br>";
				echo "Line: ".xml_get_current_line_number($this->parser)."<br>";
				echo "Column: ".xml_get_current_column_number($this->parser)."<br>";
				echo "Byte index: ".xml_get_current_byte_index($this->parser)."<br>";
				echo "\n\n\n".htmlspecialchars($xmlcode);

				die();
			}

			return $_queue[0];
		}

		function startElement($parser, $tagName, $attribs) {
			$tagName = strtolower($tagName);
			$this->cdata = "";
			switch ($tagName) {
				case "site":
					$this->userObj = new phpobject();
					break;
			}
		}

		function endElement($parser, $tagName) {
			$tagName = strtolower($tagName);

						switch ($tagName) {
				case "dogovir" :
					$this->userObj->contract_num = (int)$this->cdata;
					break;
				case "decoder" :
					$this->userObj->decoder_num = (int)$this->cdata;
					break;
				case "famali" :
					$this->userObj->surname = $this->cdata;
					break;
				case "name" :
					$this->userObj->name = $this->cdata;
					break;
				case "balans" :
					$this->userObj->balance = (float)$this->cdata;
					break;
				case "plata" :
					$this->userObj->pay = (float)$this->cdata;
					break;
				case "stan" :
					$this->userObj->status = $this->cdata;
					break;
				case "paket" :
					$this->userObj->package = $this->cdata;
					break;
				case "datapidkl" :
					$this->userObj->start_date = strtotime($this->cdata);
					break;

				case "site":
					$userObj = &$this->userObj;
					$nodeShortname = $userObj->contract_num;
					$password = $userObj->decoder_num . "";// . date("dmY", $userObj->start_date);

					$existingNode = Node::findByPath("/profile/" . $nodeShortname);

					if ($existingNode != null) {
						$userNode = &$existingNode;
					} else {
						$userNode = Node::createWithDefaultValues0("/profile", $userObj->name, $nodeShortname, "abonent_profile");
					}

					$userNode->name = $userObj->name;
					$userNode->fields["surname"]->setValue($userObj->surname);
					$userNode->fields["contract_num"]->setValue($userObj->contract_num);
					$userNode->fields["decoder_num"]->setValue($userObj->decoder_num);
					$userNode->fields["balance"]->setValue($userObj->balance);
					$userNode->fields["pay"]->setValue($userObj->pay);
					$statusBoolean = ($userObj->status == "Викл.") ? 0 : 1;
					$userNode->fields["status"]->setValue($statusBoolean);
					$userNode->fields["package"]->setValue($userObj->package);
					$userNode->fields["start_date"]->setValue(new Date($userObj->start_date));
					$userNode->fields["password"]->setValue($password);

					$userNode->store();
					break;
				case "dataroot" :
					break;
				default:
					die("unknown tag " . $tagName);
			}
		}

		function characterData($parser, $data) {
			$this->cdata .= $data;
			$this->cdata = rtrim($this->cdata);
		}
	}

	$xmlFile = implode("", $tmpFile);

	$xmlReader = new XmlImportReader();
	$xmlReader->parse($xmlFile);

	$request->setAttribute("msg", "Импорт успешно завершён");
	usetemplate("users_import/form");
}

?>