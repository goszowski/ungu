<?

require_once ("sql/mysql/ResultSet.php");

class NodeQueryBuilder {
	var $fieldsClause = array();
	var $fromClause = "";
	var $joinSpecs = array();
	var $whereClauseParts = array();
	var $groupByClauseParts = array();
	var $havingClauseParts = array();
	var $orderByClauseParts = array();
	var $offset = -1;
	var $limit = -1;

	function NodeQueryBuilder() {
	}

	function error($str) {
		die("NodeQueryBuilder error: <b>" . $str);
	}

	function addField($clause) {
		$this->fieldsClause[] = & $clause;
	}

	function setFrom($from) {
		if (!preg_match("/([\{\}\w]+) (\w+)/", $from)) {
			$this->error("Invalid from clause: '" . $from . "'");
		}

		$this->fromClause = $from;
	}

	function addJoinSpec($tableSpec, $joinCondition, $joinType="") {
		if (!preg_match("/([\{\}\w]+) (\w+)/", $tableSpec, $matches)) {
			$this->error("Invalid join table spec : '" . $tableSpec . "'");
		}
		$joinSpec = new JoinSpec($joinType, $matches[1], $matches[2], $joinCondition);
		$this->joinSpecs[] = & $joinSpec;
	}

	function addWhere($clause) {
		$this->whereClauseParts[] = & $clause;
	}

	function addGroupBy($clause) {
		$this->groupByClauseParts[] = & $clause;
	}

	function addHaving($clause) {
		$this->havingClauseParts[] = & $clause;
	}

	function addOrderBy($clause) {
		$this->orderByClauseParts[] = & $clause;
	}

	function setLimit($limit) {
		$this->limit = $limit;
	}

	function setOffset($offset) {
		$this->offset = $offset;
	}

	function& buildQuery() {
		$dbmQL =
			"SELECT " .
			implode(", ", $this->fieldsClause) .
			" FROM " .
			$this->fromClause;

			for ($i=0; $i<sizeof($this->joinSpecs); $i++) {
				$joinSpec = &$this->joinSpecs[$i];

				$dbmQL .=
				" " .
				($joinSpec->joinType ? $joinSpec->joinType . " " : "") .
				"JOIN " .
				$joinSpec->tableDef .
				" " .
				$joinSpec->tableAlias .
				" ON " .
				$joinSpec->joinCondition
				;
			}

			if(sizeof($this->whereClauseParts) > 0) {
				$dbmQL .= " WHERE (" . implode(") AND (", $this->whereClauseParts) . ")";
			}

			if(sizeof($this->groupByClauseParts) > 0) {
				$dbmQL .= " GROUP BY " . implode(", ", $this->groupByClauseParts);
			}

			if(sizeof($this->havingClauseParts) > 0) {
				$dbmQL .= " HAVING " . implode(", ", $this->havingClauseParts);
			}

			if(sizeof($this->orderByClauseParts) > 0) {
				$dbmQL .= " ORDER BY " . implode(", ", $this->orderByClauseParts);
			}

			if($this->limit != -1) {
				$dbmQL .= " LIMIT ";
				if($this->offset != -1) {
					$dbmQL .= $this->offset . ", ";
				}
				$dbmQL .= $this->limit;
			}

			$query = new nodeQuery($dbmQL);

			return $query;
	}
}

class JoinSpec {
	var $joinType = "";
	var $tableDef = "";
	var $tableAlias = "";
	var $joinCondition = "";

	function JoinSpec($joinType, $tableDef, $tableAlias, $joinCondition) {
		$this->joinType = $joinType;
		$this->tableDef = $tableDef;
		$this->tableAlias = $tableAlias;
		$this->joinCondition = $joinCondition;
	}
}

class NodeQueryResultSet extends ResultSet {
	var $tableAliases = array();

	function NodeQueryResultSet(&$rs, &$query) {
		$this->result_id = &$rs->result_id;
		$this->rowCount = &$rs->rowCount;

		$this->columnIndexes = &$rs->columnIndexes;
		$this->columnNames = &$rs->columnNames;
		$this->columnTypeNames = &$rs->columnTypeNames;
		$this->columnCount = $rs->columnCount;

		$this->currentRowIndex = $rs->currentRowIndex;

		$this->currentRow = &$rs->currentRow;

		$this->tableAliases = &$query->dbmQLfieldsClause_AllClassFieldsParts;
	}

