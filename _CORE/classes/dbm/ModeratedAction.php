<?

class ModeratedAction {
	var $id = 0;
	var $node_id = 0;
	var $user_id = 0;
	var $type = "";
	var $data = null;
	var $time = null;

	function ModeratedAction() {
	}

	function findNode() {
		$node = null;

		if ($this->node_id != -1) {
			$node = Node::findById($this->node_id);
		}

		return $node;
	}

	function load(&$rs) {
		$this->id = $rs->getInt("id");
		$this->node_id = $rs->getInt("node_id");
		$this->user_id = $rs->getInt("user_id");
		$this->type = $rs->getString("type");
		$this->data = unserialize($rs->getString("data"));
		$this->time = $rs->getDate("time");
	}

	function& findAll() {
		global $connection;

		$stmt = &$connection->prepareStatement("SELECT * FROM dbm_moderation ORDER BY time DESC");

		$rs = &$stmt->executeQuery();

        $items = array();
        while($rs->next()) {
        	$item = new ModeratedAction();
        	$item->load($rs);
        	$items[] = &$item;
        }

        $stmt->close();

        return $items;
	}

	function& findById($id) {
		global $connection;

		$stmt = &$connection->prepareStatement("SELECT * FROM dbm_moderation WHERE id=" . $id);

		$rs = &$stmt->executeQuery();

        $item = null;
        if($rs->next()) {
        	$item = new ModeratedAction();
        	$item->load($rs);
        }

        $stmt->close();

        return $item;
	}

	function remove($id) {
		global $connection;

		DBUtils::execUpdate("DELETE FROM dbm_moderation WHERE id=" . $id);
	}

	function create($node_id, $user_id, $type, &$dataObject) {
		$id = DBUtils::getNextSequnceID("moderation");
		$time = new Date();

		$sql = "INSERT INTO dbm_moderation (id, node_id, user_id, type, data, time) VALUES (?,?,?,?,?,?)";
		$params = array($id, $node_id, $user_id, $type, serialize($dataObject), $time);
		DBUtils::execUpdate($sql, $params);
	}
}

?>