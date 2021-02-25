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
    <select name="_limit_{/table/@tableId}">
      <option value="10"><xsl:if test="$limit = '10'"><xsl:attribute name="selected">selected</xsl:attribute></xsl:if>10��</option>
      <option value="50"><xsl:if test="$limit = '50'"><xsl:attribute name="selected">selected</xsl:attribute></xsl:if>50��</option>
      <option value="100"><xsl:if test="$limit = '100'"><xsl:attribute name="selected">selected</xsl:attribute></xsl:if>100��</option>
    </select>
  <input type="submit" name="smp-table-submit-button" value="ɽ��" />
</xsl:template>

<!-- �ڡ����㡼 -->
<xsl:template match="pager">
  <table class="smp-pager">
    <tr>
      <xsl:for-each select="page">
        <td>
          <xsl:choose>
            <xsl:when test="@current = 'true'">
              <xsl:attribute name="class">smp-page smp-current-page</xsl:attribute>
              <xsl:value-of select="." />
            </xsl:when>
            <xsl:when test="@omit = 'true'">
              <xsl:attribute name="class">smp-page smp-page-space</xsl:attribute>
              <xsl:value-of select="." />
            </xsl:when>
            <xsl:otherwise>
              <xsl:attribute name="class">smp-page</xsl:attribute>
              <a href="{@url}"><xsl:value-of select="." /></a>
            </xsl:otherwise>
          </xsl:choose>
        </td>
      </xsl:for-each>
    </tr>
  </table>
</xsl:template>

<!-- �ƥ����ȥ��ꥢ���β��Ԥ�ȿ�Ǥ��뤿���XSL�ƥ�ץ졼�� -->
<xsl:template name="textareaHTML">
  <xsl:param name="content" />
  <!-- ���Ԥ��ݻ������� -->
  <xsl:variable name="match">\n</xsl:variable>
  <xsl:choose>
    <xsl:when test="contains($content,$match)">
      <xsl:value-of select="substring-before($content,$match)" />
      <br />
      <!-- �Ĥ���Ѵ� -->
      <xsl:call-template name="textareaHTML">
        <xsl:with-param name="content" select="substring-after($content,$match)"/>
      </xsl:call-template>
    </xsl:when>
    <xsl:otherwise>
      <xsl:value-of select="$content" />
    </xsl:otherwise>
  </xsl:choose>
</xsl:template>

<xsl:template name="textareaContent">
  <xsl:param name="content" />
  <!-- ���Ԥ��ݻ������� -->
  <xsl:variable name="match">\n</xsl:variable>
  <xsl:choose>
    <xsl:when test="contains($content,$match)">
      <xsl:value-of select="substring-before($content,$match)" />
<!-- ���Ԥ�ɽ�� -->
<xsl:text>
</xsl:text>
      <xsl:call-template name="textareaContent">
        <xsl:with-param name="content" select="substring-after($content,$match)"/>
      </xsl:call-template>
    </xsl:when>
    <xsl:otherwise>
      <xsl:value-of select="$content" />
    </xsl:otherwise>
  </xsl:choose>
</xsl:template>

