<?

//$_settingsNode = Node::findByPath("/_site_settings");
$_settingsNode = false;
// list($_settingsNode) = getSimpleList("_site_settings", 
// 	array(
// 		"admin_email", 
// 		"admin_email_name", 
// 		"admin_email_from", 
// 		"admin_email_from_name"
// 		)
// 	);
// //_dump($_settingsNode);

// $request->setAttribute("SettingsNode", $_settingsNode);

// define("ADMIN_EMAIL", $_settingsNode["admin_email"]);
// define("ADMIN_EMAIL_FROM", $_settingsNode["admin_email_from"]);

define("LOGGED_USER_SESSION_ATTR", "_LOGGED_USER_");

function localmsg($key) {
	return $GLOBALS["LOCAL_MESSAGES"][$key];
}

function UserExist ($email) {
	
	$user = getSimpleList("user", null, "email='$email'");
	//_dump($user);
	return $user;
}
function addCount($type,$id) {
	$sql = "INSERT INTO dbm_views SET id='{$id}',pubdate=NOW()";
    DBUtils::execUpdate($sql);
}
    function setGetParam($add_arr = array())
    {
        $res = "";
        
        if(count($add_arr)>0)
        {
            $new_arr = array();

            if(strlen($_SERVER["QUERY_STRING"])>0)
            {            
                $arr_am = explode("&", $_SERVER["QUERY_STRING"]);
                foreach($arr_am as $am)
                {
                    $data = explode("=", $am);
                    $new_arr[$data[0]] = $data[1];  
                } 
                
                $merge = array_merge($new_arr, $add_arr);
            }
            else
                $merge = $add_arr;
                
            foreach($merge as $key => $val)
            {
                if($val === NULL)
                    continue;
                    
                $res .= $key."=".$val."&";
            }
                
            $res = trim($res, "&");
        }
        else
            $res = $_SERVER["QUERY_STRING"];
                        
        if(empty($res))
            return $res;
        else
            return "?".$res;
    }

function nodeName(&$node) {
	return (SITE_LOCALE == 'ru' ? $node->name : $node->tfields["name_" . SITE_LOCALE]);
}

function highLightKeywords($str, $keywords) {
	foreach ($keywords as $keyword) {
		$keyword = trim($keyword);
		if (strlen($keyword) == 0) continue;
		$upKeyword = ucfirst ($keyword);
		$str = str_replace(" ".$keyword, " <strong class='highLight'>".$keyword."</strong>", $str);
		$str = str_replace($keyword." ", "<strong class='highLight'>".$keyword."</strong> ", $str);
		$str = str_replace(" ".$upKeyword, " <strong class='highLight'>".$upKeyword."</strong>", $str);
		$str = str_replace($upKeyword." ", "<strong class='highLight'>".$upKeyword."</strong> ", $str);
	}

	return $str;
}
function getMonth($date) {
    $month = array(
      "01"=>"січня",
      "02"=>"лютого",
      "03"=>"Березня",
      "04"=>"Квітня",
      "05"=>"Травня",
      "06"=>"Червня",
      "07"=>"Липня",
      "08"=>"Серпня",
      "09"=>"Вересня",
      "10"=>"Жовтня",
      "11"=>"Листопада",
      "12"=>"Грудня"
    );
    return $month["$date"];
    }
    
function genitiveNumber($count) {
	if (LOCALE == 'en') {
		if ($count == 1) {
			return "channel";
		} else {
			return "channels";
		}
	} else if (LOCALE == 'ua') {
		$lastDigit = $count % 10;
		$secondDigit = ($count / 10) % 10;
		if ($lastDigit == 1 && $secondDigit != 1) {
			return "канал";
		} else if ($lastDigit >= 2 && $lastDigit <= 4 && $secondDigit != 1) {
			return "канала";
		} else {
			return "каналів";
		}
	} else {
		$lastDigit = $count % 10;
		$secondDigit = ($count / 10) % 10;
		if ($lastDigit == 1 && $secondDigit != 1) {
			return "канал";
		} else if ($lastDigit >= 2 && $lastDigit <= 4 && $secondDigit != 1) {
			return "канала";
		} else {
			return "каналов";
		}
	}
}

// function _draw_paging_link(&$link) {
// 	if ($link->index != 0) {
// 		$str = '';
// 	}

