<?$request->setAttribute("active_tab", "dependencies");?>
<?=usetemplate("nodes/_edit_header")?>
<?usetemplate("_res")?>

<table width=100% border=0 cellpadding=0 cellspacing=0>
<tr><td id=pad18>

<table width=100% border=0 cellpadding=0 cellspacing=0>
<tr class=top4><td id=pad1818>

<table width=100% border=0 cellpadding=0 cellspacing=0 class=backtab2>
<tr align=center id=back2>
<td width=50% class=td2><b><?=$AdminTrnsl["Available_Classes"]?></b><br>(<?=$AdminTrnsl["Click_to_add_dependence_class_to_node"]?>)</td>
<td width=50%><b><?=$AdminTrnsl["Dependent_Classes"]?></b><br>(<?=$AdminTrnsl["Click_to_remove_dependence_class_from_node"]?>)</td>
</tr>
<tr valign=top id=back3>
<td class=td2>
<?foreach ($availClasses as $class) {?><a href="/admin/nodes.php?do=dependencies&action=adddep&class_id=<?=$class->id?>&node_id=<?=$node->id?>&reload=true&return=<?=urlencode($request->getParameter("return"))?>"><?=$class->name?></a><br/><?}?><br/></td>
<td>
<?foreach ($nodeDeps as $class) {?><a href="/admin/nodes.php?do=dependencies&action=removedep&class_id=<?=$class->id?>&node_id=<?=$node->id?>&reload=true&return=<?=urlencode($request->getParameter("return"))?>"><?=$class->name?></a><br/><?}?><br/></td>
</tr>
</table>


</td>
</tr></table>
</td>
</tr></table>

</body>
</html>
