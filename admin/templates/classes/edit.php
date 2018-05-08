<?
$fieldTypes = FieldType::getAllTypes();

    ksort($fieldTypes);
    $fieldTypes = array_chunk ($fieldTypes, 18, true);
    
    $fieldTypesAssoc = $fieldTypes[0];
    $fieldTypesIndexes = $fieldTypes[1];
    
    unset($fieldTypesAssoc['filefield']);
    unset($fieldTypesAssoc['imagefield']);
    unset($fieldTypesAssoc['linkfield']); 

$MSG = null;
$rmsg = $request->getParameter("MSG");
if ($rmsg) {
	$MSG = $AdminTrnsl[$rmsg];
}
?>
<?usetemplate("classes/header")?>
<?usetemplate("_res")?>


<div class="navbar-collapse bg-white box-shadow-md hidden-xs pt-15 pb-15 pl-25 pr-25 mb-15">
    <a href="/admin/classes.php" id=black><?=$AdminTrnsl["Classes_Management"]?></a>: <b><?=$AdminTrnsl["Edit_Class"]?></b>
</div>

<?if($MSG != null):?>
    <div class="alert alert-info">
        <?=$MSG;?>
    </div>
<?endif;?>

<form action="/admin/classes.php" method="POST">
    
    <!-- hidden fields -->
    <input type="hidden" name="do" value="update">
    <input type="hidden" name="class_id" value="<?=$class->id?>">
    <!-- / hidden fields -->

    <div class="p-md pt-clear">
        <div class="p b-a bg-white m-b">

            <table class="classes_edit">
                <tr>
                    <td>ID:</td>
                    <td><?=$class->id?></td>
                </tr>

                <!-- Назва класу -->
                <tr>
                    <td><?=$AdminTrnsl["Name"];?>: <span class="text-danger">*</span></td>
                    <td>
                         <input type="text" name="name" class="form-control input-sm" style="max-width: 495px;" value="<?=prepareStringForXML($class->name)?>" size="45" required>
                        <?if($ERRORS['name'] != null):?>
                        <div class="alert alert-danger p-5">
                            <small><?=$AdminTrnsl["CLASS_EDIT_ERROR_".$ERRORS['name']]?></small>
                        </div>
                        <?endif;?>
                    </td>
                </tr>
                <!-- / Назва класу -->

                <!-- Системна назва класу -->
                <tr>
                    <td><?=$AdminTrnsl["Shortname"];?>: <span class="text-danger">*</span></td>
                    <td>
                        <input type="text" name="shortname" class="form-control input-sm" style="max-width: 495px;" value='<?=prepareStringForXML($class->shortname)?>' size="45" required>
                        <?if($ERRORS['shortname'] != null):?>
                        <div class="alert alert-danger p-5">
                            <small><?=$AdminTrnsl["CLASS_EDIT_ERROR_".$ERRORS['shortname']]?></small>
                        </div>
                        <?endif;?>
                    </td>
                </tr>
                <!-- / Системна назва класу -->

                <!-- Контроллер класу -->
                <tr>
                    <td><?=$AdminTrnsl["Default_template"];?>:</td>
                    <td>
                        <input type="text" class="form-control input-sm" name="default_template" style="max-width: 495px;" value='<?=$class->default_template?>' size="45">
                    </td>
                </tr>
                <!-- / Контроллер класу -->

                <!-- Підпис до назви розділу -->
                <tr>
                    <td><?=$AdminTrnsl["Nodename_label"];?>:</td>
                    <td>
                        <input type="text" class="form-control input-sm" style="max-width: 495px;" name="nodename_label" value="<?=$class->nodeNameLabel?>" size="45">
                    </td>
                </tr>
                <!-- / Підпис до назви розділу -->

                <!-- Сортувати по -->
                <tr>
                    <td><?=$AdminTrnsl["Nodeclass_orderbyfield"];?>:</td>
                    <td>
                        <input type="text" class="form-control input-sm" style="max-width: 495px;" name="orderby" value="<?=$class->orderBy?>" size="45">
                    </td>
                </tr>
                <!-- / Сортувати по -->

                <!-- Відображати в адмін-дереві -->
                <tr>
                    <td>&nbsp;</td>
                    <td>
                        <label class="ui-checks">
                            <input type="checkbox" name="show_at_adt" value="1" <?if($class->showAtAdminTree):?>CHECKED<?endif;?>>
                            <i></i> <?=$AdminTrnsl["ShowNodesAtAdminInterface"];?>
                        </label>
                    </td>
                </tr>
                <!-- / Відображати в адмін-дереві -->


                <?global $NODECLASS_FLAG_NAMES;?>


                <?foreach($NODECLASS_FLAG_NAMES as $flagName):?>
                    <!-- flagName -->
                    <tr>
                        <td>&nbsp;</td>
                        <td>
                            <label class="ui-checks">
                                <input type="checkbox" name="flag_<?=$flagName;?>" value="1" <?if($class->checkFlag($flagName)):?>CHECKED<?endif;?>>
                                <i></i> <?=$AdminTrnsl["NODECLASS_FLAG_" . $flagName];?>
                            </label>
                        </td>
                    </tr>
                    <!-- / flagName -->
                <?endforeach;?>

                <!-- Кнопка відправки форми -->
                <tr>
                    <td>&nbsp;</td>
                    <td>
                        <button type="submit" class="btn btn-sm btn-primary"><?=$AdminTrnsl["Submit_changes"]?></button>
                    </td>
                </tr>
                <!-- / Кнопка відправки форми -->



            </table>

        </div>
    </div>


    <?if((sizeof($fieldDefs) > 0)):?>
    <!-- Поля класу -->
    <div class="p-md pt-clear">
        <ul class="nav nav-sm nav-tabs">
            <li class="active"><a><?=$AdminTrnsl["Field_list"]?></a></li>
        </ul>
        <div class="p b-a no-b-t bg-white m-b">
            <table class="default-table table-p">
                <tr>
                    <td>ID</td>
                    <td>&nbsp;</td>
                    <td class="pr-5"><?=$AdminTrnsl["Shortname"]?></td>
                    <td class="pl-5"><?=$AdminTrnsl["Name"]?></td>
                    <td><?=$AdminTrnsl["Type"]?></td>
                    <td class="text-center"><?=$AdminTrnsl["Required"]?></td>
                    <td class="text-center"><?=$AdminTrnsl["Shown_in_Nodes_List"]?></td>
                    <td class="text-center">&nbsp;</td>
                    <td class="text-center"><?=$AdminTrnsl["Delete"]?></td>
                </tr>

                <?foreach($fieldDefs as $fd):?>
                <tr>
                    <td><?=$fd->id?></td>
                    <td class="text-center" style="min-width: 100px;">
                        <!-- Сортування -->
                        <div class="btn-group">
                            <a href="/admin/classes.php?do=movedown_field&amp;class_id=<?=$class->id;?>&amp;field_id=<?=$fd->id;?>" class="btn btn-default btn-sm"><i class="fa fa-chevron-down"></i></a>
                            <a href="/admin/classes.php?do=moveup_field&amp;class_id=<?=$class->id;?>&amp;field_id=<?=$fd->id;?>" class="btn btn-default btn-sm"><i class="fa fa-chevron-up"></i></a>
                        </div>
                        <!-- / Сортування -->
                    </td>
                    <td class="pr-5">
                        <input type="text" class="form-control input-sm" name="df_shortnames_<?=$fd->id?>" value="<?=prepareStringForHtml($fd->shortname)?>">
                        <?if($FERRORS[$fd->id]['shortname'] != null):?><div class="text-danger"><?=$AdminTrnsl["CLASS_EDIT_ERROR_".$FERRORS[$fd->id]['shortname']]?></div><?endif;?>
                    </td>
                    <td class="pl-5">
                        <input type="text" class="form-control input-sm" name="df_names_<?=$fd->id?>" value="<?=prepareStringForHTML($fd->name)?>">
                        <?if($FERRORS[$fd->id]['name'] != null):?><div class="text-danger"><?=$AdminTrnsl["CLASS_EDIT_ERROR_".$FERRORS[$fd->id]['name']]?></div><?endif;?>
                    </td>
                    <td>
                        <select class="form-control input-sm" name="df_types_<?=$fd->id?>">
                            <?foreach($fieldTypesAssoc as $ft):?>
                            <option value="<?=$ft->id?>"<?if($ft->id == $fd->field_type):?>SELECTED<?endif;?>><?=$ft->name?></option>
                            <?endforeach;?>
                        </select>
                    </td>
                    <td class="text-center">
                        <label class="ui-checks mt-10">
                            <input type="checkbox" name="df_required_<?=$fd->id;?>" value=1 <?if($fd->required):?>CHECKED<?endif;?>>
                            <i></i>
                        </label>
                    </td>
                    <td class="text-center">
                        <label class="ui-checks mt-10">
                            <input type="checkbox" name="df_shown_<?=$fd->id?>" value=1 <?if($fd->shown):?>CHECKED<?endif;?>>
                            <i></i>
                        </label>
                    </td>
                    <td>
                        <a class="btn btn-sm btn-primary" href="/admin/classes.php?do=field_params&amp;class_id=<?=$class->id?>&amp;field_id=<?=$fd->id?>"><?=$AdminTrnsl["parameters"]?></a>
                    </td>
                    <td class="text-center">
                        <label class="ui-checks mt-10">
                            <input type="checkbox" name="df_delete_<?=$fd->id?>" value="1" <?if($fd->todelete):?> CHECKED<?endif;?>>
                            <i></i>
                        </label>
                    </td>
                </tr>
                <?endforeach;?>
            </table>

            <button type="submit" class="btn btn-sm btn-primary mt-10 ml-5"><?=$AdminTrnsl["Submit_changes"]?></button>

        </div>
    </div>
    <!-- / Поля класу -->
    <?endif;?>

