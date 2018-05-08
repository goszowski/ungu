<?
include("prepend.php");
echo "<?xml version=\"1.0\" encoding=\"".ADMIN_CHARSET."\"?>";
$rn = $request->getParameter("rn");
$cs = $request->getParameter("cs");
$d = $request->getParameter("d");
?>
<deeptreeconfig>
  <TopXMLSrc>/admin/linkfieldxmlTreeGenerator.php?&amp;nodepath=<?=$rn?>&amp;allowed_classes=<?=$cs?>&amp;depth=<?=$d?>&amp;root=true</TopXMLSrc>
  <TreeLabel>Test tree</TreeLabel>
  <StartPage>/</StartPage>
  <ErrorPage>http://www.microsoft.com</ErrorPage>
  <ContentTarget>fraContent</ContentTarget>
  <TreeId>cmtree</TreeId>
  <TreeRootPath>/admin/main.php</TreeRootPath>
  <Locale>en-us</Locale>
  <LocaleTextDirection>LTR</LocaleTextDirection>
</deeptreeconfig>
