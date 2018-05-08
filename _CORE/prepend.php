<?

ini_set('mbstring.internal_encoding','UTF-8');

// error_reporting(E_ERROR | E_WARNING | E_PARSE);

error_reporting(0);
//error_reporting(E_ALL);
//ini_set("magic_quotes_gpc", "0");
//ini_set("register_globals", "0");
ini_set("memory_limit", "80M");
ini_set("max_execution_time", "30000");

if(strpos (PHP_OS, "WIN") !== false ) {
	DEFINE("SYSTEM_SEPARATOR", ";");
	DEFINE("DIR_SEPARATOR", "\\");
	DEFINE("FILE_SEPARATOR", "\\");
	DEFINE("NEWLINE", "\r\n");
} else {
	DEFINE("SYSTEM_SEPARATOR", ":");
	DEFINE("DIR_SEPARATOR", "/");
	DEFINE("FILE_SEPARATOR", "/");
	DEFINE("NEWLINE", "\n");
}

$DOC_ROOT = $_SERVER["DOCUMENT_ROOT"];

ini_set("include_path", ".".SYSTEM_SEPARATOR."$DOC_ROOT".SYSTEM_SEPARATOR."$DOC_ROOT/_CORE/classes");

DEFINE("FIELD_IMGLIB_DIR", $DOC_ROOT.DIR_SEPARATOR."imglib");
DEFINE("FIELD_SERVERFILES_DIR", $DOC_ROOT . DIR_SEPARATOR . "files");
DEFINE("FIELD_IMGLIB_THUMBNAILS_DIR", $DOC_ROOT.DIR_SEPARATOR."imglib_thumbnails");
DEFINE("FILE_FIELD_DIR", $DOC_ROOT.DIR_SEPARATOR."_files");


require_once ("sql/mysql/Connection.php");
require_once ("dbm/User.php");
require_once ("dbm/UserGroup.php");
require_once ("dbm/NodeClass.php");
require_once ("base/HTTP.php");
require_once ("base/Mail.php");
require_once ("base/Validator.php");
require_once ("base/HTMLWriter.php");
require_once ("dbm/DBUtils.php");
require_once($DOC_ROOT . "/_settings.php");
require_once ("dbm/messages_".LOCALE.".properties");
require_once ("dbm/ModeratedAction.php");

function getmicrotime() {
	list($usec, $sec) = explode(" ",microtime());
	return ((float)$usec + (float)$sec);
}

function _log($str) {
	global $___logFile;
	//$___logFile = fopen($_SERVER["DOCUMENT_ROOT"]."/sql.log", "a+");
	//fwrite($___logFile, date("Y-m-d h:i:s") . "\t" . $str . "\r\n");
	//fclose($___logFile);
}
function _log2($str) {
	global $___logFile;
	//$___logFile = fopen($_SERVER["DOCUMENT_ROOT"]."/sql.log", "a+");
	//fwrite($___logFile, date("Y-m-d h:i:s") . "\t" . $str . "\r\n");
	//fclose($___logFile);
}

$____st = getmicrotime();
$____pt = getmicrotime();

_log("\r\n\r\nScript start - " . $_SERVER['REQUEST_URI']);

function _logTimeFromStart($key = "") {
	global $____st, $____pt;
	$ct = getmicrotime();
	_log("Time from : prev. pt. - " . sprintf("%01.5f", ($ct - $____pt)) .
	",  script start - " . sprintf("%01.5f", ($ct - $____st)) .
	(strlen($key) == 0 ? "" : ", " . $key));
	$____pt = getmicrotime();
}

function _dump ( &$v ) {
    echo "<pre>";
    	var_dump($v);
    echo "</pre>";
}

function getClientIP() {
	if ($_SERVER['HTTP_CLIENT_IP']) {
		$ip = $_SERVER['HTTP_CLIENT_IP'];
	} elseif ($_SERVER['HTTP_X_FORWARDED_FOR']) {
		$ip = $_SERVER['HTTP_X_FORWARDED_FOR'];
	} elseif ($_SERVER['HTTP_X_FORWARDED']) {
		$ip = $_SERVER['HTTP_X_FORWARDED'];
	} elseif ($_SERVER['HTTP_FORWARDED_FOR']) {
		$ip = $_SERVER['HTTP_FORWARDED_FOR'];
	} elseif ($_SERVER['HTTP_FORWARDED']) {
		$ip = $_SERVER['HTTP_FORWARDED'];
	} else {
		$ip = $_SERVER['REMOTE_ADDR'];
	}

	return $ip;
}

