<?php

require_once ("dbm/FieldDef.php");
require_once ("dbm/Node.php");

DEFINE("NODECLASS_FLAG_RU_CAN_DELETE", 1);
DEFINE("NODECLASS_FLAG_RU_CAN_CREATE", 2);
DEFINE("NODECLASS_FLAG_USE_SHORTNAME", 4);
DEFINE("NODECLASS_FLAG_USE_NODENAME", 8);
DEFINE("NODECLASS_FLAG_USE_MULTIFORMS", 16);
DEFINE("NODECLASS_FLAG_USE_ADMINURL_AND_TEMPLATE", 32);
DEFINE("NODECLASS_FLAG_NODENAME_READONLY", 64);
DEFINE("NODECLASS_FLAG_CACHE", 128);
DEFINE("NODECLASS_FLAG_NAME_FROM_SHORTNAME", 256);
DEFINE("NODECLASS_FLAG_NAME_FROM_SHORTNAME_WITH_ID", 512);

global $NODECLASS_FLAG_NAMES;
$NODECLASS_FLAG_NAMES = array(
	"RU_CAN_DELETE",
	"RU_CAN_CREATE",
	"USE_SHORTNAME",
	"USE_NODENAME",
	"NODENAME_READONLY",
	"USE_MULTIFORMS",
	"USE_ADMINURL_AND_TEMPLATE",
	"CACHE",
	"NAME_FROM_SHORTNAME",
	"NAME_FROM_SHORTNAME_WITH_ID"
);

class NodeClass {
	var $id = 0;
	var $name = "";
	var $shortname = "";

	var $oldshortname = null;
	function setShortname($shortname) {
		if ($this->oldshortname == null && $shortname != $this->shortname) {
			$this->oldshortname = $this->shortname;
		}
		$this->shortname = $shortname;
	}

	var $default_template = "";
	var $showAtAdminTree = false;
	var $nodeNameLabel = "";
	var $orderBy = "";
	var $flags = 0;

	var $fieldDefs = null;

	function checkFlag($flagName) {
		$constantValue = constant("NODECLASS_FLAG_" . strtoupper($flagName));
		return (($this->flags & $constantValue) != 0);
	}

	function setFlag($flagName, $value) {
		$constantValue = constant("NODECLASS_FLAG_" . strtoupper($flagName));
		if ($value) {
			$this->flags |= $constantValue;
		} else {
			$this->flags &= ~$constantValue;
		}
	}

	function prepareFieldDefs() {
		global $connection, $DBM_FIELD_TYPES;

		$this->fieldDefs = array();
		$this->fieldDefsById = array();

		$query = "SELECT * FROM dbm_class_fields WHERE class_id=" . $this->id . " ORDER BY `order`";

		$stmt = $connection->createStatement();
		$res = $stmt->executeQuery($query);

		while ($res->next()) {
			$id = $res->getInt("id");
			$name = $res->getString("name");
			$shortname = $res->getString("shortname");
			$required = $res->getBoolean("required");
			$order = $res->getInt("order");
			$shownInNodeList = $res->getBoolean("shown");

			$type_id = $res->getInt("type_id");
			$ft = $DBM_FIELD_TYPES[$type_id];
			if ($ft == null) {
				die("No such Field Type with id=$type_id");
			}

			$fd = new FieldDef($this, $name, $shortname, $ft, $required, $shownInNodeList, $order, $id);

			$this->fieldDefs[$fd->shortname] = $fd;
			$this->fieldDefsById[$fd->id] = $fd;
		}
		$stmt->close();
	}

	/**
	 * @return FieldDef
	 */
	function getFieldDef($shortname_or_id) {
		if (is_int($shortname_or_id)) {
			if (array_key_exists($shortname_or_id, $this->fieldDefsById)) {
				return $this->fieldDefsById[$shortname_or_id];
			} else {
				return null;
			}
		} else if (is_string($shortname_or_id)) {
			if (array_key_exists($shortname_or_id, $this->fieldDefs)) {
				return $this->fieldDefs[$shortname_or_id];
			} else {
				return null;
			}
		} else {
			die ("Illegal call to NodeClass.getFieldDef : key is '" . $shortname_or_id . "'");
		}
	}

