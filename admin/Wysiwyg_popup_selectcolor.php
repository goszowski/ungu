<?require_once("prepend.php");?>
<HTML>
	<HEAD>
<meta HTTP-EQUIV="content-type" CONTENT="text/html; charset=<?=ADMIN_CHARSET?>">
	<meta http-equiv="Expires" content="0">
	<meta http-equiv="Cache-Control" content="must-revalidate, post-check=0, pre-check=0">
	<meta http-equiv="pragma" content="no-cache">
		<LINK href="/admin/css.css" type="text/css" rel="stylesheet">
			<STYLE TYPE="text/css">
 .selcolor  {cursor:hand}
 td     {height:15px; width:15px;}
			</STYLE>
			<SCRIPT LANGUAGE="JavaScript" FOR="ColorTable" EVENT="onclick">
  selhicolor.style.backgroundColor = event.srcElement.title;
  selcolor.value = event.srcElement.title;
			</SCRIPT>
			<SCRIPT LANGUAGE="JavaScript" FOR="ColorTable" EVENT="onmouseover">
  //hicolortext.innerText = event.srcElement.title;
  //hicolor.style.backgroundColor = event.srcElement.title;
			</SCRIPT>
			<SCRIPT LANGUAGE="JavaScript" FOR="ColorTable" EVENT="onmouseout">
  //hicolortext.innerText = "";
  //hicolor.style.backgroundColor = "";
			</SCRIPT>
			<SCRIPT LANGUAGE="JavaScript" FOR="btnOK" EVENT="onclick">
  window.returnValue = selcolor.value;
  window.close();
			</SCRIPT>
			<SCRIPT LANGUAGE="JavaScript" FOR="btnClear" EVENT="onclick">
  selhicolor.style.backgroundColor = '';
  //selhicolortext.innerText='';
  selcolor.value='';
			</SCRIPT>
			<SCRIPT LANGUAGE="JavaScript" FOR="selcolor" EVENT="onpropertychange">
  try{selhicolor.style.backgroundColor = selcolor.value;}
  catch(e) {}
			</SCRIPT>
			<title><?=$AdminTrnsl["WSWSelectColor"]?></title>
	</HEAD>
	<body class=main leftmargin="7" topmargin="7">
		<table border="0" cellspacing="0" cellpadding="0">
			<tr>
				<td valign="top" rowspan=2 align="left" nowrap style="font-family:Arial; font-size:11px;">
					<table ID="ColorTable" border="0" cellspacing="0" cellpadding="0" width="200" class="selcolor">
						<tr>
							<td bgcolor="#000000" title="#000000"></td>
							<td bgcolor="#003300" title="#003300"></td>
							<td bgcolor="#006600" title="#006600"></td>
							<td bgcolor="#009900" title="#009900"></td>
							<td bgcolor="#00CC00" title="#00CC00"></td>
							<td bgcolor="#00FF00" title="#00FF00"></td>
							<td bgcolor="#660000" title="#660000"></td>
							<td bgcolor="#663300" title="#663300"></td>
							<td bgcolor="#666600" title="#666600"></td>
							<td bgcolor="#669900" title="#669900"></td>
							<td bgcolor="#66CC00" title="#66CC00"></td>
							<td bgcolor="#66FF00" title="#66FF00"></td>
						</tr>
						<tr>
							<td bgcolor="#000033" title="#000033"></td>
							<td bgcolor="#003333" title="#003333"></td>
							<td bgcolor="#006633" title="#006633"></td>
							<td bgcolor="#009933" title="#009933"></td>
							<td bgcolor="#00CC33" title="#00CC33"></td>
							<td bgcolor="#00FF33" title="#00FF33"></td>
							<td bgcolor="#660033" title="#660033"></td>
							<td bgcolor="#663333" title="#663333"></td>
							<td bgcolor="#666633" title="#666633"></td>
							<td bgcolor="#669933" title="#669933"></td>
							<td bgcolor="#66CC33" title="#66CC33"></td>
							<td bgcolor="#66FF33" title="#66FF33"></td>
						</tr>
						<tr>
							<td bgcolor="#000066" title="#000066"></td>
							<td bgcolor="#003366" title="#003366"></td>
							<td bgcolor="#006666" title="#006666"></td>
							<td bgcolor="#009966" title="#009966"></td>
							<td bgcolor="#00CC66" title="#00CC66"></td>
							<td bgcolor="#00FF66" title="#00FF66"></td>
							<td bgcolor="#660066" title="#660066"></td>
							<td bgcolor="#663366" title="#663366"></td>
							<td bgcolor="#666666" title="#666666"></td>
							<td bgcolor="#669966" title="#669966"></td>
							<td bgcolor="#66CC66" title="#66CC66"></td>
							<td bgcolor="#66FF66" title="#66FF66"></td>
						</tr>
						<tr>
							<td bgcolor="#000099" title="#000099"></td>
							<td bgcolor="#003399" title="#003399"></td>
							<td bgcolor="#006699" title="#006699"></td>
							<td bgcolor="#009999" title="#009999"></td>
							<td bgcolor="#00CC99" title="#00CC99"></td>
							<td bgcolor="#00FF99" title="#00FF99"></td>
							<td bgcolor="#660099" title="#660099"></td>
							<td bgcolor="#663399" title="#663399"></td>
							<td bgcolor="#666699" title="#666699"></td>
							<td bgcolor="#669999" title="#669999"></td>
							<td bgcolor="#66CC99" title="#66CC99"></td>
							<td bgcolor="#66FF99" title="#66FF99"></td>
						</tr>
						<tr>
							<td bgcolor="#0000CC" title="#0000CC"></td>
							<td bgcolor="#0033CC" title="#0033CC"></td>
							<td bgcolor="#0066CC" title="#0066CC"></td>
							<td bgcolor="#0099CC" title="#0099CC"></td>
							<td bgcolor="#00CCCC" title="#00CCCC"></td>
							<td bgcolor="#00FFCC" title="#00FFCC"></td>
							<td bgcolor="#6600CC" title="#6600CC"></td>
							<td bgcolor="#6633CC" title="#6633CC"></td>
							<td bgcolor="#6666CC" title="#6666CC"></td>
							<td bgcolor="#6699CC" title="#6699CC"></td>
							<td bgcolor="#66CCCC" title="#66CCCC"></td>
							<td bgcolor="#66FFCC" title="#66FFCC"></td>
						</tr>
						<tr>
							<td bgcolor="#0000FF" title="#0000FF"></td>
							<td bgcolor="#0033FF" title="#0033FF"></td>
							<td bgcolor="#0066FF" title="#0066FF"></td>
							<td bgcolor="#0099FF" title="#0099FF"></td>
							<td bgcolor="#00CCFF" title="#00CCFF"></td>
							<td bgcolor="#00FFFF" title="#00FFFF"></td>
							<td bgcolor="#6600FF" title="#6600FF"></td>
							<td bgcolor="#6633FF" title="#6633FF"></td>
							<td bgcolor="#6666FF" title="#6666FF"></td>
							<td bgcolor="#6699FF" title="#6699FF"></td>
							<td bgcolor="#66CCFF" title="#66CCFF"></td>
							<td bgcolor="#66FFFF" title="#66FFFF"></td>
						</tr>
						<tr>
							<td bgcolor="#990000" title="#990000"></td>
							<td bgcolor="#993300" title="#993300"></td>
							<td bgcolor="#996600" title="#996600"></td>
							<td bgcolor="#999900" title="#999900"></td>
							<td bgcolor="#99CC00" title="#99CC00"></td>
							<td bgcolor="#99FF00" title="#99FF00"></td>
							<td bgcolor="#FF0000" title="#FF0000"></td>
							<td bgcolor="#FF3300" title="#FF3300"></td>
							<td bgcolor="#FF6600" title="#FF6600"></td>
							<td bgcolor="#FF9900" title="#FF9900"></td>
							<td bgcolor="#FFCC00" title="#FFCC00"></td>
							<td bgcolor="#FFFF00" title="#FFFF00"></td>
						</tr>
						<tr>
							<td bgcolor="#990033" title="#990033"></td>
							<td bgcolor="#993333" title="#993333"></td>
							<td bgcolor="#996633" title="#996633"></td>
							<td bgcolor="#999933" title="#999933"></td>
							<td bgcolor="#99CC33" title="#99CC33"></td>
							<td bgcolor="#99FF33" title="#99FF33"></td>
							<td bgcolor="#FF0033" title="#FF0033"></td>
							<td bgcolor="#FF3333" title="#FF3333"></td>
							<td bgcolor="#FF6633" title="#FF6633"></td>
							<td bgcolor="#FF9933" title="#FF9933"></td>
							<td bgcolor="#FFCC33" title="#FFCC33"></td>
							<td bgcolor="#FFFF33" title="#FFFF33"></td>
						</tr>
						<tr>
							<td bgcolor="#990066" title="#990066"></td>
							<td bgcolor="#993366" title="#993366"></td>
							<td bgcolor="#996666" title="#996666"></td>
							<td bgcolor="#999966" title="#999966"></td>
							<td bgcolor="#99CC66" title="#99CC66"></td>
							<td bgcolor="#99FF66" title="#99FF66"></td>
							<td bgcolor="#FF0066" title="#FF0066"></td>
							<td bgcolor="#FF3366" title="#FF3366"></td>
							<td bgcolor="#FF6666" title="#FF6666"></td>
							<td bgcolor="#FF9966" title="#FF9966"></td>
							<td bgcolor="#FFCC66" title="#FFCC66"></td>
							<td bgcolor="#FFFF66" title="#FFFF66"></td>
						</tr>
						<tr>
							<td bgcolor="#990099" title="#990099"></td>
							<td bgcolor="#993399" title="#993399"></td>
							<td bgcolor="#996699" title="#996699"></td>
							<td bgcolor="#999999" title="#999999"></td>
							<td bgcolor="#99CC99" title="#99CC99"></td>
							<td bgcolor="#99FF99" title="#99FF99"></td>
							<td bgcolor="#FF0099" title="#FF0099"></td>
							<td bgcolor="#FF3399" title="#FF3399"></td>
							<td bgcolor="#FF6699" title="#FF6699"></td>
							<td bgcolor="#FF9999" title="#FF9999"></td>
							<td bgcolor="#FFCC99" title="#FFCC99"></td>
							<td bgcolor="#FFFF99" title="#FFFF99"></td>
						</tr>
						<tr>
							<td bgcolor="#9900CC" title="#9900CC"></td>
							<td bgcolor="#9933CC" title="#9933CC"></td>
							<td bgcolor="#9966CC" title="#9966CC"></td>
							<td bgcolor="#9999CC" title="#9999CC"></td>
							<td bgcolor="#99CCCC" title="#99CCCC"></td>
							<td bgcolor="#99FFCC" title="#99FFCC"></td>
							<td bgcolor="#FF00CC" title="#FF00CC"></td>
							<td bgcolor="#FF33CC" title="#FF33CC"></td>
							<td bgcolor="#FF66CC" title="#FF66CC"></td>
							<td bgcolor="#FF99CC" title="#FF99CC"></td>
							<td bgcolor="#FFCCCC" title="#FFCCCC"></td>
							<td bgcolor="#FFFFCC" title="#FFFFCC"></td>
						</tr>
						<tr>
							<td bgcolor="#9900FF" title="#9900FF"></td>
							<td bgcolor="#9933FF" title="#9933FF"></td>
							<td bgcolor="#9966FF" title="#9966FF"></td>
							<td bgcolor="#9999FF" title="#9999FF"></td>
							<td bgcolor="#99CCFF" title="#99CCFF"></td>
							<td bgcolor="#99FFFF" title="#99FFFF"></td>
							<td bgcolor="#FF00FF" title="#FF00FF"></td>
							<td bgcolor="#FF33FF" title="#FF33FF"></td>
							<td bgcolor="#FF66FF" title="#FF66FF"></td>
							<td bgcolor="#FF99FF" title="#FF99FF"></td>
							<td bgcolor="#FFCCFF" title="#FFCCFF"></td>
							<td bgcolor="#FFFFFF" title="#FFFFFF"></td>
						</tr>
						<tr>
							<td bgcolor="#000000" title="#000000"></td>
							<td bgcolor="#333333" title="#333333"></td>
							<td bgcolor="#666666" title="#666666"></td>
							<td bgcolor="#999999" title="#999999"></td>
							<td bgcolor="#CCCCCC" title="#CCCCCC"></td>
							<td bgcolor="#FFFFFF" title="#FFFFFF"></td>
							<td bgcolor="#000000" title="#000000"></td>
							<td bgcolor="#000000" title="#000000"></td>
							<td bgcolor="#000000" title="#000000"></td>
							<td bgcolor="#000000" title="#000000"></td>
							<td bgcolor="#000000" title="#000000"></td>
							<td bgcolor="#000000" title="#000000"></td>
						</tr>
					</table>
				</td>
				<td></td>
				<td VALIGN="top" align="left" nowrap style="font-family:Arial; font-size:11px;">
					<table border="0" cellspacing="0" cellpadding="3" height=100%>
					<tr class=back2>
						<td>
							<span fckLang="DlgColorSelected"><?=$AdminTrnsl["WSWSelectColorSelected"]?></span>:
							<div style="height:74px; width:74px; border-width:1px; border-style:solid;" id="selhicolor"></div>
						</td>
					</tr><tr class=back2>
						<td VALIGN="bottom" align="left" nowrap style="font-family:Arial; font-size:11px;" class=back2>
							<INPUT TYPE="text" ID="selcolor" style="width:75px; height:20px; margin-top:0px; margin-bottom:7px;" maxlength="20">
							<input type="button" fckLang="DlgBtnOK" value="OK" name="btnOK" style="width:75px; height:22px; margin-bottom:6px" /><br>
						</td>
					</tr>
					</table>
				</td>
			</tr>
		</table>
	</body>
</HTML>