function import( $package_name ) {
	_logTimeFromStart("import start");
	$packs = explode(".", $package_name);
	$cn = array_pop($packs);
	$dir = implode("/", $packs);
	if($cn == "*") {
		$include_path = ini_get("include_path");
		$incs = explode(SYSTEM_SEPARATOR, $include_path);
		$find = false;
		foreach($incs as $inc) {
			if(@is_dir("$inc/$dir")) {
				$dir = "$inc/$dir";
				$find = true;
				break;
			}
		}
		if(!$find){
			die("FATAL ERROR in import(): can't find path '$dir' in any include dirs('$include_path')");
		}
		$files = array();
		$d = dir($dir);
		while (false !== ($entry = $d->read())) {
		    if(substr($entry, -3) == "php") {
		    	$files[] = $dir."/".$entry;
		    }
		}
		$d->close();
	} else {
		$files = array($dir."/".$cn.".php");
	}

	foreach($files as $file) {
		$d = trim("$file");
		if(!@is_dir($d)) {
			_logTimeFromStart("start require $d");
			include_once($d);
			_logTimeFromStart("end require $d");
		}
	}
	_logTimeFromStart("import end");
}

function removeWWW($url) {
	if (strpos($url, "www.") === 0) {
		return substr($url, strlen("www."));
	}
	if (strpos($url, "www2.") === 0) {
		return substr($url, strlen("www2."));
	}
	return $url;
}

function _stripslashes($str) {
	$str = str_replace("\\\\", "\\", $str);
	$str = str_replace("\\\"", "\"", $str);
	$str = str_replace("\\'", "'", $str);
	return $str;
}

function quoteString($str, $maxlen) {
		$str = str_replace("\n", " ", $str);
		$str = str_replace("\r", " ", $str);
		$str = str_replace("\t", " ", $str);

		$_words = explode(" ", $str);
		$res = "";
		$c = 0;
		for($i=0; $i< sizeof($_words); $i++) {
			$w = $_words[$i];
			if (strlen($w) == 0) {
				continue;
			}
			if (strlen($res) + strlen($w) > $maxlen) {
				break;
			}
			if ($c != 0) {
				$res .= " ";
			}
			$res .= $w;
			$c++;
		}
		return $res;
}

function quoteStringDumb($str, $maxlen) {
		if (strlen($str) > $maxlen) {
			$res = mb_substr($str, 0, $maxlen) . "...";
		} else {
			$res = $str;
		}
		return $res;
}

/**
 *  convert special chars to XML style
 */
function prepareStringForXML($str) {
	$str = (string)$str;
    $out = "";
	for($i=0; $i < mb_strlen($str); $i++) {
		$c = mb_substr($str, $i, 1);
		switch($c) {
			case '<':
				$out .= "&lt;";
				break;
			case '>':
				$out .= "&gt;";
				break;
			case '&':
				$out .= "&amp;";
				break;
			case '"':
				$out .= "&#034;";
				break;
            case '\'':
				$out .= "&#039;";
				break;
			default:
				$out .= $c;
		}
	}
	return $out;
}

/**
 *  convert special chars to HTML style
 */
function prepareStringForHTML($str) {
    $out = "";
	for($i=0; $i < strlen($str); $i++) {
		$c = $str{$i};
		switch($c) {
			case '<':
				$out .= "&lt;";
				break;
			case '>':
				$out .= "&gt;";
				break;
			case '&':
				$out .= "&amp;";
				break;
			case '"':
				$out .= "&quot;";
				break;
            case '\'':
				$out .= "&#039;";
				break;
			default:
				$out .= $c;
		}
	}
	return $out;
}

function encodeURL($uri) {
	$cb = "";
	for ($i = 0; $i < strlen($uri); $i++) {
		$ch = $uri{$i};
		switch ($ch) {
			case '?' :
			case '&' :
			case '=' :
			case ' ' :
			case '\'':
			case '"' :
			case '%' :
			case '<' :
			case '>' :
			case '+' :
			case '^' :
			case '~' :
			case '|' :
				$cb .= '%';
				$cb.= encodeHex(ord($ch) >> 4);
				$cb .= encodeHex(ord($ch));
				break;

			default :
				$cb .= $ch;
				break;
		}
	}

	return $cb;
}

