<?php

require_once ("dbm/NodeGroupRights.php");
require_once ("dbm/QL.php");

global $NULL;
$NULL = null;

class Node {
	var $id = 0;
	var $oldshortname = null;
	var $shortname = null;

	var $name = null;
	var $subtreeOrder = 0;
	var $dynamicTemplate = null;
	var $absolutePath = null;
	var $adminUrl = null;

	var $nodeClassId = null;
	var $parent_id = null;

	var $parent = null;

	var $fields = null;

	var $owner = null;

	var $timeCreated = null;
	var $timeUpdated = null;

	function setShortname($shortname) {
		if ($this->oldshortname == null && $shortname != $this->shortname) {
			$this->oldshortname = $this->shortname;
		}
		$this->shortname = $shortname;
	}

	function isRoot() {
		return ($this->parent_id == null);
	}

	function getHref() {
		//return /*"/index.php" . */$this->absolutePath;
		if ($this->absolutePath!="/" && strlen($this->absolutePath)>3)
			//return prepareHref($this->absolutePath) . ".html";
			return prepareHref($this->absolutePath) . "/";
		else {
			return $this->absolutePath;
		}
	}

	function& getNodeClass() {
		$nc = NodeClass::findById($this->nodeClassId);
		return $nc;
	}

	function Node() {
	}


	function init(&$nodeRS, &$fieldsRS, $tableAlias = "") {
		if ($tableAlias != "") {
			$fieldNamesPrefix = $tableAlias . "___";
		} else {
			$fieldNamesPrefix = "";
		}
		$this->id = $nodeRS->getInt($fieldNamesPrefix . "id");
		$this->absolutePath = $nodeRS->getString($fieldNamesPrefix . "absolute_path");
		$this->adminUrl = $nodeRS->getString($fieldNamesPrefix . "admin_url");
		$this->adminUrl = str_replace("{id}", $this->id, $this->adminUrl);
		$this->dynamicTemplate = $nodeRS->getString($fieldNamesPrefix . "dynamic_template");
		$this->name = $nodeRS->getString($fieldNamesPrefix . "name");

		$nodeClass = NodeClass::findById($nodeRS->getInt($fieldNamesPrefix . "class_id"));

		if ($nodeClass == null) {
			die("Error : class_id in database table for nodes represents non-existent NodeClass object. " . $fieldNamesPrefix . "class_id");
		}

		$this->nodeClassId = $nodeClass->id;
		$this->owner = User::findById($nodeRS->getInt($fieldNamesPrefix . "owner"));
		$this->shortname = $nodeRS->getString($fieldNamesPrefix . "shortname");
		$this->subtreeOrder = $nodeRS->getInt($fieldNamesPrefix . "subtree_order");
		$this->parent_id = $nodeRS->getObject($fieldNamesPrefix . "parent_id");
		$this->timeCreated = $nodeRS->getDate($fieldNamesPrefix . "time_created");
		$this->timeUpdated = $nodeRS->getDate($fieldNamesPrefix . "time_updated");

		$this->fields = array();
		$this->tfields = array();

		$fieldDefs = $nodeClass->getFieldDefs();

		$fieldShortnames = array_keys($fieldDefs);

		foreach ($fieldShortnames as $shortname) {
			$fieldDef = $fieldDefs[$shortname];
			//$fieldType = $fieldDef->getFieldType();
			$field = $fieldDef->getFieldInstance();
			$field->nodeId = $this->id;
			//$falias = $fieldNamesPrefix . $shortname;
			//$rsGetMethod = "get" . $fieldType->rsType;
			//$dbValue = $fieldsRS->$rsGetMethod($falias);
			$field->init($fieldsRS, $tableAlias, $shortname);
			$this->fields[$shortname] = $field;
			$this->tfields[$shortname] = $field->getValueForTemplate();
		}
		ksort($this->fields);
		ksort($this->tfields);
	}

	public static function _create($parent, $name, $shortname, $dynamic_template, $admin_url, $owner_id, $class_id, $preparedFields, $predefinedID = -1) {
		$new_id = -1;
		DBUtils::startTransaction();
		$new_id = $predefinedID == -1 ? DBUtils::getNextSequnceID("nodes") : $predefinedID;
		$lastInThisSubtree = $parent->getLastSubtreeOrder();
		$subtree_order = $lastInThisSubtree + 1;

		if (($parent != null) && ($shortname == "")) {
			$shortname = (string)$new_id;
		}
		if ($parent == null) {
			$shortname = "";
		}

		$create_params = array( $new_id, $name, $shortname, $dynamic_template, $class_id, ($parent != null) ? $parent->id : null, $owner_id, $admin_url, null, null, new Date(), new Date());

		if ($parent != null) {
			$create_params[8] = $subtree_order;
			$prependStr = $parent->isRoot() ? "" : $parent->absolutePath;
			$create_params[9] = $prependStr . "/" . $shortname;
		} else {
			$create_params[8] = 1;
			$create_params[9] = "/";
		}

		$create_sql = "INSERT INTO dbm_nodes (id, name, shortname, dynamic_template, class_id, parent_id, owner, admin_url, subtree_order, absolute_path, time_created, time_updated) VALUES (?,?,?,?,?,?,?,?,?,?,?,?)";
		DBUtils::execUpdate($create_sql, $create_params);

		$nodeClass = NodeClass::findById($class_id);
		$owner = User::findById($owner_id);
		if ($owner == null) {
			DBUtils::rollback();
			die("Create new node failed: bad owner id.");
		}

		$update_params = array( null, $new_id, null );
		$update_sql = "INSERT INTO dbm_group_rights (group_id, node_id, rights) VALUES(?, ?, ?)";
		if ($parent != null) {
			//copy all rights from parent
			$rs = DBUtils::execSelect("SELECT group_id, rights FROM dbm_group_rights WHERE node_id=" . $parent->id);

			while ($rs->next()) {
				$update_params[0] = $rs->getInt(1);
				$update_params[2] = $rs->getInt(2);
				DBUtils::execUpdate($update_sql, $update_params);
			}
		} else {
			//give rights to owner
			$update_params[0] = $owner->group->id;
			$update_params[2] = VIEW_RIGHT_MASK | WRITE_RIGHT_MASK;
			DBUtils::execUpdate($update_sql, $update_params);

			//ant not give to others
			$rs = DBUtils::execSelect("SELECT id FROM dbm_user_groups WHERE id!=" . $owner->id);
			$update_params[2] = 0;
			while ($rs->next()) {
				$update_params[0] = $rs->getInt(1);
				DBUtils::execUpdate($update_sql, $update_params);
			}
		}

		if ($parent != null) {
			//not let's make changes in submission table
			$update_sql = "INSERT INTO dbm_nodes_submission(child_id, parent_id, level_diff) VALUES(?, ?, ?)";
			$update_params[0] = $new_id;

			$rs = DBUtils::execSelect("SELECT parent_id as parent_id, level_diff+1 as level_diff FROM dbm_nodes_submission WHERE child_id=" . $parent->id);
			while ($rs->next()) {
				$update_params[1] = $rs->getInt(1);
				$update_params[2] = $rs->getInt(2);
				DBUtils::execUpdate($update_sql, $update_params);
			}

			$update_sql = "INSERT INTO dbm_nodes_submission (child_id, parent_id, level_diff) VALUES(?,?,?)";
			$update_params[0] = $new_id;
			$update_params[1] = $parent->id;
			$update_params[2] = 1;
			DBUtils::execUpdate($update_sql, $update_params);
		}

		$fieldDefs = $nodeClass->getFieldDefs();

		$fieldShortnames = array_keys($fieldDefs);

		//now store dynamic field values
		$fv_sql = "INSERT INTO " . $nodeClass->getNFVTableName() . " (node_id" . ((sizeof($fieldShortnames) > 0) ? "," : "") . implode(",", $fieldShortnames) . ") VALUES(?" . str_repeat(",?", sizeof($fieldShortnames)) . ")";
		$fv_params = array($new_id);
		$flag_double_vars = false;//kazancev
		foreach ($fieldShortnames as $shortname) {
			if ($preparedFields == null) {
				$fieldDef = &$fieldDefs[$shortname];
				$field = $fieldDef->getFieldInstance();
				$field->setDefaultValue();
			} else {
				$field = &$preparedFields[$shortname];
			}

			$field->nodeId = $new_id;
			$field->create();
			$fv_params[] = $field->value;
			
			//kazancev - start
			if (in_array($shortname, array("parent_id", "name", "absolute_path"))) {
				$flag_double_vars = true;
			}
			//kazancev - end
			
		}
		DBUtils::execUpdate($fv_sql, $fv_params);

		DBUtils::commit();
		
		//kazancev - start
		if ($flag_double_vars) {
			$tmp_node = Node::findById($new_id);
			$tmp_node->store();
		}
		//kazancev - end

		return $new_id;
	}

