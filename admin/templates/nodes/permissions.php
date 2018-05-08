<?$request->setAttribute("active_tab", "permissions");?>
<?=usetemplate("nodes/_edit_header")?>

<table width=100% border=0 cellpadding=0 cellspacing=0>
<tr><td id=pad18>

<table width=100% border=0 cellpadding=0 cellspacing=0>
<tr class=top4><td id=pad1818>

<form action="/admin/nodes.php" method="POST">
<input type="hidden" name="do" value="permissions">
<input type="hidden" name="node_id" value="<?=$node->id?>">
<input type="hidden" name="action" value="update">

<!-- begin -->
<?if ($CurrentAdminUser->group->canManageUsers) {?>
<table width=100% border=0 cellpadding=0 cellspacing=0 class=backtab>
<tr align=center id=back2>
    <td height=22 width=75%><?=$AdminTrnsl["Group"]?></td>
    <td><?=$AdminTrnsl["Read"]?></td>
    <td><?=$AdminTrnsl["Write"]?></td>
</tr>
<?$i=0;foreach ($groups as $g) { $gr = $rights[$i]; ?>
<?if ($CurrentAdminUser->group->id != $g->id && $g->id != 1) {?>
<tr id=back3>
	<td><?=$g->name?></td>
	<td align=center><input type=checkbox name='<?=$g->id?>_r' value=1 <?if ($gr->hasViewRight()) {?>CHECKED<?}?>></td>
    <td align=center><input type=checkbox name='<?=$g->id?>_w' value=1 <?if ($gr->hasWriteRight()) {?>CHECKED<?}?>></td>
</tr><?}$i++;}?>

<tr height=20 id=back2>
<td colspan=3>
<input type=checkbox name=recursive value=1>&nbsp; <?=$AdminTrnsl["Apply_this_rights_to_all_children_recursive"]?></td>
</tr>

<tr id=back2>
<td colspan=3>
<img src=/admin/_img/s.gif width=1 height=8><br>
<input type=submit name=submit value="<?=$AdminTrnsl["Submit_changes"]?>" style="width:170px" size=32 class=button5></td>
</tr>
</form>
</table>
<?}?>
<?if (!$CurrentAdminUser->group->canManageUsers) {?><?=$AdminTrnsl["This_option_is_disabled_for_your_login"]?><?}?>

</td>
</tr></table>
</td>
</tr></table>

</body>
</html>
