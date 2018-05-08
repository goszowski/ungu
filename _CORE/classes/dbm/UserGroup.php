<?php

$USER_GROUPS_CACHE = null;

class UserGroup {
	var $id = 0;
	var $name = "";
	var $description = "";
	var $canManageUsers = false;
	var $canManageClasses = false;
	var $canManageImgLib = false;
	var $restrictNodeEdit = true;
	var $isModerator = true;

	function UserGroup($id) {
		$this->id = $id;
	}

	/**
	 * Loads data for this bean from database
	 * Notice that id must specify existing row in DB
	 */
	function _load() {
		$sql = "SELECT name, description, canManageClasses, canManageUsers, canManageImgLib, restrictNodeEdit, isModerator FROM dbm_user_groups WHERE id=?";
		$rs = DBUtils::execSelect($sql, array($this->id));
		if ($rs->next()) {
			$this->name = $rs->getString(1);
			$this->description = $rs->getString(2);
			$this->canManageClasses = $rs->getBoolean(3);
			$this->canManageUsers = $rs->getBoolean(4);
			$this->canManageImgLib = $rs->getBoolean(5);
			$this->restrictNodeEdit = $rs->getBoolean(6);
			$this->isModerator = $rs->getBoolean(7);
		} else {
			die("UserGroup _load failed. No such record in database.");
		}
	}

	/**
	 * <p>Commit changes in object's data to database.
	 * Changes only name and description.
	 */
	function _store() {
		$sql = "UPDATE dbm_user_groups SET name=?, description=?, canManageClasses=?, canManageUsers=?, canManageImgLib=?, restrictNodeEdit=?, isModerator=? WHERE id=?";
		DBUtils::execUpdate($sql, array($this->name, $this->description, $this->canManageClasses, $this->canManageUsers, $this->canManageImgLib, $this->restrictNodeEdit, $this->isModerator, $this->id));
	}

	/**
	 * Removes from database
	 */
	function _remove() {
		$sql = "DELETE FROM dbm_user_groups WHERE id=?";
		DBUtils::execUpdate($sql, array($this->id));
	}

	/**
	 * Finds all in database and returns Collection of group id (Integer objects)
	 */
	public static function _findAll() {
		$rs = DBUtils::execSelect("SELECT id FROM dbm_user_groups ORDER BY id");
		$v = array();
		while ($rs->next()) {
			$v[] = $rs->getInt(1);
		}

		return $v;
	}

	/**
	 * Creates new in database and returns id of created row
	 */
	public static function _create($name, $description, $canManageClasses, $canManageUsers, $canManageImgLib, $restrictNodeEdit, $isModerator) {
		$id = DBUtils::getNextSequnceID("user_groups");
		$sql = "INSERT INTO dbm_user_groups (id, name, description,canmanageclasses,canmanageusers,canManageImgLib,restrictNodeEdit, isModerator) VALUES (?,?,?,?,?,?,?,?)";

		DBUtils::execUpdate($sql, array($id, $name, $description, $canManageClasses, $canManageUsers, $canManageImgLib, $restrictNodeEdit, $isModerator));

		$sql = "INSERT INTO dbm_group_rights SELECT ? AS group_id, id as node_id, ? AS rights FROM dbm_nodes";
		DBUtils::execUpdate($sql, array($id, 0));

		return $id;
	}

	/**
	 *
	 */
	function& _getUsers() {
		$sql = "SELECT * FROM dbm_users WHERE group_id=? ORDER BY id";
		$rs = DBUtils::execSelect($sql, array($this->id) );

		$v = array();

		while ($rs->next()) {
			$u = new User();
			$u->id = $rs->getInt("id");
			$u->group = $this;
			$u->login = $rs->getString("login");
			$u->password = $rs->getString("password");
			$u->email = $rs->getString("email");
			$v[] = $u;
		}

		return $v;
	}

	public static function& getUserGroupsCache() {
		return $GLOBALS["USER_GROUPS_CACHE"];
	}

	var $usersInGroupsCache = null;

