<?
$cname = $CM_CONTROL_NAME;
$editorID = "_wsw_editor_" . $cname;
?>

<? include_once($_SERVER["DOCUMENT_ROOT"] . "/admin/wsw_js.php"); ?>

<textarea id="<?= $editorID ?>" name="<?= $cname ?>" class="wsw_textarea"><?=prepareStringForXML($CM_PARAMS["strvalue"])?></textarea>
<? if ($cname == "body") { ?>
<hr>
<a href="#" onclick="currentWSWEditor='<?= $editorID ?>'; previewBody(); return false;"><b>Vorschau</b></a>
<hr>
<? } ?>

<?/*
<? if ($cname == "body") { ?>
<a href="#" onclick="currentWSWEditor='<?= $editorID ?>';insertEshopItem(); return false;">[Insert Produkt]</a>
<? } ?>
*/?>
<?/*
<IFRAME src="/admin/Wysywig_echovalue.php?field_shortname=<?=$cname?>" width="100%" height="700" frameborder="no" scrolling="no"></IFRAME>
<input type="hidden" name="<?=$cname?>" value="<?=prepareStringForXML($CM_PARAMS["strvalue"])?>">
*/?>