	function updateFieldDef($id, $shortname, $name, $type, $required, $shown) {
		$fd = $this->getFieldDef($id);
		$fd->setShortname($shortname);
		$fd->name = $name;
		$fd->setFieldTypeId($type);
		$fd->required = $required;
		$fd->shown = $shown;
		$fd->store();
		NodeClass::removeNodeClassFromCache($this->id);
	}

	function delFieldDef($shortname) {
		if ($this->fieldDefs !== null) {
			$fd = &$this->fieldDefs[$shortname];
			$fd->remove();
		}
		NodeClass::removeNodeClassFromCache($this->id);
	}

	function& getFieldDefs() {
		return $this->fieldDefs;
	}

	function getFieldDefsCount() {
		return sizeof($this->fieldDefs);
	}

	function addFieldDef($shortname, $name, $type_id, $required, $shown) {
		global $DBM_FIELD_TYPES;
		$sql = null;

		//$ft = &FieldType::findTypeById($type_id);
		$ft = $DBM_FIELD_TYPES[$type_id];
		if ($ft == null) {
			die("No such Field Type with id=" . $type_id);
		}

		$order = $this->getLastFieldOrder() + 1;
		$fd = new FieldDef($this, $name, $shortname, $ft, $required, $shown, $order);
		$fd->create();

		NodeClass::removeNodeClassFromCache($this->id);
	}

	function getLastFieldOrder() {
		$sql = "SELECT MAX(`order`) FROM dbm_class_fields WHERE class_id=?";
		return DBUtils::execCountSelect($sql, array($this->id));
	}

	/*=============================================
	 * Some finder methods
	 =============================================*/

	public static function reBuildNodeClassCache() {
		global $_NODE_CLASS_CACHE_BY_ID, $_NODE_CLASS_CACHE_BY_SHORTNAME;

		$v = NodeClass::_findAll();
		$nodeClassCacheById = array();
		$nodeClassCacheByName = array();
		for ($i=0; $i<sizeof($v); $i++) {
			$nc = $v[$i];
			$nodeClassCacheById[$nc->id] = $nc;
			$nodeClassCacheByName[$nc->shortname] = $nc;
		}

		$_NODE_CLASS_CACHE_BY_ID = $nodeClassCacheById;
		$_NODE_CLASS_CACHE_BY_SHORTNAME = $nodeClassCacheByName;
	}

	public static function buildNodeClassCache() {
		global $_NODE_CLASS_CACHE_BY_ID, $_NODE_CLASS_CACHE_BY_SHORTNAME;

		_read_cache("_NODE_CLASS_CACHE_BY_ID");
		_read_cache("_NODE_CLASS_CACHE_BY_SHORTNAME");

		if ($_NODE_CLASS_CACHE_BY_ID === null || $_NODE_CLASS_CACHE_BY_SHORTNAME === null
				|| !is_array($_NODE_CLASS_CACHE_BY_ID) || !is_array($_NODE_CLASS_CACHE_BY_SHORTNAME)) {
			NodeClass::reBuildNodeClassCache();
		}
	}

	public static function removeNodeClassFromCache($nc_ID_or_SHORTNAME) {
		
		global $_NODE_CLASS_CACHE_BY_ID, $_NODE_CLASS_CACHE_BY_SHORTNAME;
		if (is_string($nc_ID_or_SHORTNAME)) {
			$nc = NodeClass::findByShortname($nc_ID_or_SHORTNAME);
		} else if (is_int($nc_ID_or_SHORTNAME)) {
			$nc = NodeClass::findById($nc_ID_or_SHORTNAME);
		} else {
			die("Illegal Call to removeNodeClassFromCache!");
		}
		array_remove($_NODE_CLASS_CACHE_BY_ID, $nc->id);
		array_remove($_NODE_CLASS_CACHE_BY_SHORTNAME, $nc->shortname);
		
		NodeClass::reBuildNodeClassCache();
	}

