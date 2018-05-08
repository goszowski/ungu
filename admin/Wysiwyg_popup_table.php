<?require_once("prepend.php");?>
<html>
	<head>
<meta HTTP-EQUIV="content-type" CONTENT="text/html; charset=<?=ADMIN_CHARSET?>">
<meta http-equiv="Expires" content="0">
<meta http-equiv="Cache-Control" content="must-revalidate, post-check=0, pre-check=0">
<meta http-equiv="pragma" content="no-cache">
		<link href="/admin/css.css" type="text/css" rel="stylesheet">
		<!-- Constants -->
		<script language="javascript" src="/admin/js/wysiwyg.js.php"></script>
		<script language="javascript">
<!--
// Gets the document DOM
var oDOM = dialogArguments.objContent.DOM ;

// Gets the table if there is one selected.
var table ;
var sel = oDOM.selection.createRange();
if (oDOM.selection.type != 'Text') 
{
	if (sel.length == 1) 
	{
		if (sel.item(0).tagName == "TABLE") 
			table = sel.item(0);
	}
}

// Set the dialog title.
if (table) 
	document.write("<TITLE><?=$AdminTrnsl["WSWTableEdittable"]?></TITLE>") ;
else 
	document.write("<TITLE><?=$AdminTrnsl["WSWTableInserttable"]?></TITLE>") ;

// Fired when the window loading process is finished. It sets the fields with the
// actual values if a table is selected in the editor.
function setDefaults()
{
	// First of all, translate the dialog box texts

	if (table) {
		txtRows.value    = table.rows.length ;
		txtColumns.value = table.rows[0].cells.length ;

		// Gets the value from the Width or the Style attribute
		var iWidth  = (table.style.width  ? table.style.width  : table.width ) ;
		var iHeight = (table.style.height ? table.style.height : table.height ) ;

		if (iWidth.indexOf('%') >= 0)			// Percentual = %
		{
			iWidth = parseInt( iWidth.substr(0,iWidth.length - 1) ) ;
			selWidthType.value = "percent" ;
		}
		else if (iWidth.indexOf('px') >= 0)		// Style Pixel = px
		{																										  //
			iWidth = iWidth.substr(0,iWidth.length - 2);
			selWidthType.value = "pixels" ;
		}
		
		if (iHeight.indexOf('px') >= 0)		// Style Pixel = px
			iHeight = iHeight.substr(0,iHeight.length - 2);
		
		txtWidth.value			= iWidth ;
		txtHeight.value			= iHeight ;
		txtBorder.value			= table.border ;
		selAlignment.value		= table.align ;
		txtCellPadding.value	= table.cellPadding	;
		txtCellSpacing.value	= table.cellSpacing	;
		txtClass.value	= table.className ;
		if (table.caption) txtCaption.value	= table.caption.innerText ;		
		
		txtRows.disabled    = true ;
		txtColumns.disabled = true ;
	}	
}

// Fired when the user press the OK button
function ok() {
	if (! table) {
		var sTableAtt = "" ;

		if (txtWidth.value != '')		sTableAtt += 'width="' + txtWidth.value + (selWidthType.value == "percent" ? "%" : "") + '"' ;
		if (txtHeight.value != '')		sTableAtt += 'height="' + txtHeight.value + '"' ;
		if (txtBorder.value != '')		sTableAtt += 'border="' + txtBorder.value + '"' ;
		if (selAlignment.value != '')	sTableAtt += 'align="' + selAlignment.value + '"' ;
		if (txtCellSpacing.value != '')	sTableAtt += 'cellspacing="' + txtCellSpacing.value + '"' ;
		if (txtCellPadding.value != '')	sTableAtt += 'cellpadding="' + txtCellPadding.value + '"' ;
		if (txtClass.value != '')	sTableAtt += 'class="' + txtClass.value + '"' ;

		var oTableParms = new ActiveXObject("DEInsertTableParam.DEInsertTableParam") ;
		
		if (txtRows.value != '')	oTableParms.NumRows    = txtRows.value ;
		if (txtColumns.value != '')	oTableParms.NumCols    = txtColumns.value ;
		if (sTableAtt != '')		oTableParms.TableAttrs = sTableAtt ;
		if (txtCaption.value != '')	oTableParms.Caption    = txtCaption.value ;
		
		dialogArguments.objContent.DOM.selection.clear() ;
		dialogArguments.objContent.ExecCommand(DECMD_INSERTTABLE, 0, oTableParms);
	} else {
		// Removes the Width and Height styles
		if ( table.style.width )	table.style.removeAttribute("width") ;
		if ( table.style.height )	table.style.removeAttribute("height") ;
		
		table.width			= txtWidth.value + (selWidthType.value == "percent" ? "%" : "") ;
		table.height		= txtHeight.value ;
		table.border		= txtBorder.value ;
		table.align			= selAlignment.value ;
		table.cellPadding	= txtCellPadding.value ;
		table.cellSpacing	= txtCellSpacing.value ;
		table.className 	= txtClass.value ;
		
		if (txtCaption.value != '') {
			if (! table.caption) table.createCaption() ;
			table.caption.innerText = txtCaption.value ;
		}
//		else
//			table.deleteCaption() ;		// TODO: It causes an IE error.
	}
	
	window.close();
}

// Fired when the user press the CANCEL button.
function cancel() {
	window.close() ;
}

