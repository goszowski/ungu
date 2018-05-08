<table border="0" cellspacing="0" cellpadding="4">
<tr><td><select name="<?=$CM_CONTROL_NAME?>">
<option value="0"><?=$AdminTrnsl["FormFieldSelectForm"]?>
<? $i=0;foreach ($CM_PARAMS["all_forms"] as $form) {?>
<option value="<?=$form->id?>"<?if($CM_PARAMS["form_id"] == $form->id){?> SELECTED<?}?>><?=$form->name?><?$i++;}?>
</select></td>
</tr></table>
