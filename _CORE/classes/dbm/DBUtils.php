<?php

$_TRANSACTION_STATUS = array();

class DBUtils {
	function getConnection() {
		global $connection;
		return $connection;
	}
	public static function getNextSequnceID($seqName) {
		global $connection;

		global $connection, $_TRANSACTION_STATUS;
		if (sizeof($_TRANSACTION_STATUS) != 0) {
			$query = "SELECT value FROM dbm_sequences WHERE name='$seqName' FOR UPDATE";
		} else {
			$query = "SELECT value FROM dbm_sequences WHERE name='$seqName'";
		}
		$stmt =& $connection->createStatement();
		$rs =& $stmt->executeQuery($query);
		if ($rs->next()) {
			$value = $rs->getInt(1);
		} else {
			$value = 0;
		}
		$value++;
		$stmt->close();

		$stmt =& $connection->createStatement();
		$stmt->executeUpdate("REPLACE INTO dbm_sequences SET value=$value, name='$seqName'");
		$stmt->close();

		return $value;
	}

	public static function getCurrentSequnceID($seqName) {
		global $connection;

		$stmt =& $connection->createStatement();
		$rs =& $stmt->executeQuery("SELECT value FROM dbm_sequences WHERE name='$seqName'");
		$rs->next();
		$value = $rs->getInt(0);
		$stmt->close();

		return $value;
	}

	/**
	 * @return ResultSet
	 */
	public static function& execSelect($sql, $params = array()) {
		global $connection;
		//_dump($sql);
		if (sizeof($params) == 0) {
			$stmt =& $connection->createStatement();
			$rs =& $stmt->executeQuery($sql);

			$crs =& $rs->getCachedResultSet();

			$stmt->close();
			return $crs;	
		} else {
			$pstmt =& $connection->prepareStatement($sql);
			foreach ($params as $i=>$p) {
				$pstmt->setObject($i + 1, $p);
			}
			$rs =& $pstmt->executeQuery();

			$crs =& $rs->getCachedResultSet();

			$pstmt->close();
			return $crs;
		}
	}

	/**
	 * @param string $sql
	 * @param mixed $params
	 * @return integer
	 */
	public static function execCountSelect($sql, $params = array()) {
		global $connection;

		if (sizeof($params) == 0) {
			$stmt =& $connection->createStatement();
			$rs =& $stmt->executeQuery($sql);
			$rs->next();
			$c = $rs->getInt(1);
			$stmt->close();

			return $c;
		} else {
			$pstmt =& $connection->prepareStatement($sql);
			foreach ($params as $i=>$p) {
				$pstmt->setObject($i + 1, $p);
			}
			$rs =& $pstmt->executeQuery();
			$rs->next();
			$c = $rs->getInt(1);
			$pstmt->close();

			return $c;
		}
	}

	public static function execUpdate($sql, $params = array()) {
		global $connection;

		if (sizeof($params) == 0) {
			$stmt =& $connection->createStatement();
			$updatedCount = $stmt->executeUpdate($sql);
			$stmt->close();
			return $updatedCount;
		} else {
			$pstmt =& $connection->prepareStatement($sql);
			foreach ($params as $i=>$p) {
				$pstmt->setObject($i + 1, $p);
			}
			$updatedCount = $pstmt->executeUpdate();

			$pstmt->close();
			return $updatedCount;
		}
	}

	public static function startTransaction() {
		global $connection, $_TRANSACTION_STATUS;
		if (sizeof($_TRANSACTION_STATUS) == 0) {
			$connection->setAutoCommit(false);
			array_push($_TRANSACTION_STATUS, "COMMIT");
		} else {
			array_push($_TRANSACTION_STATUS, "");
		}
	}

	public static function commit() {
		global $connection, $_TRANSACTION_STATUS;

		if (sizeof($_TRANSACTION_STATUS) == 1) {
			$connection->commit();
		}
		array_pop($_TRANSACTION_STATUS);
	}

	public static function rollback($force = false) {
		global $connection, $_TRANSACTION_STATUS;

		if ($force) {
			if (sizeof($_TRANSACTION_STATUS) > 0) {
				$connection->rollback();
				$_TRANSACTION_STATUS = array();
			}
		} else {
			if (sizeof($_TRANSACTION_STATUS) == 1) {
				$connection->rollback();
			} else {
				$_TRANSACTION_STATUS[0] = "ROLLBACK";
			}
			array_pop($_TRANSACTION_STATUS);
		}
	}

	public static function execBatch($sqls, $params = array()) {
		global $connection;

		foreach ($sqls as $i=>$sql) {
			$pstmt =& $connection->prepareStatement($sql);
			foreach ($params[$i] as $j=>$param) {
				$pstmt->setObject($j + 1, $param);
			}
			$updatedCount = $pstmt->executeUpdate();
			$pstmt->close();
		}
	}
}

?>