function externalURL($url)  {
	if (strpos($url, "http://") === 0 || strpos($url, "https://") === 0) {
		return $url;
	} else {
		return "http://".$url;
	}
}

function externalURLNoHTTP($url)  {
	if (strpos($url, "http://") === 0) {
		return substr($url, strlen("http://"));
	} else if (strpos($url, "https://") === 0) {
		return substr($url, strlen("https://"));
	} else {
		return $url;
	}
}

function strReplaceButNotInQuotes($patt, $replacement, $s, $QUOTE_CHARACTER = "'") {
	$charbuffer = "";
	for ($j = 0; $j < strlen($s); $j++) {
		$c = $s{$j};
		if ($c == $QUOTE_CHARACTER) {
			$charbuffer .= $QUOTE_CHARACTER;
			for ($j++; $j < strlen($s) && $s{$j} != "'"; $j++)
				$charbuffer .= $s{$j};
			$charbuffer .= $QUOTE_CHARACTER;
		} else {
			$k = $j;
			for (; $k < strlen($s) && $k - $j < strlen($patt) && $s{$k} == $patt{$k - $j}; $k++) {}
			if (($k - $j) == strlen($patt)) {
				$j = $k - 1;
				$charbuffer .= $replacement;
			} else {
				$charbuffer .= $c;
			}
		}
	}
	return $charbuffer;
}

function str_transliterate_ru2en($str) {
	$ru_letters = array(
		"а"=>"a", "б"=>"b", "в"=>"v", "г"=>"g", "д"=>"d", "е"=>"e", "є"=>"e", "ё"=>"yo", 
		"ж"=>"zh", "з"=>"z", "и"=>"i", "і"=>"i", "й"=>"y", "к"=>"k", "л"=>"l", "м"=>"m",
		"н"=>"n", "о"=>"o", "п"=>"p", "р"=>"r", "с"=>"s", "т"=>"t", "у"=>"u",
		"ф"=>"f", "х"=>"kh", "ц"=>"ts", "ч"=>"ch", "ш"=>"sh", "щ"=>"sch", "ь"=>"",
		"ы"=>"y", "ъ"=>"y", "э"=>"e", "ю"=>"yu", "я"=>"ya",

		"А"=>"A", "Б"=>"B", "В"=>"V", "Г"=>"G", "Д"=>"D", "Е"=>"E", "Є"=>"E", "Ё"=>"Yo", 
		"Ж"=>"Zh", "З"=>"Z", "И"=>"I", "І"=>"I", "Й"=>"Y", "К"=>"K", "Л"=>"L", "М"=>"M",
		"Н"=>"N", "О"=>"O", "П"=>"P", "Р"=>"R", "С"=>"S", "Т"=>"T", "У"=>"U",
		"Ф"=>"F", "Х"=>"Kh", "Ц"=>"Ts", "Ч"=>"Ch", "Ш"=>"Sh", "Щ"=>"Sch", "Ь"=>"",
		"Ы"=>"Y", "Ъ"=>"Y", "Э"=>"E", "Ю"=>"Yu", "Я"=>"Ya",
		);

	$array_keys = array_keys($ru_letters);
	$resStr = '';
	for($i=0; $i < mb_strlen($str); $i++) {
		$l = mb_substr($str, $i, 1);
		if (in_array($l, $array_keys)) {
			$resStr .= $ru_letters[$l];
		} else {
			$resStr .= $l;
		}
	}

	return $resStr;
}

function name2shortname($name) {
	$name = trim($name);
	$shortname = str_transliterate_ru2en($name);
	$shortname = strtolower($shortname);
	$shortname = str_replace(" ", "-", $shortname);
	$shortname = str_replace("-", "-", $shortname);
	$shortname = str_replace("/", "-", $shortname);
	$shortname = str_replace("!", "", $shortname);
	$shortname = str_replace("?", "", $shortname);
	$shortname = str_replace("&", "-", $shortname);
	$shortname = str_replace(".", "", $shortname);
	
	$shortname = str_replace("___", "-", $shortname);
	$shortname = str_replace("__", "-", $shortname);
	
	$shortname = trans_file_name($shortname);
	
	return $shortname;
}

