<?if(! $section['inner']):?>
  <div class="alert alert-danger">Error</div>
<?else:?>
  <div class="row">
    <div class="col-xs-12 col-md-6">
      <?foreach($section['inner'] as $k=>$input):?>
        <?if($k%2 == 0):?>
          <div class="form-group">
            <label for="shipping_<?=$k?>"><?=$input['name']?></label>
            <?if($input['alternative']):?>
              <label for="shipping_<?=$k?>" class="alternative" style="display: none;"><?=$input['alternative']?></label>
            <?endif;?>
            <input class="form-control" id="shipping_<?=$k?>" type="<?=$input['type']?>" name="<?=$input['fieldname']?>" required>
          </div>
        <?endif;?>
      <?endforeach;?>
    </div>

    <div class="col-xs-12 col-md-6">
      <?foreach($section['inner'] as $k=>$input):?>
        <?if($k%2 != 0):?>
          <div class="form-group">
            <label for="shipping_<?=$k?>"><?=$input['name']?></label>
            <?if($input['alternative']):?>
              <label for="shipping_<?=$k?>" class="alternative" style="display: none;"><?=$input['alternative']?></label>
            <?endif;?>
            <input class="form-control" id="shipping_<?=$k?>" type="<?=$input['type']?>" name="<?=$input['fieldname']?>" required>
          </div>
        <?endif;?>
      <?endforeach;?>
    </div>
  </div>

<?endif;?>
