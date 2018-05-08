<?
require_once("prepend.php");
$field_shortname = $request->getParameter("field_shortname");
$cname = $field_shortname;
$currentLoggedUser = $session->getAttribute(SESSION_LOGGED_USER_ATTR);
$restrict_edit = $currentLoggedUser->group->restrictNodeEdit;
?>
<html>
<head>
<meta HTTP-EQUIV="content-type" CONTENT="text/html; charset=<?=ADMIN_CHARSET?>">
<meta http-equiv="Expires" content="0">
<meta http-equiv="Cache-Control" content="must-revalidate, post-check=0, pre-check=0">
<meta http-equiv="pragma" content="no-cache">
<script language="javascript">
	var wswName = '<?=$field_shortname?>';
</script>
	<link rel=stylesheet href="/admin/css.css" type="text/css">
    <link rel=stylesheet href="/admin/Wysiwyg_css.php" type="text/css">
	<link rel=stylesheet href="/main.css" type="text/css">
	<script type="text/javascript" src="/admin/js/wysiwyg.js.php"></script>
	<style>
.ButtonOut
{
	filter: alpha(opacity=70);
	border-right: #efefde 1px solid;
	border-top: #efefde 1px solid;
	border-left: #efefde 1px solid;
	border-bottom: #efefde 1px solid;
}

.ButtonOver
{
	background-color: #c1d2ee;
	border-right: #316ac5 1px solid;
	border-top: #316ac5 1px solid;
	border-left: #316ac5 1px solid;
	border-bottom: #316ac5 1px solid;
}
</style>

			<!-- DEC Events -->
			<script language="javascript" event="DocumentComplete" for="objContent">
<!--
initEditor();
//-->
			</script>
			<script language="javascript" event="DisplayChanged" for="objContent">
<!--
//events.fireEvent('onEditing') ;
//-->
			</script>
			<script language="javascript" event="ShowContextMenu" for="objContent">
<!--
showContextMenu() ;
//-->
			</script>
			<script language="javascript" event="ContextMenuAction(itemIndex)" for="objContent">
<!--
contextMenuAction(itemIndex) ;
//-->
			</script>
			<script language="javascript" event="onerror(msg, url, line)" for="window">
<!--
//return true ;	 // To hide errors
return false ; // To show errors
//-->
			</script>
			<script language="javascript" event="onload" for="window">
<!--
if (window == window.top)
	alert('You should not call "Wysywig_echovalue.php" directly.' ) ;
//-->
			</script>


</head>

<body class=main bottommargin="0" leftmargin="0" topmargin="0" rightmargin="0">
<table height="100%" cellspacing="0" cellpadding="0" width="100%">
<tr id="trEditor"><td>

<table border="0" cellpadding="0" cellspacing="0" height="100%" width="100%">
<tr><td>