	function prepareUsersInGroupCache() {
		if ($this->usersInGroupsCache != null)
			return;
		$m = array();
		$rs = DBUtils::execSelect("SELECT id FROM dbm_users WHERE group_id=".$this->id);
		while ($rs->next()) {
			$uid = $rs->getInt(1);
			$m[$uid] = User::findById($uid);
		}
		$this->usersInGroupsCache = $m;
	}

	public static function buildUserGroupCache() {
		global $_CLEAR_USER_GROUPS_CACHE, $USER_GROUPS_CACHE;
		if (!$_CLEAR_USER_GROUPS_CACHE) {
			_read_cache("USER_GROUPS_CACHE");
		}

		if ($USER_GROUPS_CACHE === null) {
			$USER_GROUPS_CACHE = array();
	
			$e = UserGroup::_findAll();
			foreach ($e as $group_id) {
				$group = new UserGroup($group_id);
				$group->_load();

				$USER_GROUPS_CACHE[$group->id] = $group;
			}
		}
	}

	/*=========================================================================
	
	 operations for manipulating users and user grouops
	
	=========================================================================*/
	/**
	 * Get unsorted list of user group objects
	 */
	public static function& findAll() {
		$userGroupsCache = &UserGroup::getUserGroupsCache();
		//todo: sort
		return $userGroupsCache;
	}

	/**
	 * Find object in cache
	 */
	public static function& findById($id) {
		$userGroupsCache = &UserGroup::getUserGroupsCache();
		return $userGroupsCache[$id];
	}

	public static function& findByName($name) {
		$c = &UserGroup::findAll();
		foreach ($c as $ug) {
			if ($ug->name == $name) {
				return $ug;
			}
		}
		return null;
	}

	/**
	 * Create new users group in database and put created to cache
	 */
	public static function& create($name, $description, $canManageClasses, $canManageUsers, $canManageImgLib, $restrictNodeEdit, $isModerator) {
		$id = UserGroup::_create($name, $description, $canManageClasses, $canManageUsers, $canManageImgLib, $restrictNodeEdit, $isModerator);

		$g = new UserGroup($id);
		$g->_load();
		$userGroupsCache = &UserGroup::getUserGroupsCache();
		$userGroupsCache[$id] = $g;
		return $g;
	}

	/**
	 * Commit changes to DB
	 */
	function store() {
		$this->_store();
		$userGroupsCache = &UserGroup::getUserGroupsCache();
		$userGroupsCache[$this->id] = $this;
	}

	/**
	 * Remove user groups from db and cache
	 */
	function remove() {
		$this->_remove();
		$userGroupsCache = &UserGroup::getUserGroupsCache();
		$userGroupsCache = array_remove($userGroupsCache, $this->id);
		User::buildUsersCache();
	}

	function& getUsers() {
		$this->prepareUsersInGroupCache();

		return $this->usersInGroupsCache;
	}

	function& addUser($login, $password, $email) {
		$u = new User();
		$u->group = &$this;
		$u->login = $login;
		$u->password = $password;
		$u->email = $email;
		$u->create();

		$this->prepareUsersInGroupCache();
		$this->usersInGroupsCache[$u->id] = $u;

		$userGroupsCache = &UserGroup::getUserGroupsCache();
		$userGroupsCache[$this->id] = $this;

		return $u;
	}

	function delUser($user) {
		$this->prepareUsersInGroupCache();
		$this->usersInGroupsCache = array_remove($this->usersInGroupsCache, $user->id);
		$user->remove();
	}

	public static function& getGroupSelector($selected_id) {
		$coll = UserGroup::findAll();
		$typeselect = "";
		$isselected = "";

		foreach ($coll as $g) {
			if ($g->id == $selected_id) {
				$isselected = "SELECTED";
			} else {
				$isselected = "";
			}
			$option = "<option value=" . $g->id . " " . $isselected . ">" . prepareStringForXML($g->name) . "</option>";
			$typeselect .= $option;
		}
		return $typeselect;
	}
}

?>