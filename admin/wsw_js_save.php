<script language="javascript" type="text/javascript" src="/admin/tiny_mce/tiny_mce.js"></script>
<script language="javascript" type="text/javascript">
tinyMCE.init({
	theme : "advanced",
	theme_advanced_toolbar_location : "top",
	theme_advanced_toolbar_align : "left",
	theme_advanced_buttons3_add_before : "tablecontrols,separator",
	mode: "specific_textareas", 
	editor_selector  : "wsw_textarea",
	language : "en",
	<? if($cname != "body") {?>content_css : "/main.css",<? } ?>
	plugins : "table,style",
	debug : false,
	convert_urls : false
});

function insertEshopItem() {
    w = window.open("/admin/Wysiwyg_popup_eshop.php", "wi", "resizable=yes,toolbar=no,location=no,directories=no,status=yes,menubar=no, width=400, height=500,scrollbars=yes,fullscreen=no,top=100, left=100");
    w.focus();
}

var currentWSWEditor = null;

function insertContentIntoCurrentEditor(html) {
	//tinyMCE.execCommand('mceFocus', false, currentWSWEditor); 
	tinyMCE.execCommand('mceInsertContent', false, html);
	//tinyMCE.execInstanceCommand('_wsw_editor_test', 'mceInsertContent', true, html, false);
}

function previewBody() {
	w = window.open("/admin/wsw_body_preview.php", "wswbp", "resizable=yes,toolbar=no,location=no,directories=no,status=yes,menubar=no, width=650, height=600,scrollbars=no,fullscreen=no,top=100, left=100");
}

function getWSWContent() {
	return tinyMCE.getContent();
}
</script>