	function& getNode($tableAlias = null) {
		if ($tableAlias == null) {
			if (sizeof($this->tableAliases) == 1) {
				$tableAlias = $this->tableAliases[0];
			} else {
				die("NodeQueryResultSet.getNodes(). Alias is null, cannot choose between (" . implode(", ", $this->tableAliases) . ")");
			}
		}

		$fieldNamesPrefix = $tableAlias . "___";

		$node_id = $this->getInt($fieldNamesPrefix . "id");
		if ($node_id == 0) {
			return null;
		}

		$node = new Node();

		$node->init($this, $this, $tableAlias);
		return $node;
	}
}

class NodeQuery {
	var $dbmQLdistinctClause = "";
	var $dbmQLfieldsClause = "";
	var $dbmQLfieldsClause_AllClassFieldsParts = array();
	var $dbmQLfieldsClause_OtherParts = array();

	var $dbmQLjoinSpecs = array();
	var $dbmQLbaseTableDef = "";
	var $dbmQLbaseTableAlias = "";

	var $dbmQLwhereClause = "";
	var $dbmQLgroupByClause = "";
	var $dbmQLgroupByClauseParts = array();
	var $dbmQLhavingClause = "";
	var $dbmQLorderByClause = "";
	var $dbmQLlimitClause = "";

	var $sql = "";
	var $sqlLimitClause = "";

	var $groupId = -1;

	var $rs = null;
	var $rsCount = -1;

	//join-type generation-specific
	var $classesAliasesFieldMappings = array();
	var $classesAliasesNodeTablesMappings = array();

	function execute($params = array(), $offset=-1, $limit=-1) {
		//_dump($this->sql);
		$sql = $this->sql;

		if (($offset!=-1) && ($limit!=-1)) {
			$sql .=
				"\nLIMIT " .
				$offset .
				", " .
				$limit
				;
		} else {
			$sql .= $this->sqlLimitClause;
		}

		_log2("\n\n".$sql);

		//$rs = & DBUtils::execSelect($sql, $params);

		global $connection;

		$pstmt = &$connection->prepareStatement($sql);

		foreach ($params as $i=>$p) {
			$pstmt->setObject($i + 1, $p);
		}
		$rs = $pstmt->executeQuery();
		$this->rs = new NodeQueryResultSet($rs, $this);
	}

	function& getResultSet() {
		if ($this->rs == null) {
			die("NodeQuery.getResultSet(). Result set is null. Try to call 'execute()' first.");
		}
		return $this->rs;
	}

	function executeCount($params = array()) {
		$this->count = DBUtils::execCountSelect($this->sql, $params);
		return $this->count;
	}

	function getNodes($tableAlias = null) {
		$rs = $this->getResultSet();

		$nodes = array();

		while($rs->next()) {
			$node = $rs->getNode($tableAlias);
			$nodes[] = $node;
		}

		return $nodes;
	}

	function getCount() {
		return $this->count;
	}

	function NodeQuery($dbmQL, $groupId=-1) {
		$this->groupId = $groupId;
		$this->dbmQL = $dbmQL;
		$this->parse();
		//$this->compileUsingUnions();
		$this->compileUsingJoins();
	}

