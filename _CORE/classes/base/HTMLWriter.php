<?php

/*
 Function : draw_breaking_links
 Description :

 */

class PagingLink {
	var $type = "";
	var $page = 0;
	var $first = 0;
	var $last = 0;
	var $href = "";
	var $index = 0;
}

function _default_draw_paging_link(&$link) {
	$str = '';
	if ($link->index != 0) {
		$str .= ' | ';
	}

	switch($link->type) {
		case "prev":
			$str .= '<a href="' . $link->href . '">&lt;&lt;</a>';
			break;
		case "next":
			$str .= ' <a href="' . $link->href . '">&gt;&gt;</a>';
			break;
		case "link":
			$str .= '<a href="' . $link->href . '">' . ($link->first + 1) . ".." . ($link->last  + 1) . '</a>';
			break;
		default:
			$str .= ($link->first + 1) . ".." . ($link->last  + 1);
			break;
	}

	return $str;
}

/**
 * Для рисования основных деталей страниц, таких, как :
 *
 * списки <option ... для select'ов по данным из базы данных,
 * линейки ссылок для разб. большого списка
 * @package base
 */
class HTMLWriter {
	function breaking_links_as_xml (
	$offset, //смещение от начала списка строк, передается get-/post-ом
	$rpp,//число строк на страницу
	$lpp,//число линков на странице
	$total,//общ. кол-во строк
	$hidden_names = "",//имена переменных, которые надо передать в скрипт(массив)
	$hidden_values = ""//значения <...>
	)
	{
		global $SCRIPT_NAME;//текущий скрипт

		$xml = new xmlElement("breaking_links");

		$offset = abs((int)$offset);//на всякий случай...
		if($offset > $total) {
			$offset = $total;
		}

		//подготовить список переменных, которые надо передать в скрипт
		$hiddens = "";
		if(!empty($hidden_names))
		for( $i = 0; $i < count($hidden_names); $i++)
		$hiddens .= "&".$hidden_names[$i]."=".$hidden_values[$i];

		//все остальное...
		$pagenum = (int)($offset/($rpp*$lpp));
		$startoff = $rpp*$lpp*$pagenum;

		if($startoff > 0){
			$tmp = new xmlElement("home");
			$tmp->setAttribute("href", "$SCRIPT_NAME?offset=0$hiddens");
			$xml->addNode($tmp);
			$tmp = new xmlElement("previous");
			$tmp->setAttribute("href", "$SCRIPT_NAME?offset=".($startoff-$rpp)."$hiddens");
			$xml->addNode($tmp);
		}

		$endoff = min($total, $startoff + $rpp*$lpp);

		for($off = $startoff, $i = 1; $off < $endoff; $i++, $off = $startoff + $rpp*($i-1)) {
			if($off == $offset) {
				$tagname = "current";
			} else {
				$tagname = "link";
			}

			$tmp = new xmlElement($tagname);
			$tmp->setAttribute("href", "$SCRIPT_NAME?offset=$off$hiddens");
			$tmp->cdata = ($off+1)."-".min($off+$rpp,$endoff);
			$xml->addNode($tmp);
		}

		if($endoff < $total) {
			$endpagenum = (int)(($total-1)/($rpp*$lpp));
			$endstartoff = $rpp*$lpp*$endpagenum;

			$tmp = new xmlElement("next");
			$tmp->setAttribute("href", "$SCRIPT_NAME?offset=$endoff$hiddens");
			$xml->addNode($tmp);
			$tmp = new xmlElement("end");
			$tmp->setAttribute("href", "$SCRIPT_NAME?offset=$endstartoff$hiddens");
			$xml->addNode($tmp);
		}

		return $xml;
	}

    /**
     * рисует линейку ссылок для разб. большого списка на части след. вида:
     *    < << 6 7 8 9 10 >> >
     * @access public
     * @return void
     */
    public static function draw_paging_links(
    $offset, //смещение от начала списка строк, передается get-/post-ом
    $rpp,//число строк на страницу
    $lpp,//число линков на странице
    $total,//общ. кол-во строк
    $hidden_names = "",//имена переменных, которые надо передать в скрипт(массив)
    $hidden_values = "",//значения <...>
    $draw_func_name = "_draw_paging_link",
    $anchor="",
    $pageVarName = "page"
    ) {
    	global $request;
    	$SCRIPT_NAME = $_SERVER['REQUEST_URI'];//текущий скрипт
    	$p = strpos($SCRIPT_NAME, "?");
    	if ($p !== false) {
    		$SCRIPT_NAME = substr($SCRIPT_NAME, 0, $p);
    	}

    	$hiddens = "";
    	if(!empty($hidden_names))
    	for( $i = 0; $i < count($hidden_names); $i++)
    	$hiddens .= "&".$hidden_names[$i]."=".$hidden_values[$i];

    	$pagingLinks = HTMLWriter::getPagingLinks($SCRIPT_NAME, $offset / $rpp, $rpp, $lpp, $total, $pageVarName, $hiddens, $anchor);
    	for($i = 0; $i < sizeof($pagingLinks); $i++) {
    		$link = &$pagingLinks[$i];
    	    $str = call_user_func($draw_func_name, $link);
			
				 echo $str;

    	}
    }

    public static function getPagingLinks(
    $baseURL,
    $currentPage,
    $rowsPerPage,
    $pageLinksPerPage,
    $totalCount,
    $pageVarName = "page",
    $requestParams = "",
    $anchor = ""
    )
    {
    	$links = array();

    	$startPage = $currentPage - ($currentPage % $pageLinksPerPage);
    	$lastPage = (int) ($totalCount / $rowsPerPage) - ($totalCount % $rowsPerPage == 0 ? 1 : 0);
    	$endPage = min($lastPage, $startPage + $pageLinksPerPage - 1);

    	$page = $startPage - 1;

    	$index = 0;

    	if($startPage != 0) {
    		$link = new PagingLink();
    		$link->type = "prev";
    		$link->page = $page;
    		$link->first = $page * $rowsPerPage;
    		$link->last = $link->first + $rowsPerPage - 1;
    		$link->href = $baseURL . "?" . $pageVarName . "=" . $link->page . $requestParams . $anchor;

    		$link->index = $index++;
    		$links[] = &$link;
    	}

    	$page++;

    	for(; $page <= $endPage; $page++) {
    		$link = new PagingLink();

    		$link->page = $page;
    		$link->first = $page * $rowsPerPage;
    		$link->last = min($totalCount - 1, $link->first + $rowsPerPage - 1);
    		if ($page != $currentPage) {
    			$link->type = "link";
    			$link->href = ($page == $currentPage) ? "" : $baseURL . "?" . $pageVarName . "=" . $link->page . $requestParams . $anchor;
    		} else {
    			$link->type = "cur";
    			$link->href = "";
    		}

    		$link->index = $index++;
    		$links[] = &$link;
    	}

    	if($endPage < $lastPage) {
    		$link = new PagingLink();
    		$link->type = "next";
    		$link->page = $page;
    		$link->first = $page * $rowsPerPage - 1;
    		$link->last = min($totalCount - 1, $link->first + $rowsPerPage);
    		$link->href = $baseURL . "?" . $pageVarName . "=" . $link->page . $requestParams . $anchor;

    		$link->index = $index++;
    		$links[] = &$link;
    	}

    	return $links;
    }
}

?>