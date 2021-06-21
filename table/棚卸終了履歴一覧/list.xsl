<?xml version="1.0" encoding="EUC-JP"?>
<xsl:stylesheet version="1.0" xmlns:xsl="http://www.w3.org/1999/XSL/Transform">
<xsl:output method="html" />

<xsl:template match="/">
  <xsl:apply-templates select="table" />
</xsl:template>

<!-- �����Ⱦ��֤Υƥ����� -->
<xsl:template name="sortText">
  <xsl:param name="field" />
  <xsl:variable name="appendSort" select="/table/data/@sort" />
  <xsl:choose>
    <xsl:when test="$appendSort = concat($field, '_down')"><xsl:text> ��</xsl:text></xsl:when>
    <xsl:when test="$appendSort = concat($field, '_up')"><xsl:text> ��</xsl:text></xsl:when>
  </xsl:choose>
</xsl:template>

<!-- ɽ��������ڤ��ؤ� -->
<xsl:template name="limiter">
  <xsl:param name="limit" />
    <div class="uk-width-2-3">
	    <select name="_limit_{/table/@tableId}" class=" uk-select">
	      <option value="10"><xsl:if test="$limit = '10'"><xsl:attribute name="selected">selected</xsl:attribute></xsl:if>10��</option>
	      <option value="50"><xsl:if test="$limit = '50'"><xsl:attribute name="selected">selected</xsl:attribute></xsl:if>50��</option>
	      <option value="100"><xsl:if test="$limit = '100'"><xsl:attribute name="selected">selected</xsl:attribute></xsl:if>100��</option>
	    </select>
    </div>
    <div class="uk-width-1-3">
        <input type="submit" name="smp-table-submit-button" class="uk-button uk-button-default" value="ɽ��" />
    </div>
</xsl:template>

<!-- �ڡ����㡼 -->
<xsl:template match="pager">
  <ul class="uk-pagination">
      <xsl:for-each select="page">
        <li>
          <xsl:choose>
            <xsl:when test="@current = 'true'">
              <xsl:attribute name="class">uk-active</xsl:attribute>
              <span><xsl:value-of select="." /></span>
            </xsl:when>
            <xsl:when test="@omit = 'true'">
              <xsl:attribute name="class">uk-disabled</xsl:attribute>
              <span><xsl:value-of select="." /></span>
            </xsl:when>
            <xsl:otherwise>
              <xsl:attribute name="class"></xsl:attribute>
              <a href="{@url}"><xsl:value-of select="." /></a>
            </xsl:otherwise>
          </xsl:choose>
        </li>
      </xsl:for-each>
  </ul>
</xsl:template>


<!-- �ǡ�����ʬ -->
<xsl:template match="/table">
  <script type="text/javascript" src="{@jsPath}" charset="{@jsEncode}"></script>
  <form method="post" action="{@action}">
    $hidden:table:extension$
    <div class="">
        <div class="uk-width-1-3@m">
            <font class="smp-offset-start"><xsl:value-of select="pager/@offset_start" /></font> - <font class="smp-offset-end"><xsl:value-of select="pager/@offset_end" /></font>�� / <font class="smp-count"><xsl:value-of select="data/@total" /></font>��
        </div>
        <div class="uk-width-1-3@m uk-grid">
            <xsl:call-template name="limiter"><xsl:with-param name="limit" select="data/@limit" /></xsl:call-template>
        </div>
    </div>
    <xsl:apply-templates select="pager" />
    <div class="uk-overflow-auto">
    <table class="uk-table uk-table-hover uk-table-middle uk-table-divider uk-text-nowrap">
     <thead>
     	<tr>
        <th>
        	<a href="{/table/fieldList/@idSort}">
            <xsl:text>ID</xsl:text>
            <xsl:call-template name="sortText">
              <xsl:with-param name="field" select="'id'" />
            </xsl:call-template>
          </a>
        </th>
        <th>
        	<a href="{/table/fieldList/field[@title='registrationTime']/@sort}">
            <xsl:text>��Ͽ����</xsl:text>
            <xsl:call-template name="sortText">
              <xsl:with-param name="field" select="/table/fieldList/field[@title='registrationTime']/@code" />
            </xsl:call-template>
          </a>
        </th>
        <th>
        	<a href="{/table/fieldList/field[@title='inventoryTime']/@sort}">
            <xsl:text>ê����λ����</xsl:text>
            <xsl:call-template name="sortText">
              <xsl:with-param name="field" select="/table/fieldList/field[@title='inventoryTime']/@code" />
            </xsl:call-template>
          </a>
        </th>
        <th>
        	<a href="{/table/fieldList/field[@title='itemsNumber']/@sort}">
            <xsl:text>���ܿ�</xsl:text>
            <xsl:call-template name="sortText">
              <xsl:with-param name="field" select="/table/fieldList/field[@title='itemsNumber']/@code" />
            </xsl:call-template>
          </a>
        </th>
        <th>
        	<a href="{/table/fieldList/field[@title='totalAmount']/@sort}">
            <xsl:text>��׶��</xsl:text>
            <xsl:call-template name="sortText">
              <xsl:with-param name="field" select="/table/fieldList/field[@title='totalAmount']/@code" />
            </xsl:call-template>
          </a>
        </th>
        <th>
        	<a href="{/table/fieldList/field[@title='inventoryStatus']/@sort}">
            <xsl:text>���ơ�����</xsl:text>
            <xsl:call-template name="sortText">
              <xsl:with-param name="field" select="/table/fieldList/field[@title='inventoryStatus']/@code" />
            </xsl:call-template>
          </a>
        </th>
        <th class="uk-table-shrink">
            <xsl:text>ê��������</xsl:text>
        </th>
      </tr>
      </thead>
      <tbody>
      <xsl:for-each select="data/record">
        <xsl:variable name="row" select="position() + 3" />
        <xsl:variable name="recordPosition" select="position() + number(/table/pager/@offset_start) - 1" />
        <xsl:variable name="id" select="@id" />
        <tr>
          <td>
              <xsl:value-of select="@id" />
          </td>
          <td>
          	<xsl:value-of select="usr_registrationTime/full_text" />
          </td>
          <td>
          	<xsl:value-of select="usr_inventoryTime/full_text" />
          </td>
          <td>
          	<xsl:value-of select="usr_itemsNumber" />
          </td>
          <td>
          	<xsl:if test="usr_totalAmount != ''">
              ��<xsl:value-of select="format-number(usr_totalAmount, '#,##0.00')" />
            </xsl:if>
          </td>
          <td>
          	<span class="">
            <xsl:attribute name="class">
              <xsl:choose>
                <xsl:when test='usr_inventoryStatus = "ê����"'>
                 uk-label uk-padding-small uk-label-danger uk-padding-remove-vertical
                </xsl:when>
                <xsl:when test='usr_inventoryStatus = "ê����λ"'>
                 uk-label uk-padding-small uk-label-success uk-padding-remove-vertical
                </xsl:when>
                <xsl:otherwise>
                  
                </xsl:otherwise>
              </xsl:choose>
            </xsl:attribute>
            <xsl:value-of select="usr_inventoryStatus" />
          	</span>
          </td>
          <td>
          	<a class="uk-button uk-button-primary" href="{/table/cardList/card[@title='page_263632'][@recordId=$id]}" target="_self">
          		<span>ê��������򳫤�</span>
            </a>
          </td>
        </tr>
      </xsl:for-each>
      </tbody>
    </table>
    </div>
    <xsl:apply-templates select="pager" />
  </form>
</xsl:template>

</xsl:stylesheet>