	function parse() {
		$dbmQL = $this->dbmQL;
		//_dump($dbmQL);

		$dbmQL = strReplaceButNotInQuotes("FROM", "\nFROM", $dbmQL);
		$dbmQL = strReplaceButNotInQuotes("WHERE", "\nWHERE", $dbmQL);
		$dbmQL = strReplaceButNotInQuotes("GROUP BY", "\nGROUP BY", $dbmQL);
		$dbmQL = strReplaceButNotInQuotes("HAVING", "\nHAVING", $dbmQL);
		$dbmQL = strReplaceButNotInQuotes("ORDER BY", "\nORDER BY", $dbmQL);
		$dbmQL = strReplaceButNotInQuotes("LIMIT", "\nLIMIT", $dbmQL);

		$dbmQLBaseRegex =
			"/" .
				"SELECT" .
				"(\s+DISTINCT)?" .
				"\s+(.*)" .
				"\s+FROM" .
				"\s+(.*)" .
				"(\s+WHERE\s+(.*))?" .
				"(\s+GROUP BY\s+(.*))?" .
				"(\s+HAVING\s+(.*))?" .
				"(\s+ORDER BY\s+(.*))?" .
				"(\s+LIMIT\s+(.*))?" .
			"/"
			;
		
		if (!preg_match($dbmQLBaseRegex, $dbmQL, $matches)) {
			die("Can't parse dbmQL: <br><b>" . $dbmQL);
		}
		//echo $dbmQL;
		array_shift($matches);
		//_dump($matches);

		$this->dbmQLdistinctClause = trim($matches[0]);
		$this->dbmQLfieldsClause = trim($matches[1]);

		//if (preg_match("/COUNT\(\w+\.\*\)|COUNT\(\*\)/", $this->dbmQLfieldsClause)) {
		//die("Can't parse dbmQL: COUNT(table.*) is not supported. try to use table.id or similar");
		//}

		$dbmQLfromClause = isset($matches[2]) ? trim($matches[2]) : '';
		$this->dbmQLwhereClause = isset($matches[4]) ? trim($matches[4]) : '';
		$this->dbmQLgroupByClause = isset($matches[6]) ? trim($matches[6]) : '';

		if ($this->dbmQLgroupByClause) {
			$dbmQLgroupByClauseParts = explode(",", $this->dbmQLgroupByClause);

			foreach($dbmQLgroupByClauseParts as $gbp) {
				$this->dbmQLgroupByClauseParts[] = trim($gbp);
			}
		}
		if (array_key_exists(8, $matches)) {
			$this->dbmQLhavingClause = trim($matches[8]);
		}
		if (array_key_exists(10, $matches)) {
			$this->dbmQLorderByClause = trim($matches[10]);
		}
		if (array_key_exists(12, $matches)) {
			$this->dbmQLlimitClause = trim($matches[12]);
		}

		$this->dbmQLfieldsClause_AllClassFieldsParts = array();
		$this->dbmQLfieldsClause_OtherParts = array();

		$allFieldsParts = explode(",", $this->dbmQLfieldsClause);

		$i = 0;

		foreach($allFieldsParts as $dbmQLField) {
			$dbmQLField = trim($dbmQLField);

			if (preg_match("/^([\w]+)\.\*/", $dbmQLField, $matches)) {
				$tableAlias = $matches[1];

				$this->dbmQLfieldsClause_AllClassFieldsParts[] = $tableAlias;
			} else {
				$this->dbmQLfieldsClause_OtherParts[] = $dbmQLField;
			}
		}

		$fromClauseBaseTableRegex =
			"/^" .
				"([\{\w\,\}]+)" .
				"(\s+\w+)" .
				"(.*)" .
			"/";

			if (!preg_match($fromClauseBaseTableRegex, $dbmQLfromClause, $matches)) {
				die("Can't parse dbmQL: invalid FROM clause: cannot find base table definition<br><b>" . $dbmQLfromClause);
			}

			array_shift($matches);

			$this->dbmQLbaseTableDef = trim($matches[0]);
			$this->dbmQLbaseTableAlias = trim($matches[1]);
			$dbmQLjoinClause = trim($matches[2]);
			if (in_array($this->dbmQLbaseTableAlias, array("LEFT", "JOIN", "INNER", "NATURAL"))) {
				die("Can't parse dbmQL: please specify alias for table definition <b>" . $baseTableDef);
			}

			$this->dbmQLjoinSpecs = array();

			if ($dbmQLjoinClause != "") {
				$dbmQLjoinClause = " " . $dbmQLjoinClause;//to prevent regex compat.
				$fromClauseRegex =
				"/" .
					"(\s+LEFT|\s+INNER)?" .
					"\s+JOIN\s+" .
					"([\{\w\,\}]+)" .
					"(\s+\w+)" .
					"\s+ON\s+" .
					"(\w+\.\w+\=\w+\.\w+)" .
				"/";

				$joinMatchesCount = preg_match_all($fromClauseRegex, $dbmQLjoinClause, $matches);

				if ($joinMatchesCount == 0) {
					die("Can't parse dbmQL: invalid FROM clause:<br><b>" . $dbmQLfromClause);
				}

				array_shift($matches);
				for ($i=0; $i < $joinMatchesCount; $i++) {
					$joinSpec = new JoinSpec(
					trim($matches[0][$i]),
					trim($matches[1][$i]),
					trim($matches[2][$i]),
					trim($matches[3][$i])
					);
					$this->dbmQLjoinSpecs[] = &$joinSpec;
				}
			}
	}

