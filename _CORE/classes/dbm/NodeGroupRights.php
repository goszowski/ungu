<?php

DEFINE("VIEW_RIGHT_MASK", 0x01);
DEFINE("WRITE_RIGHT_MASK", 0x02);
DEFINE("NOT_VIEW_RIGHT_MASK", 0x7E);
DEFINE("NOT_WRITE_RIGHT_MASK", 0x7D);

class NodeGroupRights {
    var $rights = 0;

    function hasViewRight() {
        return ($this->rights & VIEW_RIGHT_MASK) != 0;
    }
    function getViewRight() {
    	return $this->hasViewRight();
    }

    function hasWriteRight() {
        return ($this->rights & WRITE_RIGHT_MASK) != 0;
    }
    function getWriteRight() {
        return $this->hasWriteRight();
    }

    function setViewRight() {
        $this->rights |= VIEW_RIGHT_MASK;
    }
    function setWriteRight() {
        $this->rights |= WRITE_RIGHT_MASK;
    }

    function unsetViewRight() {
        $this->rights &= NOT_VIEW_RIGHT_MASK;
    }
    function unsetWriteRight() {
        $this->rights &= NOT_WRITE_RIGHT_MASK;
    }


    var $group = null;
    var $node = null;

    function getRights() {
        return $this->rights;
    }
    function setRights($rights) {
        $this->rights = $rights ;
    }


    function NodeGroupRights($cgroup, $cnode) {
        $this->group = $cgroup;
        $this->node = $cnode;

        if (is_int($this->node))
        	$node_id = $this->node;
        else
        	$node_id = $this->node->id;
        $query = "SELECT rights FROM dbm_group_rights WHERE group_id=" . $this->group->id . " and node_id=" . $node_id;
        $res = DBUtils::execSelect($query);

        $this->rights = 0;
        if ($res->next()) {
            $this->rights = $res->getInt("rights");
        } else {
        	$this->store();
        }
    }

    function store() {
        if (is_int($this->node))
        	$node_id = $this->node;
        else
        	$node_id = $this->node->id;
        $sql = "REPLACE INTO dbm_group_rights(rights,group_id,node_id) VALUES (?,?,?)";
        DBUtils::execUpdate($sql, array($this->rights, $this->group->id, $node_id));
    }
}

?>
