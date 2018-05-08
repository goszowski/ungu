<html>
<head>
<meta HTTP-EQUIV="content-type" CONTENT="text/html; charset=<?=ADMIN_CHARSET?>">
<meta http-equiv="Expires" content="0">
<meta http-equiv="Cache-Control" content="must-revalidate, post-check=0, pre-check=0">
<meta http-equiv="pragma" content="no-cache">
<title><?=$AdminTrnsl["Edit_User"]?></title>
<link rel=stylesheet href=/admin/css.css type="text/css">
</head>
<body leftmargin=0 topmargin=0 marginwidth=0 marginheight=0 class=main>

<table width=100% border=0 cellpadding=0 cellspacing=0 class=top3><td><img src=/admin/_img/s.gif width=1 height=1></td></table>
<table width=100% border=0 cellpadding=4 cellspacing=0>
<tr class=top41>
<td height=30 id=pad18 colspan=2 nowrap class=head><a href="/admin/users.php" id="black"><?=$AdminTrnsl["Users"]?></a>: <b><?=$AdminTrnsl["Edit_User"]?></b></td>
</tr>
</table>
<table width=100% border=0 cellpadding=0 cellspacing=0 class=top2><td><img src=/admin/_img/s.gif width=1 height=1></td></table>
<table width=100% border=0 cellpadding=4 cellspacing=0>
<tr>
<td class=head id=pad18>
<br>

<form action=/admin/users.php method=post>
<input type=hidden name=do value='update_user'>
<input type=hidden name=user_id value='<?= $u->id?>'>

<?if ($ERROR) {?><p><font color=red><b><?=$AdminTrnsl["ERROR"]?>: </b><?=$AdminTrnsl["USER_CREATE_ERROR_".$ERROR]?></font></p><?}?>
<table width=100% border=0 cellpadding=0 cellspacing=0 class=backtab3 id=back3>
<tr>
<td id=back2 class=td2><b><?=$AdminTrnsl["Login"]?>:</b></td>
<td width=100%><input type=text name=login value='<?= prepareStringForXML($u->login)?>' size=32  style="width:150px"></td>
</tr>
<tr>
<td id=back2 class=td2><b><?=$AdminTrnsl["Password"]?>:</b></td>
<td width=100%><input type=password name=passwd1 value='<?= prepareStringForXML($u->password)?>' size=32  style="width:150px"></td>
</tr>
<tr>
<td id=back2 class=td2 nowrap><b><?=$AdminTrnsl["Retype_Password"]?>:</b></td>
<td width=100%><input type=password name=passwd2 value='<?= prepareStringForXML($u->password)?>' size=32  style="width:150px"></td>
</tr>
<tr>
<td id=back2 class=td2 nowrap><b><?=$AdminTrnsl["Email"]?>:</b></td>
<td width=100%><input type=text name=email value='<?= prepareStringForXML($u->email)?>' size=25  style="width:150px"></td>
</tr>
<tr>
<td id=back2 class=td2 nowrap><b><?=$AdminTrnsl["Choose_group"]?>:</b></td>
<td width=100%><select name=user_group style="width:150px">
<? $groups = UserGroup::findAll(); ?>
<?foreach ($groups as $ug) { if ($ug->id == 1 && $CurrentAdminUser->group->id != 1) continue; ?>
<option value="<?=$ug->id?>"<?=($ug->id == $u->group->id) ? " SELECTED" : ""?>><?=$ug->name?>
<?}?>
</select></td>
</tr>
<tr id=back2>
<td class=td2>&nbsp;</td>
<td class=td2 id=pad10>
<input type=submit value="<?=$AdminTrnsl["Submit_changes"]?>" class=button5></td>
</tr>
</table>

</td>
</tr>
</form>

</table>

</td>
</tr>
</table>

</body>
</html>