	function _store() {
		DBUtils::startTransaction();
		$nodeClass = $this->getNodeClass();
		$this->getParent();
		if ($this->parent != null) {
			$prependStr = ($this->parent->isRoot()) ? "" : $this->parent->absolutePath;
			$this->absolutePath = $prependStr . "/" . $this->shortname;
		} else {
			$this->absolutePath = "/";
		}

		DBUtils::execUpdate(
			"UPDATE dbm_nodes SET name=?, shortname=?, dynamic_template=?, admin_url=?, absolute_path=?, time_updated=? WHERE id=?",
			array( $this->name, ($this->parent != null) ? $this->shortname : "", $this->dynamicTemplate, $this->adminUrl, $this->absolutePath, new Date(), $this->id)
		);

		//if shotname was changed - update all children's paths
		if (($this->oldshortname != null) && ($this->parent_id != null)) {
			$sql = "UPDATE dbm_nodes SET absolute_path = CONCAT(?, '/', ?, substring(absolute_path from ?)) WHERE id=?";

			$parentPreStr = "";
			$addN = 2 + strlen($this->oldshortname);
			if (!$this->parent->isRoot()) {
				$parentPreStr = $this->parent->absolutePath;
				$addN += strlen($parentPreStr);
			}
			$params = array( $parentPreStr, $this->shortname, $addN, null );

			$rs = DBUtils::execSelect("SELECT child_id FROM dbm_nodes_submission WHERE parent_id=" . $this->id);
			while ($rs->next()) {
				$params[3] = $rs->getInt(1);
				DBUtils::execUpdate($sql, $params);
			}

			$this->oldshortname = null;
		}

		$fieldDefs = $nodeClass->getFieldDefs();

		$fieldShortnames = array_keys($fieldDefs);

		//now store dynamic field values
		$fv_params = array();
		$fv_sql_fieldParts = array();
		foreach ($fieldShortnames as $shortname) {
			$field = $this->fields[$shortname];
			$field->update();
			//kazancev - start
			if (in_array($shortname, array("parent_id", "name", "absolute_path"))) {
				if ($shortname=="absolute_path") {
					$fv_params[] = $this->absolutePath;
				} else {
					//_dump($shortname);
					//_dump($this->$shortname);
					$fv_params[] = $this->$shortname;
				}
			} else {
				$fv_params[] = $field->value;
			}
			//kazancev - end
			//original $fv_params[] = $field->value;
			$fv_sql_fieldParts[] = $shortname . "=?";
		}
		$fv_params[] = $this->id;

		if (sizeof($fv_sql_fieldParts) > 0) {
			$fv_sql = "UPDATE " . $nodeClass->getNFVTableName() . " SET " . implode(", ", $fv_sql_fieldParts) . " WHERE node_id=?";
			DBUtils::execUpdate($fv_sql, $fv_params);
		}

		DBUtils::commit();
	}

	public static function remove($id) {
		//delete recursively all subnodes
		$node = Node::findById($id);
		$children = $node->getChildren();
		foreach ($children as $child) {
			Node::remove($child->id);
		}
		foreach ($node->fields as $field) {
			$field->remove();
		}

		//delete rows from database
		$nodeClass = $node->getNodeClass();
		$sqls =
			array(
				"DELETE FROM " . $nodeClass->getNFVTableName() . " WHERE node_id=?",
				"DELETE FROM dbm_group_rights WHERE node_id=?",
				"DELETE FROM dbm_node_dependencies WHERE node_id=?",
				"DELETE FROM dbm_nodes_submission WHERE parent_id=?",
				"DELETE FROM dbm_nodes_submission WHERE child_id=?",
				"DELETE FROM dbm_nodes WHERE id=?" );
		$params = array(
				array($id),
				array($id),
				array($id),
				array($id),
				array($id),
				array($id)
		);

		DBUtils::execBatch($sqls, $params);

		Node::removeNodeFromCache($id);
	}