//  	switch($link->type) {
// 		case "prev":
//         	$str .= '<a href="' . $link->href . '">← </a>';
// 			break;
// 		case "next":
//         	$str .= '<a href="' . $link->href . '"> →</a>';
// 			break;
// 		case "link":
//         	$str .= '<a href="' . $link->href . '">' . ($link->page + 1) . '</a></li>';
// 			break;
// 		default:
//             $str .= '<a class="active">' . ($link->page + 1) . "</a>";
// 			break;
// 	}

// 	return $str;
// }

function _draw_paging_link(&$link) {
	if ($link->index != 0) {
		$str = '';
	}

 	switch($link->type) {
		case "prev":
        	$str = '<li><a href="' . $link->href . '" aria-label="Previous"><span aria-hidden="true">&laquo;</span></a></li>';
			break;
		case "next":
        	$str = '<li><a href="' . $link->href . '" aria-label="Next"><span aria-hidden="true">&raquo;</span></a></li>';
			break;
		case "link":
        	$str = '<li><a href="' . $link->href . '">' . ($link->page + 1) . '</a></li>';
			break;
		default:
            $str = '<li class="active"><a class="bg-danger">' . ($link->page + 1) . "</a></li>";
			break;
	}

	return $str;
}

function fullURL($href) {
	$serverName = $_SERVER["SERVER_NAME"];
	return "http://" . $serverName . $href;
}

function prepareURL ($str) {
	if (mb_substr($str, 0, 7)!="http://") {
		$str = "http://" . $str;
	}
	return $str;
}

function getFileIconExt($filename) {
	$ext = getFileExt($filename);
	$iconFilePath = $_SERVER["DOCUMENT_ROOT"] . "/_img/img_load_".$ext.".gif";
	if (file_exists($iconFilePath)) {
		return $ext;
	} else {
		return "unknown";
	}
}

function wrapWords ($str, $wordlimit, $strlimit) {
	
	$_str = explode(" ", $str);
	if (count($_str)>1) {
		foreach ($_str as $key => $word) {
			if (mb_strlen($word)>$wordlimit  && mb_strlen($str)>$strlimit && $key<(count($_str)-1)) {
				$_str[$key] .= "<br>";
			}
		}
		$_str = implode(" ", $_str);
		return $_str;
	} else {
		return $str;
	}
}

function changeRegistr($text, $change_type = 'upper') { 
	
    $alfavitlover = array('ё','й','ц','у','к','е','н','г', 'ш','щ','з','х','ъ','ф','ы','в', 'а','п','р','о','л','д','ж','э', 'я','ч','с','м','и','т','ь','б','ю'); 
    $alfavitupper = array('Ё','Й','Ц','У','К','Е','Н','Г', 'Ш','Щ','З','Х','Ъ','Ф','Ы','В', 'А','П','Р','О','Л','Д','Ж','Э', 'Я','Ч','С','М','�?','Т','Ь','Б','Ю'); 
	
    if ('upper' == $change_type) {
    	return str_replace($alfavitlover,$alfavitupper,$text); 
    } else {
    	return str_replace($alfavitupper,$alfavitlover,$text); 
    }
    
} 

function prepareHref($str) {
	/*
	$t = explode("/", $str);
	
	if (count($t)>3) {
		$_t[] = array_shift($t);
		$_t[] = array_shift($t);
		$_t[] = implode("-", $t);
		$_t = implode("/", $_t);
	} else {
		$_t = $str;
	}
	unset($t);
	
	return $_t;
	*/
	return $str;
	
}

function imgBorder ($src, $width=0, $height=0, $alt='', $href='', $target='', $in_a = '', $in_img = '', $in_td = '') {
	
	//$alt = htmlspecialchars($alt);
	
	if ($width!=0) {
		$width = 'width="'. $width .'"';
	} else {
		$width = '';
	}
	
	if ($height!=0) {
		$height = 'height="'. $height .'"';
	} else {
		$height = '';
	}
	
	if ($href!='') {
		if ($target!='') {
			$target = 'target="'. $target .'"';
		} else {
			$target = '';
		}
		$href_beg = '<a href="'. $href .'" title="'. $alt .'" '. $target .' '. $in_a . ' style="padding: 0;margin: 0;">';
		$href_end = '</a>';
	}
	
	$img = 
			'<table cellpadding="0" cellspacing="0" class="photoTab">
				<tr>
					<td class="photoTd_lt"><img src="/_i/imgborder/left.gif" alt="" /></td>
					<td></td>
					<td class="photoTd_rt"><img src="/_i/imgborder/right.gif" alt="" /></td>
				</tr>
				<tr>
					<td></td>
					<td class="photoTd" '. $in_td .' style="text-align:center">'. $href_beg .'<img '. $in_img .' src="'. $src .'" '. $width .' '. $height .' alt="'. $alt .'" title="'. $alt .'" />'. $href_end .'</td>
					<td class="photoTd_borderRight"><img src="/_i/imgborder/1px.gif" width="1" height="1" /></td>
				</tr>
				<tr>
					<td class="photoTd_lb"><img src="/_i/imgborder/left-bot.gif" alt="" /></td>
					<td></td>
					<td class="phtoTd_rb"><img src="/_i/imgborder/right-bot.gif" alt="" /></td>
				</tr>
			</table>';
			
	return $img;
	
}

