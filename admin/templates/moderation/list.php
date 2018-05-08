<?usetemplate("moderation/header")?>
<table width=100% border=0 cellpadding=0 cellspacing=0 class=top3><td><img src=/admin/_img/s.gif width=1 height=1></td></table>
<table width=100% border=0 cellpadding=4 cellspacing=0>
<tr class=top41>
<td height=30 id=pad18 colspan=2 nowrap class=head><b><?=$AdminTrnsl["Nodes_Moderation"]?></b></td>
</tr>
<?if ($MSG != null) {?><tr>
<td id=pad18><span id="red"><?=$MSG?></span></td>
</tr><?}?>
</table>
<table width=100% border=0 cellpadding=0 cellspacing=0 class=top2><td><img src=/admin/_img/s.gif width=1 height=1></td></table>
<table width=100% border=0 cellpadding=4 cellspacing=0>
<tr>
<td id=pad18>

<br>
<table width=100% border=0 cellpadding=0 cellspacing=0 class=backtab2>
<tr id=back2 class=header>
<td class=td2 width=70%><?=$AdminTrnsl["Nodes_Moderation_Type"]?></td>
<td width=10%><?=$AdminTrnsl["Nodes_Moderation_NodeName"]?></a></td>
<td width=10%><?=$AdminTrnsl["Nodes_Moderation_User"]?></a></td>
<td width=10%><?=$AdminTrnsl["Nodes_Moderation_NodePath"]?></a></td>
<td width=10%><?=$AdminTrnsl["Nodes_Moderation_Time"]?></a></td>
<td><?=$AdminTrnsl["Actions"]?></td>
</tr>
<? foreach ($actions as $a) {$user = &User::findById($a->user_id);?>
<tr id=back3>
<td nowrap><?=$AdminTrnsl["Nodes_Moderation_Type_".$a->type]?></td>
<td nowrap><a href=/admin/moderation.php?do=process&id=<?=$a->id?>><?=$a->data->NodeName?></a></td>
<td nowrap><?=$user->login?></td>
<td><?=($a->type != "create") ? $a->data->NodePath : $a->data->ParentPath?></td>
<td nowrap><?=$a->time->format(DATETIME_FORMAT)?></td>
<td nowrap align=center><input type=button value="<?=$AdminTrnsl["Nodes_Moderation_Process"]?>" onclick="document.location.href='/admin/moderation.php?do=process&id=<?=$a->id?>'" class=button1><input type=button value="<?=$AdminTrnsl["Nodes_Moderation_Delete"]?>" onclick='document.location.href="/admin/moderation.php?do=delete&id=<?=$a->id?>"' class=button3></td>
</tr><?}?>
</table>
<br>
</td>
</tr>
</table>
</body>
</html>