	public static function removeNodeFromCache($nc_ID_or_PATH) {
		global $_NODES_CACHE_BY_ID, $_NODES_CACHE_BY_PATH;

		if (is_string($nc_ID_or_PATH)) {
			$n = &Node::findByPath($nc_ID_or_PATH);
		} else if (is_int($nc_ID_or_PATH)) {
			$n = &Node::findById($nc_ID_or_PATH);
		} else {
			die("Illegal Call to removeNodeClassFromCache!");
		}
		$absolutePath = $n->absolutePath;

		array_remove($_NODES_CACHE_BY_ID, $n->id);
		array_remove($_NODES_CACHE_BY_PATH, $absolutePath);
	}

	public static function& createWithDefaultValues(&$parent, $name, $shortname, $dynamic_template, $admin_url, $owner_id, $class_id) {
		global $NULL;
		if (($parent == null) && Node::hasRoot()) {
			return null;
		}
		$id = Node::_create($parent, $name, $shortname, $dynamic_template, $admin_url, $owner_id, $class_id, $NULL);
		$node = &Node::findById($id);
		return $node;
	}

	public static function& createWithDefaultValues_rnull(&$parent, $name, $shortname, $dynamic_template, $admin_url, $owner_id, $class_id) {
		global $NULL;
		if (($parent == null) && Node::hasRoot()) {
			return null;
		}
		$id = Node::_create($parent, $name, $shortname, $dynamic_template, $admin_url, $owner_id, $class_id, $NULL);
		$node = &Node::findById($id);
		return $node;
	}

	public static function& createWithDefaultValues0($parent_path, $name, $shortname, $class_shortname) {
		global $NULL;
		$parent = &Node::findByPath($parent_path);
		if (($parent == null) && Node::hasRoot()) {
			return null;
		}
		$class = &NodeClass::findByShortname($class_shortname);
		$id = Node::_create($parent, $name, $shortname, $class->default_template, "", 1, $class->id, $NULL);
		$node = &Node::findById($id);
		return $node;
	}

	public static function& createWithDefaultValues0AndID($id, $parent_path, $name, $shortname, $class_shortname) {
		global $NULL;
		$parent = &Node::findByPath($parent_path);
		if (($parent == null) && Node::hasRoot()) {
			return null;
		}
		$class = NodeClass::findByShortname($class_shortname);
		$id = Node::_create($parent, $name, $shortname, "", "", 1, $class->id, $NULL, $id);
		$res = Node::findById($id);
		return $res;
	}

	public static function& createWithPreparedValues(&$parent, $name, $shortname, $dynamic_template, $admin_url, $owner_id, $class_id, &$fields) {
		if (($parent == null) && Node::hasRoot()) {
			return null;
		}
		$id = Node::_create($parent, $name, $shortname, $dynamic_template, $admin_url, $owner_id, $class_id, $fields);
		$node = Node::findById($id);

		return $node;
	}

	public static function hasRoot() {
		$count = DBUtils::execCountSelect("SELECT COUNT(id) FROM dbm_nodes WHERE parent_id IS NULL");

		if ($count > 0) {
			return true;
		} else {
			return false;
		}
	}

	function store() {
		$this->_store();
		Node::removeNodeFromCache((int)$this->id);
	}

	public static function storeAll() {
		die("Deprecated method Node::storeAll()");
	}

	function getParent() {
		if ($this->parent == null) {
			if ($this->parent_id != null) {
				$this->parent = Node::findById($this->parent_id);
			}
		}

		return $this->parent;
	}

	function hasChildren($shortname) {
		$sql = "SELECT COUNT(id) FROM dbm_nodes WHERE parent_id=? AND shortname=?";

		$childrenCount = DBUtils::execCountSelect($sql, array( $this->id, $shortname ));

		if ($childrenCount > 0) {
			return true;
		} else {
			return false;
		}
	}

	function hasChildrenOfClasses($classes, $maxdepth=100) {
		$sql = "SELECT COUNT(n.id) FROM dbm_nodes n, dbm_nodes_submission ns WHERE ns.parent_id=? AND n.id=ns.child_id AND ns.level_diff <= ? AND n.class_id IN (" . implode(',', $classes) . ")";
		$childrenCount = DBUtils::execCountSelect($sql, array( $this->id, $maxdepth));

		if ($childrenCount > 0) {
			return true;
		} else {
			return false;
		}
	}

	function isSubNodeOf($node) {
		if (is_string($node) ) {
			$node = Node::findByPath($node);
			$node_id = $node->id;
		} elseif (is_integer($node)) {
			$node_id = $node;
		} else {
			$node_id = $node->id;
		}

		$sql = "SELECT COUNT(*) FROM dbm_nodes_submission WHERE parent_id=? AND child_id=?";

		$count = DBUtils::execCountSelect($sql, array($node_id, $this->id));

		if ($count > 0) {
			return true;
		} else {
			return false;
		}
	}

	function isSubNodeOrIsIt($node) {
		if (is_string($node) ) {
			$node = Node::findByPath($node);
			$node_id = $node->id;
		} elseif (is_integer($node)) {
			$node_id = $node;
		} else {
			$node_id = $node->id;
		}

		if ($this->id == $node_id)
			return true;

		$sql = "SELECT COUNT(*) FROM dbm_nodes_submission WHERE parent_id=? AND child_id=?";

		$count = DBUtils::execCountSelect($sql, array($node_id, $this->id));

		if ($count > 0) {
			return true;
		} else {
			return false;
		}
	}

	function setRightsToChildren($group, $rights) {
		$sql = "REPLACE INTO dbm_group_rights (rights,group_id,node_id) SELECT ? as rights, ? as group_id, ds.child_id as node_id FROM dbm_nodes_submission ds WHERE ds.parent_id=?";
		DBUtils::execUpdate($sql, array($rights, $group->id, $this->id));
	}

	function getLastSubtreeOrder() {
		$sql = "SELECT MAX(subtree_order) FROM dbm_nodes WHERE parent_id=?";
		return DBUtils::execCountSelect($sql, array( $this->id));
	}

	function& getField($shortname) {
		return $this->fields[$shortname];
	}

	function& getFields() {
		return $this->fields;
	}