function resizeCalc ($width, $height, &$n_width, &$n_height, $w_limit=0, $h_limit=0) {
	
	$n_width = $width;
	$n_height = $height;
	
	if ($width>$w_limit && $w_limit!=0) {
		$coeff = $width / $height;
		$n_width = $w_limit;
		$n_height = floor($n_width / $coeff);
	}
	
	if ($height>$h_limit && $h_limit!=0) {
		$coeff = $width / $height;
		$n_height = $h_limit;
		$n_width = floor($n_height * $coeff);
	}
}

function _getHref($url) {
	
	$tmpNode = new Node();
	$tmpNode->absolutePath = $url;
	return $tmpNode->getHref();
	
}

function findPositionWord ($keyword, $str) {
	
	$pos = mb_stripos($str, $keyword, 0);
	
	if ($pos!==false) {
		$start = $pos-50;
	} else {
		$start = 0;
	}
	if ($start<0) {
		$start = 0;
	}
	
	$str = " " . mb_substr($str, $start, 100) . " ";
	
	return $str;
	
}

function getVideoExts () {
	$videos = array(
		'wmv', 
		'avi', 
		'fla', 
		'mpg', 
	);
	
	return $videos;
}

function getSimpleList ($classname, $fieldlist = null, $where = null, $orderby = null, $limit = null, $join = null, $index_id = false, $offset = null, $debug_mode_cms = false) {
	
	//global $debug_mode_cms;
	
	$fieldlist_default = array("id", "name", "absolute_path", "shortname");
	
	if (!$fieldlist) {
		$fieldlist = $fieldlist_default;
	} else {
		$fieldlist = array_merge($fieldlist, $fieldlist_default);
	}
	
	$qb = new NodeQueryBuilder();
	$qb->setFrom("{" . $classname . "} n");
	
	if ($fieldlist == null) {
		$qb->addField("n.*");
	} elseif (is_array($fieldlist)) {
		foreach ($fieldlist as $field) {
			$qb->addField("n." . $field);
		}
	}
	
	if ($where != null) {
		$qb->addWhere($where);
	}
	
	if ($orderby == null) {
		$qb->addOrderBy("n.subtree_order");
	} else {
		$qb->addOrderBy($orderby);
	}
	
	if ($limit != null) {
		$qb->setLimit($limit);
	}
	
	if ($offset != null) {
		$qb->setOffset($offset);
	}
	
	/*
	if ($join != null) {
		$qb->addJoinSpec($join[0], $join[1], $join[2]);
	}
	*/
	if ($join != null) {
		if (is_array($join[0])) {
			foreach ($join as $join_item) {
				$qb->addJoinSpec($join_item[0], $join_item[1], $join_item[2]);
			}
		} else {
			$qb->addJoinSpec($join[0], $join[1], $join[2]);
		}
	}
	
	$selectQuery = $qb->buildQuery();
	//echo ($selectQuery->sql);
	if ($debug_mode_cms) {
		_dump($selectQuery->sql);
	}
	$selectQuery->execute();
	$rs = $selectQuery->getResultSet();
	
	$_res = array();
	 
	while ($rs->next()) {
		$tmp = array();
		foreach ($rs->columnNames as $column_id => $column_name) {
			$tmp[$column_name] = $rs->currentRow[$column_id];
		}
		if ($index_id) {
			$_res[$tmp[$index_id]] = $tmp;
		} else {
			$_res[] = $tmp;
		}
	}
	return $_res;
}

