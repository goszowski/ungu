<?

class HTTPRequest {
    var $parameters = array();
    var $attributes = array();

    var $parametersPrefix = "";

    function prepare() {
        foreach ($_GET as $k=>$v ) {
        	if (get_magic_quotes_gpc() == 1) {
        		if (!is_array($v)) {
            		$this->parameters[$k] = stripslashes($v);
            	} else {
            		$this->parameters[$k] = array();
            		foreach ($v as $_k=>$_v) {
            			$this->parameters[$k][$_k] = stripslashes($_v);
            		}
            	}
            } else {
            	$this->parameters[$k] = $_GET[$k];
            }
        }
        foreach ($_POST as $k=>$v ) {
        	if (get_magic_quotes_gpc() == 1) {
            	if (!is_array($v)) {
            		$this->parameters[$k] = stripslashes($v);
            	} else {
            		$this->parameters[$k] = array();
            		foreach ($v as $_k => $_v) {
            			if (!is_array($_v)) {
            				$this->parameters[$k][$_k] = stripslashes($_v);
            			} else {
            				$this->parameters[$k][$_k] = array();
            				foreach ($_v as $__k => $__v) {
            					$this->parameters[$k][$_k][$__k] = stripslashes($__v);
            				}
            			}
            		}
            	}
            } else {
	            $this->parameters[$k] = $_POST[$k];
	        }
        }

        foreach ($_FILES as $pfname => $pf){
            if( is_array($pf["name"]) ){
                $this->parameters[$pfname] = array();
                for($i = 0; $i < sizeof($pf["name"]); $i++){
                    $this->parameters[$pfname][$i] = new UploadedFile(
                        array( "name" => $pf["name"][$i], "type" => $pf["type"][$i],
                                "tmp_name" => $pf["tmp_name"][$i], "size" => $pf["size"][$i] ) );
                }
            }else{
                $this->parameters[$pfname] = new UploadedFile($pf);
            }
        }
    }

    function getParameter($paramName){
    	$paramName = $this->parametersPrefix . $paramName;
    	if (array_key_exists($paramName, $this->parameters)) {
        	return $this->parameters[$paramName];
        } else {
        	$res = null;
        	return $res;
        }
    }

	function getAttribute($attName) {
		return $this->attributes[$attName];
	}

	function setAttribute($attName, $value) {
		$this->attributes[$attName] = $value;
	}

    function getServerName(){
        return $_SERVER["SERVER_NAME"];
    }

    function getServerPort(){
        return $_SERVER["SERVER_PORT"];
    }

    function getParametersList() {
        return array_keys($this->parameters);
    }

    function getCookie ( $name ) {
        $cookieValue = $_COOKIE[$name];
        $cookie = null;
        if($cookieValue) {
            $cookie = $cookieValue;
        }

        return $cookie;
    }
}

class UploadedFile extends PHPObject{
    var $name = "";
    var $type = "";
    var $tmp_name = "";
    var $size = 0;

    function UploadedFile($vals = null) {
        if($vals != null) {
            $this->name = $vals["name"];
            $this->type = $vals["type"];
            $this->tmp_name = $vals["tmp_name"];
            $this->size = $vals["size"];
        }
     }

    function isValid(){
        return is_uploaded_file($this->tmp_name);
    }

    function isEmpty()  {
        return ( ( ($this->name == "none")  ||  ( $this->name == "" ) ) and ($this->size == 0) );
    }

    function isImage() {
        return ereg("^image/", $this->type);
    }

    function moveTo($destDir, $destName = null) {
        if($destName == null ){
            $destName = $this->name;
        }
        $ret = move_uploaded_file($this->tmp_name, trim("$destDir/$destName"));
        @chmod(trim("$destDir/$destName"), 0644);
        return $ret;
    }
}

/**
 * HTTP Session
 * Provides a way to identify a user across more than one page request or visit to a Web site and to store
 * information about that user.
 * This interface allows servlets to
 *
 * View and manipulate information about a session, such as the session identifier, creation time, and last accessed time
 * Bind objects to sessions, allowing user information to persist across multiple user connections
 * @package base
 */
class HTTPSession {
    /**
     * String containing the unique identifier assigned to this session
     * @access public
     * @var id string
     */
    var $id = "";
    /**
     * Variable name on client side, stores the session ID
     * @access public
     * @var creationTime integer timestamp
     */
    var $name = "";

    /**
     * Constructor
     *
     * Reads session id from request, then if it's valid, reads all session params from database,
     * changes lastAccessedTime to now
     * Else delete old session(if it exists) and creates new session
     * Then tries to sen cookie to remote mashine
     * @param confParams array config. params from conf.xml
     */
    function HTTPSession ( $confParams ) {
        session_name($confParams["name"]);
        session_start();
        $this->id = session_id();
        $this->name = session_name();
    }

    /**
     * Binds an object to this session, using the name specified.
     * If an object of the same name is already bound to the session, the object is replaced.
     * @param name string the name to which the object is bound; cannot be null
     * @param value mixed the object to be bound; cannot be null
     * @access public
     * @return void
     */
    function setAttribute ( $name, &$value ) {
        if(empty($_SESSION[$name])) {
            $_SESSION[$name] = $value;
        }
        $_SESSION[$name] = $value;
    }

    /**
     * Get attribute from session
     * @access public
     * @param name string the name of the object is unbound
     * @return mixed
     */
    function getAttribute ( $name ) {
        if(! empty($_SESSION[$name])) {
            return $_SESSION[$name];
        }
        return null;
    }

    /**
     * Removes attributes from session
     * @param name string the name of the object is unbound
     * @access public
     * @return void
     */
    function removeAttribute ( $name ) {
        global $HTTP_SESSION_VARS;
        unset($_SESSION[$name]);
		unset($HTTP_SESSION_VARS[$name]);
    }

    /**
     * Delete session and all data inside it
     * @access public
     * @return void
     */
    function invalidate () {
        return session_destroy ();
    }
}

global $request;

if ($request == null) {
	$request = new HTTPRequest();
	$request->prepare();
}

$sessParams = array("name"=>"PHPSESSID");
$session = new HTTPSession( $sessParams ) ;

?>