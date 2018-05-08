<?php

require_once ("dbm/fields/Field.php");
require_once ("dbm/fields/StringField.php");

DEFINE("MULTILINKFIELD_IDS_DELIM", ",");

class MultiLinkField extends StringField {
	function setValue($o) {
		global $AdminTrnsl;
		$fieldDef = $this->getFieldDef();
		if (is_object($o) && getClassNameLowercase($o) == 'httprequest') {
			$rValues = $o->getParameter($fieldDef->shortname);
			if ($rValues == null) {
				$rValues = array();
			}
		} elseif(is_string($o)) {
			$this->value = $o;
			return null;
		}

		$allowed_classes = $fieldDef->getParameterValue("allowed_classes");
		$root_node_path = $fieldDef->getParameterValue("root_node_path");
		$rootNode = Node::findByPath($root_node_path);

		$verror = null;
		$values = array();
		do {
			if (sizeof($rValues) == 0 && $fieldDef->required) {
				$verror = $AdminTrnsl["RequiredField"];
				break;
			}

			foreach($rValues as $nodeId) {
				$nodeId = (int)$nodeId;
				$values []= $nodeId;
				$linkedNode = Node::findById($nodeId);

				if ($linkedNode == null) {
					$verror = $AdminTrnsl["SpecifiedPathIsNotValid"];
					break;
				}

				if ($linkedNode != null && $this->nodeId != -1 && $linkedNode->id == $this->nodeId) {
					$verror = $AdminTrnsl["CantCreateLinkToItself"];
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
			}
		} while(false);

		$this->value = implode(MULTILINKFIELD_IDS_DELIM, $values);

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

		$params["linked_node_ids"] = $this->getNodeIds();

		//$params["linked_nodes"] = $this->getLinkedNodes();

		return $params;
	}

	function& getDefaultParamersForIncludeControlJSP() {
		return $this->getParamersForIncludeControlJSP();
	}

	function& getDefaultValue($fd) {
		return "";
	}

	function getJSPControlName() {
		$fieldDef = $this->getFieldDef();
		$ctype = $fieldDef->getParameterValue("control_type");
		if ($ctype == null || $ctype=="") {
			$ctype = "checkboxes";
		}

		$class = getClassNameLowercase($this);
		$cname = $class . "_" . $ctype;
		return $cname;
	}

	function& getLinkedNodes() {
		$nodeIds = $this->getNodeIds();
		$nodes = array();

		foreach ($nodeIds as $nodeId) {
			$linkedNode = Node::findById($nodeId);

			if ($linkedNode != null) {
				$nodes []= $linkedNode;
			}
		}

		return $nodes;
	}

	function getNodeIds() {
		$nodeIds = explode(MULTILINKFIELD_IDS_DELIM, $this->value);

		return $nodeIds;
	}

	function& getValueForTemplate() {
		$nodeIds = $this->getNodeIds();

		return $nodeIds;
	}

	function getHtmlVisualValue() {
		global $AdminTrnsl;
		
		$nodes = $this->getLinkedNodes();
		$htmlStrings = array();		

		for ($i = 0; $i < sizeof($nodes); $i++) {
			$node = &$nodes[$i];
			$href = "/admin/nodes.php?do=main&id=" . $node->id;
			$displayedName = $node->name;
			$htmlString = "<nobr><a href=\"" . $href . "\">" . $displayedName . "</a></nobr>";
			$htmlStrings []= $htmlString;
		}

		$html = implode(", ", $htmlStrings);

		return $html;
	}
}

?>