function getSimpleListCount ($classname, $where = null, $join = null) {
	
	$qb = new NodeQueryBuilder();
	$qb->setFrom("{" . $classname . "} n");	
	$qb->addField("count(*) cnt");
	
	if ($where != null) {
		$qb->addWhere($where);
	}
	
	if ($join != null) {
		$qb->addJoinSpec($join[0], $join[1], $join[2]);
	}
	
	$selectQuery = $qb->buildQuery();
	//echo ($selectQuery->sql);
	$selectQuery->execute();
	$rs = $selectQuery->getResultSet();
	
	$_res = array();
	 
	while ($rs->next()) {
		return $rs->getInt("cnt");
	}
}

function getImgUrl ($str) {
	return "/imglib" . $str;
}

function getImgThumbUrl ($str) {
	return "/imglib_thumbnails" . $str;
}

function getFileUrl ($str) {
	return "/files" . $str;
}

function getHref ($str) {
	//return prepareHref($str) . ".html";
	return prepareHref($str) . "/";
}

function getCurrencyRate () {
	
	global $request, $currency_rate;
	
	if ($request->getAttribute("currency_rate")!=null) {
		return $request->getAttribute("currency_rate");
	}
	
	if ($currency_rate === null) {		
		$currency = Node::findByPath("/_settings/currency");
		$currency_rate = $currency->tfields["ce_rur_usd"];
	}
	
	if ($currency_rate===null) {
		$currency_rate = 1;
	}
	$request->setAttribute("currency_rate", $currency_rate);
	return $currency_rate;
}

function getCurrencyProcent () {
	
	global $request, $currency_procent;
	
	if ($request->getAttribute("currency_procent")!=null) {
		return $request->getAttribute("currency_procent");
	}
	
	if ($currency_procent === null) {		
		$currency = Node::findByPath("/_settings/currency");
		$currency_procent = $currency->tfields["procent"];
	}
	
	$request->setAttribute("currency_procent", $currency_procent);
	return $currency_procent;
}

function getPrice ($price) {
	
	global $currency_rate;
	global $currency_procent;
	
	if ($currency_procent === null) {
		$currency_procent = getCurrencyProcent();
	}
	
	if ($currency_rate === null) {
		$currency_rate = getCurrencyRate();
	}
	
	$price = round($currency_rate * $price * (1+$currency_procent/100));
	
	return $price;
	
}

function _get_caption ($capt_index) {

	global $captions, $lang_preff;

	$lang_index = $lang_preff;
	
	if ($captions[$lang_index][$capt_index]) {
		return $captions[$lang_index][$capt_index];
	} else {
		return $capt_index;
	}
}

function is_email($string) {
    
    $string = trim($string);
    $ret = eregi(
                '^([a-z0-9_]|\\-|\\.)+'.
                '@'.
                '(([a-z0-9_]|\\-)+\\.)+'.
                '[a-z]{2,4}$',
                $string);
    
    return($ret);
}

function getCountries () {
	
	global $request;
	
	$countries = $request->getAttribute("countries");
	
	if ($countries) {
		return $countries;
	}
	
	$items = getSimpleList("countries_item", null, null, "name", null, null, "id");
	
	$request->setAttribute("countries", $items);
	
	return $items;
}

function getCountry ($id) {
	
	list($item) = getSimpleList("countries_item", array("country_code"), "id='".$id."'");
	return $item;
}

function smallTxt($str, $length, $postfix = "...") {
	
	if (mb_strlen($str)>$length) {
		return mb_substr($str, 0, $length) . $postfix;
	} else {
		return $str;
	}
	
}

function existLogin ($login) {
	
	$user = getSimpleList("user", null, "name='$login'");
	//_dump($user);
	return $user;
	
}

function checkLogin ($email, $password) {
    
	list($user) = getSimpleList("user", array("id","password","isactive", "surname", "username", "post"), "email='".$email."'");
	//_dump($user);
	
	if ($user["password"] == $password) {
		
		return $user;
	} else {
		return false;
	}
	
}

