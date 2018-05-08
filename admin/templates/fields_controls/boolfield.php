<?$cname = $CM_CONTROL_NAME;?>

<div class="mt-5">
	<label class="ui-checks mr-10">
		<input type="radio" name="<?=$cname?>" value="1"<?if ($CM_PARAMS['value']) {?> CHECKED<?}?>>
		<i></i> <?=$AdminTrnsl["Yes"]?>
	</label>

	<label class="ui-checks">
		<input type="radio" name="<?=$cname?>" value="0"<?if (!$CM_PARAMS['value']) {?> CHECKED<?}?>>
		<i></i> <?=$AdminTrnsl["No"]?>
	</label>
</div>