function trans_file_name ($str) { //kazancev
	
	$lat_alphabet = "0123456789abcdefghijklmnopqrstuvwxyz-";
	$lat_arr = array();
	for ($i=0;$i<=mb_strlen($lat_alphabet);$i++) {
		$lat_arr[] = $lat_alphabet[$i];
	}
	//var_dump($lat_arr);
	
	$new_str = array();
	for ($i=0;$i<mb_strlen($str)+1;$i++) {
		
		$chr = mb_substr($str, $i, 1);
		
		if (in_array($chr, $lat_arr)) {
			$new_str[] = $chr;
		} else {
			$new_str[] = "";
		}
	}
	
	return implode("", $new_str);
	
}

function byteToHex($b) {
	$cb = "";
	$cb .= encodeHex(($b & 0xf0) >> 4);
	$cb .= encodeHex($b & 0xf);
	return $cb;
}

function encodeHex($ch) {
	$ch &= 0xf;
	if ($ch < 10)
		return strtoupper(chr($ch + 48));
	else
		return strtoupper(chr(($ch + 97) - 10));
}

function _array_remove_filter($var) {
	return ($var != null);
}
function array_remove(&$array, $key) {
	$array[$key] = null;
	$array = array_filter($array, "_array_remove_filter");
	return $array;
}

function getFileExt($filename) {
	$temp = explode(".", $filename);
	return $temp[sizeof($temp)-1];
}


function load_view($t, $data=false) {
	global $request, $session, $path, $rs, $script_time, $premium_user, $DOC_ROOT, $developers_addr, $lng, $base, $variables, $website_data;
	$dynamic_template_path = $DOC_ROOT . "/app/views/" . $t . ".php";
	if (!file_exists($dynamic_template_path)) {
		die('Dynamic template file does not exists: ' . $t);
	}
	
	foreach ($request->attributes as $k=>$v) {
		$$k = $v;
	}


	if($data)
	{
		foreach ($data as $k=>$v) $$k = $v;
	}

	include($dynamic_template_path);
}

function load_model($n)
{
	global $DOC_ROOT;
	$model_path = $DOC_ROOT . '/app/models/' . $n . '.php';
	
	if(!file_exists($model_path))
		{
			if(in_array($_SERVER['REMOTE_ADDR'], $developers_addr) and _DEV_MODE_ === true)
			{
				echo '<div class="controller-error">Model does not exists: <br><strong>' . $n . '</strong>';
			}

		}
		else
		{
			include_once($model_path);
			return new $n();
			
		}
}

function use_controller($c, $data=false, $no_execute=false)
{

	global $routes, $base, $variables, $connection, $website_data;

	if(isset($routes[$c]))
		$c = $routes[$c];


	$is_ajax = false;

	if($no_execute === true and isset($_GET['ajax']))
	{
		$is_ajax = true;

	}

	if($is_ajax === false)
	{

		global $request, $session, $path, $premium_user, $DOC_ROOT, $developers_addr, $lng;
		$controller_path = $DOC_ROOT . '/app/controllers/' . $c . '.php';

		if(!file_exists($controller_path))
		{
			if(in_array($_SERVER['REMOTE_ADDR'], $developers_addr) and _DEV_MODE_ === true)
			{
				echo '<div class="controller-error">Controller does not exists: <br><strong>' . $c . '</strong>';
			}

		}
		else
		{
			foreach ($request->attributes as $k=>$v) {
			$$k = $v;
			}

			if($data)
			{
				foreach ($data as $k=>$v) $$k = $v;
			}

			include($controller_path);

		}
	}
}

define('META_MAX_WORDS', 20);

function _getMetaKeywords($str) {
	$str = strip_tags($str);
	$str = str_replace("\n", " ", $str);
	$str = str_replace("\r", " ", $str);

	$str = str_replace(".", " ", $str);
	$str = str_replace(",", " ", $str);
	$str = str_replace('"', " ", $str);

	$_keywords = explode(" ", $str);
	$keywords = array();
	for($i=0; $i< sizeof($_keywords) && $i<META_MAX_WORDS; $i++) {
		$k = $_keywords[$i];
		if (strlen($k) < 4) continue;
		$keywords []= $k;
	}
	return $keywords;
}

function prepareMetaDescription($str) {
	$keywords = _getMetaKeywords($str);
	return implode(" ", $keywords);
}