function Login ($user) {
	
	global $session;
	//_dump($user);
	$session->setAttribute("user_auth", $user);
	
	markOnline();
	
}
function Logout () {
	
	global $session;
	
	$user_auth = null;
	$session->setAttribute("user_auth", $user_auth);
	
}
function sendEmailTemplate ($tpl, $data, $email_data=array(), $files=array()) {
	
	global $request;
    
    //_dump($data);
    //_dump($email_data);
    
	$overall_tpls = getSimpleList("email_tpl", array("tpl_body"), "shortname='_header' OR shortname='_footer'", null, null, null, "shortname");
	
	list($tpl_item) = getSimpleList("email_tpl", array("tpl_subject", "tpl_body"),  "shortname='{$tpl}'");
		
	//_dump($tpl_item);
    //_dump($overall_tpls);exit;
	
	$_settingsNode = $request->getAttribute("SettingsNode");
	
	if ($tpl_item) {
		$tpl_subject = $tpl_item["tpl_subject"];
		$tpl_body = $tpl_item["tpl_body"];
		
	} else {
		$tpl_subject = "";
		$tpl_body = "";
	}
	
	if (count($data)>0) foreach ($data as $key => $value) {
		$tpl_subject = str_replace('{'.$key.'}', $value, $tpl_subject);
		$tpl_body = str_replace('{'.$key.'}', $value, $tpl_body);
	}
	
	if ($email_data["to"]) {
		$to = $email_data["to"];
	} else {
		$to = $_settingsNode["admin_email"];
	}
	
	if ($email_data["to_name"]) {
		$to_name = $email_data["to_name"];
	} else {
		$to_name = "";
	}
	
	if ($email_data["from"]) {
		$from = $email_data["from"];
	} else {
		$from = $_settingsNode["admin_email_from"];
	}
	
	if ($email_data["from_name"]) {
		$from_name = $email_data["from_name"];
	} else {
		$from_name = trim($_settingsNode["admin_email_from_name"]);
	}
	
	$tpl_body = $overall_tpls["_header"]["tpl_body"] . $tpl_body . $overall_tpls["_footer"]["tpl_body"];
	
	sendMailAttachNew($from, $from_name, $to, $to_name, $tpl_subject, $tpl_body, $files);
	
}

function sendMailAttachNew ($from_address, $from_name, $to_address, $to_name, $subject, $html_message, $files) {
	
	require_once($_SERVER['DOCUMENT_ROOT'] . "/_mod/email/mimemessage/email_message.php");

	$reply_name=$from_name;
	$reply_address=$from_address;
	$reply_address=$from_address;
	$error_delivery_name=$from_name;
	$error_delivery_address=$from_address;

	$email_message=new email_message_class;
	$email_message->SetEncodedEmailHeader("To",$to_address,$to_name);
	$email_message->SetEncodedEmailHeader("From",$from_address,$from_name);
	$email_message->SetEncodedEmailHeader("Reply-To",$reply_address,$reply_name);
	$email_message->SetHeader("Sender",$from_address);
	$email_message->SetEncodedHeader("Subject",$subject);

	$email_message->CreateQuotedPrintableHTMLPart($html_message,"",$html_part);

	$text_message="This is an HTML message. Please use an HTML capable mail program to read this message.";
	$email_message->CreateQuotedPrintableTextPart($email_message->WrapText($text_message),"",$text_part);

	$alternative_parts=array(
		$text_part,
		$html_part
	);
	$email_message->CreateAlternativeMultipart($alternative_parts,$alternative_part);

	$related_parts=array(
		$alternative_part,
	);
	$email_message->AddRelatedMultipart($related_parts);
	
	if (count($files)>0) foreach ($files as $file) {
		$file_attachment=array(
			"FileName"=>$file,
			"Content-Type"=>"automatic/name",
			"Disposition"=>"attachment"
		);
		$email_message->AddFilePart($file_attachment);
	}
	
	$error=$email_message->Send();
	
}

function getMessage($id) {
	
	global $session;
	
	$user_auth = $session->getAttribute("user_auth");
	
	$sql = "
	SELECT 
		msg.id ,
		msg.createdate ,
		msg.subject ,
		msg.text ,
		msg.readdate ,
		sender.name sender_login, 
		sender.id sender_id, 
		sender_info.firstname sender_firstname, 
		sender_info.surname sender_surname,
		recipient.name recipient_login, 
		recipient.id recipient_id, 
		recipient_info.firstname recipient_firstname, 
		recipient_info.surname recipient_surname
	FROM cms_messages msg
	INNER JOIN dbm_nodes sender ON msg.sender_id=sender.id
	INNER JOIN dbm_nfv_user sender_info ON msg.sender_id=sender_info.node_id
	
	INNER JOIN dbm_nodes recipient ON msg.recipient_id=recipient.id
	INNER JOIN dbm_nfv_user recipient_info ON msg.recipient_id=recipient_info.node_id
	
	WHERE (msg.recipient_id='{$user_auth["id"]}' OR msg.sender_id='{$user_auth["id"]}') AND msg.id='{$id}'
	ORDER BY msg.createdate DESC
	";
	//_dump($sql);
	$rs = DBUtils::execSelect($sql);
	$items = array();
	while ($rs->next()) {
		$tmp = null;
		$tmp["id"] = $rs->getInt("id");
		$tmp["subject"] = $rs->getString("subject");
		$tmp["text"] = $rs->getString("text");
		$tmp["createdate"] = $rs->getDate("createdate");
		$tmp["readdate"] = $rs->getDate("readdate");
		$tmp["sender_id"] = $rs->getInt("sender_id");
		$tmp["sender_login"] = $rs->getString("sender_login");
		$tmp["sender_firstname"] = $rs->getString("sender_firstname");
		$tmp["sender_surname"] = $rs->getString("sender_surname");
		$tmp["recipient_id"] = $rs->getInt("recipient_id");
		$tmp["recipient_login"] = $rs->getString("recipient_login");
		$tmp["recipient_firstname"] = $rs->getString("recipient_firstname");
		$tmp["recipient_surname"] = $rs->getString("recipient_surname");
		$items[] = $tmp;
	}
	
	list($item) = $items;
	
	return $item;
	
}

