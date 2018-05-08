<?php

require_once ("sql/mysql/Connection.php");
require_once ("sql/mysql/PreparedStatement.php");

/**
 * Database query result Set
 * @package sql
 */
class ResultSet {
	var $result_id = null;
	var $rowCount = -1;

	var $columnIndexes = array();
	var $columnNames = array();
	var $columnTypeNames = array();
	var $columnCount = -1;

	var $currentRowIndex = -1;
	var $currentRow = array();

	function ResultSet (&$res_id) {
		$this->result_id = &$res_id;
		$this->rowCount = mysqli_num_rows($this->result_id);
		$this->columnCount = mysqli_num_fields($this->result_id);
		for ($i = 0; $i < $this->columnCount; $i++) {
			//_dump(mysqli_fetch_fields($this->result_id));
			$this->columnTypeNames[$i] = mysqli_fetch_fields($this->result_id)[$i]->type;
			$this->columnNames[$i] =mysqli_fetch_fields($this->result_id)[$i]->name;
			$this->columnIndexes[$this->columnNames[$i]] = $i;
		}
	}

	function close() {
		mysqli_free_result($this->result_id);
	}

	function absolute($i) {
		$i = (int)$i;
		if ($i < 0 || $i >= $this->rowCount) {
			die("ResultSet::absolute()   Invalid index - $i");
		}
		$this->currentRowIndex = $i;
	}

	function beforeFirst() {
		$this->currentRowIndex = -1;
	}

	function afterLast() {
		$this->currentRowIndex = $this->rowCount;
	}

	function first() {
		$this->beforeFirst();
		return $this->next();
	}

	function last() {
		$this->afterLast();
		return $this->previous();
	}

	/**
	 * @return boolean
	 */
	function next() {
		$this->currentRowIndex++;
		if ($this->currentRowIndex < $this->rowCount) {
			$this->fetchRow();
			return true;
		} else {
			return false;
		}
	}

	function previous() {
		$this->currentRowIndex--;
		if ($this->currentRowIndex >= 0) {
			$this->fetchRow();
			return true;
		} else {
			return false;
		}
	}

	function fetchRow() {
		mysqli_data_seek($this->result_id, $this->currentRowIndex);
		$this->currentRow = mysqli_fetch_row($this->result_id);
	}

	function checkColumnIndex(&$i) {
		if (!is_int($i)) {
			$i = $this->columnIndexes[(string)$i];
		} else {
			$i--;
		}
		if ($i < 0 || $i >= $this->columnCount) {
			die("ResultSet::checkColumnIndex() invalid index '$i'.");
		}
	}

	function& getObject($i) {
		$this->checkColumnIndex($i);

		switch ($this->columnTypeNames[$i]) {
			case "int" :
				$res = $this->getInt($i+1);
			case "real" :
				$res = $this->getFloat($i+1);
			case "string" :
			case "blob" :
				$res = $this->getString($i+1);
			case "datetime" :
			case "date" :
				$res = &$this->getDate($i+1);
			default:
				//_dump($i);
				//echo "!!!";
				//_dump($this->columnTypeNames[$i]);
				//echo "!!!";
				$res = &$this->currentRow[$i];
		}
		return $res;
	}

	function getInt($i) {
		$this->checkColumnIndex($i);
		return (int)$this->currentRow[$i];
	}

	function getFloat($i) {
		$this->checkColumnIndex($i);
		return (float)$this->currentRow[$i];
	}

	function getBoolean($i) {
		$this->checkColumnIndex($i);
		return ((int)$this->currentRow[$i] == 1);
	}

	function getString($i) {
		$this->checkColumnIndex($i);
		return (string)$this->currentRow[$i];
	}

	function& getDate($i) {
		$this->checkColumnIndex($i);
		$date = new Date((string)$this->currentRow[$i]);
		return $date;
	}

	/**
	 * @return CachedResultSet
	 */
	function& getCachedResultSet() {
		$crs = new CachedResultSet($this);
		return $crs;
	}
}

class CachedResultSet extends ResultSet {
	var $rows = array();

	var $currentRow = array();

	/**
	 * @param ResultSet $rs
	 * @return CachedResultSet
	 */
	function CachedResultSet(&$rs) {
		$this->rowCount = $rs->rowCount;
		$this->columnIndexes = $rs->columnIndexes;
		$this->columnNames = $rs->columnNames;
		$this->columnTypeNames = $rs->columnTypeNames;
		$this->columnCount = $rs->columnCount;
		$this->currentRowIndex = -1;

		$this->rows = array();

		$rs->beforeFirst();
		while ($rs->next()) {
			$this->rows[$rs->currentRowIndex] = $rs->currentRow;
		}
	}

	function fetchRow() {
		$this->currentRow = &$this->rows[$this->currentRowIndex];
	}

	function close() { }
}

?>