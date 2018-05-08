<?php

require_once ("sql/mysql/Connection.php");
require_once ("sql/mysql/ResultSet.php");

DEFINE("SQL_DATE_FORMAT", "Y-m-d H:i:s");
DEFINE("SQL_DATEONLY_FORMAT", "Y-m-d");

class Statement {
	/**
	 * @var Connection
	 */
	var $connection = null;
	/**
	 * @var ResultSet
	 */
	var $rSet = null;
	/**
	 * @var integer
	 */
	var $uCount = -1;
	/**
	 * @param Connection $connection
	 * @return Statement
	 */
	function Statement(&$connection) {
		$this->connection = &$connection;
	}

	/**
	 * @param string $sql
	 * @return boolean
	 */
	function execute($sql) {
		$res = mysqli_query($this->connection->dblink, $sql);
		if (is_object($res)) {
			$this->rSet = new ResultSet($res);
			$this->uCount = -1;
			$ret = true;
		} else if ($res === true) {
			$this->rSet = null;
			$this->uCount = mysqli_affected_rows($this->connection->dblink);
			$ret = true;
		} else {
			printf("Errormessage: %s\n", mysqli_error($this->connection->dblink) . '<br>' . $sql);
			die();
		}
		return $ret;
	}

	/**
	 * @return ResultSet
	 */
	function& getResultSet() {
		return $this->rSet;
	}

	/**
	 * @return ResultSet
	 */
	function& executeQuery($sql) {
		$this->execute($sql);
		if ($this->rSet == null) {
			die("Invalid call to executeQuery: sql was of update type");
		}
		return $this->rSet;
	}

	/**
	 * @return integer
	 */
	function executeUpdate($sql) {
		$this->execute($sql);
		if ($this->rSet != null) {
			die("Invalid call to executeUpdate: sql was of query type");
		}
		return $this->uCount;
	}

	/**
	 * @return integer
	 */
	function getUpdateCount() {
		return $this->uCount;
	}

	function close() {
		if ($this->rSet != null) {
			$this->rSet->close();
			$this->rSet = null;
		}
	}
}

class PreparedStatement {
	/**
	 * @var Connection
	 */
	var $connection = null;
	/**
	 * @var ResultSet
	 */
	var $rSet = null;
	/**
	 * @var integer
	 */
	var $uCount = -1;

	var $numValues = 0;
	var $parts = array();
	var $values = array();

	/**
	 * @param Connection $connection
	 * @param string $sql
	 * @return PreparedStatement
	 */
	function PreparedStatement(&$connection, $sql) {
		if ($sql == null) {
			die("PreparedStatement: query is missing");
		}
		$this->_construct($sql);
		$this->connection = &$connection;
	}

	function _construct($sql) {
		$currentPart = "";
		$strLen = strlen($sql);
		for ($j = 0; $j < $strLen; $j++) {
			$c = $sql{$j};
			if ($c == '?') {
				$this->parts[] = $currentPart;
				$currentPart = "";
			} else if ($c == "'") {
				$currentPart .= "'";
				for ($j++; $j < $strLen && $sql{$j} != "'"; $j++) {
					$currentPart .= $sql{$j};
				}

				$currentPart .= "'";
			} else {
				$currentPart .= $sql{$j};
			}
		}
		$this->parts[] = $currentPart;
		$this->numValues = sizeof($this->parts) - 1;
	}

	function checkIndex($i) {
		if ($i < 1 || $i > $this->numValues) {
			die("PreparedStatement::checkIndex() invalid index - $i.");
		}
	}

	function setObject($i, &$obj) {
		$this->checkIndex($i);
		if ($obj === null) {
			$this->setNull($i);
		} elseif (is_bool($obj)) {
			$this->setBoolean($i, $obj);
		} elseif (is_int($obj)) {
			$this->setInt($i, $obj);
		} elseif (is_float($obj)) {
			$this->setFloat($i, $obj);
		} elseif (is_string($obj)) {
			$this->setString($i, $obj);
        /** ----- Godjatsky EDIT ------ **/
        } elseif (is_object($obj) && getClassNameLowercase($obj) == 'stdclass') {
			$this->setDateTime($i, $obj);
        /** --------- END ---------- **/ 
		} elseif (is_object($obj) && getClassNameLowercase($obj) == 'date') {
			$this->setDate($i, $obj);
		} else {
			$this->setString($i, (string)$obj);
		}
	}

