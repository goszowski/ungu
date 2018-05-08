<?php
$product = load_model('product');

$items = $product->getSales();
load_view('section_view', array('items'=>$items));