<table cellpadding=0 cellspacing=0 border=0>
<tr><td nowrap>
<a href="javascript://" onClick="docCommand('Cut')"><img src="/admin/_img/wysiwyg/icon2.gif" alt="<?=$AdminTrnsl["WSWHBCut"]?>" width="23" height="22" onmouseover="ButtonOver(this);" onmouseout="ButtonOut(this);" class="ButtonOut"></a>
<a href="javascript://" onClick="docCommand('Copy')"><img src="/admin/_img/wysiwyg/icon3.gif" alt="<?=$AdminTrnsl["WSWHBCopy"]?>" width="23" height="22" onmouseover="ButtonOver(this);" onmouseout="ButtonOut(this);" class="ButtonOut"></a>
<a href="javascript://" onClick="docCommand('Paste')"><img src="/admin/_img/wysiwyg/icon4.gif" alt="<?=$AdminTrnsl["WSWHBPaste"]?>" width="23" height="22" onmouseover="ButtonOver(this);" onmouseout="ButtonOut(this);" class="ButtonOut"></a>
<a href="javascript://" onClick="docCommand('Undo')"><img src="/admin/_img/wysiwyg/icon_undo.gif" alt="<?=$AdminTrnsl["WSWHBUndo"]?>" width="23" height="22" onmouseover="ButtonOver(this);" onmouseout="ButtonOut(this);" class="ButtonOut"></a>
<a href="javascript://" onClick="docCommand('Redo')"><img src="/admin/_img/wysiwyg/icon_redo.gif" alt="<?=$AdminTrnsl["WSWHBRedo"]?>" width="23" height="22" onmouseover="ButtonOver(this);" onmouseout="ButtonOut(this);" class="ButtonOut"></a>
<a href="javascript://" onClick="docCommand('Bold')"><img src="/admin/_img/wysiwyg/icon9.gif" alt="<?=$AdminTrnsl["WSWHBBold"]?>" width="23" height="22" onmouseover="ButtonOver(this);" onmouseout="ButtonOut(this);" class="ButtonOut"></a>
<a href="javascript://" onClick="docCommand('Italic')"><img src="/admin/_img/wysiwyg/icon10.gif" alt="<?=$AdminTrnsl["WSWHBItalic"]?>" width="23" height="22" onmouseover="ButtonOver(this);" onmouseout="ButtonOut(this);" class="ButtonOut"></a>
<a href="javascript://" onClick="docCommand('Underline')"><img src="/admin/_img/wysiwyg/icon11.gif" alt="<?=$AdminTrnsl["WSWHBUnderline"]?>" width="23" height="22" onmouseover="ButtonOver(this);" onmouseout="ButtonOut(this);" class="ButtonOut"></a>
<a href="javascript://" onClick="docCommand('JustifyLeft')"><img src="/admin/_img/wysiwyg/icon12.gif" alt="<?=$AdminTrnsl["WSWHBJLeft"]?>" width="23" height="22" onmouseover="ButtonOver(this);" onmouseout="ButtonOut(this);" class="ButtonOut"></a>
<a href="javascript://" onClick="docCommand('JustifyCenter')"><img src="/admin/_img/wysiwyg/icon13.gif" alt="<?=$AdminTrnsl["WSWHBJCenter"]?>" width="23" height="22" onmouseover="ButtonOver(this);" onmouseout="ButtonOut(this);" class="ButtonOut"></a>
<a href="javascript://" onClick="docCommand('JustifyRight')"><img src="/admin/_img/wysiwyg/icon14.gif" alt="<?=$AdminTrnsl["WSWHBJRight"]?>" width="23" height="22" onmouseover="ButtonOver(this);" onmouseout="ButtonOut(this);" class="ButtonOut"></a>
<a href="javascript://" onClick="docCommand('JustifyFull')"><img src="/admin/_img/wysiwyg/icon113.gif" alt="<?=$AdminTrnsl["WSWHBJFull"]?>" width="23" height="22" onmouseover="ButtonOver(this);" onmouseout="ButtonOut(this);" class="ButtonOut"></a>
<a href="javascript://" onClick="docCommand('InsertOrderedList')"><img src="/admin/_img/wysiwyg/icon15.gif" alt="<?=$AdminTrnsl["WSWHBOList"]?>" width="23" height="22" onmouseover="ButtonOver(this);" onmouseout="ButtonOut(this);" class="ButtonOut"></a>
<a href="javascript://" onClick="docCommand('InsertUnOrderedList')"><img src="/admin/_img/wysiwyg/icon16.gif" alt="<?=$AdminTrnsl["WSWHBUList"]?>" width="23" height="22" onmouseover="ButtonOver(this);" onmouseout="ButtonOut(this);" class="ButtonOut"></a>
<a href="javascript://" onClick="docCommand('Indent')"><img src="/admin/_img/wysiwyg/icon_indent.gif" alt="<?=$AdminTrnsl["WSWHBIndent"]?>" width="23" height="22" onmouseover="ButtonOver(this);" onmouseout="ButtonOut(this);" class="ButtonOut"></a>
<a href="javascript://" onClick="docCommand('Outdent')"><img src="/admin/_img/wysiwyg/icon_outdent.gif" alt="<?=$AdminTrnsl["WSWHBOutdent"]?>" width="23" height="22" onmouseover="ButtonOver(this);" onmouseout="ButtonOut(this);" class="ButtonOut"></a>&nbsp;
<a href="javascript://" onClick="removeFormat()"><img src="/admin/_img/wysiwyg/icon20.gif" alt="<?=$AdminTrnsl["WSWHBRemoveFormat"]?>" width="23" height="22" border="0" onmouseover="ButtonOver(this);" onmouseout="ButtonOut(this);" class="ButtonOut"></a>
<a href="javascript://" onClick="wswClean()"><img src="/admin/_img/wysiwyg/icon21.gif" alt="<?=$AdminTrnsl["WSWHBClean"]?>" width="23" height="22" onmouseover="ButtonOver(this);" onmouseout="ButtonOut(this);" class="ButtonOut"></a>
</td>
</tr></table>

