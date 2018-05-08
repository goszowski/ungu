<?php 

$items = getSimpleList('section');

load_view('partials/_menu', array('items'=>$items));