	function addDepend(&$dep_class) {
		global $connection;
		if ($dep_class == null)
			return;
		$sql = "INSERT INTO dbm_class_dependencies (class_id, submissed_class_id) VALUES (?,?)";
		$pstmt = $connection->prepareStatement($sql);
		$pstmt->setInt(1, $this->id);
		$pstmt->setInt(2, $dep_class->id);
		$pstmt->executeUpdate();
		$pstmt->close();

		//let's delete dependencies of ADDED class from node depends of nodes of THIS class
		$sql = "SELECT id FROM dbm_nodes WHERE class_id=?";
		$pstmt = $connection->prepareStatement($sql);
		$pstmt->setInt(1, $this->id);
		$rs = $pstmt->executeQuery();

		$ids = "";
		$first = true;
		while ($rs->next()) {
			if (!$first) {
				$ids .= ',';
			}
			$ids .= $rs->getString(1);
			$first = false;
		}
		$pstmt->close();
		
		if(strlen($ids) != 0) {
			$sql = "DELETE FROM dbm_node_dependencies WHERE submissed_class_id= ? and node_id IN (" . $ids . ")";
			$pstmt = $connection->prepareStatement($sql);
			$pstmt->setInt(1, $dep_class->id);
			$pstmt->executeUpdate();
		}
		$pstmt->close();
	}

	function removeDepend() {
		$c = $this->getDependentClasses();
		foreach ($c as $nc) {
			$this->removeDependentClass($nc);
		}
	}

	function removeDependentClass(&$nc) {
		global $connection;
		if ($nc == null)
			return;

		//find all nodes which have subnodes of removed class
		$pstmt = $connection->prepareStatement("SELECT DISTINCT n1.id, n2.parent_id FROM  dbm_nodes n1, dbm_nodes n2 WHERE n1.id=n2.parent_id AND n1.class_id=? AND n2.class_id=?");
		$pstmt->setInt(1, $this->id);
		$pstmt->setInt(2, $nc->id);
		$rs = $pstmt->executeQuery();

		$ids = array();
		while ($rs->next()) {
			$ids[] = $rs->getInt(1);
		}
		$pstmt->close();

		//add to all finided nodes dependency of nc
		$pstmt = $connection->prepareStatement("INSERT INTO dbm_node_dependencies (node_id, submissed_class_id) VALUES(?, ?)");
		for ($i = 0; $i < sizeof($ids); $i++) {
			$pstmt->setInt(1, $ids[$i]);
			$pstmt->setInt(2, $nc->id);
			$pstmt->executeUpdate();
		}
		$pstmt->close();

		//remove dependency from class
		$pstmt = $connection->prepareStatement("DELETE FROM dbm_class_dependencies WHERE class_id=? and submissed_class_id=?");
		$pstmt->setInt(1, $this->id);
		$pstmt->setInt(2, $nc->id);
		$pstmt->executeUpdate();
		$pstmt->close();
	}

	function& getDependentClasses() {
		global $connection;
		$coll = array();

		$query = "SELECT submissed_class_id FROM dbm_class_dependencies WHERE class_id=" . $this->id;
		$stmt = $connection->prepareStatement($query);
		$res = $stmt->executeQuery();

		while ($res->next()) {
			$nc = NodeClass::findById($res->getInt("submissed_class_id"));
			$coll[$nc->id] = $nc;
		}

		$stmt->close();
		return $coll;
	}

	function hasDependentClass($class_id) {
		global $connection;

		$query = " SELECT class_id FROM dbm_class_dependencies WHERE class_id=? AND submissed_class_id=?";
		$pstmt = $connection->prepareStatement($query);
		$pstmt->setInt(1, $this->id);
		$pstmt->setInt(2, $class_id);
		$res = $pstmt->executeQuery();

		if ($res->next()) {
			$pstmt->close();
			return true;
		}

		$pstmt->close();
		return false;
	}

