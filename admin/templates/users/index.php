<html>
<head>
<meta HTTP-EQUIV="content-type" CONTENT="text/html; charset=<?=ADMIN_CHARSET?>">
<meta http-equiv="Expires" content="0">
<meta http-equiv="Cache-Control" content="must-revalidate, post-check=0, pre-check=0">
<meta http-equiv="pragma" content="no-cache">
<title>Admin Module</title>
<link rel=stylesheet href=/admin/css.css type="text/css">
</head>
<body leftmargin=0 topmargin=0 marginwidth=0 marginheight=0 class=main>

<table width=100% border=0 cellpadding=0 cellspacing=0 class=top3><td><img src=/admin/_img/s.gif width=1 height=1></td></table>
<table width=100% border=0 cellpadding=4 cellspacing=0>
<tr class=top41>
<td height=30 id=pad18 colspan=2 nowrap class=head><b><?=$AdminTrnsl["Users_Management"]?></b></td>
</tr>
</table>
<table width=100% border=0 cellpadding=0 cellspacing=0 class=top2><td><img src=/admin/_img/s.gif width=1 height=1></td></table>
<table width=100% border=0 cellpadding=4 cellspacing=0>
<form action="">
<tr class=top4>
<td height=36 id=pad18 nowrap>
<!-- create class -->
<input type=button value="<?=$AdminTrnsl["Add_User"]?>" title="<?=$AdminTrnsl["Add_User"]?>" style="width:150px" onclick="document.location.href='/admin/users.php?do=create_user_form'" class=button1>
<input type=button value="<?=$AdminTrnsl["Add_Group"]?>" title="<?=$AdminTrnsl["Add_Group"]?>" style="width:150px" onclick="document.location.href='/admin/users.php?do=create_group_form'" class=button1>
</td>
</tr>
</form>
<tr>
<td id=pad18 class=h4>

<script language="JavaScript">
//<!--
    function do_confirm(gid, uid) {
        if (confirm('<?=$AdminTrnsl["Are_you_sure"]?>')) {
            document.confirm_form.group_id.value=gid;
            document.confirm_form.user_id.value=uid;
            document.confirm_form.submit();
        }
    }
//-->
</script>
<div id=pad5><b class=h3><?=$AdminTrnsl["Users"]?>:</b></div>
<table width=100% border=0 cellpadding=0 cellspacing=0 class=backtab2>
<form action=/admin/users.php method=post name=confirm_form>
<input type=hidden name=do value='remove_user'>
<input type=hidden name=group_id value=-1>
<input type=hidden name=user_id value=-1>
<input type=hidden name=confirmation value='Yes' >
</form>

<?
	$groups = &UserGroup::findAll();

	foreach ($groups as $ug) {

	if ($ug->id == 1 && $CurrentAdminUser->group->id != 1) continue;
?>
<tr id=back2 class=header>
<td class=td2 align=right>&nbsp;&nbsp;<?=$AdminTrnsl["Group"]?>:</td>
<td class=td2 width=90%><?if($ug->id != $CurrentAdminUser->group->id){?><a href=/admin/users.php?do=edit_group&group_id=<?= $ug->id?>><?= $ug->name?></a><?}else{?><?= $ug->name?><?}?></td>
<td nowrap>
<?if($ug->id != $CurrentAdminUser->group->id){?><input type=button value="<?=$AdminTrnsl["Edit"]?>" style="width:80px" onclick="document.location.href='/admin/users.php?do=edit_group&group_id=<?= $ug->id?>'" class=button1><?}?>
<? if (sizeof($ug->getUsers()) == 0 && $CurrentAdminUser->group->id != $ug->id) { ?> <input type=button value="<?=$AdminTrnsl["Del"]?>" style="width:80px" class=button4 onclick="document.location.href='/admin/users.php?do=delete_group&group_id=<?=$ug->id?>';"><? } ?>&nbsp;</td>
</tr>
<?
	if (sizeof($ug->getUsers()) != 0) { 
		$users = $ug->getUsers();
		foreach ($users as $u) {
?>
<tr id=back3>
<td class=td2 width=15%>&nbsp;</td>
<td><a href=/admin/users.php?do=edit&user_id=<?=$u->id?>><?=$u->login?></a></td>
<td nowrap width=10%>
<input type=button value="<?=$AdminTrnsl["Edit"]?>" style="width:80px" onclick="document.location.href='/admin/users.php?do=edit&user_id=<?=$u->id?>'" class=button1><? if ($CurrentAdminUser->id != $u->id) { ?>&nbsp;<input type=button value="<?=$AdminTrnsl["Del"]?>" style="width:80px" onclick="do_confirm(<?=$ug->id?>, <?=$u->id?>)" class=button4><?}?></td>
</tr>
<?
		}
	}
}
?>
</table>

<div id=pad5><b class=h3><?=$AdminTrnsl["Add_New_User"]?>:</b></div>

<?if ($ERROR) {?><p><span id=red><b><?=$AdminTrnsl["ERROR"]?>: </b><? $AdminTrnsl["USER_CREATE_ERROR_".$ERROR]?></span></p><?}?>

<table width=100% border=0 cellpadding=0 cellspacing=0 class=backtab2>
<form action=/admin/users.php method=post>
<input type=hidden name=do value='create_user'>
<?
    $desc = $request->getParameter("desc");
?>
<tr id=back3>
<td class=td2 align=right><?=$AdminTrnsl["Login"]?>:</td>
<td width=100%><input type=text name=login value='' size=250  style="width:150px"></td>
</tr>
<tr id=back3>
<td class=td2 align=right><?=$AdminTrnsl["Password"]?>:</td>
<td width=100%><input type=password name=passwd1 value='' size=25  style="width:150px"></td>
</tr>
<tr id=back3>
<td class=td2 align=right nowrap><?=$AdminTrnsl["Retype_Password"]?>:</td>
<td width=100%><input type=password name=passwd2 value='' size=25  style="width:150px"></td>
</tr>
<tr id=back3>
<td class=td2 class=td2 align=right nowrap><?=$AdminTrnsl["Email"]?>:</td>
<td width=100%><input type=text name=email value='' size=25  style="width:150px"></td>
</tr>
<tr id=back3>
<td class=td2 align=right nowrap><?=$AdminTrnsl["Choose_group"]?>:</td>
<td width=100%><select name=user_group style="width:150px">
<?foreach ($groups as $ug) { if ($ug->id == 1 && $CurrentAdminUser->group->id != 1) continue; ?>
<option value="<?=$ug->id?>"><?=$ug->name?>
<?}?>
</select></td>
</tr>
<!-- change button -->
<tr id=back2>
<td class=td2>&nbsp;</td>
<td class=td2 colspan=2 id=pad10><input type=submit name=submit value='<?=$AdminTrnsl["Add_New_User"]?>' size=32 class=button5 style="width:200px"></td>
</tr>
</form>
</table>

</td>
</tr>
</table>

</body>
</html>
