<?require_once("prepend.php");?>
<html>
<head><meta HTTP-EQUIV="content-type" CONTENT="text/html; charset=<?=ADMIN_CHARSET?>">
	<meta http-equiv="Expires" content="0">
	<meta http-equiv="Cache-Control" content="must-revalidate, post-check=0, pre-check=0">
	<meta http-equiv="pragma" content="no-cache">
<link rel=stylesheet href="/admin/css.css" type="text/css">
	<title><?=$AdminTrnsl["Image_Library"]?></title>
</head>

<script>
//<!--
var realWidth = null;
var realHeight = null;

var frameSrc = '/admin/imglib.php';
var sel = window.opener.objContent.DOM.selection.createRange() ;

if (window.opener.objContent.DOM.selection.type != 'Text' && sel.length == 1) {
	var img = sel.item(0);
	var imgSrc = img.src.substring(img.src.indexOf('/',8));
}

var isHelp = ('<?=$request->getParameter("for_help")?>' == 'yes');
if (isHelp) {
	frameSrc += '?cdir=_help_images_&for_help=true';
} else if(img) {
	il = imgSrc.substring(0, 7);
	li = imgSrc.lastIndexOf('/')
	if (li == 0 || il != '/imglib') {
		cdir = '';
	} else {
		cdir = imgSrc.substring(8, li);
	}

	frameSrc += '?cdir=' + escape(cdir);
}

function setComboValue(cmb, value) {
	for (var i = 0 ; i < cmb.options.length ; i++) {
		if (cmb.options[i].value == value) {
			cmb.options[i].selected = true ;
			break;
		}
	}
}

function setImage(img, w, h) {
	var relUrl = '/imglib' + img.src.substring(img.src.indexOf('/', 8)+18);
	imagePropsForm.src.value = relUrl;

	realWidth = img.width;
	realHeight = img.height;
	imagePropsForm.W.value = w;
	imagePropsForm.H.value = h;

	//imagePropsForm.border.value = '0';
	//imagePropsForm.vspace.value = '0';
	//imagePropsForm.hspace.value = '0';
	//setComboValue(imagePropsForm.align, '');
}