	/*
	 function createUnionSelectClause($dbmQLspec) {
	 $dbmQLspec = trim($dbmQLspec);
	 $nodeClassesShortnames = explode(",", substr($dbmQLspec, 1, strlen($dbmQLspec) - 2));

		$i = 0;

		$commonFieldShortnames = array();
		$allFieldShortnames = array();
		$nodeClasses = array();

		foreach($nodeClassesShortnames as $classShortname) {
		$nodeClass = & NodeClass::findByShortname(trim($classShortname));
		if ($nodeClass == null) {
		_dump($this);
		die("compileToSQLQuery: can't find NodeClass : " . $classShortname);
		}

		$fieldShortnames = array_keys($nodeClass->fieldDefs);

		$nodeClasses[$classShortname] = &$nodeClass;

		if ($i == 0) {
		$commonFieldShortnames = $fieldShortnames;
		} else {
		$commonFieldShortnames = array_intersect($commonFieldShortnames, $fieldShortnames);
		}

		$allFieldShortnames = array_merge($allFieldShortnames, $fieldShortnames);

		$i++;
		}

		$allFieldShortnames = array_unique($allFieldShortnames);

		$unionParts = array();

		foreach($nodeClassesShortnames as $classShortname) {
		$nodeClass = & NodeClass::findByShortname(trim($classShortname));

		$fieldShortnames = array_keys($nodeClass->fieldDefs);
		$nTableAlias = "n";//.$nodeClass->id;
		$fvTableAlias = "fv";//.$nodeClass->id;

		$fieldDefs = array(
		//"id", "name", "shortname", "dynamic_template", "class_id",
		//"parent_id", "owner", "admin_url", "subtree_order", "absolute_path", "time_created", "time_updated"
		$nTableAlias . ".*"
		);

		foreach($fieldShortnames as $fieldShortname) {
		$fieldDefs[] =
		$fvTableAlias .
		"." .
		$fieldShortname .
		" AS " .
		$fieldShortname
		;
		}

		foreach($allFieldShortnames as $fieldShortname) {
		if (in_array($fieldShortname, $fieldShortnames)) {
		continue;
		}

		$fieldDefs[] = "NULL AS " . $fieldShortname;
		}

		$unionSelect =
		"  SELECT " .
		implode(", ", $fieldDefs) .
		"\n  FROM" .
		" dbm_nodes " .
		$nTableAlias .
		" JOIN " .
		$nodeClass->getNFVTableName() .
		" " .
		$fvTableAlias .
		" ON" .
		" " . $nTableAlias . ".id=" . $fvTableAlias . ".node_id"
		;
		$unionParts[] = $unionSelect;
		}

		return (implode("\nUNION ALL\n", $unionParts));
		}

		function compileUsingUnions() {
		$nodeClasses = array();
		$nodeClassesShortnames = array();
		$commonFieldShortnames = array();

		$baseTableClause =
		"(\n" .
		NodeQuery::createUnionSelectClause($this->dbmQLbaseTableDef) .
		"\n) " .
		$this->dbmQLbaseTableAlias
		;

		$whereClauseParts = array();

		if ($this->dbmQLwhereClause) {
		$whereClauseParts[] = $this->dbmQLwhereClause;
		}

		if ($this->groupId != -1) {
		$joinSpec = new JoinSpec(
		"",
		"dbm_group_rights",
		"gr",
		$this->dbmQLbaseTableAlias . ".id=gr.node_id"
		);
		$this->dbmQLjoinSpecs[] = &$joinSpec;
		$whereClauseParts[] = "gr.group_id=" . $this->groupId . " AND gr.rights&" . VIEW_RIGHT_MASK . "!=0";
		}

		$sql =
		"SELECT " .
		$this->dbmQLfieldsClause .
		"\nFROM\n" .
		$baseTableClause
		;

		for ($i=0; $i<sizeof($this->dbmQLjoinSpecs); $i++) {
		$joinSpec = &$this->dbmQLjoinSpecs[$i];

		if ((strpos($joinSpec->tableDef, "{") === 0) && (strpos($joinSpec->tableDef, "}") === (strlen($joinSpec->tableDef) - 1))) {
		$joinSpec->tableDef =
		"(" .
		NodeQuery::createUnionSelectClause($joinSpec->tableDef) .
		"\n)"
		;
		}

		$sql .=
		($joinSpec->joinType ? $joinSpec->joinType . " " : "") .
		"\nJOIN " .
		$joinSpec->tableDef .
		" " .
		$joinSpec->tableAlias .
		" ON " .
		$joinSpec->joinCondition
		;
		}

		if(sizeof($whereClauseParts) > 0) {
		$sql .=
		"\nWHERE (" .
		implode(") AND (", $whereClauseParts) .
		")"
		;
		}

		if($this->dbmQLgroupByClause) {
		$sql .=
		"\nGROUP BY " .
		$this->dbmQLgroupByClause
		;
		}

		if($this->dbmQLhavingClause) {
		$sql .=
		"\nHAVING " .
		$this->dbmQLhavingClause
		;
		}

		if($this->dbmQLorderByClause) {
		$sql .=
		"\nORDER BY " .
		$this->dbmQLorderByClause
		;
		}

		if($this->dbmQLlimitClause) {
		$this->sqlLimitClause =
		"\nLIMIT " .
		$this->dbmQLlimitClause
		;
		}

		$this->sql = "\n" . $sql . "\n";
		}
		*/

