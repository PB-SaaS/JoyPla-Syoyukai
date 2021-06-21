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
            <xsl:text>ʧ������</xsl:text>
            <xsl:call-template name="sortText">
              <xsl:with-param name="field" select="/table/fieldList/field[@title='registrationTime']/@code" />
            </xsl:call-template>
          </a>
        </th>
        <th>
          <a href="{/table/fieldList/field[@title='payoutHistoryId']/@sort}">
            <xsl:text>ʧ���ֹ�</xsl:text>
            <xsl:call-template name="sortText">
              <xsl:with-param name="field" select="/table/fieldList/field[@title='payoutHistoryId']/@code" />
            </xsl:call-template>
          </a>
        </th>
        <th>
          <a href="{/table/fieldList/field[@title='sourceDivision']/@sort}">
            <xsl:text>ʧ�и�</xsl:text>
            <xsl:call-template name="sortText">
              <xsl:with-param name="field" select="/table/fieldList/field[@title='sourceDivision']/@code" />
            </xsl:call-template>
          </a>
        </th>
        <th>
          <a href="{/table/fieldList/field[@title='targetDivision']/@sort}">
            <xsl:text>ʧ����</xsl:text>
            <xsl:call-template name="sortText">
              <xsl:with-param name="field" select="/table/fieldList/field[@title='targetDivision']/@code" />
            </xsl:call-template>
          </a>
        </th>
        <th>
          <a href="{/table/fieldList/field[@title='itemsNumber']/@sort}">
            <xsl:text>�ʿ�</xsl:text>
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
        <th class="uk-table-shrink"></th>
      </tr>
      </thead>
      <tbody>
      <xsl:for-each select="data/record">
        <xsl:variable name="row" select="position() + 3" />
        <xsl:variable name="recordPosition" select="position() + number(/table/pager/@offset_start) - 1" />
        <xsl:variable name="id" select="@id" />
        <tr>
          <xsl:attribute name="class">
            <xsl:text>smp-row-</xsl:text><xsl:value-of select="$row" />
            <xsl:text> smp-row-data</xsl:text>
            <xsl:if test="string(./*/@hasError) = 'true'">
              <xsl:text> smp-valid-err-row</xsl:text>
            </xsl:if>
          </xsl:attribute>
          <td>
              <xsl:value-of select="@id" />
          </td>
          <td>
            <xsl:value-of select="usr_registrationTime/full_text" />
          </td>
          <td>
            <xsl:value-of select="usr_payoutHistoryId" />
          </td>
          <td>
            <xsl:value-of select="usr_sourceDivision" />
          </td>
          <td>
            <xsl:value-of select="usr_targetDivision" />
          </td>
          <td>
            <xsl:value-of select="usr_itemsNumber" />
          </td>
          <td>
            <xsl:if test="usr_totalAmount != ''">
              ��<xsl:value-of select="format-number(usr_totalAmount, '#,###.00')" />
            </xsl:if>
          </td>
          <td>
            <a class="uk-button uk-button-primary" href="{/table/cardList/card[@title='page_263580'][@recordId=$id]}" target="_self">
            �ܺ�
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
