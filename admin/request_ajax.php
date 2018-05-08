<?
require_once('../_CORE/prepend.php');
Header("Content-Type: text/html; charset=" . SITE_CHARSET);
require_once('../_site_settings.php');

/*if (mb_strlen($request->getParameter("q"))<2) {
	return;
}*/
switch($request->getParameter("do")) {
	
	case "show" :
		
		$fields = array();
		
		if ($request->getParameter("class_name")=="country" || $request->getParameter("class_name")=="region" || $request->getParameter("class_name")=="city") {
			$fields = array("name");
		}
		
		if (count($fields)>1) {
			foreach ($fields as $field) {
				$add_where[] = "$field LIKE '%".addslashes(stripslashes($request->getParameter("q")))."%'";
			}
			$add_where = " OR " . implode(" OR ", $add_where);
		}
        		
		$where = "base_n.name LIKE '%".addslashes(stripslashes($request->getParameter("q")))."%' $add_where ";
        		
		$items = getSimpleList($request->getParameter("class_name"), $fields, $where);
		
		foreach ($items as $item) {
			echo $item["name"];
			if (count($fields)>1) {
				foreach ($fields as $field) {
					echo ", " . $item[$field];
				}
				
			}
			echo "|" . $item["absolute_path"] . "\n";
		}
		break;
        
	default :
		//
		break;
		
}

?>