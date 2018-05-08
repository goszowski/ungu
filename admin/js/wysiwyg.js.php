<?require_once("../prepend.php");?>
//
// Command IDs
//
DECMD_BOLD =					5000
DECMD_COPY =					5002
DECMD_CUT =						5003
DECMD_DELETE =					5004
DECMD_DELETECELLS =				5005
DECMD_DELETECOLS =				5006
DECMD_DELETEROWS =				5007
DECMD_FINDTEXT =				5008
DECMD_FONT =					5009
DECMD_GETBACKCOLOR =			5010
DECMD_GETBLOCKFMT =				5011
DECMD_GETBLOCKFMTNAMES =		5012
DECMD_GETFONTNAME =				5013
DECMD_GETFONTSIZE =				5014
DECMD_GETFORECOLOR =			5015
DECMD_HYPERLINK =				5016
DECMD_IMAGE =					5017
DECMD_INDENT =					5018
DECMD_INSERTCELL =				5019
DECMD_INSERTCOL =				5020
DECMD_INSERTROW =				5021
DECMD_INSERTTABLE =				5022
DECMD_ITALIC =					5023
DECMD_JUSTIFYCENTER =			5024
DECMD_JUSTIFYLEFT =				5025
DECMD_JUSTIFYRIGHT =			5026
DECMD_LOCK_ELEMENT =			5027
DECMD_MAKE_ABSOLUTE =			5028
DECMD_MERGECELLS =				5029
DECMD_ORDERLIST =				5030
DECMD_OUTDENT =					5031
DECMD_PASTE =					5032
DECMD_REDO =					5033
DECMD_REMOVEFORMAT =			5034
DECMD_SELECTALL =				5035
DECMD_SEND_BACKWARD =			5036
DECMD_BRING_FORWARD =			5037
DECMD_SEND_BELOW_TEXT =			5038
DECMD_BRING_ABOVE_TEXT =		5039
DECMD_SEND_TO_BACK =			5040
DECMD_BRING_TO_FRONT =			5041
DECMD_SETBACKCOLOR =			5042
DECMD_SETBLOCKFMT =				5043
DECMD_SETFONTNAME =				5044
DECMD_SETFONTSIZE =				5045
DECMD_SETFORECOLOR =			5046
DECMD_SPLITCELL =				5047
DECMD_UNDERLINE =				5048
DECMD_UNDO =					5049
DECMD_UNLINK =					5050
DECMD_UNORDERLIST =				5051
DECMD_PROPERTIES =				5052

// OLE_TRISTATE
OLE_TRISTATE_UNCHECKED =		0
OLE_TRISTATE_CHECKED =			1
OLE_TRISTATE_GRAY =				2

// DHTMLEDITCMDF
DECMDF_NOTSUPPORTED =			0 
DECMDF_DISABLED =				1 
DECMDF_ENABLED =				3
DECMDF_LATCHED =				7
DECMDF_NINCHED =				11

var BrowserInfo = new Object() ;
BrowserInfo.MajorVer = navigator.appVersion.match(/MSIE (.)/)[1] ;
BrowserInfo.MinorVer = navigator.appVersion.match(/MSIE .\.(.)/)[1] ;
BrowserInfo.IsIE55OrMore = BrowserInfo.MajorVer >= 6 || ( BrowserInfo.MajorVer >= 5 && BrowserInfo.MinorVer >= 5 ) ;

var bInitialized = false ;
var bDataLoaded  = false ;
var inHelp = (inHelp) ? true : false;

function initEditor() {
	if (! bInitialized) {
		bInitialized = true ;	
		objContent.BaseURL = document.location.protocol + '//' + document.location.host + '/' ;
	}
	if (! bDataLoaded && ! objContent.Busy) {
		bDataLoaded = true ;
		objContent.DOM.body.onpaste = onPaste;
		objContent.DOM.createStyleSheet('/main.css');
		setLinkedField();
	}
}

