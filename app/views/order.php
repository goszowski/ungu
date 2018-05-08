<?load_view('partials/_head');?>
<div class="container">
	<div class="row">
		<div class="col-xs-12 col-sm-4 col-md-3 col-lg-2">
			<?use_controller('partials/_menu');?>
		</div>
		
		<div class="col-xs-12 col-sm-8 col-md-9 col-lg-10">
			<form action="/order?confirm" method="POST" class="order-form">
				<input type="hidden" name="token" value="<?=$token?>">
				
				<div class="form-group">
					
					<div class="row">
						<div class="col-xs-12 col-sm-4">
							<div class="form-group">
								<label>Contact name</label>
								<input class="form-control" type="text" name="contact_name" required>
							</div>
						</div>
						<div class="col-xs-12 col-sm-4">
							<div class="form-group">
								<label>Contact surname</label>
								<input class="form-control" type="text" name="contact_surname" required>
							</div>
						</div>
						<div class="col-xs-12 col-sm-4">
							<div class="form-group">
								<label>Contact phone</label>
								<input class="form-control" type="text" name="contact_phone" required>
							</div>
						</div>
					</div>
				</div>
				
				
				
				<div class="form-group pt-15">
					<label>Delivery information</label>
					<div class="row">
						<div class="col-xs-12 col-sm-6">
							<div class="form-group">
								<label>Country</label>
								<input class="form-control" type="text" name="country" required>
							</div>
						</div>
						<div class="col-xs-12 col-sm-6">
							<div class="form-group">
								<label>City</label>
								<input class="form-control" type="text" name="city" required>
							</div>
						</div>
						<div class="col-xs-12 col-sm-6">
							<div class="form-group">
								<label>Street</label>
								<input class="form-control" type="text" name="street" required>
							</div>
						</div>
						<div class="col-xs-12 col-sm-6">
							<div class="form-group">
								<label>Appartment number</label>
								<input class="form-control" type="text" name="appartament">
							</div>
						</div>
					</div>
				</div>
				
				<div class="form-group">
					<button type="submit" class="btn btn-lg btn-black text-uppercase">Confirm</button>
				</div>
			</form>
		</div>
	</div>
</div>
<?load_view('partials/_foot');?>