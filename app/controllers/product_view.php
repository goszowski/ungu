<?php 
$product = load_model('product');
//$item = getSimpleList('product', array('price', 'image', 'sizes', 'reference', 'info', 'available'), 'id='.$CurrentNode->id, false, 1)[0];
$item = $product->getById($CurrentNode->id);
$images = getSimpleList('image', array('image'), 'parent_id='.$CurrentNode->id);
if($item['sizes']) $sizes = getSimpleList('size', false, 'id IN ('.$item['sizes'].')');
else $sizes = false;

load_view('product_view', array('item'=>$item, 'images'=>$images, 'sizes'=>$sizes));