	function addDependentClass(&$dep_class) {
		if ($dep_class == null)
			return;
		$nodeClass = $this->getNodeClass();
		if ($nodeClass->hasDependentClass($dep_class->id)) {
			die("Can't add dependent class to a node: specified class already exists in nodeClass deps.");
		}
		$sql = "INSERT INTO dbm_node_dependencies (node_id, submissed_class_id) VALUES (?,?)";
		DBUtils::execUpdate($sql, array( $this->id, $dep_class->id));
	}

	function removeDepend(&$nc) {
		if ($nc == null)
			return;
		$sql = "DELETE FROM dbm_node_dependencies WHERE node_id=? and submissed_class_id=?";
		DBUtils::execUpdate($sql, array( $this->id, $nc->id));
	}

	function& findDepends() {
		$coll = array();
		$query = "SELECT submissed_class_id FROM dbm_node_dependencies WHERE node_id=" . $this->id;
		$rs = DBUtils::execSelect($query);

		while ($rs->next()) {
			$nc = NodeClass::findById($rs->getInt("submissed_class_id"));
			$coll[$nc->id] = $nc;
		}
		return $coll;
	}

	function& findAllDepends() {
		$nodeClass = $this->getNodeClass();
		$coll = $nodeClass->getDependentClasses();
		$query = " SELECT submissed_class_id FROM dbm_node_dependencies WHERE node_id=" . $this->id;
		$rs = DBUtils::execSelect($query);

		while ($rs->next()) {
			$nc = NodeClass::findById($rs->getInt("submissed_class_id"));
			$coll[$nc->id] = $nc;
		}
		return $coll;
	}

	/**
	 * @return Node
	 */
	public static function& findById($id) {
		global $_NODES_CACHE_BY_ID, $_NODES_CACHE_BY_PATH, $_USE_NODES_CACHE_;

		$id = (int)$id;

		if (!$_USE_NODES_CACHE_ || !array_key_exists($id, $_NODES_CACHE_BY_ID)) {
			$params = array($id);

			$nrs = DBUtils::execSelect("SELECT * FROM dbm_nodes WHERE id=?", $params);



			$node = null;

			if ($nrs->next()) {
				$ncID = $nrs->getInt("class_id");
				$nc = NodeClass::findById($ncID);
				if($nc)
				{
					$frs = DBUtils::execSelect("SELECT * FROM " . $nc->getNFVTableName() . " WHERE node_id=?", $params);
					$node = new Node();
					$frs->next();
					$node->init($nrs, $frs);

					if ($_USE_NODES_CACHE_ && $nc->checkFlag("CACHE")) {
						$_NODES_CACHE_BY_ID[$id] = $node;
						$_NODES_CACHE_BY_PATH[$node->absolutePath] = $node;
					}
				}
				
			}

			return $node;
		} else {
			return $_NODES_CACHE_BY_ID[$id];
		}
	}

	public static function findAbsolutePathById($id) {
		global $_NODES_CACHE_BY_ID, $_NODES_CACHE_BY_PATH, $_USE_NODES_CACHE_;

		$id = (int)$id;
		if (!$_USE_NODES_CACHE_ || !array_key_exists($id, $_NODES_CACHE_BY_ID)) {
			$params = array($id);
			$nrs = &DBUtils::execSelect("SELECT absolute_path FROM dbm_nodes WHERE id=?", $params);

			$node = null;

			if ($nrs->next()) {
				return $nrs->getString(1);
			} else {
				return null;
			}
		} else {
			return $_NODES_CACHE_BY_ID[$id]->absolutePath;
		}
	}

	/**
	 * @return Node
	 */
	public static function findRoot() {
		$nrs = DBUtils::execSelect("SELECT * FROM dbm_nodes WHERE parent_id IS NULL");

		$node = null;
		if ($nrs->next()) {
			$nodeID = $nrs->getInt("class_id");
			$ncID = $nrs->getInt("class_id");
			$nc = NodeClass::findById($ncID);
			$frs = DBUtils::execSelect("SELECT * FROM " . $nc->getNFVTableName() . " WHERE node_id=" . $nodeID);
			$node = new Node();
			$frs->next();
			$node->init($nrs, $frs);
		}

		return $node;
	}

	/**
	 * @return Node
	 */
	public static function& findByPath($path) {
		global $_NODES_CACHE_BY_ID, $_NODES_CACHE_BY_PATH, $_USE_NODES_CACHE_;

		if (!is_string($path)) {
			_dump($path);
			try {
				throw new Exception();
			} catch(Exception $e) {
				_dump($e);
			}
			die("Invalid call to Node::findByPath()");
		}

		if (!$_USE_NODES_CACHE_ || !array_key_exists($path, $_NODES_CACHE_BY_PATH)) {
			$params = array( $path );
			$nrs = &DBUtils::execSelect("SELECT * FROM dbm_nodes WHERE absolute_path=?", $params);
			$node = null;
			if ($nrs->next()) {
				$nodeID = $nrs->getInt("id");
				$ncID = $nrs->getInt("class_id");
				$nc = NodeClass::findById($ncID);
				if($nc)
				{
					$frs = &DBUtils::execSelect("SELECT * FROM " . $nc->getNFVTableName() . " WHERE node_id=" . $nodeID);
					$node = new Node();
					$frs->next();
					$node->init($nrs, $frs);

					if ($_USE_NODES_CACHE_ && $nc->checkFlag("CACHE")) {
						$_NODES_CACHE_BY_PATH[$path] = &$node;
						$_NODES_CACHE_BY_ID[$node->id] = &$node;
					}
				}
			}

			return $node;
		} else {
			return $_NODES_CACHE_BY_PATH[$path];
		}
	}

	public static function& selectNodesNSQLOnly($sql, $params) {
		global $connection;

		$nodes = array();

		$pstmt = &$connection->prepareStatement($sql);

		foreach ($params as $i=>$p) {
			$pstmt->setObject($i + 1, $p);
		}

		$nrs = &DBUtils::execSelect($sql, $params);

		$nodes = array();

		while($nrs->next()) {
			$nodeID = $nrs->getInt("id");
			$ncID = $nrs->getInt("class_id");

			$nc = NodeClass::findById($ncID);

			if ( /*$nc->checkFlag("CACHE")*/ true) {
				$node = & Node::findById($nodeID);
			} else {
				$frs = &DBUtils::execSelect("SELECT * FROM " . $nc->getNFVTableName() . " WHERE node_id=" . $nodeID);

				$node = new Node();

				$frs->next();

				$node->init($nrs, $frs);
			}

			$nodes[] = &$node;
		}

		return $nodes;
	}

