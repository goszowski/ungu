<table>
	<tr>
		<td>
			<?$i=0;?>
			<?foreach($CM_PARAMS["available_values"] as $avail_value):?>
				<?if($i%2 == 0):?>
				<div>
					<?$checked = false; foreach($CM_PARAMS["values"] as $value) {if($value == $i) {$checked = true;}}?>
					<label class="ui-checks">
						<input type="checkbox" name="<?=$CM_CONTROL_NAME?>[]" value="<?=$i?>"<?if($checked){?> checked<?}?>>
						<i></i> <?=$avail_value?>
					</label>
				</div>
				<?endif;?>
			<?$i++;?>
			<?endforeach;?>
		</td>

		<td>
			<?$i=0;?>
			<?foreach($CM_PARAMS["available_values"] as $avail_value):?>
				<?if($i%2 != 0):?>
				<div>
					<?$checked = false; foreach($CM_PARAMS["values"] as $value) {if($value == $i) {$checked = true;}}?>
					<label class="ui-checks">
						<input type="checkbox" name="<?=$CM_CONTROL_NAME?>[]" value="<?=$i?>"<?if($checked){?> checked<?}?>>
						<i></i> <?=$avail_value?>
					</label>
				</div>
				<?endif;?>
			<?$i++;?>
			<?endforeach;?>
		</td>
	</tr>
</table>

<?if($CM_PARAMS["haveOther"]):?>
	<input type="text" class="form-control input-sm" name="<?=$CM_CONTROL_NAME?>" value="<?=$CM_PARAMS["other"]?>" placeholder="<?=$CM_PARAMS["other_name"]?>" style="width: 200px;">
<?endif;?>