var oLinkedField = null ;
function setLinkedField() {
	oLinkedField = parent.document.getElementsByName(wswName)[0] ;
	
	if (! oLinkedField) return ;

	// __tmpFCKRemove__ added and removed to solve DHTML component error when loading "<p><hr></p>"
	objContent.DOM.body.innerHTML = "<div id=__tmpFCKRemove__>&nbsp;</div>" + oLinkedField.value ;
	objContent.DOM.getElementById('__tmpFCKRemove__').removeNode(true) ;

	var oForm = oLinkedField.form ;

	if (!oForm) return ;

	// Attaches the field update to the onsubmit event
	oForm.attachEvent("onsubmit", setFieldValue) ;
	// Attaches the field update to the submit method (IE doesn't fire onsubmit on this case)
	if (! oForm.updateFCKEditor) oForm.updateFCKEditor = new Array() ;
	oForm.updateFCKEditor[oForm.updateFCKEditor.length] = setFieldValue ;
	if (! oForm.originalSubmit) {
		oForm.originalSubmit = oForm.submit ;
		oForm.submit = function() {
			if (this.updateFCKEditor) {
				for (var i = 0 ; i < this.updateFCKEditor.length ; i++) {
					this.updateFCKEditor[i]() ;
				}
			}
			this.originalSubmit() ;
		}
	}
}

function setFieldValue() {
	if (trSource.style.display != "none") {
		switchEditMode() ;
	}

	oLinkedField.value = objContent.DOM.body.innerHTML ;
}

function onPaste() {
	if (BrowserInfo.IsIE55OrMore) {
		var sHTML = GetClipboardHTML() ;
		var re = /<\w[^>]* class="?MsoNormal"?/gi ;
		if ( re.test( sHTML ) ) {
			if ( confirm( "<?=$AdminTrnsl["PasteWordConfirm"]?>" ) ) {
				cleanAndPaste( sHTML ) ;
				return false ;
			}
		}
	}
	else
		return true ;
}

function decCommand(cmdId, cmdExecOpt, url) {
	var status = objContent.QueryStatus(cmdId) ;
	
	if ( status != 1 && status != 0 ) {
		if (cmdExecOpt == null)
			cmdExecOpt = 0 ;
		objContent.ExecCommand(cmdId, cmdExecOpt, url);
	}
	objContent.focus() ;
}

function checkDecCommand(cmdId) {
	if (objContent.Busy) return OLE_TRISTATE_GRAY ;
	switch (objContent.QueryStatus(cmdId)) {
		case (DECMDF_DISABLED || DECMDF_NOTSUPPORTED) :
			return OLE_TRISTATE_GRAY ;
		case (DECMDF_ENABLED || DECMDF_NINCHED) :
			return OLE_TRISTATE_UNCHECKED ;
		default :			// DECMDF_LATCHED
			return OLE_TRISTATE_CHECKED ;
	}
}


function docCommand(command) {
	objContent.DOM.execCommand(command) ;
	objContent.focus();
}

function ShowDialog(pagePath, args, width, height) {
	return showModalDialog(pagePath, args, "dialogWidth:" + width + "px;dialogHeight:" + height + "px;help:no;scroll:no;status:no");
}

function getSelectedHtml() {
	var selection = objContent.DOM.selection;
	var range = selection.createRange();

	var stype = selection.type.toLowerCase();
	if (stype != "none") {
		if (stype == "control")
			return range.item(0).outerHTML;
		else
			return range.htmlText;
	} else
		return '';
}

function insertHtml(html) {
	if (objContent.DOM.selection.type.toLowerCase() != "none")
		objContent.DOM.selection.clear() ;
	objContent.DOM.selection.createRange().pasteHTML(html) ; 
}

function GetClipboardHTML() {
	var oDiv = document.getElementById("divTemp")
	oDiv.innerHTML = "" ;

	var oTextRange = document.body.createTextRange() ;
	oTextRange.moveToElementText(oDiv) ;
	oTextRange.execCommand("Paste") ;

	var sData = oDiv.innerHTML ;
	oDiv.innerHTML = "" ;

	return sData ;
}

function switchEditMode() {
	var bSource = (trSource.style.display == "none") ;

	if (bSource) 
		txtSource.value = objContent.DOM.body.innerHTML ;
	else {
		objContent.DOM.body.innerHTML = "<div id=__tmpFCKRemove__>&nbsp;</div>" + txtSource.value ;
		objContent.DOM.getElementById('__tmpFCKRemove__').removeNode(true) ;
	}
		
	trEditor.style.display = bSource ? "none" : "inline" ;
	trSource.style.display = bSource ? "inline" : "none" ;
	return false;

	//events.fireEvent('onViewMode', bSource) ;
}

