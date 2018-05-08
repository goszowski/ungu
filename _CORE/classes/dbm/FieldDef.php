<?php

require_once ("dbm/FieldType.php");

class FieldDef {
	var $id = 0;
	var $nodeClassId = null;
	var $name = "";
	var $shortname = "";

	var $oldshortname = null;
	function setShortname($shortname) {
		if ($this->oldshortname == null && $shortname != $this->shortname) {
			$this->oldshortname = $this->shortname;
		}
		$this->shortname = $shortname;
	}

	var $fieldTypeId = null;

	var $oldfieldTypeId = null;
	function setFieldTypeId($fieldTypeId) {
		if ($this->oldfieldTypeId == null && $fieldTypeId != $this->fieldTypeId) {
			$this->oldfieldTypeId = $this->fieldTypeId;
		}
		$this->fieldTypeId = $fieldTypeId;
	}

	var $shown = false;
	var $required = false;
	var $order = 0;

	function& getNodeClass() {
		$nc = NodeClass::findById($this->nodeClassId);
		return $nc;
	}

	function& getFieldType() {
		global $DBM_FIELD_TYPES;
		return $DBM_FIELD_TYPES[$this->fieldTypeId];
	}

	function FieldDef(&$nodeClass, $name, $shortname, &$ft, $required, $shownInNodeList, $order, $id = -1) {
		$this->id = $id;
		$this->nodeClassId = $nodeClass->id;
		$this->name = $name;
		$this->shortname = $shortname;
		$this->fieldTypeId = $ft->id;
		$this->required = $required;
		$this->shown = $shownInNodeList;
		$this->order = $order;

		if ($id != -1) {
			$this->prepareFieldParamValues();
		}
	}

	/**
	 * prepare field parameter cache
	 */
	function prepareFieldParamValues() {
		global $connection;

		$sql = "SELECT shortname,_value FROM dbm_field_parameters WHERE field_id=?";
		$pstmt = $connection->prepareStatement($sql);
		$pstmt->setInt(1, $this->id);
		$rs = $pstmt->executeQuery();

		$this->fieldParameters = array();

		while ($rs->next()) {
			$this->fieldParameters[$rs->getString(1)] = $rs->getString(2);
		}

		$pstmt->close();
	}

    function& getFieldInstance() {
    	$fieldType = $this->getFieldType();
    	$field = $fieldType->getFieldInstance($this);
    	return $field;
    }

	var $fieldParameters = null;

	/**
	 * create entry in database
	 */
	function create() {
		$this->id = DBUtils::getNextSequnceID("class_fields");

		$params = array($this->id, $this->name, $this->shortname, $this->nodeClassId, $this->fieldTypeId, $this->required, $this->shown, $this->order);
		DBUtils::execUpdate(
			"INSERT INTO dbm_class_fields (id, name, shortname, class_id, type_id, required, shown, `order`) VALUES (?,?,?,?,?,?,?,?)",
			$params
		);

		$fieldType = $this->getFieldType();
		$parameters = $fieldType->parameterList;
		foreach ($parameters as $parameter) {
			$params = array($parameter->shortname, $this->id, $parameter->default_value);
			DBUtils::execUpdate(
				"INSERT INTO dbm_field_parameters (shortname,field_id, _value) VALUES (?,?,?)",
				$params
			);
		}
		$nodeClass = $this->getNodeClass();
		if ($nodeClass == null) {
			assert("FieldDef create failed: nodeClass == null");
		}

		$tableName = $nodeClass->getNFVTableName();

		$ft = $this->getFieldType();

		DBUtils::execUpdate("ALTER TABLE " . $tableName . " ADD COLUMN " . $this->shortname . " " . $ft->dbType);
		//DBUtils::execUpdate("ALTER TABLE " . $tableName . " ADD COLUMN " . $this->shortname . " " . $ft->dbType . " CHARACTER SET " . DB_CHARSET);

		$value = $this->getDefaultValue();
		DBUtils::execUpdate("UPDATE " . $tableName . " SET ".$this->shortname." = ?", array($value));
	}

	function& getDefaultValue() {
		$ft = $this->getFieldType();
		$value = call_user_func (array($ft->phpClass, "getDefaultValue"), $this);
		return $value;
	}

	/**
	 * set
	 */
	function setParameterValue($shortname, $value) {
		if ($this->fieldParameters[$shortname] !== null) {
			DBUtils::execUpdate("UPDATE dbm_field_parameters SET _value=? WHERE field_id=? AND shortname=?",
				array( $value, $this->id, $shortname ));
		} else {
			DBUtils::execUpdate("INSERT INTO dbm_field_parameters (field_id, shortname, _value) VALUES(?, ?, ?)",
				array( $this->id, $shortname, $value ));
		}

		$this->fieldParameters[$shortname] = $value;

		NodeClass::removeNodeClassFromCache($this->nodeClassId);
	}