	public static function& findByQuery($dbmQL, $params = array(), $offset=-1, $limit=-1, $groupId=-1) {
		$query = new NodeQuery($dbmQL, $groupId);
		$query->execute($params, $offset, $limit);
		$nodes = $query->getNodes("n");
		return $nodes;
	}

	public static function findCountByQuery($dbmQL, $params = array(), $groupId=-1) {
		$query = new NodeQuery($dbmQL, $groupId);
		$query->executeCount($params);
		$count = $query->getCount();
		return $count;
	}

	function getChildrenCount() {
		$sql = "SELECT COUNT(*) FROM dbm_nodes WHERE parent_id=?";
		$params = array($this->id);

		$cc = DBUtils::execCountSelect($sql, $params);
		return $cc;
	}

	function getChildrenCountForGroup($group_id) {
		$sql = "SELECT COUNT(*) FROM dbm_nodes n, dbm_group_rights gr WHERE n.parent_id=? AND gr.group_id=? AND gr.node_id=n.id AND gr.rights&" . VIEW_RIGHT_MASK . "!=0";
		$params = array($this->id, $group_id);

		$cc = DBUtils::execCountSelect($sql, $params);
		return $cc;
	}

	function& getChildrenIds() {
		$sql = "SELECT id FROM dbm_nodes WHERE parent_id=" . $this->id;

		$rs = & DBUtils::execSelect($sql);
		$ids = array();
		while($rs->next()) {
			$ids[] = $rs->getInt("id");
		}
		return $ids;
	}

	function& getChildren($orderBy="n.subtree_order", $offset=-1, $limit=-1) {
		return $this->getChildrenForGroup($groupId = -1, $orderBy, $offset, $limit);
	}

	function& getChildrenForGroup($groupId, $orderBy="n.subtree_order", $offset=-1, $limit=-1) {
		$classes = $this->findAllDepends();
		if (sizeof($classes) == 0) {
			$res = array();
			return $res;
		}
		return $this->getChildrenOfClassesForGroup($groupId, $classes, $orderBy, $offset, $limit);
	}

	function getChildrenCountOfClass($class_id_or_nc) {
		$output = null;
		$nc = &_getNodeClass($class_id_or_nc);
		if($nc)
		{
			$class_id = $nc->id;

			$params = array($this->id, $class_id);
			$output = DBUtils::execCountSelect("SELECT COUNT(*) FROM dbm_nodes WHERE parent_id=? AND class_id=?", $params);
		}
		return $output;
		
	}

	function getChildrenCountOfClassForGroup($group_id, $class_id_or_nc) {
		$nc = &_getNodeClass($class_id_or_nc);
		$class_id = $nc->id;

		$sql =
			"SELECT COUNT(*) FROM dbm_nodes n JOIN dbm_group_rights gr ON gr.node_id=n.id" .
				" WHERE "
				. " n.parent_id=? AND n.class_id=?"
				. " AND gr.group_id=? AND gr.rights&" . VIEW_RIGHT_MASK . "!=0";

		return DBUtils::execCountSelect($sql, array($this->id, $class_id, $group_id));
	}

	function getChildrenCountOfClasses($classes) {
		$class_ids = array();
		foreach($classes as $c) {
			$nc = &_getNodeClass($c);
			$class_ids[] = $nc->id;
		}

		$params = array($this->id);
		return DBUtils::execCountSelect("SELECT COUNT(*) FROM dbm_nodes WHERE parent_id=? AND class_id IN (" . implode(",", $class_ids) . ") ", $params);
	}

	function getChildrenCountOfClassesForGroup($group_id, $classes) {
		$class_ids = array();
		foreach($classes as $c) {
			$nc = &_getNodeClass($c);
			$class_ids[] = $nc->id;
		}

		$sql =
			"SELECT COUNT(*) FROM dbm_nodes n JOIN dbm_group_rights gr ON gr.node_id=n.id" .
				" WHERE "
				. " n.parent_id=?"
				. " AND gr.group_id=? AND gr.rights&" . VIEW_RIGHT_MASK . "!=0"
				. " AND class_id IN (" . implode(",", $class_ids) . ") ";
		$params = array($this->id, $group_id);
		return DBUtils::execCountSelect($sql, $params);
	}

	function& getChildrenOfClass($class_id_or_nc, $orderBy = "subtree_order", $offset=-1, $limit=-1) {
		$res = $this->getChildrenOfClasses(array($class_id_or_nc), $orderBy, $offset, $limit);
		return $res;
	}

	function& getChildrenOfClassForGroup($group_id, $class_id_or_nc, $orderBy = "subtree_order", $offset=-1, $limit=-1) {
		$res = $this->getChildrenOfClassesForGroup($group_id, array($class_id_or_nc), $orderBy, $offset, $limit);
		return $res;
	}

	function& getChildrenOfClasses($classes, $orderBy="n.subtree_order", $offset=-1, $limit=-1) {
		$res = $this->getChildrenOfClassesForGroup($groupId = -1, $classes, $orderBy, $offset, $limit);
		return $res;
	}

	function& getChildrenOfClassesForGroup($groupId, $classes, $orderBy="n.subtree_order", $offset=-1, $limit=-1) {
		$classShortnames = array();
		foreach($classes as $c) {
			$nc = &_getNodeClass($c);
			if ($nc == null) {
				die("getChildrenOfClassesForGroup : can't find class '" . $c . "'");
			}
			$classShortnames[] = $nc->shortname;
		}

		$params = array($this->id);

		$dbmQL = "SELECT n.* FROM {" . implode(",", $classShortnames) . "} n WHERE n.parent_id=?" .
					" ORDER BY " . $orderBy;

		$nodes = Node::findByQuery($dbmQL, $params, $offset, $limit, $groupId);

		return $nodes;
	}

	function& getChildrenForAdminTree(&$user, $offset=-1, $limit=-1) {
		$allClasses = $this->findAllDepends();
		$classes = array();
		foreach($allClasses as $c) {
			if ($c->showAtAdminTree == true) {
				$classes[] = $c;
			}
		}
		$group = $user->group;
		return $this->getChildrenOfClassesForGroup($group->id, $classes, "n.subtree_order", $offset, $limit);
	}

	function getChildrenCountForAdminTree($user) {
		$allClasses = $this->findAllDepends();
		$classes = array();
		foreach($allClasses as $c) {
			if ($c->showAtAdminTree == true) {
				$classes[] = $c;
			}
		}

		if (sizeof($classes) == 0) {
			return 0;
		} else {
			return $this->getChildrenCountOfClassesForGroup($user->group->id, $classes);
		}
	}

