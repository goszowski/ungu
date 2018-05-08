<?
include("../prepend.php");
echo "<?xml version=\"1.0\" encoding=\"".ADMIN_CHARSET."\"?>";
?>
<deeptreeconfig>
  <TopXMLSrc>/admin/xmlTreeGenerator.php?nodeid=-1<?if ($request->getParameter("selected_id")!=null) {?>&amp;selected_id=<?=$request->getParameter("selected_id")?><?}?></TopXMLSrc>
  <TreeLabel>Test tree</TreeLabel>
  <StartPage>/</StartPage>
  <ErrorPage>http://www.microsoft.com</ErrorPage>
  <ContentTarget>fraContent</ContentTarget>
  <TreeId>cmtree</TreeId>
  <TreeRootPath>/admin/main.php</TreeRootPath>
  <Locale>en-us</Locale>
  <LocaleTextDirection>LTR</LocaleTextDirection>
</deeptreeconfig>
