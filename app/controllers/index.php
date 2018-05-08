<?php 
$product = load_model('product');
// items on home page 
$items = $product->getHome();

load_view('index', array('items'=>$items));