	function& createFieldMappings($classesShortnames, $nodesTableAlias, $fvTablesAliases) {
		$fieldMappings = array();

		foreach($classesShortnames as $classShortname) {
			$nodeClass = NodeClass::findByShortname(trim($classShortname));
			if ($nodeClass == null) {
				die("can't create mapping for class name '" . $classShortname . "'");
			}

			$fieldShortnames = array_keys($nodeClass->fieldDefs);

			$fvTableAlias = $fvTablesAliases[$nodeClass->shortname];

			foreach($fieldShortnames as $fieldShortname) {
				if (array_key_exists($fieldShortname, $fieldMappings)) {
					continue;
				}
				$allClassesWithThisShortname = NodeQuery::getAllClassesHavingShortname($classesShortnames, $fieldShortname);

				if (sizeof($allClassesWithThisShortname) == 1) {
					$fieldMappings[$fieldShortname] = $fvTableAlias . "." . $fieldShortname;
				} else {
					$fieldSpec = "CASE";
					foreach($allClassesWithThisShortname as $cs) {
						$cfvTableAlias = $fvTablesAliases[$cs];
						$tfSpec = $cfvTableAlias . "." . $fieldShortname;
						$fieldSpec .= " WHEN " . $tfSpec . " IS NOT NULL THEN " . $tfSpec;
					}
					$fieldSpec .= " ELSE NULL";
					$fieldSpec .= " END";

					$fieldMappings[$fieldShortname] = $fieldSpec;
				}
			}
		}

		return $fieldMappings;
	}

	function getAllClassesHavingShortname($classesShortnames, $fieldShortname) {
		$allClassesWithThisShortname = array();
		foreach($classesShortnames as $classShortname) {
			$classShortname = trim($classShortname);
			$nodeClass = & NodeClass::findByShortname($classShortname);

			$fieldShortnames = array_keys($nodeClass->fieldDefs);
			if (in_array($fieldShortname, $fieldShortnames)) {
				$allClassesWithThisShortname[] = $classShortname;
			}
		}

		return $allClassesWithThisShortname;
	}

