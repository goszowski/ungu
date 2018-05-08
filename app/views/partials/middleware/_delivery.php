<?if(! $section['inner']):?>
  <div class="alert alert-danger">Error</div>
<?else:?>
  <ul class="ungu-radio-icon">
    <input type="hidden" name="delivery_option" value="<?=$section['inner'][0]['id']?>">
    <?foreach($section['inner'] as $k=>$delivery):?>
      <li data-value="<?=$delivery['id']?>" class="<?if($k==0):?>checked<?endif;?> <?if($delivery['affect_alternative']):?>affect_alternative<?endif;?>">
        <span class="ungu-label"><?=$delivery['name']?></span>
        <span class="ungu-icon"><img src="/imglib<?=$delivery['icon']?>" alt="" /></span>
      </li>
    <?endforeach;?>
  </ul>
<?endif;?>
