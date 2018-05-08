<?load_view('partials/_head');?>
<div class="container">
	<div class="row">
		<div class="col-xs-12 col-sm-4 col-md-3 col-lg-2">
			<?use_controller('partials/_menu');?>
		</div>

		<form action="/middleware" method="POST">
		<input type="hidden" name="product_id" value="<?=$item['id']?>">
		<input type="hidden" name="do" value="create_order">
		<div class="col-xs-12 col-sm-8 col-md-9 col-lg-10">
			<div class="container-fluid">
				<div class="hidden-xs"><?use_controller('partials/_social');?></div>
				<div class="row">
					<div class="col-xs-12 col-sm-6">

						<!-- Desktop/tablet -->
						<div class="owl-carousel-navs container-fluid magnific-popup-wrapper hidden-xs">
							<?foreach($images as $image):?>
								<div class="product-image-thumb mb-10 magnific-item" href="/imglib<?=$image['image']?>">
									<div class="image-section">
										<img src="/imglib<?=$image['image']?>">
									</div>
								</div>
							<?endforeach;?>
						</div>
						<!-- / Desktop/tablet -->

						<!-- Mobile only -->
						<div class="owl-carousel-wrapper visible-xs">
							<div class="owl-carousel">
								<?foreach($images as $image):?>
									<div class="item">
										<div class="image-section">
											<img src="/imglib<?=$image['image']?>">
										</div>
									</div>
								<?endforeach;?>
							</div>
						</div>
						<!-- / Mobile only -->

					</div>

					<div class="col-xs-12 col-sm-6">
						<div style="margin-left: auto; margin-right: auto; max-width: 370px;">
							<h2 class="h3 text-uppercase text-center"><b><?=$item['name']?></b></h2>
							<div class="text-center">
								<small>Product Code: <?=$item['reference']?></small>
							</div>

							<hr class="small">


							<?if($item['available']):?>
							<div class="mt-15 mb-15 text-warning text-center">
								<?if($item['price_with_sale']):?>
									<h3><?=number_format($item['price_with_sale']);?> <?=$website_data['currency']?> (<strike><?=$item['price']?> <?=$website_data['currency']?></strike>)</h3>
								<?else:?>
									<h3><?=number_format($item['price']);?> <?=$website_data['currency']?></h3>
								<?endif;?>
							</div>
							<?endif;?>

							<!-- Sizes -->
							<?if($sizes and $item['available']):?>
								<select name="size_id" class="form-control input-lg mt-30" required>
									<!-- <option value="" selected>Size</option> -->
									<?foreach($sizes as $k=>$size):?>
										<option value="<?=$size['id']?>"><?=$size['name']?></option>
									<?endforeach;?>
								</select>
								<button type="submit" class="btn btn-lg btn-black btn-block text-uppercase mt-50 l-space">
									<?if($item['preorder']):?>Pre-order<?else:?>Order now<?endif;?>
								</button>
							<?else:?>
								<div class="alert alert-danger text-center text-uppercase">Out of stock</div>
							<?endif;?>
							<!-- / Sizes -->

							<hr class="mt-50">

							<div class="text-center text-muted">
								<?=$item['info']?>
							</div>

							<hr>
						</div>
					</div>
				</div>
			</div>
		</div>
		</form>
	</div>
</div>

<script>
	$(function(){

		$('.magnific-popup-wrapper a.magnific-item').on('click', function(){
			var link = $(this).attr('href');
			$('#image-view').attr('src', link);
			return false;
		});

		$('.owl-carousel').owlCarousel({
			items:1,
			loop:false,
			center:true,
			margin:15,
			stagePadding: 25
		});

		$('.owl-carousel-navs').owlCarousel({
			items:1,
			loop:false,
			nav: true,
			navRewind: false
		});
	});
</script>

<?load_view('partials/_foot');?>
