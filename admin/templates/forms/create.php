<?usetemplate("forms/header")?>
<table width=100% border=0 cellpadding=0 cellspacing=0 class=top3><td><img src=/admin/_img/s.gif width=1 height=1></td></table>
<table width=100% border=0 cellpadding=4 cellspacing=0>
<tr class=top41>
<td height=30 id=pad18 colspan=2 nowrap class=head><a href="/admin/forms.php" id=black><?=$AdminTrnsl["Forms_Management"]?></a>: <b><?=$AdminTrnsl["Creating_New_Form"]?></b></td>
</tr>
</table>
<table width=100% border=0 cellpadding=0 cellspacing=0 class=top2><td><img src=/admin/_img/s.gif width=1 height=1></td></table>
<table width=100% border=0 cellpadding=4 cellspacing=0>
<?if ($MSG != null) {?><tr>
<td id=pad18><span id="red"><?=$MSG?></span></td>
</tr><?}?>
<tr>
<td id=pad18>

<form action=/admin/forms.php method=post>
<input type="hidden" name="do" value="create">
<br>

<table width=100% border=0 cellpadding=0 cellspacing=0 class=backtab3>

<tr id=back2>
<td id=pad5><nobr><b><?=$AdminTrnsl["Name"]?></b> <span id=red>*</span></nobr></td>
<td id=back3 width=80%>
<input type=text name=name style="width:495px" value="<?=htmlrparamOut("name")?>">
<?if ($ERRORS['name'] != null) {?><div class=errmsg><?= $AdminTrnsl["FORM_EDIT_ERROR_".$ERRORS['name']]?></div><?}?></td>
</tr>

<tr id=back2>
<td id=pad5><nobr><b><?=$AdminTrnsl["FormDescrText"]?></b></nobr></td>
<td id=back3 width=80%>
<textarea name=description_text style="width:495px"><?=htmlrparamOut("description_text")?></textarea>
<?if ($ERRORS['description_text'] != null) {?><div class=errmsg><?= $AdminTrnsl["FORM_EDIT_ERROR_".$ERRORS['description_text']]?></div><?}?></td>
</tr>

<tr id=back2>
<td id=pad5><nobr><b><?=$AdminTrnsl["FormSuccessText"]?></b></nobr></td>
<td id=back3 width=80%>
<textarea name=success_text style="width:495px"><?=htmlrparamOut("success_text")?></textarea>
<?if ($ERRORS['success_text'] != null) {?><div class=errmsg><?= $AdminTrnsl["FORM_EDIT_ERROR_".$ERRORS['success_text']]?></div><?}?></td>
</tr>

<tr id=back2>
<td id=pad5>&nbsp;</td>
<td id=back3 width=80%><input type=checkbox name=inc_sub value=1 <?if ($request->getParameter("inc_sub") == 1) {?> CHECKED<?}?>>&nbsp;<?=$AdminTrnsl["Forms_inc_sub"]?></td>
</tr>
<tr id=back2>
<td>&nbsp;</td>
<td id=pad10>
<input type=submit name=submit value="<?=$AdminTrnsl["Create_Form"]?>" size=32 style="width:150px" class=button5></td>
</tr>
</form>
</table>


</td>
</tr>
</table>

</body>
</html>
