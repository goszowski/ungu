<?$request->setAttribute("active_tab", "sorting");?>
<?=usetemplate("nodes/_edit_header")?>
<?usetemplate("_res")?>

<table width=100% border=0 cellpadding=0 cellspacing=0>
<tr><td id=pad18>
<table width=100% border=0 cellpadding=0 cellspacing=0>
<tr class=top4><td id=pad1818>

<script language="javascript">
function move_kid(kid,to){
    document.sort_form.kid_id.value=kid;
    document.sort_form.kid_act.value = to;
    document.sort_form.submit();
}
</script>

<form action=/admin/nodes.php?do=children_sort&reload=1 method=post name=sort_form>
<input type=hidden name=action value='tree_order'>
<input type=hidden name=node_id value='<?=$node->id?>'>
<input type=hidden name=kid_id value='-1'>
<input type=hidden name=kid_act value='none'>
</form>


<table width=100% border=0 cellpadding=0 cellspacing=0 class=backtab2>
<tr id=back2 class=header>
<td width=10% class=td2><b><?=$AdminTrnsl["Action"]?></b></td>
<td><b><?=$AdminTrnsl["Name"]?></b></td>
<td><b>class</b></td>
</tr>
<?forEach ($children as $child) {?>
<tr id=back3>
<td class=td2 nowrap>
&nbsp;<input type=button style="width:21px" value="" title="<?=$AdminTrnsl["bottom"]?>" class=button43 onClick="move_kid(<?=$child->id?>,'bottom');"><input type=button style="width:21px" value="" title="<?=$AdminTrnsl["top"]?>" class=button44 onClick="move_kid(<?=$child->id?>,'top');">
<input type=button style="width:21px" value="" title="<?=$AdminTrnsl["down"]?>" class=button41 onClick="move_kid(<?=$child->id?>,'down');"><input type=button style="width:21px" value="" title="<?=$AdminTrnsl["up"]?>" class=button42 onClick="move_kid(<?=$child->id?>,'up');"></td>
<td><?=$child->name?></td>
<td><span style="color: gray">[<?=$child->getNodeClass()->name?>]</span></td>
</tr>
<?}?>
</table>

</td>
</tr>
</table>

</td>
</tr></table>
</td>
</tr></table>

</body>
</html>