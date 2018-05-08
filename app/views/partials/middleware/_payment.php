<?if(! $section['inner']):?>
  <div class="alert alert-danger">Error</div>
<?else:?>


  <ul class="ungu-radio-icon">
    <input type="hidden" name="payment_option" value="<?=$section['inner'][0]['id']?>">
    <?foreach($section['inner'] as $k=>$payment):?>
      <?if($payment['is_active']):?>
      <li data-value="<?=$payment['id']?>" class="<?if($k==0):?>checked<?endif;?>">
        <span class="ungu-label"><?=$payment['name']?></span>
        <?if($payment['icon']):?><span class="ungu-icon"><img src="/imglib<?=$payment['icon']?>" alt="" /></span><?endif;?>
      </li>
      <?endif;?>
    <?endforeach;?>
  </ul>
<?endif;?>
