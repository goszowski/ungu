<?require_once("prepend.php");?>
<html>
	<head>
<meta HTTP-EQUIV="content-type" CONTENT="text/html; charset=<?=ADMIN_CHARSET?>">
<meta http-equiv="Expires" content="0">
<meta http-equiv="Cache-Control" content="must-revalidate, post-check=0, pre-check=0">
<meta http-equiv="pragma" content="no-cache">
		<meta name="vs_targetSchema" content="http://schemas.microsoft.com/intellisense/ie5">
		<link href="/admin/css.css" type="text/css" rel="stylesheet">
		<script language="javascript">
<!--
document.write('<title><?=$AdminTrnsl["WSWTableCellProperties"]?></title>') ;

// Array of selected Cells
var aTD = new Array() ;

function setDefaults() {
	// Gets the document DOM
	var oDOM = dialogArguments.objContent.DOM ;
	var oRange = oDOM.selection.createRange() ;

	var oParent = oRange.parentElement() ;

	while (oParent && oParent.tagName != "td" && oParent.tagName != "TABLE")
		oParent = oParent.parentNode ;

	if ( oParent.tagName == "td" )
		aTD[0] = oParent ;
	else if ( oParent.tagName == "TABLE" ) {
		// Loops throw all cells cheking if the selection, or part of it, is inside the cell
		// and then add it to the selectec cells collection
		for (i = 0 ; i < oParent.cells.length ; i++) {
			var oCellRange = oDOM.selection.createRange() ;
			oCellRange.moveToElementText(oParent.cells[i]) ;
			if ( oRange.inRange( oCellRange ) 
				|| ( oRange.compareEndPoints('StartToStart',oCellRange) >= 0 &&  oRange.compareEndPoints('StartToEnd',oCellRange) <= 0 )
				|| ( oRange.compareEndPoints('EndToStart',oCellRange) >= 0 &&  oRange.compareEndPoints('EndToEnd',oCellRange) <= 0 ) )
			{
				aTD[aTD.length] = oParent.cells[i] ;
			}
		}
	}
	
	if (aTD[0]) {
		var iWidth = aTD[0].width ;
		
		if (iWidth.indexOf('%') >= 0) {
			iWidth = iWidth.substr(0,iWidth.length - 1) ;
			selWidthType.value = "percent" ;
		}
	
		txtWidth.value			= iWidth ;
		txtHeight.value			= aTD[0].height ;
		selWordWrap.value		= ! aTD[0].noWrap ;
		selHAlign.value			= aTD[0].align ;
		selVAlign.value			= aTD[0].vAlign ;
		txtRowSpan.value		= aTD[0].rowSpan ;
		txtCollSpan.value		= aTD[0].colSpan ;
		txtBackColor.value		= aTD[0].bgColor ; 
		txtBorderColor.value	= aTD[0].borderColor ;
	}
}

function ok() {
	for( i = 0 ; i < aTD.length ; i++ ) {
		if (txtWidth.value			!= "") aTD[i].width			= txtWidth.value + (selWidthType.value == "percent" ? "%" : "") ;		else aTD[i].removeAttribute("width") ;
		if (txtHeight.value			!= "") aTD[i].height		= txtHeight.value ;		else aTD[i].removeAttribute("height") ;
		if (selWordWrap.value		!= "") aTD[i].noWrap		= selWordWrap.value == "false" ; else aTD[i].removeAttribute("noWrap") ;
		if (selHAlign.value			!= "") aTD[i].align			= selHAlign.value ;		else aTD[i].removeAttribute("align") ;
		if (selVAlign.value			!= "") aTD[i].vAlign		= selVAlign.value ;		else aTD[i].removeAttribute("vAlign") ;
		if (txtRowSpan.value		!= "") aTD[i].rowSpan		= txtRowSpan.value ;	else aTD[i].removeAttribute("rowSpan") ;
		if (txtCollSpan.value		!= "") aTD[i].colSpan		= txtCollSpan.value ;	else aTD[i].removeAttribute("colSpan") ;
		if (txtBackColor.value		!= "") aTD[i].bgColor		= txtBackColor.value ;	else aTD[i].removeAttribute("bgColor") ;
		if (txtBorderColor.value	!= "") aTD[i].borderColor	= txtBorderColor.value ; else aTD[i].removeAttribute("borderColor") ;
	}
	
	cancel() ;
}

// Fired when the user press the CANCEL button.
function cancel()  {
	window.returnValue = null ;
	window.close() ;
}

function SelectBackColor() {
	var sColor = SelectColor() ;
	if (sColor) txtBackColor.value = sColor ;
}

function SelectBorderColor() {
	var sColor = SelectColor() ;
	if (sColor) txtBorderColor.value = sColor ;
}

function SelectColor() {
	return showModalDialog("/admin/Wysiwyg_popup_selectcolor.php", dialogArguments, "dialogWidth:310px;dialogHeight:240px;help:no;scroll:no;status:no");
}