<table cellpadding=0 cellspacing=0 border=0>
<tr>
<td valign=center><input type="checkbox" name="wysiwyg_<?=$cname?>_mode_chekbox" onClick="return switchEditMode()"/><?=$AdminTrnsl["View_HTML"]?></td>
<td valign=center>&nbsp;&nbsp;<?=$AdminTrnsl["WSWFontSize"]?>: 
<select onChange="insertOuterTag(this.options[this.selectedIndex].value)">
<option value="">
<option value="H1">H1
<option value="H2">H2
<option value="H3">H3
<option value="H4">H4
</select></td>
<td valign=center>
<a href="javascript://" onClick="wswHLinkDialog()"><img src="/admin/_img/wysiwyg/icon5.gif" alt="<?=$AdminTrnsl["WSWHBHLink"]?>" width="23" height="22" onmouseover="ButtonOver(this);" onmouseout="ButtonOut(this);" class="ButtonOut"></a>
<?if(!$restrict_edit){?><a href="javascript://" onClick="wswLargeImagePopup()"><img src="/admin/_img/wysiwyg/icon115.gif" alt="<?=$AdminTrnsl["WSWHBHLinkToImage"]?>" width="23" height="22" onmouseover="ButtonOver(this);" onmouseout="ButtonOut(this);" class="ButtonOut"></a><?}?>
<a href="javascript://" onClick="wswRemoveHLink()"><img src="/admin/_img/wysiwyg/icon51.gif" alt="<?=$AdminTrnsl["WSWHBRemoveHLink"]?>" width="23" height="22" onmouseover="ButtonOver(this);" onmouseout="ButtonOut(this);" class="ButtonOut"></a>
<a href="javascript://" onClick="docCommand('Superscript')"><img src="/admin/_img/wysiwyg/icon_sup.gif" alt="<?=$AdminTrnsl["WSWHBSup"]?>" width="23" height="22" onmouseover="ButtonOver(this);" onmouseout="ButtonOut(this);" class="ButtonOut"></a>
<a href="javascript://" onClick="docCommand('Subscript')"><img src="/admin/_img/wysiwyg/icon_sub.gif" alt="<?=$AdminTrnsl["WSWHBSub"]?>" width="23" height="22" onmouseover="ButtonOver(this);" onmouseout="ButtonOut(this);" class="ButtonOut"></a>
<!-- <a href="javascript://" onClick="wswSelectColor()"><img src="/admin/_img/wysiwyg/icon8.gif" alt="<?=$AdminTrnsl["WSWHBColor"]?>" width="23" height="22" onmouseover="ButtonOver(this);" onmouseout="ButtonOut(this);" class="ButtonOut"></a> -->
<a href="javascript://" onClick="dialogTable()"><img src="/admin/_img/wysiwyg/icon17.gif" alt="<?=$AdminTrnsl["WSWHBTable"]?>" width="23" height="22 border="0" onmouseover="ButtonOver(this);" onmouseout="ButtonOut(this);" class="ButtonOut"></a>
<a href="javascript://" onClick="dialogImage()"><img src="/admin/_img/wysiwyg/icon6.gif" alt="<?=$AdminTrnsl["WSWHBImage"]?>" width="23" height="22" onmouseover="ButtonOver(this);" onmouseout="ButtonOut(this);" class="ButtonOut"></a>
<a href="javascript://" onClick="insertHRule()"><img src="/admin/_img/wysiwyg/icon18.gif" alt="<?=$AdminTrnsl["WSWHBHRule"]?>" width="23" height="22" border="0" onmouseover="ButtonOver(this);" onmouseout="ButtonOut(this);" class="ButtonOut"></a>
<a href="javascript://" onClick="insertSpecialChar()"><img src="/admin/_img/wysiwyg/icon19.gif" alt="<?=$AdminTrnsl["WSWHBCSChar"]?>" width="23" height="22" border="0" onmouseover="ButtonOver(this);" onmouseout="ButtonOut(this);" class="ButtonOut"></a>
</td>
</tr>
</table>
<? if ($field_shortname == "body") { ?>
<button onclick="insertHtmlFromURL('/_wsw_last_galleries')">Текст по теме "Выставки"</button>
<button onclick="insertHtmlFromURL('/_wsw_last_master_classes')">Текст по теме "Мастер-классы"</button>
<? } ?>
</td>
</tr>
<tr height="100%">
<td>
								<object class="EditorArea" id="objContent" classid="clsid:2D360201-FFF5-11D1-8D03-00A0C959BC0A"
									codebase="dhtmled.cab" viewastext style="background-color:black">
									<param name="ActivateApplets" value="0">
									<param name="ActivateActiveXControls" value="0">
									<param name="ActivateDTCs" value="1">
									<param name="ShowDetails" value="0">
									<param name="ShowBorders" value="0">
									<param name="Appearance" value="1">
									<param name="Scrollbars" value="1">
									<param name="ScrollbarAppearance" value="1">
									<param name="SourceCodePreservation" value="1">
									<param name="AbsoluteDropMode" value="0">
									<param name="SnapToGrid" value="0">
									<param name="SnapToGridX" value="50">
									<param name="SnapToGridY" value="50">
									<param name="UseDivOnCarriageReturn" value="1">
								</object>
</td>
</tr></table>
</td>
</tr>
<tr id="trSource" style="DISPLAY: none">
<td>

<table border="0" cellpadding="0" cellspacing="0" height="100%" width="100%">
<tr><td>

<table cellpadding=0 cellspacing=0 border=0>
<tr>
<td><input type="checkbox" name="wysiwyg_<?=$cname?>_mode_chekbox" onClick="return switchEditMode()" CHECKED><?=$AdminTrnsl["View_HTML"]?></td>
</tr>
</table>
<div id="divToolbarSource"></div></td>
</tr>
<tr height="100%"><td><textarea class="EditorArea" id="txtSource"></textarea></td></tr>
</table>
</td>
</tr></table>
<div id="divTemp" style="VISIBILITY: hidden; OVERFLOW: hidden; POSITION: absolute; WIDTH: 1px; HEIGHT: 1px"></div>
</body>
</html>