	function getCountOfRecursiveChildrenOfClass($nc) {
		return $this->getCountOfRecursiveChildrenOfClasses(array($nc));
	}

	function getCountOfRecursiveChildrenOfClassForGroup($groupId, $nc) {
		return $this->getCountOfRecursiveChildrenOfClassesForGroup($groupId, array($nc));
	}

	public static function getCountOfRecursiveChildrenOfClasses($classes) {
		$class_ids = array();
		foreach($classes as $c) {
			$nc = &_getNodeClass($c);
			$class_ids[] = $nc->id;
		}

		return DBUtils::execCountSelect(
				"SELECT COUNT(*)" .
				" FROM dbm_nodes n" .
				" JOIN dbm_nodes_submission ns ON n.id=ns.child_id" .
				" WHERE ns.parent_id=?" .
					" AND class_id IN (" . implode(",", $class_ids) . ")"
				, array($this->id, $nc->id));
	}

	public static function getCountOfRecursiveChildrenOfClassesForGroup($groupId, $classes) {
		$class_ids = array();
		foreach($classes as $c) {
			$nc = &_getNodeClass($c);
			$class_ids[] = $nc->id;
		}

		return DBUtils::execCountSelect(
				"SELECT COUNT(*)" .
				" FROM dbm_nodes n" .
				" JOIN dbm_nodes_submission ns ON n.id=ns.child_id" .
				" JOIN dbm_group_rights gr ON n.id=gr.node_id" .
				" WHERE ns.parent_id=?" .
					" AND class_id IN (" . implode(",", $class_ids) . ")" .
					" AND gr.group_id=? AND gr.rights&?!=0"
				, array($this->id, $nc->id, $groupId, VIEW_RIGHT_MASK));
	}

	function& getRecursiveChildrenOfClass($nc, $orderBy="", $offset=-1, $limit=-1) {
		return $this->getRecursiveChildrenOfClasses(array($nc), $orderBy, $offset, $limit);
	}

	function& getRecursiveChildrenOfClasses($classes, $orderBy="n.id", $offset=-1, $limit=-1) {
		$classShortnames = array();
		foreach($classes as $c) {
			$nc = &_getNodeClass($c);
			$classShortnames[] = $nc->shortname;
		}

		$params = array($this->id);

		$dbmQL = "SELECT n.*" .
					" FROM {" . implode(",", $classShortnames) . "} n" .
					" JOIN dbm_nodes_submission ns ON n.id=ns.child_id" .
					" WHERE ns.parent_id=?" .
					" ORDER BY " . $orderBy . (($orderBy == "n.subtree_order") ? ", n.id" : "")
		;

		$nodes = & Node::findByQuery($dbmQL, $params, $offset, $limit, $groupId = -1);

		return $nodes;
	}

	function& getRecursiveChildrenOfClassesForGroup($groupId, $classes, $orderBy="n.id", $offset=-1, $limit=-1) {
		$classShortnames = array();
		foreach($classes as $c) {
			$nc = &_getNodeClass($c);
			$classShortnames[] = $nc->shortname;
		}

		$params = array($this->id);

		$dbmQL = "SELECT n.* FROM {" . implode(",", $classShortnames) . "} n JOIN dbm_nodes_submission ns ON n.id=ns.child_id" .
					" WHERE ns.parent_id=?" .
					" ORDER BY " . $orderBy . ", n.id"
		;

		$nodes =& Node::findByQuery($dbmQL, $params, $offset, $limit, $groupId);

		return $nodes;
	}

	function& getRecursiveChildrenAll() {
		$params = array( $this->id);
		return Node::selectNodesNSQLOnly(
			"SELECT n.* FROM dbm_nodes n, dbm_nodes_submission as ns".
			" WHERE n.id=ns.child_id AND ns.parent_id=?".
			" ORDER BY n.subtree_order, ns.level_diff".
			($orderdesc? " DESC" : ""), $params);
	}

	function& getParentList() {
		$res = & Node::selectNodesNSQLOnly("SELECT n.* FROM dbm_nodes n, dbm_nodes_submission ns WHERE n.id=ns.parent_id AND ns.child_id=? ORDER BY ns.level_diff desc", array($this->id));
		return $res;
	}

	function moveUpInSubtreeOrder() {
		$prevOrder = 0;
		$prevId = 0;

		$sql = "";

		if ($this->parent_id != null) {
			$sql = "SELECT id, subtree_order FROM dbm_nodes WHERE parent_id=" . $this->parent_id . " AND subtree_order < " . $this->subtreeOrder . " ORDER by subtree_order desc LIMIT 1";
		} else {
			$sql = "SELECT id, subtree_order FROM dbm_nodes WHERE parent_id is NULL AND subtree_order < " . $this->subtreeOrder . " ORDER by subtree_order desc LIMIT 1";
		}

		$rs = &DBUtils::execSelect($sql);
		if ($rs->next()) {
			$prevId = $rs->getInt(1);
			$prevOrder = $rs->getInt(2);
		}

		if ($prevOrder != 0) {
			$params = array(
						array( -1, $this->id),
						array($this->subtreeOrder, $prevId),
						array($prevOrder, $this->id));
			$sql = "UPDATE dbm_nodes SET subtree_order=? WHERE id=?";
			$batch = array($sql, $sql, $sql);
			DBUtils::execBatch($batch, $params);
		}
		Node::removeNodeFromCache($prevId);
		Node::removeNodeFromCache($this->id);
	}

	function moveDownInSubtreeOrder() {
		$prevOrder = 0;
		$prevId = 0;
		if ($this->parent_id != null) {
			$sql = "SELECT id, subtree_order FROM dbm_nodes WHERE parent_id=" . $this->parent_id . " AND subtree_order > " . $this->subtreeOrder . " ORDER by subtree_order LIMIT 1";
		} else {
			$sql = "SELECT id, subtree_order FROM dbm_nodes WHERE parent_id IS NULL AND subtree_order > " . $this->subtreeOrder . " ORDER by subtree_order LIMIT 1";

		}
		$rs = &DBUtils::execSelect($sql);
		if ($rs->next()) {
			$prevId = $rs->getInt(1);
			$prevOrder = $rs->getInt(2);
		}

		if ($prevOrder != 0) {
			$params = array(
						array(-1, $this->id),
						array($this->subtreeOrder, $prevId),
						array($prevOrder, $this->id));
			$sql = "UPDATE dbm_nodes SET subtree_order=? WHERE id=?";
			$batch = array($sql, $sql, $sql);
			DBUtils::execBatch($batch, $params);
		}
		Node::removeNodeFromCache($prevId);
		Node::removeNodeFromCache($this->id);
	}

