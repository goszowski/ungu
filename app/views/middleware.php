<?load_view('partials/_head');?>
<div class="container">
	<div class="row">
		<div class="col-xs-12 col-sm-4 col-md-3 col-lg-2">
			<?use_controller('partials/_menu');?>
		</div>

		<div class="col-xs-12 col-sm-8 col-md-9 col-lg-10 pb-100">
			<div class="hidden-xs"><?use_controller('partials/_social');?></div>
			<?if(! $sections):?>
        <div class="alert alert-danger">Error</div>
      <?else:?>
        <form action="/order" method="post">
          <input type="hidden" name="do" value="<?=$do?>">
					<input type="hidden" name="product_id" value="<?=$product['id']?>">
					<input type="hidden" name="size_id" value="<?=$size_data['id']?>">
          <?foreach($sections as $key=>$section):?>
            <h2 class="h3 text-uppercase"><?=(++$key)?>. <?=$section['name']?></h2>
            <?load_view('partials/middleware/_'.$section['type'], ['section'=>$section, 'product'=>$product, 'size_data'=>$size_data])?>
            <hr>
          <?endforeach;?>

          <div class="text-center">
            <button type="submit" class="btn btn-lg btn-black btn-block text-uppercase l-space" name="button"><i class="fa fa-check"></i>Checkout</button>
          </div>

        </form>
      <?endif;?>
		</div>
	</div>
</div>

<script>
	$(function(){
		$('.ungu-radio-icon > li').on('click', function(){
			$(this).parent().children('li').removeClass('checked');
			$(this).addClass('checked');
			$(this).parent().children('input[type="hidden"]').val($(this).data('value'));

			if($(this).hasClass('affect_alternative')) {
				$('label.alternative').each(function(){
					$(this).parent().children('label').hide();
					$(this).show();
				});
			}
			else {
				{
					$('label.alternative').each(function(){
						$(this).parent().children('label').show();
						$(this).hide();
					});
				}
			}
		});
	});
</script>
<?load_view('partials/_foot');?>
