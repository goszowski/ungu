<?php
$product = load_model('product'); // Loading product model
$middleware = load_model('middleware'); // loading middleware model
$size = load_model('size');

$product_id = $_POST['product_id'];
$do = $_POST['do'];
$size_id = $_POST['size_id'];
$size_data = $size->get($size_id);

$item = $product->getById($product_id);

// Middleware sections
$sections = $middleware->getSections();

load_view('middleware', ['product'=>$item, 'do'=>$do, 'size_id'=>$size_id, 'sections'=>$sections, 'size_data'=>$size_data]);