	function compileUsingJoins() {
		//create mappings and join specs for base table

		$sqlFromSpec = "";
		$sqlJoinSpecs = array();

		$classesAliasesFieldMappings = array();
		$classesAliasesNodeTablesMappings = array();

		$whereClauseParts = array();

		if (_isTableDefClassSpec($this->dbmQLbaseTableDef)) {
			$baseTableClassesShortnames = _getClassesShortnamesFromTableSpec($this->dbmQLbaseTableDef);

			$baseTablePrefix = "base";
			$baseTableNodesTableAlias = $baseTablePrefix . "_n";

			$sqlFromSpec = "dbm_nodes " . $baseTableNodesTableAlias;

			$baseTableFieldMappings = array();

			$baseJoinedFVTablesAliases = array();

			$baseTableClassesIDs = array();

			$i = 0;

			foreach($baseTableClassesShortnames as $classShortname) {
				$nodeClass = & NodeClass::findByShortname(trim($classShortname));
				$baseTableClassesIDs[] = $nodeClass->id;

				$joinedTableAlias = $baseTablePrefix . "_fv_" . $i;

				$baseJoinedFVTablesAliases[$classShortname] = $joinedTableAlias;

				$nodeClassFieldsCount = sizeof($nodeClass->fieldDefs);

				if($nodeClassFieldsCount > 0) {
					$joinSpec = new JoinSpec(
					((sizeof($baseTableClassesShortnames) > 1) ? "LEFT" : ""),
					$nodeClass->getNFVTableName(),
					$joinedTableAlias,
					$joinedTableAlias . ".node_id=" . $baseTableNodesTableAlias . ".id"
					);

					$sqlJoinSpecs[] = $joinSpec;
				}

				$i++;
			}

			$fieldMappings = NodeQuery::createFieldMappings($baseTableClassesShortnames, $baseTableNodesTableAlias, $baseJoinedFVTablesAliases);

			if (sizeof($baseTableClassesShortnames) > 1) {
				$whereClauseParts[] = $baseTableNodesTableAlias . ".class_id" . " IN (" . implode(", ", $baseTableClassesIDs) . ")";
			} else if (sizeof($baseTableClassesShortnames) == 1) {
				if (sizeof($sqlJoinSpecs) == 0) {
					$whereClauseParts[] = $baseTableNodesTableAlias . ".class_id=" . $baseTableClassesIDs[0];
				}
			}

			$classesAliasesFieldMappings[$this->dbmQLbaseTableAlias] = $fieldMappings;
			$classesAliasesNodeTablesMappings[$this->dbmQLbaseTableAlias] = $baseTableNodesTableAlias;
		} else {
			$sqlFromSpec = $this->dbmQLbaseTableDef . " " . $this->dbmQLbaseTableAlias;
		}

		//create joing for newlinkfields
		$allTablesAliases = array($this->dbmQLbaseTableAlias => $this->dbmQLbaseTableDef);

		foreach($this->dbmQLjoinSpecs as $dbmJoinSpec) {
			$allTablesAliases[$dbmJoinSpec->tableAlias] = $dbmJoinSpec->tableDef;
		}

		foreach($this->dbmQLfieldsClause_AllClassFieldsParts as $tableAlias) {
			$tableDef = $allTablesAliases[$tableAlias];
			if (!_isTableDefClassSpec($tableDef)) {
				continue;
			}

			$alreadyCreatedLinkFieldJoins = array();
			$classesShortnames = _getClassesShortnamesFromTableSpec($tableDef);
			foreach($classesShortnames as $classShortname) {
				$nodeClass = & NodeClass::findByShortname(trim($classShortname));

				foreach($nodeClass->fieldDefs as $fieldDef) {
					$ft = $fieldDef->getFieldType();
					if (($ft->name == "NewLink") && !in_array($fieldDef->shortname, $alreadyCreatedLinkFieldJoins) && ($fieldDef->getParameterValue("lazy_loading") == 0)) {
						$alreadyCreatedLinkFieldJoins[] = $fieldDef->shortname;

						$allowedClasses = $fieldDef->getParameterValue("allowed_classes");
						if ($allowedClasses == "*") {
							die("NodeQuery: You can not use lazy loading and (allowed_classes = *). " . $classShortname . "." . $fieldDef->shortname);
						}

						$joinedTableAlias = $tableAlias ."___" . $fieldDef->shortname;
						$joinSpec = new JoinSpec(
							"LEFT",
							"{" . $allowedClasses . "}",
							$joinedTableAlias,
							$joinedTableAlias . ".id=" . $tableAlias . "." . $fieldDef->shortname
							);

							$this->dbmQLfieldsClause_AllClassFieldsParts[] = $joinedTableAlias;
							$this->dbmQLjoinSpecs[] = &$joinSpec;
					}
				}
			}
		}

		//convert dbmQL joins to real sql joins
		$joinSpecIndex = 0;

		foreach($this->dbmQLjoinSpecs as $dbmJoinSpec) {
			if (!_isTableDefClassSpec($dbmJoinSpec->tableDef)) {
				$sqlJoinSpecs[] = $dbmJoinSpec;

				continue;
			}

			$joinedTableClassesShortnames = _getClassesShortnamesFromTableSpec($dbmJoinSpec->tableDef);

			$joinedTablePrefix = "join_" . $joinSpecIndex;
			$joinedNodesTableAlias = $joinedTablePrefix . "_n";

			$joinedJoinedFVTablesAliases = array();

			$i = 0;

			$joinedTablesJoinSpecs = array();

			$joinedTableClassesIDs = array();
			foreach($joinedTableClassesShortnames as $classShortname) {
				$nodeClass = NodeClass::findByShortname(trim($classShortname));
				$joinedTableClassesIDs[] = $nodeClass->id;

				$joinedTableAlias = $joinedTablePrefix  . "_fv_" . $i;

				$joinedJoinedFVTablesAliases[$classShortname] = $joinedTableAlias;

				$nodeClassFieldsCount = sizeof($nodeClass->fieldDefs);

				if($nodeClassFieldsCount > 0) {
					$joinSpec = new JoinSpec(
					((sizeof($joinedTableClassesShortnames) > 1) ? "LEFT" : ""),
					$nodeClass->getNFVTableName(),
					$joinedTableAlias,
					$joinedTableAlias . ".node_id=" . $joinedNodesTableAlias . ".id"
					);

					$joinedTablesJoinSpecs[] = &$joinSpec;
				}

				$i++;
			}

			$joinedNodesTableJoinSpec = new JoinSpec(
			$dbmJoinSpec->joinType,
					"dbm_nodes",
					$joinedNodesTableAlias,
					$dbmJoinSpec->joinCondition
					);

					if (sizeof($joinedTableClassesShortnames) > 1) {
						$joinedNodesTableJoinSpec->joinCondition .= " AND " . $joinedNodesTableAlias . ".class_id" . " IN (" . implode(", ", $joinedTableClassesIDs) . ")";
					} else if (sizeof($joinedTableClassesShortnames) == 1) {
						if (sizeof($joinedTablesJoinSpecs) == 0) {
							$joinedNodesTableJoinSpec->joinCondition .= " AND " . $joinedNodesTableAlias . ".class_id" . "=" . $joinedTableClassesIDs[0];
						}
					}

					$sqlJoinSpecs[] = $joinedNodesTableJoinSpec;

					$sqlJoinSpecs = array_merge($sqlJoinSpecs, $joinedTablesJoinSpecs);

					$fieldMappings = NodeQuery::createFieldMappings($joinedTableClassesShortnames, $joinedNodesTableAlias, $joinedJoinedFVTablesAliases);
					$classesAliasesFieldMappings[$dbmJoinSpec->tableAlias] = $fieldMappings;
					$classesAliasesNodeTablesMappings[$dbmJoinSpec->tableAlias] = $joinedNodesTableAlias;

					$joinSpecIndex++;
		}

		$this->classesAliasesFieldMappings = $classesAliasesFieldMappings;
		$this->classesAliasesNodeTablesMappings = $classesAliasesNodeTablesMappings;

		//create sql fields clause

		$sqlFielsClauseParts = array();

		global $_node_table_fields_;

		foreach($this->dbmQLfieldsClause_AllClassFieldsParts as $tableAlias) {
			if ($classesAliasesFieldMappings[$tableAlias] === null) {
				_dump($tableAlias);
				_dump($classesAliasesFieldMappings[$tableAlias]);
				_dump($classesAliasesFieldMappings);
				die("!!!");
			}
			$prefix = $tableAlias . "___";
			$nodeTable = $classesAliasesNodeTablesMappings[$tableAlias];
			foreach($_node_table_fields_ as $ntf) {
				$sqlField = $nodeTable . "." . $ntf . " AS " . $prefix . $ntf;

				$sqlFielsClauseParts[] = $sqlField;
			}

			foreach($classesAliasesFieldMappings[$tableAlias] as $field => $fieldSpec) {
				$sqlField = $fieldSpec . " AS " . $prefix . $field;

				$sqlFielsClauseParts[] = $sqlField;
			}
		}

		foreach($this->dbmQLfieldsClause_OtherParts as $dbmQLField) {
			if (preg_match("/^COUNT\(([\w]+)\.\*\)/", $dbmQLField, $matches)) {
				$tableAlias = $matches[1];

				$sqlField = "COUNT(" . $classesAliasesNodeTablesMappings[$tableAlias] . ".id)";

				$sqlFielsClauseParts[] = $sqlField;
			} else if (preg_match("/^([\w]+)\.([\w]+)$/", $dbmQLField, $matches)) {
				$tableAlias = $matches[1];
				$field = $matches[2];
				if (array_key_exists($tableAlias, $this->classesAliasesNodeTablesMappings)) {
					if (_isFieldOfNodesTable($field)) {
						$sqlField = $classesAliasesNodeTablesMappings[$tableAlias] . "." . $field;
					} else {
						$sqlField = $classesAliasesFieldMappings[$tableAlias][$field];
					}
					$sqlField .=  " AS " . $field;

					$sqlFielsClauseParts[] = $sqlField;
				} else {
					$sqlFielsClauseParts[] = $tableAlias . "." . $field;
				}
			} else {
				$sqlField = $this->replaceTableAliasesFieldsByJoinedSpecs($dbmQLField);

				$sqlFielsClauseParts[] = $sqlField;
			}
		}

		for($i=0; $i < sizeof($sqlJoinSpecs); $i++) {
			$joinSpec = $sqlJoinSpecs[$i];
			$joinSpec->joinCondition = $this->replaceTableAliasesFieldsByJoinedSpecs($joinSpec->joinCondition);
		}

		if ($this->dbmQLwhereClause) {
			$whereClause = $this->replaceTableAliasesFieldsByJoinedSpecs($this->dbmQLwhereClause);
			$whereClauseParts[] = $whereClause;
		}

		if ($this->groupId != -1) {
			$joinSpec = new JoinSpec(
					"",
					"dbm_group_rights",
					"gr",
					"base_n.id=gr.node_id"
					);
					$sqlJoinSpecs[] = $joinSpec;
					$whereClauseParts[] = "gr.group_id=" . $this->groupId . " AND gr.rights&" . VIEW_RIGHT_MASK . "!=0";
		}

		$groupByClauseParts = array();

		foreach($this->dbmQLgroupByClauseParts as $gbp) {
			$groupByClauseParts[] = $this->replaceTableAliasesFieldsByJoinedSpecs($gbp);
		}
		$groupByClause = implode(",", $groupByClauseParts);
		$havingClause = $this->replaceTableAliasesFieldsByJoinedSpecs($this->dbmQLhavingClause);
		$orderByClause = $this->replaceTableAliasesFieldsByJoinedSpecs($this->dbmQLorderByClause);

		//_dump($sqlJoinSpecs);
		//_dump($sqlFielsClauseParts);
		//_dump($whereClauseParts);
		//_dump($classesAliasesFieldMappings);
		//_dump($classesAliasesNodeTablesMappings);

		$sql =
			"SELECT " .
			$this->dbmQLdistinctClause .
			" " .
			implode("\n, ", $sqlFielsClauseParts) .
			"\nFROM\n" .
			$sqlFromSpec
			;

			for ($i=0; $i<sizeof($sqlJoinSpecs); $i++) {
				$joinSpec = $sqlJoinSpecs[$i];

				$sql .=
				"\n" .
				($joinSpec->joinType ? $joinSpec->joinType . " " : "") .
				"JOIN " .
				$joinSpec->tableDef .
				" " .
				$joinSpec->tableAlias .
				" ON " .
				$joinSpec->joinCondition
				;
			}

			if(sizeof($whereClauseParts) > 0) {
				$sql .=
				"\nWHERE (" .
				implode(") AND (", $whereClauseParts) .
				")"
				;
			}

			if($groupByClause) {
				$sql .=
				"\nGROUP BY " .
				$groupByClause
				;
			}

			if($havingClause) {
				$sql .=
				"\nHAVING " .
				$havingClause
				;
			}

			if($orderByClause) {
				$sql .=
				"\nORDER BY " .
				$orderByClause
				;
			}

			if($this->dbmQLlimitClause) {
				$this->sqlLimitClause =
				"\nLIMIT " .
				$this->dbmQLlimitClause
				;
			}

			$this->sql = "\n" . $sql . "\n";
	}