function markOnline () {
	
	global $session;
	
	$user_auth = $session->getAttribute("user_auth");
	
	if ($user_auth)  {
		$sql = "REPLACE INTO cms_users_online SET last_action=now(), user_id='{$user_auth["id"]}'";
		$rs = DBUtils::execUpdate($sql);
	}
	
}

function delOnline () {
	
	global $session;
	
	$user_auth = $session->getAttribute("user_auth");
	
	if ($user_auth)  {
		$sql = "DELETE FROM cms_users_online WHERE user_id='{$user_auth["id"]}'";
		$rs = DBUtils::execUpdate($sql);
	}
	
}

function getSimpleListSQL ($sql, $field_array_index=null) {
	
	//global $debug_mode_cms;
	$_res = false;
	
	$rs = DBUtils::execSelect($sql);
	
	while ($rs->next()) {
		$tmp = array();
		foreach ($rs->columnNames as $column_id => $column_name) {
			$tmp[$column_name] = $rs->currentRow[$column_id];
		}
		if ($field_array_index) {
			$_res[$tmp[$field_array_index]] = $tmp;
		} else {
			$_res[] = $tmp;
		}
		
	}
	return $_res;
}

function getSimpleListCountSQL ($sql) {
	
	return  DBUtils::execCountSelect($sql);
	
}

function getFullItemByItem ($item) {
	
	global $_NODE_CLASS_CACHE_BY_ID;
	
	if ($_NODE_CLASS_CACHE_BY_ID[$item["class_id"]]) {
		$tname = "dbm_nfv_" . $_NODE_CLASS_CACHE_BY_ID[$item["class_id"]]->shortname;
		$sql = "SELECT * FROM $tname WHERE node_id='{$item["id"]}'";
		list($item_info) = getSimpleListSQL($sql);
		return array_merge($item, $item_info);
	} else {
		return false;
	}
	
}

function getByPath ($path) {
	
	$sql = " SELECT * FROM dbm_nodes WHERE absolute_path='$path' ";
	list($item) = getSimpleListSQL($sql);	
	return $item;
	
}

function getByID ($id) {
	
	$sql = " SELECT * FROM dbm_nodes WHERE id='$id' ";
	list($item) = getSimpleListSQL($sql);	
	return $item;
	
}

function getItemByPath ($path) {
	
	return getFullItemByItem(getByPath($path));
	
}

function getItemByID ($id) {
	
	return getFullItemByItem(getByID($id));
	
}

function getMenus () {
	
	$array_classes = array(
		//"132" => "feedback",
		"36" => "section",
		"164" => "head"
        //"56" => "section_text_image"
		/*
        "90" => "section_contacts",
		"99" => "subsection_text_image",
		"134" => "subsection_news",
        */
		
	);
	
	$classes_id = array();
	foreach ($array_classes as $class_id => $class_shortname) {
		$classes_id[] = $class_id;
		$cc++;
	}
	
	$sql = "
	SELECT  base_n.id AS id
	, base_n.name AS name
	, base_n.shortname AS shortname
	, base_n.dynamic_template AS dynamic_template
	, base_n.subtree_order AS subtree_order
	, base_n.absolute_path AS absolute_path
	, base_n.parent_id AS parent_id
	 
	FROM	
	dbm_nodes base_n
	
	WHERE (base_n.class_id IN (". implode(", ", $classes_id) .") AND id<>'39870')
	
	ORDER BY base_n.subtree_order
	";
	
	//_dump($sql);
	
	$items = array();
	
	$_items = getSimpleListSQL($sql);
	foreach ($_items as $item) {
		$items[$item["parent_id"]][$item["id"]] = $item;
	}
	return $items;
}

