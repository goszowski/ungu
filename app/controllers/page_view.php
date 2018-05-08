<?php 
$item = getSimpleList('page', array('text', 'show_title'), 'id='.$CurrentNode->id, false, 1)[0];

load_view('page_view', array('item'=>$item));