<!-- �ǡ�����ʬ -->
<xsl:template match="/table">
  <script type="text/javascript" src="{@jsPath}" charset="{@jsEncode}"></script>
  <form method="post" action="{@action}">
    $hidden:table:extension$
    <table id="smp-table-{@tableId}" class="smp-table" cellspacing="0" cellpadding="0" style="border-collapse:collapse;">
      <tr class="smp-row-1 smp-row-normal" style="height:20;">
        <td class="smp-cell-1-1 smp-cell smp-cell-row-1 smp-cell-col-1"></td>
        <td class="smp-cell-1-2 smp-cell smp-cell-row-1 smp-cell-col-2"></td>
        <td class="smp-cell-1-3 smp-cell smp-cell-row-1 smp-cell-col-3"></td>
        <td class="smp-cell-1-4 smp-cell smp-cell-row-1 smp-cell-col-4" style="font-size:10pt;color:#444444;" align="right" colspan="2">
          <font class="smp-offset-start"><xsl:value-of select="pager/@offset_start" /></font> - <font class="smp-offset-end"><xsl:value-of select="pager/@offset_end" /></font>�� / <font class="smp-count"><xsl:value-of select="data/@total" /></font>��<br /><xsl:call-template name="limiter"><xsl:with-param name="limit" select="data/@limit" /></xsl:call-template>
        </td>
      </tr>
      <tr class="smp-row-2 smp-row-normal" style="height:20;">
        <td class="smp-cell-2-1 smp-cell smp-cell-row-2 smp-cell-col-1" style="padding:0px;font-size:10pt;font-weight:bold;" align="left" colspan="2">
          <font class="smp-title">�ƥʥ�Ȱ���</font>
        </td>
        <td class="smp-cell-2-3 smp-cell smp-cell-row-2 smp-cell-col-3" style="font-size:10pt;" align="right" colspan="3">
          <xsl:apply-templates select="pager" />
        </td>
      </tr>
      <tr class="smp-row-3 smp-row-sort" style="height:20;">
        <td class="smp-cell-sort smp-cell-3-1 smp-cell smp-cell-row-3 smp-cell-col-1" style="border:1px solid #999999;padding:5px;font-size:10pt;font-weight:bold;color:#444444;background-color:#DCDCDE;" align="center">
          <a href="{/table/fieldList/@idSort}">
            <xsl:text>id</xsl:text>
            <xsl:call-template name="sortText">
              <xsl:with-param name="field" select="'id'" />
            </xsl:call-template>
          </a>
        </td>
        <td class="smp-cell-sort smp-cell-3-2 smp-cell smp-cell-row-3 smp-cell-col-2" style="border:1px solid #999999;padding:5px;font-size:10pt;font-weight:bold;color:#444444;background-color:#DCDCDE;" align="center">
          <a href="{/table/fieldList/field[@title='registrationTime']/@sort}">
            <xsl:text>��Ͽ����</xsl:text>
            <xsl:call-template name="sortText">
              <xsl:with-param name="field" select="'f002349673'" />
            </xsl:call-template>
          </a>
        </td>
        <td class="smp-cell-sort smp-cell-3-3 smp-cell smp-cell-row-3 smp-cell-col-3" style="border:1px solid #999999;padding:5px;font-size:10pt;font-weight:bold;color:#444444;background-color:#DCDCDE;" align="center">
          <a href="{/table/fieldList/field[@title='tenantId']/@sort}">
            <xsl:text>�ƥʥ��ID</xsl:text>
            <xsl:call-template name="sortText">
              <xsl:with-param name="field" select="'f002348836'" />
            </xsl:call-template>
          </a>
        </td>
        <td class="smp-cell-sort smp-cell-3-4 smp-cell smp-cell-row-3 smp-cell-col-4" style="border:1px solid #999999;padding:5px;font-size:10pt;font-weight:bold;color:#444444;background-color:#DCDCDE;" align="center">
          <a href="{/table/fieldList/field[@title='tenantName']/@sort}">
            <xsl:text>�ƥʥ��̾</xsl:text>
            <xsl:call-template name="sortText">
              <xsl:with-param name="field" select="'f002349674'" />
            </xsl:call-template>
          </a>
        </td>
        <td class="smp-cell-sort smp-cell-3-5 smp-cell smp-cell-row-3 smp-cell-col-5" style="border:1px solid #999999;padding:5px;font-size:10pt;font-weight:bold;color:#444444;background-color:#DCDCDE;" align="center">
          <a href="{/table/fieldList/field[@title='note']/@sort}">
            <xsl:text>����</xsl:text>
            <xsl:call-template name="sortText">
              <xsl:with-param name="field" select="'f002349675'" />
            </xsl:call-template>
          </a>
        </td>
      </tr>
      <xsl:for-each select="data/record">
        <xsl:variable name="row" select="position() + 3" />
        <xsl:variable name="recordPosition" select="position() + number(/table/pager/@offset_start) - 1" />
        <xsl:variable name="id" select="@id" />
        <tr style="height:20;">
          <xsl:attribute name="class">
            <xsl:text>smp-row-</xsl:text><xsl:value-of select="$row" />
            <xsl:text> smp-row-data</xsl:text>
            <xsl:if test="string(./*/@hasError) = 'true'">
              <xsl:text> smp-valid-err-row</xsl:text>
            </xsl:if>
          </xsl:attribute>
          <td class="smp-cell-data smp-cell-{$row}-1 smp-cell smp-cell-row-{$row} smp-cell-col-1" align="center">
            <xsl:attribute name="style">
              <xsl:choose>
                <xsl:when test="position() mod 2 = 1">
                  border-width:0px 1px;border-style:solid;border-color:#999999;padding:5px;font-size:10pt;color:#444444;
                </xsl:when>
                <xsl:otherwise>
                  border-width:0px 1px;border-style:solid;border-color:#999999;padding:5px;font-size:10pt;color:#444444;background-color:#F2F5F8;
                </xsl:otherwise>
              </xsl:choose>
            </xsl:attribute>
            <a class="smp-cell-id" href="{/table/cardList/card[@title='page_263721'][@recordId=$id]}" target="_self">
              <xsl:value-of select="@id" />
            </a>
          </td>
          <td class="smp-cell-data smp-cell-{$row}-2 smp-cell smp-cell-row-{$row} smp-cell-col-2" align="left">
            <xsl:attribute name="style">
              <xsl:choose>
                <xsl:when test="position() mod 2 = 1">
                  border-width:0px 1px;border-style:solid;border-color:#999999;padding:5px;font-size:10pt;color:#444444;
                </xsl:when>
                <xsl:otherwise>
                  border-width:0px 1px;border-style:solid;border-color:#999999;padding:5px;font-size:10pt;color:#444444;background-color:#F2F5F8;
                </xsl:otherwise>
              </xsl:choose>
            </xsl:attribute>
            <xsl:value-of select="usr_registrationTime/full_text" />
          </td>
          <td class="smp-cell-data smp-cell-{$row}-3 smp-cell smp-cell-row-{$row} smp-cell-col-3" align="left">
            <xsl:attribute name="style">
              <xsl:choose>
                <xsl:when test="position() mod 2 = 1">
                  border-width:0px 1px;border-style:solid;border-color:#999999;padding:5px;font-size:10pt;color:#444444;
                </xsl:when>
                <xsl:otherwise>
                  border-width:0px 1px;border-style:solid;border-color:#999999;padding:5px;font-size:10pt;color:#444444;background-color:#F2F5F8;
                </xsl:otherwise>
              </xsl:choose>
            </xsl:attribute>
            <xsl:value-of select="usr_tenantId" />
          </td>
          <td class="smp-cell-data smp-cell-{$row}-4 smp-cell smp-cell-row-{$row} smp-cell-col-4" align="left">
            <xsl:attribute name="style">
              <xsl:choose>
                <xsl:when test="position() mod 2 = 1">
                  border-width:0px 1px;border-style:solid;border-color:#999999;padding:5px;font-size:10pt;color:#444444;
                </xsl:when>
                <xsl:otherwise>
                  border-width:0px 1px;border-style:solid;border-color:#999999;padding:5px;font-size:10pt;color:#444444;background-color:#F2F5F8;
                </xsl:otherwise>
              </xsl:choose>
            </xsl:attribute>
            <xsl:value-of select="usr_tenantName" />
          </td>
          <td class="smp-cell-data smp-cell-{$row}-5 smp-cell smp-cell-row-{$row} smp-cell-col-5" align="left">
            <xsl:attribute name="style">
              <xsl:choose>
                <xsl:when test="position() mod 2 = 1">
                  border-width:0px 1px;border-style:solid;border-color:#999999;padding:5px;font-size:10pt;color:#444444;
                </xsl:when>
                <xsl:otherwise>
                  border-width:0px 1px;border-style:solid;border-color:#999999;padding:5px;font-size:10pt;color:#444444;background-color:#F2F5F8;
                </xsl:otherwise>
              </xsl:choose>
            </xsl:attribute>
            <xsl:call-template name="textareaHTML"><xsl:with-param name="content" select="usr_note" /></xsl:call-template>
          </td>
        </tr>
      </xsl:for-each>
      <tr class="smp-row-14 smp-row-normal" style="height:10;">
        <td class="smp-cell-14-1 smp-cell smp-cell-row-14 smp-cell-col-1" style="font-size:10pt;border:1px solid #999999;background-color:#DCDCDE;" align="left" colspan="5">
          
        </td>
      </tr>
      <tr class="smp-row-15 smp-row-normal" style="height:20;">
        <td class="smp-cell-15-1 smp-cell smp-cell-row-15 smp-cell-col-1" style="font-size:10pt;" align="right" colspan="5">
          <xsl:apply-templates select="pager" />
        </td>
      </tr>
    </table>
  </form>
</xsl:template>

</xsl:stylesheet>