function prepareMetaKeywords($str) {
	$keywords = _getMetaKeywords($str);
	return implode(",", $keywords);
}

_logTimeFromStart("point1");
/**************************************************************************************************/

DEFINE("_CACHE_DIR_", $_SERVER["DOCUMENT_ROOT"] . "/cache");

function _ftok($path, $proj) {
	return strlen($path)+0xff3;
}

function _write_cache($varname) {

	if ($GLOBALS[$varname]) {
		$cacheFile = fopen(_CACHE_DIR_ . "/" . $varname, "w+");
		fwrite($cacheFile,  serialize($GLOBALS[$varname]));
		fclose($cacheFile);
	}

/*

	if ($GLOBALS[$varname]) {
		$cache_key = _ftok ($varname, "dbm");

		$serializedValue = serialize($GLOBALS[$varname]);

		$shm_id = @shmop_open($cache_key, "a", 0, 0);
		if ($shm_id) {
			shmop_delete($shm_id);
			shmop_close($shm_id);
		}
		$shm_id = shmop_open($cache_key, "c", 0644, strlen($serializedValue));
		if ($shm_id) {
			shmop_write($shm_id, $serializedValue, 0);
			shmop_close($shm_id);
		}
	}
//*/
}

function _read_cache($varname) {

	$cacheFileStr = @file_get_contents (_CACHE_DIR_ . "/" . $varname);

	if ($cacheFileStr) {
		$GLOBALS[$varname] = unserialize($cacheFileStr);
	} else {
		$GLOBALS[$varname] = null;
	}

	return null;

/*
		//$cache_key = ftok ($_SERVER["SERVER_NAME"] . "/" . "NID");
		$cache_key = _ftok ($varname, "dbm");
		$shm_id = @shmop_open($cache_key, "a", 0, 0);
		if ($shm_id) {
			$cacheFileStr = shmop_read($shm_id, 0, shmop_size($shm_id));
			shmop_close($shm_id);
		}
		//_dump($cacheFileStr);
//*/
}

function ___shutdown_function() {
	
	DBUtils::rollback(true);

	_write_cache("_NODE_CLASS_CACHE_BY_ID");
	_write_cache("_NODE_CLASS_CACHE_BY_SHORTNAME");
	_write_cache("_NODES_CACHE_BY_ID");
	_write_cache("_NODES_CACHE_BY_PATH");
	_write_cache("USERS_CACHE");
	_write_cache("USER_GROUPS_CACHE");
}

register_shutdown_function("___shutdown_function");
/**************************************************************************************************/

function list_dir_contents ($path) {
	$files = array();
	if ($dir = @opendir($path)) {
		while (($file = readdir($dir)) !== false) {
			if ($file=='.' || $file=='..') {
				continue;
			}
			$file = $path.'/'.$file;
			$files[] = $file;
		}  
		closedir($dir);
	}
	return $files;
}

function list_dir_contents_only_dirs ($path) {
	$files = array();
	if ($dir = @opendir($path)) {
		while (($file = readdir($dir)) !== false) {
			if ($file=='.' || $file=='..') {
				continue;
			}
			$file = $path.'/'.$file;
			if (is_dir($file)) {
				$files[] = $file;
			}
		}  
		closedir($dir);
	}
	return $files;
}

function list_dir_contents_only_files ($path) {
	$files = array();
	if ($dir = @opendir($path)) {
		while (($file = readdir($dir)) !== false) {
			if ($file=='.' || $file=='..') {
				continue;
			}
			$file = $path.'/'.$file;
			if (!is_dir($file)) {
				$files[] = $file;
			}
		}  
		closedir($dir);
	}
	return $files;
}

//simple object
class PHPObject {
    function PHPObject() {
    }
}

class Date {
	var $time = 0;

	function Date($o = null) {
		if (is_string($o)) {
			if (preg_match("/([0-9]{1,2})\\.([0-9]{1,2})\\.([0-9]{4})/", $o)) {
				list($d,$m,$y) = explode(".", $o);
				$this->time = mktime(0, 0, 0, $m, $d, $y);
			} else {
				$this->time = strtotime($o);
			}
		} elseif (is_integer($o)) {
			$this->time = $o;
		} else {
			$this->time = time();
		}
	}

	function format($format = "Y-m-d") {
		return date($format, $this->time);
	}