	function moveHomeInSubtreeOrder() {
		$prevOrder = 0;
		$prevId = 0;

		DBUtils::execUpdate("UPDATE dbm_nodes SET subtree_order=-1 WHERE id=" . $this->id);

		if ($this->parent_id != null) {
			$sql = "SELECT id, subtree_order FROM dbm_nodes WHERE parent_id=" . $this->parent_id . " AND id != " . $this->id . " ORDER by subtree_order desc";
		} else {
			$sql = "SELECT id, subtree_order FROM dbm_nodes WHERE parent_id IS NULL AND id != " . $this->id . " ORDER by subtree_order desc";
		}
		$rs = &DBUtils::execSelect($sql);
		while ($rs->next()) {
			$prevId = $rs->getInt(1);
			$prevOrder = $rs->getInt(2);

			DBUtils::execUpdate("UPDATE dbm_nodes SET subtree_order=" . ($prevOrder + 1) . " WHERE id=" . $prevId);
		}

		if ($prevOrder != 0) {
			DBUtils::execUpdate("UPDATE dbm_nodes SET subtree_order=" . $prevOrder . " WHERE id=" . $this->id);
		}
		Node::removeNodeFromCache($prevId);
		Node::removeNodeFromCache($this->id);
	}

	function moveEndInSubtreeOrder() {
		$Order = 0;

		if ($this->parent_id != null) {
			$sql = "SELECT max(subtree_order) FROM dbm_nodes WHERE parent_id=?";
			$Order = DBUtils::execCountSelect($sql, array( $this->parent_id ));
		} else {
			$sql = "SELECT max(subtree_order) FROM dbm_nodes WHERE parent_id IS NULL";
			$Order = DBUtils::execCountSelect($sql);
		}
		$sql = "UPDATE dbm_nodes SET subtree_order=? WHERE id=?";
		DBUtils::execUpdate($sql, array( $Order + 1, $this->id));
		Node::removeNodeFromCache($this->id);
	}

	function moveToNewParent($new_parent_id) {
		if (is_object($new_parent_id)) {
			$new_parent_id = $new_parent_id->id;
		}
		$moveToNewParentapSQL = "UPDATE dbm_nodes SET absolute_path = CONCAT(?, substring(absolute_path from ?)) WHERE id=?";
		$oldPath = $this->absolutePath;
		do {
			if ($this->parent_id == null) {
				break;
			}

			$new_parent = Node::findById($new_parent_id);
			$oldAbsolutePath = $this->absolutePath;
			if ($new_parent == null)
				break;
			$this->absolutePath = ($new_parent->isRoot() ? "" : $new_parent->absolutePath) . "/" . $this->shortname;

			$sql = "UPDATE dbm_nodes SET parent_id=?, absolute_path=?, subtree_order=? WHERE id=?";
			$params = array( $new_parent_id, $this->absolutePath, $new_parent->getLastSubtreeOrder() + 1, $this->id);
			DBUtils::execUpdate($sql, $params);

			$params2 = array( $this->absolutePath, strlen($oldAbsolutePath) + 1, null );

			$rs = &DBUtils::execSelect("SELECT child_id FROM dbm_nodes_submission WHERE parent_id=" . $this->id);
			while ($rs->next()) {
				$params2[2] = $rs->getInt(1);
				DBUtils::execUpdate($moveToNewParentapSQL, $params2);
			}

			Node::recursiveUpdateSubmission($this, $new_parent_id);
		} while (false);
				
		//kazancev - start
		$fieldDefs = $this->getNodeClass()->getFieldDefs();
		$fieldShortnames = array_keys($fieldDefs);
		$flag_double_vars = false;//kazancev
		foreach ($fieldShortnames as $shortname) {
			if (in_array($shortname, array("parent_id", "name", "absolute_path"))) {
				$flag_double_vars = true;
			}
		}
		if ($flag_double_vars) {
			$tmp_node = Node::findById($this->id);
			$tmp_node->store();
			//_dump($tmp_node->absolutePath);
		}
		//kazancev - end
		
		Node::removeNodeFromCache($this->id);
		Node::removeNodeFromCache($oldPath);
	}

	public static function recursiveUpdateSubmission(&$node, $new_parent_id) {
		DBUtils::execUpdate("DELETE FROM dbm_nodes_submission WHERE child_id=" . $node->id);

		$sql3 = "INSERT INTO dbm_nodes_submission (child_id, parent_id, level_diff) VALUES(?, ?, ?)";
		$params3 = array( $node->id, null, null );

		$rs = &DBUtils::execSelect("SELECT parent_id, level_diff+1 FROM dbm_nodes_submission WHERE child_id=" . $new_parent_id);
		while ($rs->next()) {
			$params3[1] = $rs->getInt(1);
			$params3[2] = $rs->getInt(2);
			DBUtils::execUpdate($sql3, $params3);
		}

		DBUtils::execUpdate("INSERT INTO dbm_nodes_submission (child_id, parent_id, level_diff) VALUES(" . $node->id . "," . $new_parent_id . ",1)");
		$children = $node->getChildren();
		for($i=0;$i<sizeof($children);$i++) {
			Node::recursiveUpdateSubmission($children[$i], $node->id);
		}
	}

	function& getAllSubnodesIds() {
		$res = array();
		$rs = &DBUtils::execSelect("SELECT n.id FROM dbm_nodes n, dbm_nodes_submission ns WHERE ns.parent_id=? AND n.id=ns.child_id", array($this->id));
		while ($rs->next()) {
			$res[] = $rs->getInt(1);
		}
		return $res;
	}

