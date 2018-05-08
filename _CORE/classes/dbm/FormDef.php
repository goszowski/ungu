<?

define("tn_form_field_defs", "dbm_form_field_defs");
define("tn_form_defs", "dbm_form_defs");

class FormFieldType {
	var $shortname = "";
	var $name = "";

    function getAllTypes() {
        return $GLOBALS["__shortname_form_type_hash"];
    }

    function findById($id) {
        return $GLOBALS["__shortname_form_type_hash"][$id];
    }

    /**
     * No public constructor!
     * Only several instances for each form field type
     */
    function FormFieldType($shortname, $name) {
        $this->shortname = $shortname;
        $this->name = $name;
        if (!array_key_exists("__shortname_form_type_hash", $GLOBALS)) $GLOBALS["__shortname_form_type_hash"] = array();
        $GLOBALS["__shortname_form_type_hash"][$shortname] = &$this;
    }
}

global $AdminTrnsl;
$FFT_STRING = new FormFieldType("string", $AdminTrnsl["FormFieldTypeString"]);
$FFT_TEXTAREA = new FormFieldType("textarea", $AdminTrnsl["FormFieldTypeTextarea"]);
$FFT_DATE = new FormFieldType("date", $AdminTrnsl["FormFieldTypeDate"]);
$FFT_EMAIL = new FormFieldType("email", $AdminTrnsl["FormFieldTypeEmail"]);

class FormFieldDef {
	var $id = 0;
	var $form_id = 0;
	var $name = "";
	var $type_shortname = "";
	var $required = 0;

	var $type = null;

	function load(&$rs) {
		$this->id = $rs->getInt("id");
		$this->form_id = $rs->getInt("form_id");
		$this->name = $rs->getString("name");
		$this->type_shortname = $rs->getString("type_shortname");
		$this->required = $rs->getInt("required");
		$this->type = &FormFieldType::findById($this->type_shortname);
	}

	function moveUpInOrder() {
		$prevId = 0;

		$sql = "SELECT id FROM " . tn_form_field_defs . " WHERE form_id=" .$this->form_id ." AND id < " . $this->id . " ORDER by id DESC LIMIT 1";

		$rs = &DBUtils::execSelect($sql);
		if ($rs->next()) {
			$prevId = $rs->getInt(1);
		}

		if ($prevId != 0) {
			$params = array(
				array( -1, $this->id),
				array( $this->id, $prevId ),
				array( $prevId, -1 )
				);
			$sql = "UPDATE " . tn_form_field_defs . " SET id=? WHERE id=?";
			$batch = array( $sql, $sql, $sql);
			DBUtils::execBatch($batch, $params);
		}
	}

	function moveDownInOrder() {
		$prevId = 0;

		$sql = "SELECT id FROM " . tn_form_field_defs . " WHERE form_id=" . $this->form_id . " AND id > " . $this->id . " ORDER by id LIMIT 1";

		$rs = &DBUtils::execSelect($sql);
		if ($rs->next()) {
			$prevId = $rs->getInt(1);
		}

		if ($prevId != 0) {
			$params = array(
				array(-1, $this->id),
				array($this->id, $prevId),
				array($prevId, -1)
				);
			$sql = "UPDATE " . tn_form_field_defs . " SET id=? WHERE id=?";
			$batch = array( $sql, $sql, $sql);
			DBUtils::execBatch($batch, $params);
		}
	}

	function validateValue($strValue) {
		$error_code = "";
		switch($this->type_shortname) {
			case "date" : case "string" : case "textarea" :
				if ($this->required && !$strValue) {
					$error_code = "required_field";
				}
				break;
			case "email" :
				if ($this->required && !$strValue) {
					$error_code = "required_field";
				} else if (!Validator::checkEmail($strValue)) {
					$error_code = "bad_email";
				}
				break;
			default:
				break;
		}

		return $error_code;
	}
}

class FormDef {
	var $id = 0;
	var $name = "";

	var $description_text = "";
	var $success_text = "";

	var $inc_sub = 0;
	var $fieldDefs = array();

	function load(&$rs) {
		$this->id = $rs->getInt("id");
		$this->name = $rs->getString("name");
		$this->description_text = $rs->getString("description_text");
		$this->success_text = $rs->getString("success_text");
		$this->inc_sub = $rs->getInt("inc_sub");

		global $connection;

		$stmt = &$connection->prepareStatement("SELECT * FROM " . tn_form_field_defs . " WHERE form_id=" . $this->id . " ORDER BY id");

		$rs = &$stmt->executeQuery();

		while($rs->next()) {
			$fieldDef = new FormFieldDef();
			$fieldDef->load($rs);
			$this->fieldDefs[$fieldDef->id] = &$fieldDef;
		}

		$stmt->close();
	}

	function& findById($id) {
		global $connection;

		$stmt = &$connection->prepareStatement("SELECT * FROM " . tn_form_defs . " WHERE id=" . $id . "");

		$rs = &$stmt->executeQuery();

		while($rs->next()) {
			$formDef = new FormDef();
			$formDef->load($rs);
		}

		$stmt->close();

		return $formDef;
	}

	function& findAll() {
		global $connection;

		$stmt = &$connection->prepareStatement("SELECT * FROM " . tn_form_defs . " ORDER BY id");

		$rs = &$stmt->executeQuery();

		$formDefs = array();
		while($rs->next()) {
			$formDef = new FormDef();
			$formDef->load($rs);
			$formDefs[] = &$formDef;
		}

		$stmt->close();

		return $formDefs;
	}

	function& create($name, $description_text, $success_text, $inc_sub) {
		$sql = "INSERT INTO " . tn_form_defs . " (id, name, description_text, success_text, inc_sub) VALUES(?,?,?,?,?)";
		$id = DBUtils::getNextSequnceID("form_defs");
		DBUtils::execUpdate($sql, array($id, $name, $description_text, $success_text, $inc_sub));
		$formDef = &FormDef::findById($id);
		return $formDef;
	}

	function store() {
		$sql = "UPDATE " . tn_form_defs . " SET name=?, description_text=?, success_text=?, inc_sub=? WHERE id=?";
		DBUtils::execUpdate($sql, array($this->name, $this->description_text, $this->success_text, $this->inc_sub, $this->id));
	}

	function remove() {
		$sql = "DELETE FROM " . tn_form_defs . " WHERE id=?";
		DBUtils::execUpdate($sql, array($this->id));
	}

	function& addFieldDef($name, $type_shortname, $required) {
		$sql = "INSERT INTO " . tn_form_field_defs . " (id, form_id, name, type_shortname, required) VALUES(?,?,?,?,?)";
		$id = DBUtils::getNextSequnceID("form_field_defs");
		DBUtils::execUpdate($sql, array($id, $this->id, $name, $type_shortname, $required));
	}

	function updateFieldDef($id, $name, $type_shortname, $required) {
		$sql = "UPDATE " . tn_form_field_defs . " SET name=?, type_shortname=?, required=? WHERE id=?";
		DBUtils::execUpdate($sql, array($name, $type_shortname, $required, $id));
	}

	function deleteFieldDef($id) {
		$sql = "DELETE FROM " . tn_form_field_defs . " WHERE id=?";
		DBUtils::execUpdate($sql, array($id));
	}
}

?>