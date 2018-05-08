<?usetemplate("classes/header")?>
<?usetemplate("_res")?>

<table width=100% border=0 cellpadding=0 cellspacing=0 class=top3><td><img src=/admin/_img/s.gif width=1 height=1></td></table>
<table width=100% border=0 cellpadding=4 cellspacing=0>
<tr class=top41>
<td height=30 id=pad18 colspan=2 nowrap class=head><a href="/admin/classes.php" id=black><?=$AdminTrnsl["Classes_Management"]?></a>: <b><?=$AdminTrnsl["Dependences"]?></b></td>
</tr>
</table>
<table width=100% border=0 cellpadding=0 cellspacing=0 class=top2><td><img src=/admin/_img/s.gif width=1 height=1></td></table>
<table width=100% border=0 cellpadding=4 cellspacing=0>

<script language="JavaScript">
    // <!--

    var strCannotChose = '<?=$AdminTrnsl["You_cannot_select_this_option"]?>';
    var strAlreadyChosen = '<?=$AdminTrnsl["You_have_already_selected_this_class"]?>';
    var strCannotRemove = '<?=$AdminTrnsl["This_selection_cannot_be_removed"]?>';

	var intReturn=0;

    function add_dep(source, destin) {
    // add class dependencies

			// Retrieve the position of the selected option
			var index = source.selectedIndex;
			// For single select drop-down lists, use the selectedIndex property instead of loop
			if (index > -1) {
				for(var i=0; i<source.options.length; i++) {
					if (source.options[i].selected) {
						// Retrieve the selected options details
						var srcValue = source.options[i].value;
						var srcText = source.options[i].text;
						// Check if the option is either a title
						if (srcValue == "-1") {/*Do nothing, this is not an selectable option*/}
						// Check if the option can be added
						else if (srcValue == "0") {alert(strCannotChose);}
						// Check if the option has already been chosen
						else if (CheckArray(destin, srcValue)) {
							alert(strAlreadyChosen);
						}
						// Display an error if the maximum options has been exceeded
						// Check if the maximum options has not been exceeded
						// is not, add the option to the selection list
						else  {
							var objOption = new Option(source.options[i].text,source.options[i].value);
							destin.options[destin.options.length+1];
							destin.options[destin.options.length] = objOption;
							source.options[i--] = null;
						}
					}
				}
			}
    }

	// Check if the list value already exists in the options
	function CheckArray(source,value) {
		for(i=0; i<source.options.length; i++) {
			if (source.options[i].value == value) {intReturn=1; return true;}
		}
		intReturn=0;
		return false;
	}

    function selectOptions(source) {
        for(i=0; i<source.options.length; i++) {
            // Set to false to ignore the first line
            if (i==0) source.options[i].selected = true;
            else source.options[i].selected = true;
        }

        // if (source.options.length == 0) { return false;}
        // else {return true;}
        return true;
    }

    // -->
</script>

<form action=/admin/classes.php method=post name=depend_form onsubmit="return selectOptions(document.depend_form['class_deps[]']);">
<input type="hidden" name="do" value="update_depends">
<input type=hidden name=class_id value='<?=$class->id?>'>

<tr>
<td id=pad18>

<table width=100% border=0 cellpadding=2 cellspacing=1> 
<tr>
<td><b><?=$AdminTrnsl["Name"]?>:</b>&nbsp;</td>
<td width=100%><?=$class->name?></td>
</tr>
<tr>
<td><b><?=$AdminTrnsl["Shortname"]?>:</b>&nbsp;</td>
<td><?=$class->shortname?></td>
</tr>
<tr>
<td nowrap><b><?=$AdminTrnsl["Default_template"]?>:</b>&nbsp;</td>
<td><?=$class->default_template?></td>
</tr>
</table>

</td>
</tr>

<tr><td id=pad18><hr size=1></td></tr>
<tr>
<td id=pad18 class=h4>

<table width=100% border=0 cellpadding=2 cellspacing=1 class=backtab>
<tr align=center class=back2>
    <td height=22 width=45%><?=$AdminTrnsl["Available_Classes"]?></td>
    <td>&nbsp;</td>
    <td width=45%><?=$AdminTrnsl["Dependent_from_Classes"]?></td>
</tr>
<tr align=center class=back3>
    <td><select name=free_class[] style="width:100%" size=10 multiple>
            <?foreach ($allClasses as $nodeClass) if (!in_array($nodeClass->id, array_keys ($allDepends))) {?>
                <option value="<?=$nodeClass->id?>"><?=$nodeClass->name?></option>
            <?}?>
    	</select>
    </td>
<td><input type=button name=b_add value=">>"  style="width:120" onClick="add_dep(document.depend_form['free_class[]'],document.depend_form['class_deps[]']);" class=but><br>
<input type=button name=b_remove value="<<"  style="width:120" onClick="add_dep(document.depend_form['class_deps[]'], document.depend_form['free_class[]']);" class=but></td>
    <td><select name=class_deps[] style="width:100%" size=10 multiple>
            <?foreach ($allDepends as $depClass) {?>
                <option value="<?=$depClass->id?>"><?=$depClass->name?></option>
            <?}?>
    </select></td>
</tr>
<tr align=center class=back2>
<td colspan=3>
<img src=/admin/_img/s.gif width=1 height=8><br>
<input type=submit value="<?=$AdminTrnsl["Update"]?>" style="width:100px" class=but></td>
</tr>
</table>

</td>
</tr></table>
</form>

</body>
</html>
