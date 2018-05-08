<?php 
$product = load_model('product');

$items = $product->getByCategory($CurrentNode->id);
load_view('section_view', array('items'=>$items));