/**
 * 
 * Votes functions start
 * 
 * */

function checkAccessVoteById ($obj_parent_id, $ip) {
	
	deleteExpireVoting();
	
	$sql = "SELECT count(vote_id) FROM dbm_add_votes WHERE vote_id='".$obj_parent_id."' AND ip='".$ip."'";
	$cnt = DBUtils::execCountSelect($sql);
	return ($cnt==0);
	
}

function VoteById($obj_parent_id, $obj_id, $ip, $rate=1) {
	
	$days = 3600*24*1;
	
	deleteExpireVoting();
	//_dump($obj_parent_id); exit;
	if (checkAccessVoteById($obj_parent_id, $ip)) {
		$sql = "SELECT count(node_id) FROM dbm_nfv_voting_object WHERE node_id='".$obj_id."'";
		//_dump($sql);
		$cnt = DBUtils::execCountSelect($sql);
		if ($cnt>0) {
			$sql = " UPDATE dbm_nfv_voting_object SET vote_total_cnt=vote_total_cnt+'".$rate."' WHERE node_id='".$obj_id."' ";
			//_dump($sql);
			DBUtils::execUpdate($sql);
			$sql = "INSERT INTO dbm_add_votes (vote_id, ip, expire_time) VALUES ('".$obj_parent_id."', '".$ip."', '".date("Y-m-d H:i:s", time()+$days)."') ";
			//_dump($sql);
			DBUtils::execUpdate($sql);	
		}
	} else {
		return false;
	}
	
	return true;
	
}

function deleteExpireVoting () {
	
	$sql = "DELETE FROM dbm_add_votes WHERE expire_time<=now()";
	$rs = DBUtils::execUpdate($sql);
	
}
/**
 * 
 * Votes functions end
 * 
 * */

/**
 * 
 * Last days function begin
 * 
 **/

    function lastDays($date, $time)
    {
        if(!empty($time))
        {
            $totime = strtotime($date.$time);
            $time = ", ".date("H:i", $totime).":";
        }
        else
        {
            $totime = strtotime($date);
            $time = ":";
        }
                
        if(date("d.m.Y", $totime) == date("d.m.Y", strtotime("now")))
            return $date = "Сегодня".$time;
        elseif(date("d.m.Y", $totime) == date("d.m.Y", strtotime("now -1 day")))
            return $date = "Вчера".$time;
        elseif(strlen($time)<2)
            return $date = date("d.m.Y", $totime).":";
        else
            return $date = date("d.m.Y - H:i", $totime).":";
    }
/**
 * 
 * Last days function end
 * 
 **/

/**
 * 
 * Comments functions begin
 * 
 * */

    function addLike($id_user, $id_object, $classname)
    {
        $sql = "INSERT INTO `dbm_likes` 
                        SET `id_user`='".$id_user."', `id_object`='".$id_object."' ";
        DBUtils::execUpdate($sql);
        
        $sql = "UPDATE `dbm_nfv_".$classname."` SET `cnt`=`cnt`+1 WHERE `node_id`='".$id_object."'";
        DBUtils::execUpdate($sql);
    }
    
    function delLike($id_user, $id_object, $classname)
    {
        $sql = "DELETE FROM `dbm_likes` 
                        WHERE `id_user`='".$id_user."' AND `id_object`='".$id_object."' ";
        DBUtils::execUpdate($sql);
        
        $sql = "UPDATE `dbm_nfv_".$classname."` SET `cnt`=`cnt`-1 WHERE `node_id`='".$id_object."'";
        DBUtils::execUpdate($sql);        
    }

/**
 * 
 * Comments functions end
 * 
 * */
 
 /**
 * 
 * Parse function begin
 * 
 * @param string | $url // parse url
 * @param string | $start // tag to start position for parse
 * @param string | $end // tag to end position for parse
 * */
    function parseBlock($url, $start, $end)
    {
        if($url && $start && $end)
        {
            $str = file_get_contents($url);
    
            $start = strpos($str, $start);
    
            $len = strpos($str, $end) - $start;
                        
            $block = substr($str, $start, $len);
            return $block;
        }
        else
            return false;
    }