	function getDay1() {
		return $this->format("j");
	}

	function getDay() {
		return $this->format("d");
	}

	function getMonth() {
		return $this->format("m");
	}

	function getYear() {
		return $this->format("Y");
	}

	function getHour() {
		return $this->format("H");
	}

	function getMinute() {
		return $this->format("i");
	}

	function getSecond() {
		return $this->format("s");
	}
}

/**************************************************************************************************/

/**
 * @return Node
 */
function& getTopSection($node) {
	$path = $node->absolutePath;
	$k = strpos($path, '/', 1);
	if ($k===false)
		$path = substr($path, 0);
	else
		$path = substr($path, 0, strpos($path, "/", 2));
   	$res = Node::findByPath($path);
   	return $res;
}

function& getTopSectionS($path) {
	$k = strpos($path, '/', 1);
	if ($k===false)
		$path = substr($path, 0);
	else
		$path = substr($path, 0, strpos($path, "/", 2));
   	$res = &Node::findByPath($path);
   	return $res;
}

/**
 * @return Node
 */
function& getL2pSection(&$node) {
	$items = explode("/", $node->absolutePath);
	if (sizeof($items) > 2) {
		$res = Node::findByPath('/'.$items[1].'/'.$items[2]);
		return $res;
	}

   	return null;
}

/**
 * @return Node
 */
function& getL3pSection(&$node) {
	$items = explode("/", $node->absolutePath);
	if (sizeof($items) > 3) {
		$res = Node::findByPath('/'.$items[1].'/'.$items[2].'/'.$items[3]);
		return $res;
	}

   	return null;
}

/**
 * @return Node
 */
function& getL4pSection(&$node) {
	$items = explode("/", $node->absolutePath);
	if (sizeof($items) > 4) {
		$res = Node::findByPath('/'.$items[1].'/'.$items[2].'/'.$items[3].'/'.$items[4]);
		return $res;
	}

   	return null;
}

function getClassNameLowercase(&$object) {
	return strtolower(get_class($object));
}

function useEmailTemplate($template, $patterns, $values) {
	$text = $template;
	for ($i=0; $i<sizeof($patterns); $i++) {
		$text = str_replace($patterns[$i], $values[$i], $text);
	}
	return $text;
}

function formatCurrency($number) {
	return sprintf("%01.2f", $number);
}
/**************************************************************************************************/

/* Обрізання рядка. Додано: 07.01.2012. Ярослав Гошовський */
function truncate_words($text, $limit)
{
	$text=mb_substr($text,0,$limit);
	/*если не пустая обрезаем до  последнего  пробела*/
	if(mb_substr($text,mb_strlen($text)-1,1) && mb_strlen($text)==$limit)
	{
		$textret=mb_substr($text,0,mb_strlen($text)-mb_strlen(strrchr($text,' ')));
		if(!empty($textret))
		{
			return $textret;
		}
	}
	return $text;
}
/* Кінець функції обрізання рядка. Додано: 07.01.2012. Ярослав Гошовський */


function date_return_n($line, $seconds = false, $year_active = false){
if(!empty($line))
	{
	$arr = explode("-", $line);
	$year = $arr[0];	$mouns = $arr[1];
	$day = $arr[2];		$h = $arr[3];
	$m = $arr[4];		$s = $arr[5];
	
	$date_return = $day." ".getMonth($mouns);
	if($year_active == true)
		{
		$date_return .= " ".$year." года";
		}
	if(!empty($h) && !empty($m))
		{
		$date_return .= " ". $h . ":" . $m;
		}
	if(!empty($s) && $seconds == true)
		{
		$date_return .= ":".$s;
		}
	return $date_return;
	}
}

function timestamp_to_normal($timestamp, $show_time=false){
	$date = explode(" ",$timestamp);
	if(count($date)>0)
		{
		$tmp = explode(":",$date[1]);  
		$tmp2 = explode("-",$date[0]);
		$result = $tmp2[2]." ".getMonth($tmp2[1])." ".$tmp2[0];
		if($show_time!=false) $result .= " ".$tmp[0].":".$tmp[1];
		return $result;
		}
	else return false;
}

