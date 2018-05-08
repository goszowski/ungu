<?php

require_once ("dbm/fields/StringField.php");

define ("LINKFIELD_DB_DELIM", "%|!");
define ("DB_ESCAPED_LINKFIELD_DB_DELIM", "\\%|!");

class LinkField extends StringField {
	var $nodePath = "";
	var $linkedNodeId = 0;
	var $nodeName = "";

	function LinkField() {}

	function init($fieldRS, $tableAlias, $shortname) {
		Field::init($fieldRS, $tableAlias, $shortname);
		if ($this->value) {
			list($this->linkedNodeId, $this->nodePath, $this->nodeName) = explode(LINKFIELD_DB_DELIM, $this->value);
		}
	}

	function setValue($o) {
		global $AdminTrnsl;
		if (is_object($o) && getClassNameLowercase($o) == 'httprequest') {
			$rValue = $o->getParameter($this->fieldDefShortname);
		} elseif(is_string($o)) {
			$rValue = $o;
		}

		$fieldDef = $this->getFieldDef();

		$allowed_classes = $fieldDef->getParameterValue("allowed_classes");
		$root_node_path = $fieldDef->getParameterValue("root_node_path");

		$verror = null;
		$this->value = "";
		do {
			if (strpos($rValue, LINKFIELD_DB_DELIM) !== false) {
				list($this->linkedNodeId, $this->nodePath, $this->nodeName) = explode(LINKFIELD_DB_DELIM, $rValue);
				$this->value = $rValue;
				break;
			}
			if (strlen($rValue) != 0) {
				$rootNode = &Node::findByPath($root_node_path);
				if ($rootNode == null) {
					$verror = $AdminTrnsl["BadRootNodePath"];
					break;
				}
				$linkedNode = &Node::findByPath($rValue);
				if ($linkedNode != null && $this->nodeId != -1 && $linkedNode->id == $this->nodeId) {
					$verror = $AdminTrnsl["CantCreateLinkToItself"];
					break;
				}
				if ($linkedNode == null) {
					$verror = $AdminTrnsl["SpecifiedPathIsNotValid"];
					break;
				}
				if (!$linkedNode->isSubNodeOf($rootNode)) {
					$verror = $AdminTrnsl["SpecifiedPathIsNotGranted"];
					break;
				}
	
				$linkedNodeClass = $linkedNode->getNodeClass();
				$classname = $linkedNodeClass->shortname;
				if ($allowed_classes != "*" && strpos("," . $allowed_classes . ",", "," . $classname . ",") === false) {
					$verror = $AdminTrnsl["SpecifiedPathIsNotValidClass"];
					break;
				}

				$this->value = $linkedNode->id . LINKFIELD_DB_DELIM . $linkedNode->absolutePath . LINKFIELD_DB_DELIM . $linkedNode->name;
			} else if($fieldDef->required) {
				$verror = $AdminTrnsl["RequiredField"];
				break;
			}
		} while(false);

		return $verror;
	}

	function getParamersForIncludeControlJSP() {
		$params = array();

		$fieldDef = $this->getFieldDef();
		$fieldType = $fieldDef->getFieldType();
		$classParams = $fieldType->getParametersNames();

		foreach ($classParams as $fieldParameterShortname) {
			$params[$fieldParameterShortname] = $fieldDef->getParameterValue($fieldParameterShortname);
		}

		$params["linked_node_path"] =  $this->nodePath;
		$params["linked_node_id"] =  $this->linkedNodeId;

		if ($this->value) {
			$href = "/admin/nodes.php?do=main&id=" . $this->linkedNodeId;
			$displayedName = $this->nodeName;
		}

		$params["linked_node_name"] = $displayedName;
		$params["linked_node_admin_href"] = $href;

		$params["db_value"] =  $this->value;

		return $params;
	}

	function& getValueForTemplate() {
		$value = array();
		$value["nodePath"] = $this->nodePath;
		$value["nodeId"] = $this->linkedNodeId;
		$value["nodeName"] = $this->nodeName;
		return $value;
	}

	function& getLinkedNode() {
		do {
			if ($this->value) {
				$linkedNode = &Node::findByPath($this->nodePath);
				if ($linkedNode == null) {
					break;
				}

				return $linkedNode;
			}
		} while(false);//catch

		$v = null;
		return $v;
	}

	function getDefaultValue($fd) {
        	$dv = $fd->getParameterValue("default_value");

		if ($dv == null)
			$dv = "";

		return $dv;
	}

	function getJSPControlName() {
		$fieldDef = $this->getFieldDef();
		$ctype = $fieldDef->getParameterValue("control_type");
		if ($ctype == null || $ctype=="") {
			$ctype = "popup";
		}

		$class = getClassNameLowercase($this);
		$cname = $class . "_" . $ctype;
		return $cname;
	}

	function getHtmlVisualValue() {
		global $AdminTrnsl;
		if ($this->value) {
			$href = "/admin/nodes.php?do=main&id=" . $this->linkedNodeId;
			$displayedName = $this->nodeName;

			return "<nobr><a href=\"" . $href . "\">" . $displayedName . "</a></nobr>";
		} else {
			return "&nbsp;";
		}
	}

	function sqlEqualsClauseById($nodeID) {
		return " LIKE '" . $nodeID . DB_ESCAPED_LINKFIELD_DB_DELIM . "%'";
	}

	function sqlEqualsClauseByNode(&$node) {
		return "='" . $node->id . LINKFIELD_DB_DELIM . $node->absolutePath . LINKFIELD_DB_DELIM . $node->name . "'";
	}
}

?>