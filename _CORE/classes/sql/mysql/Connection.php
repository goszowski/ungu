<?

require_once ("sql/mysql/ResultSet.php");
require_once ("sql/mysql/PreparedStatement.php");

class Connection {
	var $dblink = null;

	/**
	 * @param string $url
	 * @param mixed $properties
	 * @return Connection
	 */
	function Connection ($url, $properties=null) {
		$i = strpos($url, "://");
		if ($i === false)
			die("mysql Connection()   Bad url. [1]");
		$i += 3;
		$j = strpos($url, '/', $i);
		if ($j === false)
			die("mysql Connection()   Bad url. [2]");
		$port = 3306;
		$l = strpos($url, ':', $i);
		$host = "";
		if ($l !== false && $l < $j) {
			$host =  substr($url, $i, $l-$i);
			$port = (int)substr($url,$l + 1, $j-$l-1);
		} else {
			$host = substr($url, $i, $j-$i);
		}

		$paramsIndex = strpos($url, '?', $j);
		$dbname = "";
		if ($paramsIndex !== false) {
			$dbname = substr($url, $j + 1, $paramsIndex - $j - 1);
			$params = substr($url, $paramsIndex+1);

	        $fromIndex = 0;
	        for (;$fromIndex < strlen($params);) {
	            $indx = strpos($params, '&', $fromIndex);
            	$eindx = strpos($params, '=', $fromIndex);
	            if ($indx === false) {
	            	$properties[substr($params, $fromIndex, $eindx - $fromIndex)] = substr($params, $eindx+1);
	                break;
	            }
                $properties[substr($params, $fromIndex, $eindx-$fromIndex)] = substr($params, $eindx+1, $indx-$eindx-1);
	            $fromIndex = $indx+1;
	        }
		} else {
			$dbname = substr($url, $j + 1);
		}

		$user = null;
		$password = null;
		if ($properties != null) {
			$user = $properties["user"];
			$password = $properties["password"];
		}

		$this->dblink = mysqli_connect("$host:$port", $user, $password) or die("Can't connect to mysql server.");
		mysqli_select_db($this->dblink, $dbname) or die("Can't select db.");
	}

	/**
	 * @param string $sql
	 * @return PreparedStatement
	 */
	function& prepareStatement($sql) {
		$pstmt = new PreparedStatement($this, $sql);
		return $pstmt;
	}

	/**
	 * @return Statement
	 */
	function& createStatement() {
		$stmt = new Statement($this, null);
		return $stmt;
	}

    function close() {
        mysql_close($this->dblink);
    }

	function commit() {
		DBUtils::execUpdate("COMMIT");
	}

	function rollback() {
		DBUtils::execUpdate("ROLLBACK");
	}
	
	function setAutoCommit($mode) {
		if ($mode == false) {
			DBUtils::execUpdate("BEGIN");
		}
	}

	function setCharset($charset) {
		DBUtils::execUpdate("SET CHARACTER SET '".DB_CHARSET."'");
		DBUtils::execUpdate("SET NAMES '".DB_CHARSET."'");
	}
}

?>
