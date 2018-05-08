<?php

// Product model

class product {

	public $fields = array('price', 'sale', 'image', 'reference', 'available', 'preorder', 'info', 'sizes');


	private function sales_operations($items)
	{
		if($items)
		{
			foreach($items as $k=>$item)
			{
				if($item['sale'] and $item['sale'] > 0 and $item['sale'] <= 100)
				{
					$price = $item['price'];
					$new_price = $price - $price * $item['sale'] / 100;
					$items[$k]['price_with_sale'] = $new_price;
				}
			}
		}

		return $items;
	}


	// Get items by some category
	// return arr
	public function getByCategory($category_id)
	{
		// TODO: add limits and order
		$items = getSimpleList('product', $this->fields, 'parent_id='.$category_id, 'pubdate DESC');

		// Sales operations
		$items = $this->sales_operations($items);

		return $items;
	}


	// Get item only on home page
	// return arr
	public function getHome()
	{
		// TODO: add limits and order
		$items = getSimpleList('product', $this->fields, 'on_home=1', 'pubdate DESC');

		// Sales operations
		$items = $this->sales_operations($items);

		return $items;
	}


	// Get only news in full catalog
	// return arr
	public function getNew()
	{
		// TODO: add limits and order
		$items = getSimpleList('product', $this->fields, 'new_in=1', 'pubdate DESC');

		// Sales operations
		$items = $this->sales_operations($items);

		return $items;
	}

	// Get only items whith sales in full catalog
	// return arr
	public function getSales()
	{
		// TODO: add limits and order
		$items = getSimpleList('product', $this->fields, 'sale>0 and sale<=100', 'pubdate DESC');

		// Sales operations
		$items = $this->sales_operations($items);

		return $items;
	}


	public function getById($id)
	{
		// TODO: add limits and order
		$items = getSimpleList('product', $this->fields, 'id='.$id, false, 1);

		// Sales operations
		$items = $this->sales_operations($items);

		return $items[0];
	}


}
