<?php
$product = load_model('product');

$items = $product->getNew();
load_view('section_view', array('items'=>$items));