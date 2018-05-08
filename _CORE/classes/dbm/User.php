<?php

$USERS_CACHE = null;

class User {
	var $id = 0;
	var $email = "";
	var $group = null;
	var $login = "";
	var $password = "";

	function _create() {
		$this->id = DBUtils::getNextSequnceID("users");
		$sql = "INSERT INTO dbm_users (id, login, password, group_id,email) VALUES (?,?,?,?,?)";

		DBUtils::execUpdate($sql, array( $this->id, $this->login, $this->password, $this->group->id, $this->email));
	}

	function _store() {
		$sql = "UPDATE dbm_users SET login=?, password=?, group_id=?, email=? WHERE id=?";

		DBUtils::execUpdate($sql, array( $this->login, $this->password, $this->group->id, $this->email, $this->id));
		UserGroup::buildUserGroupCache();
	}

	function _remove() {
		$sql = "DELETE FROM dbm_users WHERE id=?";
		DBUtils::execUpdate($sql, array($this->id));
	}

	public static function& _findAll() {
		$sql = "SELECT * FROM dbm_users";
		$rs = DBUtils::execSelect($sql);
		$v = array();
		while ($rs->next()) {
			$u = new User();
			$u->id = $rs->getInt("id");
			$u->login = $rs->getString("login");
			$u->password = $rs->getString("password");
			$u->email = $rs->getString("email");
			$u->group = UserGroup::findById($rs->getInt("group_id"));
			$v[] = $u;
		}

		return $v;
	}

	public static function& getUsersCache() {
		return $GLOBALS["USERS_CACHE"];
	}

	public static function buildUsersCache() {
		global $_CLEAR_USERS_CACHE, $USERS_CACHE;
		if (!$_CLEAR_USERS_CACHE) {
			_read_cache("USERS_CACHE");
		}

		if ($USERS_CACHE === null) {
			$USERS_CACHE = array();
			$users = User::_findAll();
			foreach ($users as $user) {
				$USERS_CACHE[$user->login] = $user;
				$USERS_CACHE[$user->id] = $user;
			}
		}
	}

	function create() {
		$this->_create();
		$usersCache = &User::getUsersCache();
		$usersCache[$this->id] = $this;
		$usersCache[$this->login] = $this;
	}

	function store() {
		$this->_store();
	}

	function remove() {
		$usersCache = &User::getUsersCache();
		$usersCache = array_remove($usersCache, $this->id);
		$usersCache = array_remove($usersCache, $this->login);
		$this->_remove();

	}

	public static function& findByLogin($login) {
		$usersCache = &User::getUsersCache();
		$x = $usersCache[$login];
		return $x;
	}

	public static function& findByEmail($email) {
		$fu = null;
		$usersCache = &User::getUsersCache();
		foreach ($usersCache as $u) {
			if ($u->email == $email)
				$fu = $u;
		}

		return $fu;
	}

	public static function& findById($id) {
		$usersCache = &User::getUsersCache();
		return $usersCache[$id];
	}

	function canViewNode(&$node) {
		$ngr = new NodeGroupRights($this->group, $node);
		return $ngr->getViewRight();
	}

	function canEditNode(&$node) {
		$ngr = new NodeGroupRights($this->group, $node);
		return $ngr->getWriteRight();
	}
}

?>