	function replaceTableAliasesFieldsByJoinedSpecs($s) {
		
		$resultStr = "";

		$QUOTE_CHARACTER = "'";

		$tokenStr = "";
		for ($j = 0; $j < strlen($s); $j++) {
			$c = $s{$j};

			if ($c == $QUOTE_CHARACTER) {
				
				$tokenStr .= $QUOTE_CHARACTER;

				for ($j++; $j < strlen($s) && $s{$j} != "'"; $j++)
				$tokenStr .= $s{$j};

				$tokenStr .= $QUOTE_CHARACTER;
			} else if (_isSQLDelimiter($c)) {
				$tokenStr = $this->replaceTableAliasesFieldsByJoinedSpecsForToken($tokenStr);

				$resultStr .= $tokenStr . $c;
				$tokenStr = "";
			} else {
				$tokenStr .= $c;
			}
		}

		$tokenStr = $this->replaceTableAliasesFieldsByJoinedSpecsForToken($tokenStr);
		$resultStr .= $tokenStr;
		
		return $resultStr;
	}

	function replaceTableAliasesFieldsByJoinedSpecsForToken($tokenStr) {
		if (preg_match("/^([\w]+)\.([\w]+)/", $tokenStr, $matches)) {
			$tableAlias = $matches[1];
			$field = $matches[2];
			if (array_key_exists($tableAlias, $this->classesAliasesNodeTablesMappings)) {
				if (_isFieldOfNodesTable($field)) {
					$tokenStr = $this->classesAliasesNodeTablesMappings[$tableAlias] . "." . $field;
				} else {
					$tokenStr = $this->classesAliasesFieldMappings[$tableAlias][$field];
				}
			}
		}
		return $tokenStr;
	}
}



function _isTableDefClassSpec($tableDef) {
	return ((strpos($tableDef, "{") === 0) && (strpos($tableDef, "}") === (strlen($tableDef) - 1)));
}

function _getClassesShortnamesFromTableSpec($tableDef) {
	return explode(",", substr($tableDef, 1, strlen($tableDef) - 2));;
}


global $_node_table_fields_;
$_node_table_fields_ = array(
	"id", "name", "shortname", "dynamic_template", "class_id",
	"parent_id", "owner", "admin_url", "subtree_order", "absolute_path", "time_created", "time_updated"
	);

	function _isSQLDelimiter($c) {
		global $_sql_delimiters_;
		return in_array($c, $_sql_delimiters_);
	}

	global $_sql_delimiters_;
	$_sql_delimiters_ = array(
	"(", ")", "=", ":", ",",
	"+", "-", "*", "/",
	"!", "|", "^", "&", ">", "<",
	" ", "\n", "\r", "\t"
	);

	function _isFieldOfNodesTable($fieldShortname) {
		global $_node_table_fields_;
		return (in_array($fieldShortname, $_node_table_fields_));
	}

?>