</form>





<form action="/admin/classes.php" method="POST">

    <input type="hidden" name="do" value="add_field">
    <input type="hidden" name="class_id" value="<?=$class->id?>">

    <!-- Додати нове поле -->
    <div class="p-md pt-clear">
        <ul class="nav nav-sm nav-tabs">
            <li class="active"><a><?=$AdminTrnsl["Add_new_field"]?></a></li>
        </ul>
        <div class="p b-a no-b-t bg-white m-b">
            <table class="default-table table-p">
                <tr>
                    <td><?=$AdminTrnsl["Shortname"]?></td>
                    <td><?=$AdminTrnsl["Name"]?></td>
                    <td><?=$AdminTrnsl["Type"]?></td>
                    <td class="text-center"><?=$AdminTrnsl["Required"]?></td>
                    <td class="text-center"><?=$AdminTrnsl["Shown_in_Nodes_List"]?></td>
                </tr>

                <tr>
                    <td>
                        <input type="text" class="form-control input-sm" name="nfshortname" value="<?=$nfparams->shortname?>">
                        <?if($AFERRORS['shortname'] != null):?>
                            <div class="text-danger"><?=$AdminTrnsl["CLASS_EDIT_ERROR_".$AFERRORS['shortname']]?></div>
                        <?endif;?>
                    </td>
                    <td>
                        <input type="text" class="form-control input-sm" name="nfname" value="<?=$nfparams->name?>">
                        <?if($AFERRORS['name'] != null):?>
                            <div class="text-danger"><?=$AdminTrnsl["CLASS_EDIT_ERROR_".$AFERRORS['name']]?></div>
                        <?endif;?>
                    </td>
                    <td>
                        <select class="form-control input-sm" name="nffieldtype">
                            <?foreach($fieldTypesAssoc as $ft):?>
                            <option value="<?=$ft->id?>"<?if($ft->id == $nfparams->field_type):?>SELECTED<?endif;?>><?=$ft->name?></option>
                            <?endforeach;?>
                        </select>
                    </td>
                    <td class="text-center">
                        <label class="ui-checks mt-10">
                            <input type="checkbox" name="nfrequired" value="1" <?if($nfparams->required == 1):?>CHECKED<?endif;?>>
                            <i></i>
                        </label>
                    </td>
                    <td class="text-center">
                        <label class="ui-checks mt-10">
                            <input type="checkbox" name="nfshown" value="1" <?if($nfparams->shown == 1):?>CHECKED<?endif;?>>
                            <i></i>
                        </label>
                    </td>
                </tr>
            </table>

            <button type="submit" class="btn btn-sm btn-primary mt-10 ml-5"><?=$AdminTrnsl["Add"]?></button>

        </div>
    </div>
</form>



</body>
</html>
