<?if(!$items):?>
<div class="alert alert-info">
	No items
</div>
<?else:?>
<div class="container-fluid pl-1-xs pr-1-xs">
	<div class="row">
		<?foreach($items as $item):?>
		<div class="col-xs-12 col-sm-6 mb-30 pl-1-xs pr-1-xs">
			<a class="product-list-item" href="<?=$item['absolute_path']?>">
				<div class="image-section">
					<img src="/imglib<?=$item['image']?>" alt="<?=$item['name']?>">
				</div>
				<div class="text-center pt-10 text-uppercase">
					<small><?=$item['name']?></small>
				</div>
				<div class="text-center product-list-item-title">
					
					<?if($item['available']):?>
						<?if($item['price_with_sale']):?>
							<small><?=$item['price_with_sale']?> <?=$website_data['currency']?> (<strike><?=$item['price']?> <?=$website_data['currency']?></strike>)</small>
						<?else:?>
							<small><?=$item['price']?> <?=$website_data['currency']?></small>
						<?endif;?>
					<?else:?>
						<span class="label label-danger text-uppercase">Out of stock</span>
					<?endif;?>
				</div>
			</a>
		</div>
		<?endforeach;?>
	</div>
</div>
<?endif;?>