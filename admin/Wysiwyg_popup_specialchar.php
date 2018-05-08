<?require_once("prepend.php");?>
<HTML>
	<HEAD>
<meta HTTP-EQUIV="content-type" CONTENT="text/html; charset=<?=ADMIN_CHARSET?>">
<meta http-equiv="Expires" content="0">
<meta http-equiv="Cache-Control" content="must-revalidate, post-check=0, pre-check=0">
<meta http-equiv="pragma" content="no-cache">
<link rel=StyleSheet href=/admin/css.css type=text/css>
<style type="text/css">
.Active 	{cursor: hand; background-color: #ffffcc; text-align: center; }
/*.MainTable 	{border-right: #e8e8e8 5px solid; border-top: #e8e8e8 5px solid; border-left: #e8e8e8 5px solid; border-bottom: #e8e8e8 5px solid; }*/
.Sample td	{border: 1px solid #bcbcbc; font-size:24px;}
.Empty 		{border-right: 1px solid; border-top: 1px solid; border-left: 1px solid; width: 1%; cursor: default; border-bottom: 1px solid;}
.SpecTab td	{border-bottom:1 solid #bcbcbc; border-left:1 solid #bcbcbc; padding:5 5 5 5}
</style>
<script language="javascript">
<!--
document.write("<title><?=$AdminTrnsl["WSWInsertSpecialChar"]?></title>") ;

var oSample ;

function insertChar(charValue) {
	window.returnValue = charValue ;
	window.close();
}

function over(td) {
	oSample.innerHTML = td.innerHTML ;
	td.className = 'Active' ;
}

function out(td) {
	oSample.innerHTML = "&nbsp;" ;
	td.className = 'Disactive' ;
}

function CloseWindow() {
	window.returnValue = null ;
	window.close() ;
}

//-->
			</script>
</HEAD>
<BODY topmargin="0" rightmargin="0" bottommargin="0" leftmargin="0">
<table width=100% height=100% border=0 cellpadding=0 cellspacing=0><td align=center valign=middle>

<table border=0 cellpadding=0 cellspacing=18 width=100%>
<tr>
<td rowspan=2 width=100%>

<table width=100% border=0 cellpadding=0 cellspacing=0 class=SpecTab>
<script language="javascript">
<!--
var aChars = ["!","&quot;","#","$","%","&","\\'","(",")","*","+","-",".","/","0","1","2","3","4","5","6","7","8","9",":",";","&lt;","=","&gt;","?","@","A","B","C","D","E","F","G","H","I","J","K","L","M","N","O","P","Q","R","S","T","U","V","W","X","Y","Z","[","]","^","_","`","a","b","c","d","e","f","g","h","i","j","k","l","m","n","o","p","q","r","s","t","u","v","w","x","y","z","{","|","}","~","&euro;","ƒ","„","…","†","‡","ˆ","\‰","Š","‹","Œ","&lsquo;","&rsquo;","&rsquo;","&ldquo;","&rdquo;","•","&ndash;","&mdash;","˜","™","š","›","œ","Ÿ","&iexcl;","&cent;","&pound;","&pound;","&curren;","&yen;","&brvbar;","&sect;","&uml;","&copy;","&ordf;","&laquo;","&not;","­","&reg;","&macr;","&deg;","&plusmn;","&sup2;","&sup3;","&acute;","&micro;","&para;","&middot;","&cedil;","&sup1;","&ordm;","&raquo;","&frac14;","&frac12;","&frac34;","&iquest;","&Agrave;","&Aacute;","&Acirc;","&Atilde;","&Auml;","&Aring;","&AElig;","&Ccedil;","&Egrave;","&Eacute;","&Ecirc;","&Euml;","&Igrave;","&Iacute;","&Icirc;","&Iuml;","&ETH;","&Ntilde;","&Ograve;","&Oacute;","&Ocirc;","&Otilde;","&Ouml;","&times;","&Oslash;","&Ugrave;","&Uacute;","&Ucirc;","&Uuml;","&Yacute;","&THORN;","&szlig;","&agrave;","&aacute;","&acirc;","&atilde;","&auml;","&aring;","&aelig;","&ccedil;","&egrave;","&eacute;","&ecirc;","&euml;","&igrave;","&iacute;","&icirc;","&iuml;","&eth;","&ntilde;","&ograve;","&oacute;","&ocirc;","&otilde;","&ouml;","&divide;","&oslash;","&ugrave;","&uacute;","&ucirc;","&uuml;","&uuml;","&yacute;","&thorn;","&yuml;"] ;

var cols = 20 ;

var i = 0 ;
while (i < aChars.length)
{
	document.write("<tr id=back2>") ;
	for(var j = 0 ; j < cols ; j++) 
	{
		if (aChars[i])
		{
			document.write('<td align=center width=5% onclick="insertChar(\'' + aChars[i].replace(/&/g, "&amp;") + '\')" onmouseover="over(this)" onmouseout="out(this)">') ;
			document.write(aChars[i]) ;
		}
		else
			document.write("<td class=Empty>&nbsp;") ;
		document.write("</td>") ;
		i++ ;
	}
	document.write("</tr>") ;
}
//-->
 		</script>
</table>
</td>
<td valign=top>

<table width=100% border=0 cellpadding=0 cellspacing=0 class=Sample>
<tr id=back3>
<td id=SampleTD width=50 height=50 align=center>&nbsp;</td>
</tr>
</table>

</td>
</tr>
<tr>
<td align=right valign=bottom>
<INPUT type="button" fckLang="DlgBtnCancel" value="<?=$AdminTrnsl["WSWSCharCancel"]?>" onclick="CloseWindow();" class=button5>
</td>
</tr>
</table>

</td></table>
</BODY>
</HTML>
<script language="javascript">
<!--
oSample = document.getElementById("SampleTD") ;
//-->
</script>
