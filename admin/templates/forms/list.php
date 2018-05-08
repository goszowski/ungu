<?usetemplate("forms/header")?>
<table width=100% border=0 cellpadding=0 cellspacing=0 class=top3><td><img src=/admin/_img/s.gif width=1 height=1></td></table>
<table width=100% border=0 cellpadding=4 cellspacing=0>
<tr class=top41>
<td height=30 id=pad18 colspan=2 nowrap class=head><b><?=$AdminTrnsl["Forms_Management"]?></b></td>
</tr>
<?if ($MSG != null) {?><tr>
<td id=pad18><span id="red"><?=$MSG?></span></td>
</tr><?}?>
</table>
<table width=100% border=0 cellpadding=0 cellspacing=0 class=top2><td><img src=/admin/_img/s.gif width=1 height=1></td></table>
<table width=100% border=0 cellpadding=4 cellspacing=0>
<form action="/admin/forms.php" method=POST>
<input type="hidden" name="do" value="create_form">
<tr class=top4>
<td height=36 id=pad18 nowrap><input type=submit value="<?=$AdminTrnsl["Create_New_Form"]?>" title="" style="width:150px" class=button1>
</tr>
</form>
<tr>
<td id=pad18>

<br>
<table width=100% border=0 cellpadding=0 cellspacing=0 class=backtab2>
<tr id=back2 class=header>
<td class=td2 width=70%><a href="/admin/forms.php?sortby=name"><?=$AdminTrnsl["Name"]?></a></td>
<td><?=$AdminTrnsl["Actions"]?></td>
</tr>
<? foreach ($formDefs as $formDef) {?>
<tr id=back3>
<td class=td2><a href=/admin/forms.php?do=edit&form_id=<?=$formDef->id?>><?=$formDef->name?></a></td>
<td nowrap align=center><input type=button value="<?=$AdminTrnsl["Delete"]?>" style="width:60px" onclick='if(confirm("<?=$AdminTrnsl[Warning_form_delete]?>")) document.location.href="/admin/forms.php?do=delete&form_id=<?=$formDef->id?>"' class=button3></td>
</tr><?}?>
</table>
<br>
</td>
</tr>
</table>
</body>
</html>