<a href="/" class="logo logo-lg hidden-xs pl-5">
	<img src="/asset/images/logo.png" style="max-width: 60px;">
</a>

<div class="container-fluid visible-xs pb-15-xs">
	<div class="row">
		<div class="col-xs-6">
			<a href="/" class="logo">
				<div class="image-section">
					<img src="/asset/images/logo.png" style="max-width: 40px;">
				</div>
			</a>
		</div>
		<div class="col-xs-6 text-right">
			<button class="btn menu-toogle" type="button" onclick="$('ul.menu').toggleClass('active');"><i class="fa fa-bars fa-2x"></i></button>
		</div>
	</div>
</div>

<ul class="menu text-left-xs pb-15-xs-important">
	<li class="lg text-uppercase <?if($path=='/new-in'):?>active<?endif;?> mb-20-important"><a href="/new-in">New in</a></li>
	<?foreach($items as $item):?>
		<li class="<?if($CurrentNode->id == $item['id'] or $CurrentNode->parent_id == $item['id']):?>active<?endif;?>">
			<a href="<?=$item['absolute_path']?>"><?=$item['name']?></a>
		</li>
	<?endforeach;?>
	<li class="text-uppercase line-red <?if($path=='/sales'):?>active<?endif;?>"><a href="/sales">Sales</a></li>
	<li class="text-uppercase <?if($path=='/contact'):?>active<?endif;?>"><a href="/contact">Contact</a></li>

	<div class="visible-xs">
		<?use_controller('partials/_social');?>
	</div>

</ul>


