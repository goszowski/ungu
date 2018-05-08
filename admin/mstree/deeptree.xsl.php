<?
include("../prepend.php");
echo "<?xml version=\"1.0\" encoding=\"".ADMIN_CHARSET."\"?>";
?>

<xsl:stylesheet xmlns:xsl="http://www.w3.org/TR/WD-xsl">

<xsl:template match="/">
	<xsl:apply-templates />
</xsl:template>

<xsl:template match="/Tree">
  <xsl:apply-templates/>
</xsl:template>

<xsl:template match="Separator">
  <div class="Separator"><div class="Line"><img src="/admin/_img/s.gif" width="1" height="1" /></div></div>
</xsl:template>

<xsl:template match="PagingArrow">
          <img>
				<xsl:attribute name="src"><xsl:value-of select="@imgSrc" /></xsl:attribute>
				<xsl:attribute name="border">0</xsl:attribute>
				<xsl:attribute name="width">84</xsl:attribute>
				<xsl:attribute name="height">9</xsl:attribute>
          <xsl:attribute name="onClick"><xsl:value-of select="@onClick" /></xsl:attribute>
          </img>
</xsl:template>


<xsl:template match="TreeNode">
  <div class="clsItem" type="leaf">
    <xsl:attribute name="id">div<xsl:value-of select="@NodeId" /></xsl:attribute>
    <xsl:choose>
      <xsl:when test="@NodeImgSrc">
        <img type="img"><xsl:attribute name="src"><xsl:value-of select="@NodeImgSrc" /></xsl:attribute></img></xsl:when>
      <xsl:otherwise>
        <span class="clsSpace" type="img">
        <xsl:attribute name="id">img<xsl:value-of select="@NodeId" /></xsl:attribute>
        <span class="clsLeaf">.</span></span></xsl:otherwise>
    </xsl:choose><span class="clsLabel" type="label">
      <xsl:attribute name="id"><xsl:value-of select="@NodeId" /></xsl:attribute>
      <xsl:attribute name="title"><xsl:value-of select="@Title" /></xsl:attribute>
      <xsl:choose>
        <xsl:when test="@Href">
          <a>
            <xsl:choose>
                <xsl:when test="@Target">
                    <xsl:attribute name="target"><xsl:value-of select="@Target" /></xsl:attribute>
                </xsl:when>
                <xsl:otherwise></xsl:otherwise>
            </xsl:choose>
            <xsl:attribute name="tabindex">-1</xsl:attribute>
            <xsl:attribute name="href"><xsl:value-of select="@Href" /></xsl:attribute>
            <xsl:value-of select="@Title" />
          </a>
        </xsl:when>
        <xsl:otherwise>
          <xsl:value-of select="@Title" />
        </xsl:otherwise>
      </xsl:choose>
    </span>
  </div>
</xsl:template>

<xsl:template match="TreeNode[* or @NodeXmlSrc]">
  <div class="clsItem" type="parent">
  <xsl:attribute name="id">div<xsl:value-of select="@NodeId" /></xsl:attribute>
    <span class="clsSpace" type="img">
    <xsl:attribute name="id">img<xsl:value-of select="@NodeId" /></xsl:attribute>
    <span class="clsCollapse">+</span></span><span class="clsLabel" type="label">
      <xsl:attribute name="xmlsrc"><xsl:value-of select="@NodeXmlSrc" /></xsl:attribute>
      <xsl:attribute name="id"><xsl:value-of select="@NodeId" /></xsl:attribute>
      <xsl:attribute name="title"><xsl:value-of select="@Title" /></xsl:attribute>
      <xsl:choose>
        <xsl:when test="@Href">
          <a>
            <xsl:choose>
                <xsl:when test="@Target">
                    <xsl:attribute name="target"><xsl:value-of select="@Target" /></xsl:attribute>
                </xsl:when>
                <xsl:otherwise></xsl:otherwise>
            </xsl:choose>
            <xsl:attribute name="tabindex">-1</xsl:attribute>
            <xsl:attribute name="href"><xsl:value-of select="@Href" /></xsl:attribute>
            <xsl:value-of select="@Title" />
          </a>
        </xsl:when>
        <xsl:otherwise>
          <xsl:value-of select="@Title" />
        </xsl:otherwise>
      </xsl:choose>
    </span>
    <div class="hide" type="container">
    <xsl:attribute name="id">cnt<xsl:value-of select="@NodeId" /></xsl:attribute>
    <xsl:apply-templates /></div>
  </div>
</xsl:template>

<xsl:template match="TreeNode[@expanded]">
  <div class="clsItem" type="parent" state="shown">
  <xsl:attribute name="id">div<xsl:value-of select="@NodeId" /></xsl:attribute>
    <span class="clsSpace" type="img">
    <xsl:attribute name="id">img<xsl:value-of select="@NodeId" /></xsl:attribute>
    <span class="clsExpand">-</span></span><span class="clsLabel" type="label">
      <xsl:attribute name="xmlsrc"><xsl:value-of select="@NodeXmlSrc" /></xsl:attribute>
      <xsl:attribute name="id"><xsl:value-of select="@NodeId" /></xsl:attribute>
      <xsl:attribute name="title"><xsl:value-of select="@Title" /></xsl:attribute>
      <xsl:choose>
        <xsl:when test="@Href">
          <a>
            <xsl:choose>
                <xsl:when test="@Target">
                    <xsl:attribute name="target"><xsl:value-of select="@Target" /></xsl:attribute>
                </xsl:when>
                <xsl:otherwise></xsl:otherwise>
            </xsl:choose>
            <xsl:attribute name="tabindex">-1</xsl:attribute>
            <xsl:attribute name="href"><xsl:value-of select="@Href" /></xsl:attribute>
            <xsl:value-of select="@Title" />
          </a>
        </xsl:when>
        <xsl:otherwise>
          <xsl:value-of select="@Title" />
        </xsl:otherwise>
      </xsl:choose>
    </span>
    <div class="shown" type="container">
    <xsl:attribute name="id">cnt<xsl:value-of select="@NodeId" /></xsl:attribute>
    <xsl:apply-templates /></div>
  </div>
</xsl:template>

<xsl:template match="@Target">
  <xsl:copy><xsl:value-of /></xsl:copy>
</xsl:template>

<xsl:template match="/TreeNode">
  <xsl:apply-templates/>
</xsl:template>
<xsl:template match="TreeNode/Tree">
  <xsl:apply-templates />
</xsl:template>
<xsl:template match="/Separator">
  <xsl:apply-templates/>
</xsl:template>

</xsl:stylesheet>