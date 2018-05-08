<html>
<head>
<meta HTTP-EQUIV="content-type" CONTENT="text/html; charset=<?=ADMIN_CHARSET?>">
<meta http-equiv="Expires" content="0">
<meta http-equiv="Cache-Control" content="must-revalidate, post-check=0, pre-check=0">
<meta http-equiv="pragma" content="no-cache">
<title><?=$AdminTrnsl["Add_New_Group"]?></title>
<link rel=stylesheet href=/admin/css.css type="text/css">
</head>
<body leftmargin=0 topmargin=0 marginwidth=0 marginheight=0 class=main>

<table width=100% border=0 cellpadding=0 cellspacing=0 class=top3><td><img src=/admin/_img/s.gif width=1 height=1></td></table>
<table width=100% border=0 cellpadding=4 cellspacing=0>
<tr class=top41>
<td height=30 id=pad18 colspan=2 nowrap class=head><a href="/admin/users.php" id="black"><?=$AdminTrnsl["Users"]?></a>: <b><?=$AdminTrnsl["Add_New_Group"]?></b></td>
</tr>
</table>
<table width=100% border=0 cellpadding=0 cellspacing=0 class=top2><td><img src=/admin/_img/s.gif width=1 height=1></td></table>
<table width=100% border=0 cellpadding=4 cellspacing=0>
<tr>
<td id=pad18>
<br>

<?if ($ERROR) {?><p><font color=red><b><?=$AdminTrnsl["ERROR"]?>: </b><?=$AdminTrnsl["USER_CREATEGROUP_ERROR_".$ERROR]?></font></p><?}?>
<form action=/admin/users.php method=post>
<input type=hidden name=do value='create_group'>

<table width=100% border=0 cellpadding=0 cellspacing=0 class=backtab3 id=back3>
<tr>
<td id=back2 class=td2 nowrap><b><?=$AdminTrnsl["Name"]?></b></td>
<td width=100%><input type=text name=name value='<?=prepareStringForHTML($request->getParameter("name")."")?>' size=32  style="width:495px"></td>
</tr>
<tr valign=top>
<td id=back2 class=td2 nowrap><b><?=$AdminTrnsl["Description"]?></b></td>
<td><textarea cols="40" rows="3" name=desc style="width:495px"><?=prepareStringForHTML($request->getParameter("desc")."")?></textarea></tr>

<?if($CurrentAdminUser->group->id == 1){?>
<tr>
<td id=back2 class=td2 nowrap><b><?=$AdminTrnsl["Can_manage_classes"]?></b></td>
<td>
<table border=0 cellpadding=0 cellspacing=0 class=nobacktab><td><input type=checkbox name=can_man_class value=1></td><td>&nbsp;</td></table>
</td>
</tr>
<?}?>

<tr>
<td id=back2 class=td2 nowrap><b><?=$AdminTrnsl["Can_manage_users"]?></b></td>
<td>
<table border=0 cellpadding=0 cellspacing=0 class=nobacktab><td><input type=checkbox name=can_man_user value=1></td><td>&nbsp;</td></table></td>
</tr>

<tr>
<td id=back2 class=td2><b><?=$AdminTrnsl["Can_manage_imglib"]?></b></td>
<td>
<table border=0 cellpadding=0 cellspacing=0 class=nobacktab><td><input type=checkbox name=can_man_imglib value=1></td><td>&nbsp;</td></table></td>
</tr>

<?if($CurrentAdminUser->group->id == 1){?>
<tr>
<td id=back2 class=td2 nowrap><b><?=$AdminTrnsl["Restrict_nodeedit"]?></b></td>
<td>
<table border=0 cellpadding=0 cellspacing=0 class=nobacktab><td><input type=checkbox name=restrict_nodeedit value=1></td><td>&nbsp;</td></table></td>
</tr>
<?}?>

<?if(_MODERATION_FEATURE_ === true && $CurrentAdminUser->group->isModerator){?>
<tr>
<td id=back2 class=td2 nowrap><b><?=$AdminTrnsl["Is_Moderator"]?></b></td>
<td>
<table border=0 cellpadding=0 cellspacing=0 class=nobacktab><td><input type=checkbox name=is_moderator value=1></td><td>&nbsp;</td></table></td>
</tr>
<?}else{?>
<input type=hidden name=is_moderator value="<?=_MODERATION_FEATURE_ !== true ? 1 : ""?>">
<?}?>

<tr id=back2>
<td class=td2>&nbsp;</td>
<td><input type=submit name=submit value='<?=$AdminTrnsl["Add_Group"]?>' size=32 class=button5 style="width:200px"></td>
</tr>
</table>
</form>

</td>
</tr>
</table>

</body>
</html>