/**
 * 
 * Parse function end
 * 
 * */
 
function allowedImgs () {
	
	return array(
		'image/png',
		'image/gif',
		'image/jpeg',
	);
	
}


function getSimpleSQL ($classname, $fieldlist = null, $where = null, $orderby = null, $limit = null, $join = null, $index_id = false, $offset = null, $debug_mode_cms = false) {
	
	//global $debug_mode_cms;
	
	$fieldlist_default = array("node_id id", "name", "absolute_path");
	
	if (!$fieldlist) {
		$fieldlist = $fieldlist_default;
	} elseif($fieldlist[0]=="n.*" || $fieldlist[0]=="*") {
		$fieldlist = $fieldlist;
	} else {
		$fieldlist = array_merge($fieldlist, $fieldlist_default);
	}
	
	$from = "dbm_nfv_" . $classname . " n";
	
	$_fieldlist = array();
	if (is_array($fieldlist)) {
		foreach ($fieldlist as $field) {
			if (strpos($field, '.')===false) {
				$_fieldlist[] = "n.$field";
			} else {
				$_fieldlist[] = "$field";
			}
		}
		$fieldlist = implode(", ", $_fieldlist);
	}
	
	if ($where != null) {
		$where = str_replace("n.id", "n.node_id", $where);
		$where = "WHERE $where";
	}
	
	if ($orderby == null) {
		$orderby = "ORDER BY n.node_id";
	} else {
		$orderby = str_replace(" subtree_order", " node_id", $orderby);
		$orderby = str_replace(" n.subtree_order", " n.node_id", $orderby);
		$orderby = "ORDER BY $orderby";
	}
	
	if ($limit != null) {
		if ($offset != null) {
			$limit = "LIMIT $offset, $limit";
		} else {
			$limit = "LIMIT $limit";
		}
	}
	
	if ($join != null) {
		if (!is_array($join[0])) {
			$join = array($join);
		}
		$joins = array();
		foreach ($join as $join_item) {
			$join_item[1] = str_replace("n.id", "n.node_id", $join_item[1]);
			$joins[] = "{$join_item[2]} JOIN {$join_item[0]} ON {$join_item[1]}";
		}
		$join = implode(" ", $joins);
	}
	
	$sql = "SELECT $fieldlist FROM $from $join $where $orderby $limit ";
	
	if ($debug_mode_cms) {
		_dump($sql);
		//echo ($sql);
	}
	
	return getSimpleListSQL($sql, $index_id);
	
}

function getSimpleCountSQL ($classname, $where = null, $join = null) {
	
	$from = "dbm_nfv_" . $classname . " n";
	
	if ($where != null) {
		$where = "WHERE $where";
	}
	
	if ($join != null) {
		if (!is_array($join[0])) {
			$join = array($join);
		}
		$joins = array();
		foreach ($join as $join_item) {
			$join_item[1] = str_replace("n.id", "n.node_id", $join_item[1]);
			$joins[] = "{$join_item[2]} JOIN {$join_item[0]} ON {$join_item[1]}";
		}
		$join = implode(" ", $joins);
	}
	
	$sql = "SELECT count(node_id) FROM $from $join $where ";
	
	//_dump($sql);
	
	return getSimpleListCountSQL($sql);
}

function getMenusForSitemap () {
    $array_classes = array(
        "1" => "class_for_index",
        "36" => "section",
        "132" => "contacts",
        "56" => "section_text_image",
        "155" => "subsection_text_image",
        
    );
    
    $classes_id = array();
    foreach ($array_classes as $class_id => $class_shortname) {
        $classes_id[] = $class_id;
        $cc++;
    }
    
    $sql = "
        SELECT  base_n.id AS id
        , base_n.name AS name
        , base_n.shortname AS shortname
        , base_n.dynamic_template AS dynamic_template
        , base_n.subtree_order AS subtree_order
        , base_n.absolute_path AS absolute_path
        , base_n.parent_id AS parent_id
        
        FROM 
        dbm_nodes base_n
        
        WHERE (base_n.class_id IN (". implode(", ", $classes_id) .")) 
        
        ORDER BY base_n.subtree_order
    ";
    //_dump($sql);
    $items = array();
    
    $_items = getSimpleListSQL($sql);

    if($_items){
        foreach ($_items as $item) {
            $items[$item["parent_id"]][$item["id"]] = $item;
        }
    }
    
    return $items;
}

?>
