<?php

require_once ("dbm/fields/IntegerField.php");

class BoolRField extends IntegerField {
	function findCheckedNodesOrdered() {
		$fieldDef = $this->getFieldDef();
		$nodeClass = $fieldDef->getNodeClass();
		$q = "SELECT n.* FROM {" . $nodeClass->shortname . "} n WHERE n." . $fieldDef->shortname . "=1 AND n.id!=" . $this->nodeId . " ORDER BY n.subtree_order";
		$nodes = Node::findByQuery($q);

		return $nodes;
	}

	function setValue($o) {
		global $AdminTrnsl;
		$fieldDef = $this->getFieldDef();

		if (is_object($o) && getClassNameLowercase($o) == 'httprequest') {
			$rValue = (int)$o->getParameter($fieldDef->shortname);
		} else if (is_bool($o)) {
			$rValue = (bool)$o ? 1 : 0;
		} else {
			$rValue = (int)$o;
		}

		$verror = null;
		if (($rValue == 0) || ($rValue == 1)) {
			$this->value = $rValue;
		} else {
			$verror = $AdminTrnsl["BadBoolValue"];
		}

		return $verror;
	}

	function update() {
		$this->uncheckLast();
	}

	function create() {
		$this->uncheckLast();
	}

	function uncheckLast() {
		$fieldDef = $this->getFieldDef();
		$max_checked = $fieldDef->getParameterValue("max_checked");

		if ($this->value == 1) {
			$checkedNodes = $this->findCheckedNodesOrdered();

			if (sizeof($checkedNodes) > ($max_checked - 1)) {
				for($i=0; $i < (sizeof($checkedNodes) - ($max_checked - 1)); $i++) {
					$node = $checkedNodes[$i];
					$node->fields[$fieldDef->shortname]->value = 0;
					$node->store();
				}
			}
		}
	}

	function getParamersForIncludeControlJSP() {
		$params = array();
		$params["value"] = $this->value == 1;

		return $params;
	}

	function getDefaultParamersForIncludeControlJSP() {
		$params = array();
		$params["value"] = $this->value == 1;

		return $params;
	}

	function getHtmlVisualValue() {
		return ($this->value) == 1 ? $AdminTrnsl["Yes"] : $AdminTrnsl["No"];
	}
}

?>