	/*=========================================================================
	    operations for manipulations node classes list
	=========================================================================*/
	/**
	 * Find insance if NodeClass by id in cache
	 */
	public static function& findById($id) {
		global $_NODE_CLASS_CACHE_BY_ID, $_NODE_CLASS_CACHE_BY_SHORTNAME;

		$output = null;

		if (array_key_exists($id, $_NODE_CLASS_CACHE_BY_ID)) {
			return $_NODE_CLASS_CACHE_BY_ID[$id];
		} else {
			return $output;
		}
	}

	/**
	 * Find insance if NodeClass by shortname in cache
	 * @return NodeClass
	 */
	public static function& findByShortname($shortname) {
		global $_NODE_CLASS_CACHE_BY_ID, $_NODE_CLASS_CACHE_BY_SHORTNAME;
		if (array_key_exists($shortname, $_NODE_CLASS_CACHE_BY_SHORTNAME)) {
			return $_NODE_CLASS_CACHE_BY_SHORTNAME[$shortname];
		} else {
			return null;
		}
	}

	public static function& findAll($orderby="id") {
		return NodeClass::findAll_ordered($orderby);
	}

	public static function& findAll_ordered($sortBy) {
		global $_NODE_CLASS_CACHE_BY_ID, $_NODE_CLASS_CACHE_BY_SHORTNAME;

		switch ($sortBy) {
			case "name":
				$res = array_values($_NODE_CLASS_CACHE_BY_SHORTNAME);
				usort($res, "_nodeClassNameSort");
				return $res;
			case "shortname":
				ksort($_NODE_CLASS_CACHE_BY_SHORTNAME);
				return $_NODE_CLASS_CACHE_BY_SHORTNAME;
			default:
				$nodeClassCacheById = $_NODE_CLASS_CACHE_BY_ID;
				ksort($nodeClassCacheById);
				return $nodeClassCacheById;
		}
	}

	public static function& findAllShownAtAdminTree() {
		global $_NODE_CLASS_CACHE_BY_ID, $_NODE_CLASS_CACHE_BY_SHORTNAME;
		$c = array();
		foreach ($_NODE_CLASS_CACHE_BY_SHORTNAME as $nc) {
			if ($nc->showAtAdminTree) {
				$c[] = $nc;
			}
		}
		return $c;
	}

	/**
	 * Create new instance of NodeClass. Both in database and object cache
	 */
	function create() {
		global $_NODE_CLASS_CACHE_BY_ID, $_NODE_CLASS_CACHE_BY_SHORTNAME;
		$this->_create();
		NodeClass::reBuildNodeClassCache();
	}

	/**
	 * Create new instance of NodeClass. Both in database and object cache
	 */
	function remove() {
		$this->_remove();

		NodeClass::reBuildNodeClassCache();
	}

	/**
	 * Find all in database and return as vector
	 */
	public static function _findAll() {
		global $connection;
		$result = array();

		$query = "SELECT * FROM dbm_classes";
		$stmt = $connection->createStatement();
		$res = $stmt->executeQuery($query);

		while ($res->next()) {
			$nc = new NodeClass();
			$nc->id = $res->getInt("id");
			$nc->prepareFieldDefs();
			$nc->name = $res->getString("name");
			$nc->shortname = $res->getString("shortname");
			$nc->default_template = $res->getString("default_template");
			$nc->showAtAdminTree = $res->getBoolean("showAtAdminTree");
			$nc->nodeNameLabel = $res->getString("nodename_label");
			$nc->orderBy = $res->getString("orderby");
			$nc->flags = $res->getString("flags");
			$result[] = $nc;
		}
		$stmt->close();
		return $result;
	}

