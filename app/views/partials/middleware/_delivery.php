<?if(! $section['inner']):?>
  <div class="alert alert-danger">Error</div>
<?else:?>
  <div class="row">
    <div class="col-md-12 col-lg-6">
      <label for="delivery_option">Choice of deliery</label>
      <select name="delivery_option" class="form-control" id="delivery_option">
        <?foreach($section['inner'] as $k=>$delivery):?>
          <option value="<?=$delivery['id']?>"><?=$delivery['name']?></option>
        <?endforeach;?>
      </select>
    </div>
  </div>
<?endif;?>