	function setNull($i) {
		$this->checkIndex($i);
		$this->values[$i] = "null";
	}

	function setBoolean($i, $boolean) {
		$this->checkIndex($i);
		$boolean = (boolean)$boolean;
		$this->values[$i] = ($boolean ? "1" : "0");
	}

	function setInt($i, $int) {
		$this->checkIndex($i);
		$int = (int)$int;
		$this->values[$i] = "".$int;
	}

	function setFloat($i, $float) {
		$this->checkIndex($i);
		$float = (float)$float;
		$this->values[$i] = "".$float;
	}

	function setString($i, $s) {
		$this->checkIndex($i);
		$sql = "";
		if ($s === null) {
			$this->setNull($i);
		} else {
			$sql .= "'";
			//			$j = strlen($s);
			//			for ($k = 0; $k < $j; $k++) {
			//				$c = $s{$k};
			//				if ($c == "'")
			//					$sql .= "\\'";
			//				else if ($c == '"')
			//					$sql .= "\\\"";
			//				else if (ord($c) == 0)
			//					$sql .= "\\0";
			//				else if ($c == "\\") {
			//					$sql .= "\\\\";
			//				} else
			//					$sql .= $c;
			//			}
			$sql .= mysqli_real_escape_string($this->connection->dblink, $s);

			$sql .= "'";
			$this->values[$i] = $sql;
		}
	}

	function setDate($i, &$date) {
		$this->checkIndex($i);
		if ($date === null) {
			$this->setNull($i);
		} else {
			if (!is_object($date) || getClassNameLowercase($date) != 'date' ) {
				die("Invalid argument for setDate");
			}
            //_dump($this->values[$i]->time);
            //$this->values[$i] = "'".date(SQL_DATE_FORMAT, $this->values[$i]->time)."'";
			$this->values[$i] = "'".$date->format(SQL_DATE_FORMAT)."'";
		}
	}
    
    /** ----- Godjatsky EDIT ------ **/
	function setDateTime($i, &$date) {
		$this->checkIndex($i);
		if ($date === null) {
			$this->setNull($i);
		} else {
			if (!is_object($date) || getClassNameLowercase($date) != 'stdclass' ) {
				die("Invalid argument for setDate");
			}
            //$this->values[$i] = "'".date(SQL_DATE_FORMAT, $this->values[$i]->time)."'";
			$this->values[$i] = "'".date(SQL_DATE_FORMAT, $date->time)."'";
		}
	}
    /** -------- END ---------- **/

	function _makeSQL() {
		if (sizeof($this->values) != $this->numValues) {
			die("PreparedStatement:execute()  not all ? params was set.");
		}
		$sql = "";
		for ($i = 0; $i < $this->numValues; $i++) {
			$sql .= $this->parts[$i].$this->values[$i+1];
		}
		$sql .= $this->parts[$i];

		return $sql;
	}

	/**
	 * @param unknown_type $sql
	 * @return boolean
	 */
	function execute() {
		$sql = $this->_makeSQL();
		$res = mysqli_query($this->connection->dblink, $sql);
		if (is_object($res)) {
			$this->rSet = new ResultSet($res);
			$this->uCount = -1;
			$ret = true;
		} else if ($res === true) {
			$this->rSet = null;
			$this->uCount = mysqli_affected_rows($this->connection->dblink);
			$ret = true;
		} else {
			printf("Errormessage: %s\n", mysqli_error($this->connection->dblink) . '<br>' . $sql);
			die();
		}
		return $ret;
	}

	/**
	 * @return ResultSet
	 */
	function& getResultSet() {
		return $this->rSet;
	}

	/**
	 * @return ResultSet
	 */
	function& executeQuery() {
		$sql = $this->_makeSQL();
		$this->execute($sql);
		if ($this->rSet == null) {
			die("Invalid call to executeQuery: sql was of update type");
		}
		return $this->rSet;
	}

	/**
	 * @return integer
	 */
	function executeUpdate() {
		$sql = $this->_makeSQL();
		$this->execute($sql);
		if ($this->rSet != null) {
			die("Invalid call to executeUpdate: sql was of query type");
		}
		return $this->uCount;
	}

	/**
	 * @return integer
	 */
	function getUpdateCount() {
		return $this->uCount;
	}

	function close() {
		if ($this->rSet != null) {
			$this->rSet->close();
			$this->rSet = null;
		}
	}
}
?>