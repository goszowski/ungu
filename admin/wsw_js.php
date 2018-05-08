<script type="text/javascript" src="/admin/tinymce/jscripts/tiny_mce/tiny_mce.js" ></script >

<script type="text/javascript">
	tinyMCE.init({
		// General options
		//mode : "textareas",
		
		mode : "specific_textareas",
        language : "ru",
		editor_selector : "wsw_textarea",
		
		//mode : "exact",
		//elements : "_wsw_editor_<?=$cname?>",
		
		theme : "advanced",
		plugins : "images,pagebreak,style,layer,table,save,advhr,advimage,advlink,emotions,iespell,inlinepopups,insertdatetime,preview,media,searchreplace,print,contextmenu,paste,directionality,fullscreen,noneditable,visualchars,nonbreaking,xhtmlxtras,template,wordcount,advlist, blockquote",

		// Theme options
		theme_advanced_buttons1 : "images,bold,italic,underline,strikethrough,blockquote,|,justifyleft,justifycenter,justifyright,justifyfull,styleselect,formatselect,fontselect,fontsizeselect,forecolor,backcolor",
		theme_advanced_buttons2 : "bullist,numlist,|,outdent,indent,|,undo,redo,|,link,unlink,anchor,image,cleanup,help,code,|,forecolor,backcolor",
		theme_advanced_buttons3 : "tablecontrols,|,hr,removeformat,visualaid,|,sub,sup,|,charmap,iespell,media,advhr,|,print,|,ltr,rtl,|,fullscreen",
		theme_advanced_buttons4 : "insertlayer,moveforward,movebackward,absolute,|,cite,abbr,acronym,del,ins,|,nonbreaking,pagebreak,|,insertfile,insertimage",
        theme_advanced_toolbar_location : "top",
		theme_advanced_toolbar_align : "left",
		theme_advanced_statusbar_location : "bottom",
		theme_advanced_resizing : true,
        

		// Example content CSS (should be your site CSS)
		content_css : "css/content.css",
		
		// My settings 
		relative_urls : false,
		convert_urls : true,
		
		force_br_newlines : false,
		force_p_newlines : true,

		// Drop lists for link/image/media/template dialogs
		template_external_list_url : "lists/template_list.js",
		external_link_list_url : "lists/link_list.js",
		external_image_list_url : "lists/image_list.js",
		media_external_list_url : "lists/media_list.js",

		// Style formats
		style_formats : [
			{title: 'Quote', selector: 'p', classes: 'quote'},
			{title: 'Image Right', selector: 'img', classes: 'right_img'},
			{title : 'Bold text', inline : 'b'},
			{title : 'Red text', inline : 'span', styles : {color : '#ff0000'}},
			{title : 'Red header', block : 'h1', styles : {color : '#ff0000'}},
			{title : 'Example 1', inline : 'span', classes : 'example1'},
			{title : 'Example 2', inline : 'span', classes : 'example2'},
			{title : 'Table styles'},
			{title : 'Table row 1', selector : 'tr', classes : 'tablerow1'}
		],

		// Replace values for the template plugin
		template_replace_values : {
			username : "Some User",
			staffid : "991234"
		}
	});
</script>

<script language="javascript" type="text/javascript">


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