function pastePlainText() {
	var sText = HTMLEncode( clipboardData.getData("Text") ) ;
	sText = sText.replace(/\n/g,'<BR>') ;
	insertHtml(sText) ;
}

function pasteFromWord() {
	if (BrowserInfo.IsIE55OrMore)
		cleanAndPaste( GetClipboardHTML() ) ;
	else if ( confirm( "This command is available for Internet Explorer version 5.5 or more. Do you want to paste without cleaning?" ) )
		decCommand(DECMD_PASTE) ;
}

function cleanAndPaste( html ) {
	insertHtml( cleanHTML(html) ) ;
}

function cleanHTML( html ) {
	// Remove all SPAN tags
	html = html.replace(/<\/?SPAN[^>]*>/gi, "" );
	// Remove all H1-H4 tags
	html = html.replace(/<\/?H1[^>]*>/gi, "" );
	html = html.replace(/<\/?H2[^>]*>/gi, "" );
	html = html.replace(/<\/?H3[^>]*>/gi, "" );
	html = html.replace(/<\/?H4[^>]*>/gi, "" );
	// Remove all FONT tags
	html = html.replace(/<\/?FONT[^>]*>/gi, "" );
	// Remove Class attributes
	html = html.replace(/<(\w[^>]*) class=([^ |>]*)([^>]*)/gi, "<$1$3") ;
	// Remove Style attributes
	html = html.replace(/<(\w[^>]*) style="([^"]*)"([^>]*)/gi, "<$1$3") ;
	// Remove Lang attributes
	html = html.replace(/<(\w[^>]*) lang=([^ |>]*)([^>]*)/gi, "<$1$3") ;
	// Remove Face attributes
	html = html.replace(/<(\w[^>]*) face=([^ |>]*)([^>]*)/gi, "<$1$3") ;
	// Remove XML elements and declarations
	html = html.replace(/<\\?\?xml[^>]*>/gi, "") ;
	// Remove Tags with XML namespace declarations: <o:p></o:p>
	html = html.replace(/<\/?\w+:[^>]*>/gi, "") ;
	// Replace the &nbsp;
	html = html.replace(/&nbsp;/, " " );
	// Transform <P> to <DIV>
	// var re = new RegExp("(<P)([^>]*>.*?)(<\/P>)","gi") ;	// Different because of a IE 5.0 error
	// html = html.replace( re, "<div$2</div>" ) ;

	return html;
}