	/**
	 * create entry in database
	 */
	function _create() {
		global $connection;
		$this->id = DBUtils::getNextSequnceID("classes");
		$sql = "INSERT INTO dbm_classes (id, name, shortname, default_template, showAtAdminTree, nodename_label, orderby, flags) VALUES (?,?,?,?,?,?,?,?)";
		$pstmt = $connection->prepareStatement($sql);
		$pstmt->setInt(1, $this->id);
		$pstmt->setString(2, $this->name);
		$pstmt->setString(3, $this->shortname);
		$pstmt->setString(4, $this->default_template);
		$pstmt->setBoolean(5, $this->showAtAdminTree);
		$pstmt->setString(6, $this->nodeNameLabel);
		$pstmt->setString(7, $this->orderBy);
		$pstmt->setInt(8, $this->flags);

		$pstmt->executeUpdate();
		$pstmt->close();

		$tableName = $this->getNFVTableName();
		DBUtils::execUpdate("DROP TABLE IF EXISTS " . $tableName);
		DBUtils::execUpdate("CREATE TABLE " . $tableName . " (node_id INTEGER NOT NULL REFERENCES dbm_nodes(id)) CHARACTER SET " . DB_CHARSET);
		DBUtils::execUpdate("ALTER TABLE " . $tableName . " ADD INDEX (node_id)");
	}

	/**
	 * update entry in database
	 */
	function store() {
		global $connection;
		$sql = "UPDATE dbm_classes SET id=?, name=?, shortname=?, default_template=?, showAtAdminTree=?, nodename_label=?, orderby=?, flags=? WHERE id=?";
		$pstmt = &$connection->prepareStatement($sql);
		$pstmt->setInt(1, $this->id);
		$pstmt->setString(2, $this->name);
		$pstmt->setString(3, $this->shortname);
		$pstmt->setString(4, $this->default_template);
		$pstmt->setBoolean(5, $this->showAtAdminTree);
		$pstmt->setString(6, $this->nodeNameLabel);
		$pstmt->setString(7, $this->orderBy);
		$pstmt->setInt(8, $this->flags);
		$pstmt->setInt(9, $this->id);

		$pstmt->executeUpdate();
		$pstmt->close();

		NodeClass::removeNodeClassFromCache($this->id);

		if ($this->oldshortname != null) {
			$oldTableName = $this->getNFVTableName($this->oldshortname);
			$newTableName = $this->getNFVTableName($this->shortname);
			
			DBUtils::execUpdate("ALTER TABLE " . $oldTableName . " RENAME TO " . $newTableName);
		}

	}

	/**
	 * Delete entry from database
	 */
	function _remove() {
		global $connection;
		$sql = "DELETE FROM dbm_class_dependencies WHERE submissed_class_id=?";
		$pstmt = $connection->prepareStatement($sql);
		$pstmt->setInt(1, $this->id);
		$pstmt->executeUpdate();

		$pstmt->close();
		$sql = "DELETE FROM dbm_node_dependencies WHERE submissed_class_id=?";
		$pstmt = $connection->prepareStatement($sql);
		$pstmt->setInt(1, $this->id);
		$pstmt->executeUpdate();

		foreach ($this->fieldDefs as $fdef) {
			$fdef->remove();
		}

		$pstmt->close();
		$sql = "DELETE FROM dbm_class_dependencies WHERE class_id=?";
		$pstmt = $connection->prepareStatement($sql);
		$spstmt = $connection->prepareStatement("SELECT id FROM dbm_nodes WHERE class_id=?");
		$pstmt->setInt(1, $this->id);
		$spstmt->setInt(1, $this->id);
		$pstmt->executeUpdate();
		$rs = $spstmt->executeQuery();
		while ($rs->next()) {
			Node::remove($rs->getInt(1));
		}

		$spstmt->close();
		$pstmt->close();
		$sql = "DELETE FROM dbm_classes WHERE id=?";
		$pstmt = $connection->prepareStatement($sql);
		$pstmt->setInt(1, $this->id);
		$pstmt->executeUpdate();
		$pstmt->close();

		DBUtils::execUpdate("DROP TABLE IF EXISTS " . $this->getNFVTableName());
	}

	function getNFVTableName($shortname=null) {
		if ($shortname == null) {
			$shortname = $this->shortname;
		}

		return ("dbm_nfv_" . $shortname);
	}
}

function _nodeClassNameSort($a, $b) {
	return strcmp($a->name, $b->name);
}

?>