function sortNewToOld($a, $b) {
if ($a['date'] === $b['date']) return 0;
return $a['date'] < $b['date'] ? 1 : -1;
}
function sortOldToNew($a, $b) {
if ($a['date'] === $b['date']) return 0;
return $a['date'] > $b['date'] ? 1 : -1;
}
function sortPopular($a, $b) {
$d = explode("|", $a['rating']);
$c = explode("|", $b['rating']);
if (count($d) === count($c)) return 0;
return $d < $c ? 1 : -1;
}





	
//import("shop.*");

$connection = new Connection($connect_params["url"], $connect_params["params"]);

$connection->setCharset(DB_CHARSET);



function _addNode($classname, $shortname, $parent_id, $name, $filds)
{
	global $connection;
	list($classData) = getSimpleListSQL("SELECT * FROM dbm_classes WHERE shortname='".$classname."' LIMIT 1");
	list($sequencesData) = getSimpleListSQL("SELECT value FROM dbm_sequences WHERE name='nodes' LIMIT 1");
	list($subtreeOrderData) = getSimpleListSQL("SELECT subtree_order FROM dbm_nodes WHERE parent_id=".$parent_id." ORDER BY subtree_order DESC LIMIT 1");
	list($parentNodeData) = getSimpleListSQL("SELECT absolute_path FROM dbm_nodes WHERE id=".$parent_id." LIMIT 1");

	// dbm_nodes

	$class_id = $classData["id"]; // ID класу
	$node_id = 1 + (int)$sequencesData["value"]; // NODE ID
	$dynamic_template = $classData["default_template"];
	$subtree_order = 1 + (int)$subtreeOrderData["subtree_order"];
	$shortname = $shortname . "_" . $subtree_order;
	$absolute_path = $parentNodeData["absolute_path"]."/".$shortname;
	$owner = 1;
	$time_created = date("Y-m-d H:i:s");
	$time_updated = $time_created;

	// insert into dbm_nodes

	$sql_1 = "
		INSERT INTO dbm_nodes (id, shortname, name, dynamic_template, subtree_order, class_id, parent_id, absolute_path, owner, time_created, time_updated)
		VALUES ('{$node_id}', '{$shortname}', '{$name}', '{$dynamic_template}', '{$subtree_order}', '{$class_id}', '{$parent_id}', '{$absolute_path}', '{$owner}', '{$time_created}', '{$time_updated}')
	";

	// insert into dbm_nfv

	$sql_2 = "INSERT INTO dbm_nfv_".$classname." (node_id";
		foreach($filds as $key=>$fild)
		{
			$sql_2 .= ", " . $key;
		}
	$sql_2 .= ") VALUES('".$node_id."'";
		foreach($filds as $key=>$fild)
		{
			$sql_2 .= ", '" . $fild . "'";
		}
	$sql_2 .= ")";

	// update counter

	$sql_3 = "UPDATE dbm_sequences SET value='{$node_id}' WHERE name='nodes'";

	// add group rights 1

	$sql_4 = "INSERT INTO dbm_group_rights (group_id, node_id, rights) VALUES ('1', '{$node_id}', '3')";

	// add group rights 2

	$sql_5 = "INSERT INTO dbm_group_rights (group_id, node_id, rights) VALUES ('2', '{$node_id}', '3')";

	// submission

	$sql_6 = "INSERT INTO dbm_nodes_submission (child_id, parent_id, level_diff) VALUES ('{$node_id}', '{$parent_id}', '1')";

	//submission

	$sql_7 = "INSERT INTO dbm_nodes_submission (child_id, parent_id, level_diff) VALUES ('{$node_id}', '1', '2')";

	$Statement = new Statement($connection);

	$Statement->execute($sql_1) or die("addNode_1: ".mysql_error());
	$Statement->execute($sql_2) or die("addNode_2: ".mysql_error());
	$Statement->execute($sql_3) or die("addNode_3: ".mysql_error());
	$Statement->execute($sql_4) or die("addNode_4: ".mysql_error());
	$Statement->execute($sql_5) or die("addNode_5: ".mysql_error());
	$Statement->execute($sql_6) or die("addNode_6: ".mysql_error());
	$Statement->execute($sql_7) or die("addNode_7: ".mysql_error());

	return $node_id;
}



$_USE_NODES_CACHE_ = true;

NodeClass::buildNodeClassCache();
UserGroup::buildUserGroupCache();
User::buildUsersCache();
Node::readCache();

_logTimeFromStart("point4");
?>