	/**
	 * get
	 */
	function& getParameterValue($shortname) {
		if (!array_key_exists($shortname, $this->fieldParameters)) {
			$fieldType = $this->getFieldType();
			$param = $fieldType->getParameter($shortname);
			return $param->default_value;
		} else {
			return $this->fieldParameters[$shortname];
		}
	}

	/**
	 * update entry in database
	 */
	function store() {
		global $DBM_FIELD_TYPES;

		$sql = "UPDATE dbm_class_fields SET shortname=?, name=?, type_id=?, required=?, shown=?, `order`=? WHERE id=?";
		DBUtils::execUpdate($sql, array($this->shortname, $this->name, $this->fieldTypeId, $this->required, $this->shown, $this->order, $this->id));
		NodeClass::removeNodeClassFromCache($this->nodeClassId);

		if (($this->oldshortname != null) || ($this->oldfieldTypeId != null)) {
			$ft = $this->getFieldType();
			$nodeClass = $this->getNodeClass();
			$tableName = $nodeClass->getNFVTableName();
			if ($this->oldshortname == null) {
				$this->oldshortname = $this->shortname;
			}
			//DBUtils::execUpdate("ALTER TABLE " . $tableName . " CHANGE COLUMN " . $this->oldshortname . " " . $this->shortname . " " . $ft->dbType . " CHARACTER SET " . DB_CHARSET);
			DBUtils::execUpdate("ALTER TABLE " . $tableName . " CHANGE COLUMN " . $this->oldshortname . " " . $this->shortname . " " . $ft->dbType);
		}

		if ($this->oldfieldTypeId != null) {
			$ft = $this->getFieldType();
			$sql = "DELETE FROM dbm_field_parameters WHERE field_id=?";
			DBUtils::execUpdate($sql, array($this->id));

			$parameters = &$ft->parameterList;
			foreach ($parameters as $parameter) {
				$sql = "INSERT INTO dbm_field_parameters (shortname,field_id,_value) VALUES (?,?,?)";
				DBUtils::execUpdate($sql, array($parameter->shortname, $this->id, $parameter->default_value));
			}
		}

		$this->oldshortname = null;
		//clean nodes cache
		Node::cleanCache();
		NodeClass::removeNodeClassFromCache($this->nodeClassId);
	}

	/**
	 * Delete entry from database
	 */
	function remove() {
		DBUtils::execUpdate("DELETE FROM dbm_field_parameters WHERE field_id=" . $this->id);
		DBUtils::execUpdate("DELETE FROM dbm_class_fields WHERE id=" . $this->id);
		NodeClass::removeNodeClassFromCache($this->nodeClassId);

		$nodeClass = $this->getNodeClass();
		$tableName = $nodeClass->getNFVTableName();
		DBUtils::execUpdate("ALTER TABLE " . $tableName . " DROP COLUMN " . $this->shortname);
	}

	function moveUpInOrder() {
		$prevOrder = 0;
		$prevId = 0;

		$sql = "SELECT id, `order` FROM dbm_class_fields WHERE class_id=" .$this->nodeClassId ." AND `order` < " . $this->order . " ORDER by `order` DESC LIMIT 1";

		$rs = DBUtils::execSelect($sql);
		if ($rs->next()) {
			$prevId = $rs->getInt(1);
			$prevOrder = $rs->getInt(2);
		}

		if ($prevId != 0) {
			$params = array(
						array( -1, $this->id),
						array($this->order, $prevId),
						array($prevOrder, $this->id));
			$sql = "UPDATE dbm_class_fields SET `order`=? WHERE id=?";
			$batch = array( $sql, $sql, $sql);
			DBUtils::execBatch($batch, $params);
		}
		NodeClass::removeNodeClassFromCache($this->nodeClassId);
	}

	function moveDownInOrder() {
		$prevOrder = 0;
		$prevId = 0;

		$sql = "SELECT id, `order` FROM dbm_class_fields WHERE class_id=" . $this->nodeClassId . " AND `order` > " . $this->order . " ORDER by `order` LIMIT 1";

		$rs = DBUtils::execSelect($sql);
		if ($rs->next()) {
			$prevId = $rs->getInt(1);
			$prevOrder = $rs->getInt(2);
		}

		if ($prevId != 0) {
			$params = array(
						array(-1, $this->id),
						array($this->order, $prevId),
						array($prevOrder, $this->id));
			$sql = "UPDATE dbm_class_fields SET `order`=? WHERE id=?";
			$batch = array( $sql, $sql, $sql);
			DBUtils::execBatch($batch, $params);
		}
		NodeClass::removeNodeClassFromCache($this->nodeClassId);
	}
}

?>