function IsDigit() {
	return ((event.keyCode >= 48) && (event.keyCode <= 57))
}
//-->
		</script>
	</head>
	<body class=main bottommargin="5" leftmargin="5" topmargin="5" rightmargin="5" onload="setDefaults()">
		<table cellSpacing="2" cellPadding="0" width="100%" border="0">
		<tr>
			<td>
				<table cellSpacing="2" cellPadding="0" width="100%" border="0">
				<tr>
					<td>
						<table cellSpacing="2" cellPadding="2" border="0">
						<tr class=back2>
							<td nowrap><span fckLang="DlgCellWidth"><?=$AdminTrnsl["WSWTableWidth"]?></span>:</td>
							<td>&nbsp;<input onkeypress="event.returnValue=IsDigit();" id="txtWidth" type="text" maxLength="4"
									size="3" name="txtWidth">&nbsp;<select id="selWidthType" name="selWidthType">
									<option fckLang="DlgCellWidthPx" value="pixels" selected><?=$AdminTrnsl["WSWTablepixels"]?></option>
									<option fckLang="DlgCellWidthPc" value="percents"><?=$AdminTrnsl["WSWTablepercents"]?></option>
								</select></td>
						</tr>
						<tr class=back2>
							<td nowrap><span fckLang="DlgCellHeight"><?=$AdminTrnsl["WSWTableHeight"]?></span>:</td>
							<td>&nbsp;<INPUT id="txtHeight" type="text" maxLength="4" size="3" name="txtHeight" onkeypress="event.returnValue=IsDigit();">&nbsp;<span fckLang="DlgCellWidthPx"><?=$AdminTrnsl["WSWTablepixels"]?></span></td>
						</tr>
						<tr class=back2>
							<td nowrap><span fckLang="DlgCellWordWrap"><?=$AdminTrnsl["WSWTableWordWrap"]?></span>:</td>
							<td>&nbsp;<select id="selWordWrap" name="selAlignment">
									<option fckLang="DlgCellWordWrapNotSet" value="" selected>&lt;<?=$AdminTrnsl["WSWTableNotset"]?>&gt;</option>
									<option fckLang="DlgCellWordWrapYes" value="true"><?=$AdminTrnsl["Yes"]?></option>
									<option fckLang="DlgCellWordWrapNo" value="false"><?=$AdminTrnsl["No"]?></option>
								</select></td>
						</tr>
						<tr class=back2>
							<td nowrap><span fckLang="DlgCellHorAlign"><?=$AdminTrnsl["WSWTableHorizontalAlignment"]?></span>:</td>
							<td>&nbsp;<select id="selHAlign" name="selAlignment">
									<option fckLang="DlgCellHorAlignNotSet" value="" selected>&lt;<?=$AdminTrnsl["WSWTableNotset"]?>&gt;</option>
									<option fckLang="DlgCellHorAlignLeft" value="left"><?=$AdminTrnsl["WSWTableAlignLeft"]?></option>
									<option fckLang="DlgCellHorAlignCenter" value="center"><?=$AdminTrnsl["WSWTableAlignCenter"]?></option>
									<option fckLang="DlgCellHorAlignRight" value="right"><?=$AdminTrnsl["WSWTableAlignRight"]?></option>
								</select></td>
						</tr>
						<tr class=back2>
							<td nowrap><span fckLang="DlgCellVerAlign"><?=$AdminTrnsl["WSWTableVertivalAlignement"]?></span>:</td>
							<td>&nbsp;<select id="selVAlign" name="selAlignment">
									<option fckLang="DlgCellVerAlignNotSet" value="" selected>&lt;<?=$AdminTrnsl["WSWTableNotset"]?>&gt;</option>
									<option fckLang="DlgCellVerAlignTop" value="top"><?=$AdminTrnsl["WSWTableAlignTop"]?></option>
									<option fckLang="DlgCellVerAlignMiddle" value="middle"><?=$AdminTrnsl["WSWTableAlignMiddle"]?></option>
									<option fckLang="DlgCellVerAlignBottom" value="bottom"><?=$AdminTrnsl["WSWTableAlignBottom"]?></option>
									<option fckLang="DlgCellVerAlignBaseline" value="baseline"><?=$AdminTrnsl["WSWTableAlignBaseline"]?></option>
								</select></td>
						</tr>
						</table>
					</td>
					<td align="right">
						<table cellSpacing="2" cellPadding="2" border="0">
						<tr class=back2>
							<td nowrap><span fckLang="DlgCellRowSpan"><?=$AdminTrnsl["WSWTableRowsSpan"]?></span>:</td>
							<td>&nbsp; <input onkeypress="event.returnValue=IsDigit();" id="txtRowSpan" type="text" maxLength="3"
									size="2" name="txtRows"></td>
							<td></td>
						</tr>
						<tr class=back2>
							<td nowrap><span fckLang="DlgCellCollSpan"><?=$AdminTrnsl["WSWTableColumnsSpan"]?></span>:</td>
							<td>&nbsp; <input onkeypress="event.returnValue=IsDigit();" id="txtCollSpan" type="text" maxLength="2"
									size="2" name="txtColumns"></td>
							<td></td>
						</tr>
						<tr class=back2>
							<td nowrap><span fckLang="DlgCellBackColor"><?=$AdminTrnsl["WSWTableBackgroundColor"]?></span>:</td>
							<td>&nbsp;<input id="txtBackColor" type="text" size="8" name="txtCellSpacing"></td>
							<td>&nbsp; <input type="button" fckLang="DlgCellBtnSelect" value="Select" onclick="SelectBackColor()" class=but2></td>
						</tr>
						<tr class=back2>
							<td nowrap><span fckLang="DlgCellBorderColor"><?=$AdminTrnsl["WSWTableBorderColor"]?></span>:</td>
							<td>&nbsp;<input id="txtBorderColor" type="text" size="8" name="txtCellPadding"></td>
							<td>&nbsp; <input type="button" fckLang="DlgCellBtnSelect" value="Select" onclick="SelectBorderColor()" class=but2></td>
						</tr>
						</table>
					</td>
				</tr>
				</table>
			</td>
		</tr>
		<tr>
			<td align="middle">
				<input type="button" fckLang="DlgBtnOK" value="OK" onclick="ok();" style="WIDTH: 100px" class=but2>&nbsp; <input type="button" fckLang="DlgBtnCancel" value="Cancel" onclick="cancel();" class=but2>
			</td>
		</tr>
		</table>
	</body>
</html>