	function& hasChildrenForMoveNodeTree(&$user, &$movedNode) {
		$nodeClass = $movedNode->getNodeClass();
		$movedNodeParent = $movedNode->getParent();
		if ($movedNodeParent == null) {
			return false;
		}

		$nsql = "SELECT COUNT(n.id) FROM dbm_nodes n, dbm_nodes_submission ns, dbm_node_dependencies nd, dbm_group_rights gr";
		$csql = "SELECT COUNT(n.id) FROM dbm_nodes n, dbm_nodes_submission ns, dbm_class_dependencies cd, dbm_group_rights gr";

		$subnodes = $movedNode->getAllSubnodesIds();

		$subnodesIds = "";
		foreach ($subnodes as $sid) {
			$subnodesIds .= "," . $sid;
		}

		$nsql .= " WHERE n.id=ns.child_id AND ns.parent_id=? AND n.id NOT IN (?, ?" . $subnodesIds . ") AND nd.node_id=n.id AND nd.submissed_class_id=?";
		$nsql .= " AND gr.node_id=n.id AND gr.group_id=? AND gr.rights & ? != 0";
		$csql .= " WHERE n.id=ns.child_id AND ns.parent_id=? AND n.id NOT IN (?, ?" . $subnodesIds . ") AND cd.class_id=n.class_id AND cd.submissed_class_id=?";
		$csql .= " AND gr.node_id=n.id AND gr.group_id=? AND gr.rights & ? != 0";
		$sqlParams = array( $this->id, $movedNode->id, $movedNodeParent->id, $nodeClass->id, $user->group->id, WRITE_RIGHT_MASK);

		$depsInSubnodes = DBUtils::execCountSelect($nsql, $sqlParams);
		$depsInClass = DBUtils::execCountSelect($csql, $sqlParams);

		return ($depsInSubnodes + $depsInClass) > 0;
	}

	function& getChildrenForUser(&$currentUser) {
		return $this->getChildrenForGroup($currentUser->group->id);
	}

	public static function& findNodesByFullTextSearch($query) {
		$nodes = array();
		$node_ids = array();
		$_keywords = explode(" ", $query);
		$keywords = array();
		foreach($_keywords as $k) {
			$k = trim($k);
			if (!$k) continue;
			$keywords[] = $k;
		}

		$rss = array();
		$rss []= &DBUtils::execSelect("SELECT node_id, value_str FROM dbm_text_index WHERE value_str LIKE '%" . addslashes(stripslashes($query)) . "%'");
				
		//$rss []= &DBUtils::execSelect("SELECT node_id, value_str FROM dbm_text_index WHERE MATCH(value_str) AGAINST (?)", array($query));
		//$rss []= &DBUtils::execSelect("SELECT id as node_id, name as value_str FROM dbm_nodes WHERE MATCH(name) AGAINST (?)", array($query));

		for($i=0;$i<sizeof($rss);$i++) {
			$rs = &$rss[$i];
			while ($rs->next()) {
				$id = $rs->getInt("node_id");
				//echo $rs->getInt("node_id") . highLightKeywords($rs->getString("value_str"), $keywords) . "<br>";
	
				$node = &Node::findById($id);
				if (!($node->dynamicTemplate && file_exists($_SERVER['DOCUMENT_ROOT'] . "/dynamic_templates/" . $node->dynamicTemplate . ".php"))) {
					$node = $node->getParent();
				}
				if (!($node->dynamicTemplate && file_exists($_SERVER['DOCUMENT_ROOT'] . "/dynamic_templates/" . $node->dynamicTemplate . ".php"))) {
					$node = $node->getParent();
				}
				if ($node->dynamicTemplate && file_exists($_SERVER['DOCUMENT_ROOT'] . "/dynamic_templates/" . $node->dynamicTemplate . ".php") && ($node->getNodeClass()->shortname!="admin_section" && $node->getNodeClass()->shortname!="system_section")) {
					if (in_array($node->id, $node_ids)) continue;
					$item = new PHPObject();
	
					$item->href = $node->getHref();
					$item->name = highLightKeywords($node->name, $keywords);
					//$item->text = highLightKeywords(strip_tags(quoteString($rs->getString("value_str"), 100)), $keywords);
					//_dump(strip_tags($rs->getString("value_str")));
					$item->text = highLightKeywords(findPositionWord(implode(" ", $keywords), strip_tags($rs->getString("value_str"))), $keywords);
	
					$nodes []= $item;
					$node_ids [] = $node->id;
				}
			}
		}

		return $nodes;
	}

	public static function buildFullTextIndex() {
		DBUtils::execUpdate("DELETE FROM dbm_text_index");
		$classesList = NodeClass::findAll();
		foreach($classesList as $nc) {
			$fieldDefs = $nc->getFieldDefs();
			foreach($fieldDefs as $fieldDef) {
				$ft = $fieldDef->getFieldType();
				if (in_array($ft->name, array("String", "Textarea", "WYSiWYG text"))) {
					$sql = "INSERT INTO dbm_text_index (node_id, value_str) " .
						"SELECT node_id, `".$fieldDef->shortname."` AS value_str FROM " . $nc->getNFVTableName();
					DBUtils::execUpdate($sql);
					$sql = "INSERT INTO dbm_text_index (node_id, value_str) " .
						"SELECT id as node_id, name AS value_str FROM dbm_nodes";
					DBUtils::execUpdate($sql);
				}
			}
		}
	}

	function equals(&$node) {
		return $this->id==$node->id;
	}

	public static function readCache() {
		global $_NODES_CACHE_BY_ID, $_NODES_CACHE_BY_PATH;

		_read_cache("_NODES_CACHE_BY_ID");
		_read_cache("_NODES_CACHE_BY_PATH");

		if ($_NODES_CACHE_BY_ID === null || $_NODES_CACHE_BY_PATH === null
			|| !is_array($_NODES_CACHE_BY_ID) || !is_array($_NODES_CACHE_BY_PATH)) {
			$_NODES_CACHE_BY_PATH = array();
			$_NODES_CACHE_BY_ID = array();
		}
	}

	public static function cleanCache() {
		global $_NODES_CACHE_BY_ID, $_NODES_CACHE_BY_PATH;

		$keys = array_merge(array_keys($_NODES_CACHE_BY_PATH), array_keys($_NODES_CACHE_BY_ID));
		foreach($keys as $k) {
			Node::removeNodeFromCache($k);
		}
	}
}

function &_getNodeClass($class_id_or_nc) {
	if (is_object($class_id_or_nc)) {
		$nc = $class_id_or_nc;
	} else if (is_integer($class_id_or_nc)) {
		$nc = &NodeClass::findById($class_id_or_nc);
	} else {
		$nc = &NodeClass::findByShortname(trim($class_id_or_nc));
	}
	return $nc;
}

?>