function myescape(str) {
	return str.replace(/"/, '&quote;');
}
//"
function insertImg() {
	var f = imagePropsForm;
	if (f.src.value != '') {
		window.opener.wswReplaceSelectedHTML(
			'<IMG src="' + f.src.value + '"' +
			(f.border.value != '' ? ' border='+ f.border.value : '') +
			(f.W.value != '' ? ' width=' + f.W.value : '') +
			(f.H.value != '' ? ' height=' + f.H.value : '') +
			(f.alt.value != '' ? ' alt="' + myescape(f.alt.value) + '"' : '') +
			(f.vspace.value != '' ? ' vspace=' + f.vspace.value : '') +
			(f.hspace.value != '' ? ' hspace=' + f.hspace.value : '') +
			(f.align.options[f.align.selectedIndex].value != '' ? ' align=' + f.align.options[f.align.selectedIndex].value : '') +
			'>');
		window.close();
	} else {
		alert('Please select image.');
	}
}

function preparePage() {
	if (img) {
		imagePropsForm.src.value = imgSrc;
		imagePropsForm.alt.value = img.alt;
		realWidth = img.width;
		realHeight = img.height;
		imagePropsForm.W.value = img.width;
		imagePropsForm.H.value = img.height;
		imagePropsForm.border.value = img.border;
		imagePropsForm.vspace.value = img.vspace;
		imagePropsForm.hspace.value = img.hspace;
		setComboValue(imagePropsForm.align, img.align);
	}
}

function sizeChanged(axe) {
	var cprops = imagePropsForm.cproportions;
	var W = imagePropsForm.W
	var H = imagePropsForm.H
	if (realWidth && cprops.checked) {
		if ((axe) == "Width") {
			if (W.value != "") {
				if (! isNaN(W.value))
					H.value = Math.round( realHeight * ( W.value  / realWidth ) ) ;
			} else
				H.value = "" ;
		} else
			if (H.value != "") {
				if (! isNaN(H.value))
					W.value  = Math.round( realWidth  * ( H.value / realHeight ) ) ;
			} else
				W.value = "" ;
	}
}

function onCproportionsClick() {
	sizeChanged("Width") ;
}

function resetSize() {
	if (realWidth) {
		imagePropsForm.W.value = realWidth;
		imagePropsForm.H.value = realHeight;
	}
}

//-->
</script>

<body leftmargin=0 topmargin=0 marginwidth=0 marginheight=0 class=main onLoad="preparePage()">

<script>
document.write('<iframe src="'+frameSrc+'" width=100% height=60%></iframe>');
</script>

<table width=100% border=0 cellpadding=0 cellspacing=0><td id=pad18 style="padding-top:18px;padding-bottom:18px">

<table width=100% border=0 cellpadding=0 cellspacing=0 class=backtab>
<tr class=header id=back3><td colspan=3>&nbsp;&nbsp;</td></tr>
<form name="imagePropsForm">
<tr valign=top>
<td class=td2 width=50% style="padding:0px; border:0px">

<table width=100% border=0 cellpadding=0 cellspacing=0 class=backtab>
<tr id=back3>
<td nowrap align=right><b><?=$AdminTrnsl["ImgLibImgSrc"]?>:</b></td>
<td><input type="text" name="src" value="" readonly style="width:150px; border:0;background-color:transparent;margin:4px; font-size:10px"></td>
</tr>
<tr id=back3>
<td nowrap align=right><b><?=$AdminTrnsl["ImgLibImgAlt"]?>:</b></td>
<td><input type="text" name="alt" value="" style="width:150px"></td>
</tr>
<tr id=back3>
<td nowrap align=right><b><?=$AdminTrnsl["ImgLibImgAlign"]?>:</b></td>
<td height=35><select name="align" style="width:150px">
<option value="" selected></option>
<option value="left"><?=$AdminTrnsl["ImgLibImgLeft"]?></option>
<option value="absBottom"><?=$AdminTrnsl["ImgLibImgAbsBottom"]?></option>
<option value="absMiddle"><?=$AdminTrnsl["ImgLibImgAbsMiddle"]?></option>
<option value="baseline"><?=$AdminTrnsl["ImgLibImgBaseline"]?></option>
<option value="bottom"><?=$AdminTrnsl["ImgLibImgBottom"]?></option>
<option value="middle"><?=$AdminTrnsl["ImgLibImgMiddle"]?></option>
<option value="right"><?=$AdminTrnsl["ImgLibImgRight"]?></option>
<option value="textTop"><?=$AdminTrnsl["ImgLibImgTextTop"]?></option>
<option value="top"><?=$AdminTrnsl["ImgLibImgTop"]?></option>
</select></td>
</tr>
</table>

</td>
<td width=50% style="padding:0px; border:0px; border-left:1 solid #bcbcbc;">

<table width=100% border=0 cellpadding=0 cellspacing=0 class=backtab>
<tr id=back3>
<td align=right><b>W:</b></td>
<td><input type="text" name="W" value="" style="width:40px" onkeyup="sizeChanged('Width');"></td>
<td align=right><b>H:</b></td>
<td><input type="text" name="H" value="" style="width:40px" onkeyup="sizeChanged('Height');"></td>
<td><input type="checkbox" name="cproportions" value="1" checked onClick="onCproportionsClick()"></td>
<td nowrap><?=$AdminTrnsl["ImgLibImgConstraintproportions"]?></td>
</tr>
<tr id=back3>
<td nowrap align=right><b><?=$AdminTrnsl["ImgLibImgVspace"]?>:</b></td>
<td><input type="text" name="vspace" value="0" style="width:40px"></td>
<td nowrap align=right><b><?=$AdminTrnsl["ImgLibImgHspace"]?>:</b></td>
<td><input type="text" name="hspace" value="0" style="width:40px"></td>
<td colspan=2><a href="javascript:resetSize()"><?=$AdminTrnsl["ImgLibImgResetsize"]?></a></td>
</tr>
<tr id=back3>
<td nowrap align=right><b><?=$AdminTrnsl["ImgLibImgBorder"]?>:</b></td>
<td><input type="text" name="border" value="0" style="width:40px"></td>
<td colspan=4>&nbsp;</td>
</tr>
</table>

</td></tr>
<tr id=back3 align=center>
<td colspan=3 id=pad10><input type=button value="<?=$AdminTrnsl["ImgLibImgInsert"]?>" onClick="insertImg()" class=button5>&nbsp;<input type=reset value="<?=$AdminTrnsl["ImgLibImgClear"]?>" class=button5>&nbsp;<input type=button value="<?=$AdminTrnsl["ImgLibImgCancel"]?>" onClick="window.close()" class=button5></td>
</tr>
</form>
</table>

</td></table>

</body>
</html>
