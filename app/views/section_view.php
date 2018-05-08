<?load_view('partials/_head');?>
<div class="container">
	<div class="row">
		<div class="col-xs-12 col-sm-4 col-md-3 col-lg-2">
			<?use_controller('partials/_menu');?>
		</div>

		<div class="col-xs-12 col-sm-8 col-md-9 col-lg-10">
			<div class="hidden-xs"><?use_controller('partials/_social');?></div>
			<?load_view('partials/_items', array('items'=>$items))?>
		</div>
	</div>
</div>
<?load_view('partials/_foot');?>