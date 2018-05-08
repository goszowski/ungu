

<div class="container mt-35">
	<div class="row">
		<div class="col-xs-12 col-sm-3 pb-100 product-view-images-section">

			<div class="stock-view-image">
				<a href="#">
					<img src="images/stock-item.jpg" alt="...">
				</a>
			</div>

			<div class="stock-view-thimbs">
				<div class="item">
					<a href="#">
						<img src="images/stock-item.jpg" alt="...">
					</a>
				</div>

				<div class="item">
					<a href="#">
						<img src="images/stock-item.jpg" alt="...">
					</a>
				</div>

				<div class="item">
					<a href="#">
						<img src="images/stock-item.jpg" alt="...">
					</a>
				</div>
			</div>

		</div>

		<article class="col-xs-12 col-sm-9">
			<h1 class="h4 text-primary">Lorem ipsum dolor sit amet, consectetur adipisicing elit.</h1>
			<p><small>Номер товара: <b>12441</b></small></p>

			<p>Lorem ipsum dolor sit amet, consectetur adipisicing elit, sed do eiusmod
			tempor incididunt ut labore et dolore magna aliqua. Ut enim ad minim veniam,
			quis nostrud exercitation ullamco</p>

			<p>Labore et dolore magna aliqua. Ut enim ad minim veniam,
			quis nostrud exercitation ullamco laboris nisi ut aliquip ex ea commodo
			consequat. Duis aute irure dolor in reprehenderit in voluptate velit esse
			cillum dolore eu fugiat nulla pariatur. Excepteur sint occaecat cupidatat non
			proident, sunt in culpa qui officia deserunt mollit anim id est laborum.</p>

			<div class="product-view-controls">
				<div class="product-view-control mt-25">

					<div class="text-uppercase">
						<div class="product-view-control-icon"><i class="fa fa-paint-brush"></i></div> <b>Цвет</b>
					</div>

					<div class="colors mt-15">
						<input type="hidden" name="color" value="white">
						<input type="hidden" name="color" value="colored">

						<div class="color-check"></div>
						<div class="color-check">
							<div class="color-check-inner blue"></div>
							<div class="color-check-inner red"></div>
							<div class="color-check-inner yellow"></div>
							<div class="color-check-inner green"></div>
						</div>
					</div>
				</div>

				<div class="product-view-control mt-25">

					<div class="text-uppercase">
						<div class="product-view-control-icon"><i class="fa fa-expand"></i></div> <b>Размер</b>
					</div>

					<div class="colors mt-15">
						<input type="hidden" name="color" value="white">
						<input type="hidden" name="color" value="colored">

						<div class="color-check"></div>
						<div class="color-check">
							<div class="color-check-inner blue"></div>
							<div class="color-check-inner red"></div>
							<div class="color-check-inner yellow"></div>
							<div class="color-check-inner green"></div>
						</div>
					</div>
				</div>

				<div class="product-view-control mt-25">
					<button class="btn btn-lg btn-primary text-uppercase"><i class="fa fa-credit-card"></i> Беру!</button>
				</div>
			</div>
		</article>
	</div>
</div>

<script type="text/javascript">
	$(function(){
		$('.stock-view-thimbs').owlCarousel({
			items: 2
		});
	});	
</script>