function IsDigit() {
	return ((event.keyCode >= 48) && (event.keyCode <= 57))
}
//-->
		</script>
	</head>
	<body class=main bottommargin="5" leftmargin="5" topmargin="5" rightmargin="5" onload="setDefaults()">
		<table cellSpacing="2" cellPadding="0" width="100%" border="0" height="100%">
		<tr>
			<td class=back2>
				<table cellSpacing="0" cellPadding="2" border="0">
				<tr>
					<td><span fckLang="DlgTableRows"><?=$AdminTrnsl["WSWTableRows"]?></span>:</td>
					<td><input id="txtRows" type="text" maxLength="3" size="2" value="3" name="txtRows" onkeypress="event.returnValue=IsDigit();"></td>
					<td align=right><span fckLang="DlgTableCellPad"><?=$AdminTrnsl["WSWTableCellpadding"]?></span>:</td>
					<td><input id="txtCellPadding" type="text" maxLength="2" size="2" value="1" name="txtCellPadding" onkeypress="event.returnValue=IsDigit();"></td>
				</tr>
				<tr>
					<td><span fckLang="DlgTableColumns"><?=$AdminTrnsl["WSWTableColumns"]?></span>:</td>
					<td><input id="txtColumns" type="text" maxLength="2" size="2" value="2" name="txtColumns" onkeypress="event.returnValue=IsDigit();"></td>
					<td align=right><span fckLang="DlgTableCellSpace"><?=$AdminTrnsl["WSWTableCellspacing"]?></span>:</td>
					<td><input id="txtCellSpacing" type="text" maxLength="2" size="2" value="1" name="txtCellSpacing" onkeypress="event.returnValue=IsDigit();"></td>
				</tr>
				</table>
			</td>
			<td align="middle" rowspan=3 valign=bottom>
				<input style="WIDTH: 70px" type="button" value="OK" onclick="ok();"><br>
				<input style="WIDTH: 70px" type="button" value="<?=$AdminTrnsl["WSWSCharCancel"]?>" onclick="cancel();">
			</td>
		</tr>
		<tr class=back2>
			<td>
				<table cellSpacing="0" cellPadding="2" border="0">
				<tr>
					<td><span fckLang="DlgTableWidth"><?=$AdminTrnsl["WSWTableWidth"]?></span>:</td>
					<td><input id="txtWidth" type="text" maxLength="4" size="1" value="200" name="txtWidth" onkeypress="event.returnValue=IsDigit();"></td>
					<td><select id="selWidthType" name="selWidthType">
							<option fckLang="DlgTableWidthPx" value="pixels" selected><?=$AdminTrnsl["WSWTablepixels"]?></option>
							<option fckLang="DlgTableWidthPc" value="percent"><?=$AdminTrnsl["WSWTablepercents"]?></option>
						</select>
					</td>
					<td rowspan=2 colspan=2>&nbsp;</td>
				</tr>
				<tr>
					<td><span fckLang="DlgTableHeight"><?=$AdminTrnsl["WSWTableHeight"]?></span>:</td>
					<td><INPUT id="txtHeight" type="text" maxLength="4" size="1" name="txtHeight" onkeypress="event.returnValue=IsDigit();"></td>
					<td><span fckLang="DlgTableWidthPx"><?=$AdminTrnsl["WSWTablepixels"]?></span></td>
				</tr>
				<tr>
					<td><span fckLang="DlgTableHeight">Class</span>:</td>
					<td colspan=2><select id="txtClass" name="txtClass"><option value="tab1">tab1<option value="tab2">tab2</select></td>
				</tr>
				</table>
				<table cellSpacing="0" cellPadding="2" border="0">
				<tr>
					<td width=1%><span fckLang="DlgTableAlign"><?=$AdminTrnsl["WSWTableAlign"]?></span>:</td>
					<td width=1% align=left><select id="selAlignment" name="selAlignment">
							<option fckLang="DlgTableAlignNotSet" value="" selected>&lt;<?=$AdminTrnsl["WSWTableNotset"]?>&gt;</option>
							<option fckLang="DlgTableAlignLeft" value="left"><?=$AdminTrnsl["WSWTableAlignLeft"]?></option>
							<option fckLang="DlgTableAlignCenter" value="center"><?=$AdminTrnsl["WSWTableAlignCenter"]?></option>
							<option fckLang="DlgTableAlignRight" value="right"><?=$AdminTrnsl["WSWTableAlignRight"]?></option>
						</select></td>
				</tr>
				<tr>
					<td align=right nowrap><span fckLang="DlgTableBorder"><?=$AdminTrnsl["WSWTableBordersize"]?></span>:</td>
					<td width=1%>&nbsp;<INPUT id="txtBorder" type="text" maxLength="2" size="2" value="1" name="txtBorder" onkeypress="event.returnValue=IsDigit();"></td>
				</tr>
				</table>
			</td>
		</tr>
		<tr class=back2>
			<td>
				<table cellSpacing="0" cellPadding="2" width="100%" border="0">
				<tr>
					<td><span fckLang="DlgTableCaption"><?=$AdminTrnsl["WSWTableCaption"]?></span>:</td>
					<td width="100%">
						<input id="txtCaption" type="text" style="WIDTH: 100%"></td>
				</tr>
				</table>
			</td>
		</tr>
		</table>
	</body>
</html>
