<?if(! $section['inner']):?>
  <div class="alert alert-danger">Error</div>
<?else:?>

  <div class="row">
    <div class="col-md-12 col-lg-6">
      <label for="payment_option">Choice of payment</label>
      <select name="payment_option" class="form-control" id="payment_option">
        <?foreach($section['inner'] as $k=>$payment):?>
          <?if($payment['is_active']):?>
            <option value="<?=$payment['id']?>"><?=$payment['name']?></option>
          <?endif;?>
        <?endforeach;?>
      </select>
    </div>
  </div>

<?endif;?>
