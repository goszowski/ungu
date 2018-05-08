<?require_once("prepend.php");?>
<html>
<head>
	<meta HTTP-EQUIV="content-type" CONTENT="text/html; charset=<?=ADMIN_CHARSET?>">
	<meta http-equiv="Expires" content="0">
	<meta http-equiv="Cache-Control" content="must-revalidate, post-check=0, pre-check=0">
	<meta http-equiv="pragma" content="no-cache">
    <title><?=$AdminTrnsl["Insert_HyperLink"]?></title>
	<link rel=stylesheet href=/admin/css.css type="text/css">
<script>

DECMD_HYPERLINK =				5016
DECMD_UNLINK =					5050
document.IN_HREF=true;
// Gets the document DOM
var oDOM = dialogArguments.objContent.DOM ;

function setDefaults() {
	var oLink = getSelectionLink() ;
	if (oLink != null) {
		txtURL.value    = oLink.getAttribute("href", 2);
		//txtTitle.value  = oLink.title ;
		//selTarget.value = oLink.target ;
		if (oLink.target == '_blank') {
			target_blank.checked = true;
		}
	}
}

function getSelectionLink() {
	var oParent ;
	var oRange ;

	if (oDOM.selection.type == "Control") {
		oRange = oDOM.selection.createRange() ;
		for ( i = 0 ; i < oRange.length ; i++ ) {
			if (oRange(i).parentNode) {
				oParent = oRange(i).parentNode ;
				break ;
			}
		}
	} else {
		oRange  = oDOM.selection.createRange() ;
		oParent = oRange.parentElement() ;
	}

	while (oParent && oParent.nodeName != "A") {
		oParent = oParent.parentNode ;
	}

	if (oParent && oParent.nodeName == "A") {
		oDOM.selection.empty() ;
		oRange = oDOM.selection.createRange() ;
		oRange.moveToElementText( oParent ) ;
		oRange.select() ;
		
		return oParent ;
	} else
		return null ;
}

function addHref() {
	var sUrl = txtURL.value ;

	if (sUrl == "")
		dialogArguments.decCommand( DECMD_UNLINK ) ;
	else {
		dialogArguments.decCommand( DECMD_HYPERLINK, 2, "javascript:void(0);" ) ;
		for (i = 0 ; i < oDOM.links.length ; i++) {
			if ( oDOM.links[i].href == "javascript:void(0);" ) {
				oDOM.links[i].href = sUrl ;
				
				//if (txtTitle.value == "")
					oDOM.links[i].removeAttribute("title",0) ;
				//else
				//	oDOM.links[i].title  = txtTitle.value ;

				//if (selTarget.value == "")
				//	oDOM.links[i].removeAttribute("target",0) ;
				//else
				//	oDOM.links[i].target = selTarget.value ;
				
				if (!target_blank.checked)
					oDOM.links[i].removeAttribute("target",0) ;
				else
					oDOM.links[i].target = "_blank";
			}
		}
	}

	cancel() ;
}

function cancel() {
	window.returnValue = null ;
	window.close() ;
}

function selectNode() {
	wnc = window.open('/admin/fields_controls/Wysiwyg_popup_addlocalhref.jsp', 'wni', 'resizable=yes,toolbar=no,location=no,directories=no,status=yes,menubar=no, width=400, height=500,scrollbars=yes,fullscreen=no,top=100, left=100');
	wnc.setLink = setLink;
    wnc.focus();
}

function selectImage() {
	wdfc = window.open("/admin/imglib.php", "wdfi", "resizable=yes,toolbar=no,location=no,directories=no,status=yes,menubar=no, width=550, height=400,scrollbars=yes,fullscreen=no,top=100, left=100");
	//wdfc.setDFileLink = setDFileLink;
    wdfc.focus();
    
}

function setLink(URL) {
	txtURL.value = URL;
}

function setDFileLink(fileName) {
	txtURL.value = '/download_files' + fileName;
}

function setImageLink(imagePath) {
	txtURL.value = '/imglib' + imagePath;
}
</script>
</head>
<body leftmargin=0 topmargin=0 marginwidth=0 marginheight=0 class=main onload="setDefaults()">

<table width=100% border=0 cellpadding=4 cellspacing=0>
<tr class=main-top1>
<td height=30 class=h4 id=pad18 nowrap><b class=h3><?=$AdminTrnsl["Insert_HyperLink"]?></b>&nbsp;</td>
</tr>
<!--tr class=main-top2>
<td height=20 class=small id=pad18>&nbsp;</td>
</tr-->
</table>

<table width=100% border=0 cellpadding=4 cellspacing=0>
<tr><td id=pad18 class=h4>

<br>

<input type="text" value="http://" id="txtURL">
<br>
<input type="checkbox" id=target_blank>target="_blank"<br>
<input type="button" class="but2" value="<?=$AdminTrnsl["Insert_HyperLink"]?>" OnClick="addHref()"><br>



</td></tr></table>

</body>
</html>