function HTMLEncode(text) {
	text = text.replace(/&/g, "&amp;") ;
	text = text.replace(/"/g, "&quot;") ;
	text = text.replace(/</g, "&lt;") ;
	text = text.replace(/>/g, "&gt;") ;
	text = text.replace(/'/g, "&#146;") ;

	return text ;
}

function wswClean() {
	objContent.DOM.body.innerHTML = "<div id=__tmpFCKRemove__>&nbsp;</div>" + cleanHTML(objContent.DOM.body.innerHTML);
	objContent.DOM.getElementById('__tmpFCKRemove__').removeNode(true) ;
}

function wswHLinkDialog() {
	if (checkDecCommand(DECMD_HYPERLINK) != OLE_TRISTATE_GRAY) {
		ShowDialog('/admin/Wysiwyg_popup_addhref.php', window, 400, 500);
    	objContent.focus();
    }
}

function wswLargeImagePopup() {
	if (checkDecCommand(DECMD_HYPERLINK) != OLE_TRISTATE_GRAY) {
		ShowDialog('/admin/Wysiwyg_popup_addimagepopup.php', window, 400, 500);
    	objContent.focus();
    }
}

function wswRemoveHLink() {
    decCommand(DECMD_UNLINK);
}

function wswSelectColor() {
    var color = showModalDialog("/admin/Wysiwyg_popup_selectcolor.php", window, "dialogWidth:310px;dialogHeight:240px;help:no;scroll:no;status:no");
    decCommand(DECMD_SETFORECOLOR, 0, color );
}

function dialogImage() {
    w = window.open("/admin/Wysiwyg_popup_image.php", "wi", "resizable=yes,toolbar=no,location=no,directories=no,status=yes,menubar=no, width=650, height=600,scrollbars=no,fullscreen=no,top=100, left=100");
    w.focus();
}

function wswInsertFile() {
    w = window.open("/admin/filelib.php", "wi", "resizable=yes,toolbar=no,location=no,directories=no,status=yes,menubar=no, width=550, height=400,scrollbars=yes,fullscreen=no,top=100, left=100");
    w.focus();
}

function insertHRule() {
	insertHtml('<hr>')
	objContent.focus();
}

function insertSpecialChar() {
	var html = ShowDialog("/admin/Wysiwyg_popup_specialchar.php", window, 700, 350);
	if (html) insertHtml(html) ;
	objContent.focus();
}

function removeFormat() {
	if (objContent.DOM.selection.type.toLowerCase() != "control") {
		decCommand(DECMD_REMOVEFORMAT);
	}
	objContent.focus();
}

function doFontSize(combo) {
	if (objContent.DOM.selection.type.toLowerCase() != "control") {
		if (combo.value == null || combo.value == "") {
			// TODO: Remove font size attribute (Now it works with size 3. Will it work forever?)
			objContent.ExecCommand(DECMD_SETFONTSIZE, 0, 3);
		} else {
			objContent.ExecCommand(DECMD_SETFONTSIZE, 0, parseInt(combo.value));
		}
	}

	objContent.focus();
}

function dialogTable(searchParentTable) {
	if (searchParentTable) {
		var oRange  = objContent.DOM.selection.createRange() ;
		var oParent = oRange.parentElement() ;

		while (oParent && oParent.nodeName != "TABLE") {
			oParent = oParent.parentNode ;
		}

		if (oParent && oParent.nodeName == "TABLE") {
			var oControlRange = objContent.DOM.body.createControlRange();
			oControlRange.add( oParent ) ;
			oControlRange.select() ;
		}
		else
			return ;
	}

	ShowDialog("/admin/Wysiwyg_popup_table.php", window, 400, 300);
	objContent.focus() ;
}

function dialogTableCell() {
	ShowDialog("/admin/Wysiwyg_popup_tablecell.php", window, 570, 220);
	objContent.focus() ;
}

function wswPasteOuterHTML(startHTML, endHTML) {
	var selection = objContent.DOM.selection;
	var range = selection.createRange();
	var html = startHTML + getSelectedHtml() + endHTML;
	if (selection.type == "Control") {
		range.item(0).outerHTML = html;
	} else {
		range.pasteHTML(html);
	}
}

function insertOuterTag(tagName) {
	wswPasteOuterHTML("<" + tagName + ">", "</" + tagName + ">");
}

function wswReplaceSelectedHTML(HTML) {
    var selection = objContent.DOM.selection;
	var range = selection.createRange();
    if(selection.type == "Control") {
        range.item(0).outerHTML = HTML;
    } else {
        range.pasteHTML(HTML);
    }
}

function insertHtmlFromURL(url) {
	ajax(url, insertHtml);
}

function ajax(url, object){
	var xmlhttpr = null;
	if (window.XMLHttpRequest) {
		try {
			xmlhttpr = new XMLHttpRequest();
		} catch(e) {}
	} else if (window.ActiveXObject) {
		try {
			xmlhttpr = new ActiveXObject('Msxml2.XMLHTTP');
		} catch(e) {
			try {
	            xmlhttpr = new ActiveXObject('Microsoft.XMLHTTP');
	        } catch(e) {
	        	try { 
	        		xmlhttpr = new ActiveXObject("MSXML2.XMLHTTP.3.0");
	        	} catch(e) {}
	        }
        }
    }
    
    if (xmlhttpr == null) {
    	alert("Your browser doesn't support data transfer using JavaScript");
    }

	url = url + (url.indexOf("?") != -1 ? '&' : '?') + '_UIN_=' + Math.floor(Math.random()*99999999999999999);

	xmlhttpr.open("GET", url, false);
	xmlhttpr.setRequestHeader("Content-Type", "application/x-www-form-urlencoded"); 

	xmlhttpr.onreadystatechange = function() {
		if(xmlhttpr.readyState == 4 && xmlhttpr.status == 200) {
			if(xmlhttpr.responseText) {
				var obj = xmlhttpr.responseText;
				object(obj);
			}
		}
	}
	xmlhttpr.send(null);
}

//////////////////////////////
//Context menus
//////////////////////////////
TBCMD_DEC		= 0 ;
TBCMD_DOC		= 1 ;
TBCMD_CUSTOM	= 2 ;

// Contants
var MENU_SEPARATOR = "" ; // Context menu separator

// The last context menu
var ContextMenu = new Array() ;

// Avaliable context menu options
var GeneralContextMenu	= new Array() ;
var TableContextMenu	= new Array() ;
var LinkContextMenu		= new Array() ;

function ContextMenuSeparator() {
	this.Text = MENU_SEPARATOR ;
}
// Class that represents an item on the context menu
function ContextMenuItem(text, command, commandType) {
	this.Text			= text ;
	this.Command		= command || "void(0)" ;
	this.CommandType	= commandType || TBCMD_DEC ;
	
	switch (this.CommandType) {
		case TBCMD_DEC :
			this.Command     = "decCommand(" + command + ")" ;
			this.CommandId   = command ;
			break ;
		case TBCMD_DOC :
			this.Command     = "docCommand('" + command + "')" ;
			this.CommandCode = command ;
			break ;
		default :	// TBCMD_CUSTOM
			this.Command     = command ;
			break ;
	}
}

GeneralContextMenu[0] = new ContextMenuItem("<?=$AdminTrnsl["WSWCut"]?>", DECMD_CUT) ;
GeneralContextMenu[1] = new ContextMenuItem("<?=$AdminTrnsl["WSWCopy"]?>", DECMD_COPY) ;
GeneralContextMenu[2] = new ContextMenuItem("<?=$AdminTrnsl["WSWPaste"]?>", DECMD_PASTE) ;

LinkContextMenu[0] = new ContextMenuSeparator() ;
if (!inHelp) {
	LinkContextMenu[1] = new ContextMenuItem("<?=$AdminTrnsl["WSWEditLink"]?>", "wswHLinkDialog()", TBCMD_CUSTOM) ;
} else {
	LinkContextMenu[1] = new ContextMenuItem("<?=$AdminTrnsl["WSWEditLink"]?>", "wswInsertHelpHLink()", TBCMD_CUSTOM) ;
}
LinkContextMenu[2] = new ContextMenuItem("<?=$AdminTrnsl["WSWRemoveLink"]?>", DECMD_UNLINK) ;

TableContextMenu[0]  = new ContextMenuSeparator() ;
TableContextMenu[1]  = new ContextMenuItem("<?=$AdminTrnsl["WSWInsertRow"]?>", DECMD_INSERTROW) ;
TableContextMenu[2]  = new ContextMenuItem("<?=$AdminTrnsl["WSWDeleteRows"]?>", DECMD_DELETEROWS) ;
TableContextMenu[3]  = new ContextMenuSeparator() ;
TableContextMenu[4]  = new ContextMenuItem("<?=$AdminTrnsl["WSWInsertColumn"]?>", DECMD_INSERTCOL) ;
TableContextMenu[5]  = new ContextMenuItem("<?=$AdminTrnsl["WSWDeleteColumns"]?>", DECMD_DELETECOLS) ;
TableContextMenu[6]  = new ContextMenuSeparator() ;
TableContextMenu[7]  = new ContextMenuItem("<?=$AdminTrnsl["WSWInsertCell"]?>", DECMD_INSERTCELL) ;
TableContextMenu[8]  = new ContextMenuItem("<?=$AdminTrnsl["WSWDeleteCells"]?>", DECMD_DELETECELLS) ;
TableContextMenu[9]  = new ContextMenuItem("<?=$AdminTrnsl["WSWMergeCells"]?>", DECMD_MERGECELLS) ;
TableContextMenu[10] = new ContextMenuItem("<?=$AdminTrnsl["WSWSplitCell"]?>", DECMD_SPLITCELL) ;
TableContextMenu[11] = new ContextMenuSeparator() ;
TableContextMenu[12] = new ContextMenuItem("<?=$AdminTrnsl["WSWCellProperties"]?>", "dialogTableCell()", TBCMD_CUSTOM) ;
TableContextMenu[13] = new ContextMenuItem("<?=$AdminTrnsl["WSWTableProperties"]?>", "dialogTable(true)", TBCMD_CUSTOM) ;

function showContextMenu() {
	// Resets the context menu. 
	ContextMenu = new Array() ;

	var i ;
  	var index = 0;

	// Always show general menu options
	for ( i = 0 ; i < GeneralContextMenu.length ; i++ ) {
		ContextMenu[index++] = GeneralContextMenu[i] ;
	}
	// If over a link
	if (checkDecCommand(DECMD_UNLINK) == OLE_TRISTATE_UNCHECKED) {
		for ( i = 0 ; i < LinkContextMenu.length ; i++ ) {
			ContextMenu[index++] = LinkContextMenu[i] ;
		}	
	}

	// If inside a table, load table menu options
	if (objContent.QueryStatus(DECMD_INSERTROW) != DECMDF_DISABLED) {
		for ( i = 0 ; i < TableContextMenu.length ; i++ ) {
			ContextMenu[index++] = TableContextMenu[i] ;
		}
	}

	// Verifies if the selection is a TABLE or IMG
	var sel = objContent.DOM.selection.createRange() ;
	var sTag ;
	if (objContent.DOM.selection.type != 'Text' && sel.length == 1)
		sTag = sel.item(0).tagName ;

	if (sTag == "TABLE") {
		ContextMenu[index++] = new ContextMenuSeparator() ;
		ContextMenu[index++] = new ContextMenuItem("<?=$AdminTrnsl["WSWTableProperties"]?>", "dialogTable()", TBCMD_CUSTOM) ;
	} else if (sTag == "IMG") {
		ContextMenu[index++] = new ContextMenuSeparator() ;
		if (inHelp) {
			ContextMenu[index++] = new ContextMenuItem("<?=$AdminTrnsl["WSWImageProperties"]?>", "dialogHelpImage()", TBCMD_CUSTOM) ;
		} else {
			ContextMenu[index++] = new ContextMenuItem("<?=$AdminTrnsl["WSWImageProperties"]?>", "dialogImage()", TBCMD_CUSTOM) ;
		}
	}

	// Set up the actual arrays that get passed to SetContextMenu
	var menuStrings = new Array() ;
	var menuStates  = new Array() ;
	for ( i = 0 ; i < ContextMenu.length ; i++ ) {
		menuStrings[i] = ContextMenu[i].Text ;

		if (menuStrings[i] != MENU_SEPARATOR) 
			switch (ContextMenu[i].CommandType) {
				case TBCMD_DEC :
					menuStates[i] = checkDecCommand(ContextMenu[i].CommandId) ;
					break ;
				case TBCMD_DOC :
					menuStates[i] = checkDocCommand(ContextMenu[i].CommandCode) ;
					break ;
				default :
					menuStates[i] = OLE_TRISTATE_UNCHECKED ;
					break ;
			}
		else
			menuStates[i] = OLE_TRISTATE_CHECKED ;
	}

	// Set the context menu
	objContent.SetContextMenu(menuStrings, menuStates);
}

function contextMenuAction(itemIndex) {
	eval(ContextMenu[itemIndex].Command) ;
}

function ButtonOver(oImage, Name) {
	if (oImage.className != "ButtonOver") oImage.className = "ButtonOver";
}

function ButtonOut(oImage, Name) {
	if (oImage.className != "ButtonOut") oImage.className = "ButtonOut";
}

function doStyle(command) {
	decCommand(DECMD_REMOVEFORMAT);
	var oSelection = objContent.DOM.selection ;
	var oTextRange = oSelection.createRange() ;
	
	if (oSelection.type == "Text") {
		decCommand(DECMD_REMOVEFORMAT);

		var oFont = document.createElement("FONT") ;
		oFont.innerHTML = oTextRange.htmlText ;
		
		var oParent = oTextRange.parentElement() ;
		var oFirstChild = oFont.firstChild ;
		
		if (oFirstChild.nodeType == 1 && oFirstChild.outerHTML == oFont.innerHTML && 
				(oFirstChild.tagName == "SPAN"
				|| oFirstChild.tagName == "FONT"
				|| oFirstChild.tagName == "P"
				|| oFirstChild.tagName == "DIV"))
		{
			oParent.className = command.value ;
		} else {
			oFont.className = command.value ;
			oTextRange.pasteHTML( oFont.outerHTML ) ;
		}
	}
	else if (oSelection.type == "Control" && oTextRange.length == 1) {
		var oControl = oTextRange.item(0) ;
		oControl.className = command.value ;
	}

	command.selectedIndex = 0 ;
	
	objContent.focus();
}
