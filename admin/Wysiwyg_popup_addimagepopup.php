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
DECMD_HYPERLINK =				5016
DECMD_UNLINK =					5050

var oDOM = dialogArguments.objContent.DOM ;

function setImage(img, w, h) {
	var relUrl = img.src.substring(img.src.indexOf('/', 8)+18);
	insertImg(relUrl, w, h);
}

function insertImg(sUrl, w, h) {
	if (sUrl == "") {
		dialogArguments.decCommand( DECMD_UNLINK );
	} else {
		dialogArguments.decCommand( DECMD_HYPERLINK, 2, "javascript:void(0);" ) ;
		for (i = 0 ; i < oDOM.links.length ; i++) {
			var link = oDOM.links[i];
			if ( link.href == "javascript:void(0);" ) {
				link.href = "/imglib"+sUrl;
				link.onclick = "openw('/showpic.php?img="+sUrl+"', "+(w+10)+", "+(h+10)+"); return false;";

				link.removeAttribute("title", 0);

				link.removeAttribute("target",0) ;
			}
		}
	}

	cancel() ;
}

function cancel() {
	window.returnValue = null ;
	window.close() ;
}

</script>

<body leftmargin=0 topmargin=0 marginwidth=0 marginheight=0 class=main">
<form name="imagePropsForm">
</form>
<iframe src="/admin/imglib.php" width=100% height=100%></iframe>
</body>
</html>
