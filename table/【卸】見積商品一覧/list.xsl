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
    	<input id="smp-table-reset-button" class="smp-table-button uk-button uk-button-default uk-margin-small-right" type="reset" value="�ꥻ�å�" onclick="SpiralTable.allReset({/table/@tableId});" />
    	<input id="smp-table-delete-button" class="smp-table-button uk-button uk-button-danger uk-margin-small-right" type="submit" name="smp-table-submit-button" value="���" onclick="return SpiralTable.confirmation({/table/@tableId}, this);" />
    	<input id="smp-table-delete-button" class="smp-table-button uk-button uk-button-primary" type="button" name="smp-table-submit-button" value="�ɲ�" onclick="document.reqItemsReg.submit()" />
    <table class="uk-table uk-text-nowrap uk-table-divider">
      <thead>
	<tr>
        <th>
          <input type="checkbox" onclick="SpiralTable.allCheck({/table/@tableId}, this)" onkeydown="return SpiralTable.keyCheck(event);" class="uk-checkbox" />
        </th>
        <th>
          ���ѷ��
        </th>
        <th>
          <a href="{/table/fieldList/field[@title='makerName']/@sort}">
            <xsl:text>�᡼����</xsl:text>
            <xsl:call-template name="sortText">
              <xsl:with-param name="field" select="/table/fieldList/field[@title='makerName']/@code" />
            </xsl:call-template>
          </a>
        </th>
        <th>
          <a href="{/table/fieldList/field[@title='catalogNo']/@sort}">
            <xsl:text>������No</xsl:text>
            <xsl:call-template name="sortText">
              <xsl:with-param name="field" select="/table/fieldList/field[@title='catalogNo']/@code" />
            </xsl:call-template>
          </a>
        </th>
        <th>
          <a href="{/table/fieldList/field[@title='serialNo']/@sort}">
            <xsl:text>���ꥢ��No</xsl:text>
            <xsl:call-template name="sortText">
              <xsl:with-param name="field" select="/table/fieldList/field[@title='serialNo']/@code" />
            </xsl:call-template>
          </a>
        </th>
        <th>
          <a href="{/table/fieldList/field[@title='itemName']/@sort}">
            <xsl:text>����̾</xsl:text>
            <xsl:call-template name="sortText">
              <xsl:with-param name="field" select="/table/fieldList/field[@title='itemName']/@code" />
            </xsl:call-template>
          </a>
        </th>
        <th>
          <a href="{/table/fieldList/field[@title='itemCode']/@sort}">
            <xsl:text>���ʥ�����</xsl:text>
            <xsl:call-template name="sortText">
              <xsl:with-param name="field" select="/table/fieldList/field[@title='itemCode']/@code" />
            </xsl:call-template>
          </a>
        </th>
        <th>
          <a href="{/table/fieldList/field[@title='itemStandard']/@sort}">
            <xsl:text>����</xsl:text>
            <xsl:call-template name="sortText">
              <xsl:with-param name="field" select="/table/fieldList/field[@title='itemStandard']/@code" />
            </xsl:call-template>
          </a>
        </th>
        <th>
          <a href="{/table/fieldList/field[@title='itemJANCode']/@sort}">
            <xsl:text>JAN������</xsl:text>
            <xsl:call-template name="sortText">
              <xsl:with-param name="field" select="/table/fieldList/field[@title='itemJANCode']/@code" />
            </xsl:call-template>
          </a>
        </th>
        <th>
          <a href="{/table/fieldList/field[@title='officialFlag']/@sort}">
            <xsl:text>������ʥե饰</xsl:text>
            <xsl:call-template name="sortText">
              <xsl:with-param name="field" select="/table/fieldList/field[@title='officialFlag']/@code" />
            </xsl:call-template>
          </a>
        </th>
        <th>
          <a href="{/table/fieldList/field[@title='officialprice']/@sort}">
            <xsl:text>�������</xsl:text>
            <xsl:call-template name="sortText">
              <xsl:with-param name="field" select="/table/fieldList/field[@title='officialprice']/@code" />
            </xsl:call-template>
          </a>
        </th>
        <th>
          <a href="{/table/fieldList/field[@title='officialpriceOld']/@sort}">
            <xsl:text>��������</xsl:text>
            <xsl:call-template name="sortText">
              <xsl:with-param name="field" select="/table/fieldList/field[@title='officialpriceOld']/@code" />
            </xsl:call-template>
          </a>
        </th>
        <th>
          <a href="{/table/fieldList/field[@title='minPrice']/@sort}">
            <xsl:text>�Ǿ�����ñ�̤β���</xsl:text>
            <xsl:call-template name="sortText">
              <xsl:with-param name="field" select="/table/fieldList/field[@title='minPrice']/@code" />
            </xsl:call-template>
          </a>
        </th>
        <th>
          <a href="{/table/fieldList/field[@title='quantity']/@sort}">
            <xsl:text>����</xsl:text>
            <xsl:call-template name="sortText">
              <xsl:with-param name="field" select="/table/fieldList/field[@title='quantity']/@code" />
            </xsl:call-template>
          </a>
        </th>
        <th>
          <a href="{/table/fieldList/field[@title='quantityUnit']/@sort}">
            <xsl:text>����ñ��</xsl:text>
            <xsl:call-template name="sortText">
              <xsl:with-param name="field" select="/table/fieldList/field[@title='quantityUnit']/@code" />
            </xsl:call-template>
          </a>
        </th>
        <th>
          <a href="{/table/fieldList/field[@title='itemUnit']/@sort}">
            <xsl:text>�Ŀ�ñ��</xsl:text>
            <xsl:call-template name="sortText">
              <xsl:with-param name="field" select="/table/fieldList/field[@title='itemUnit']/@code" />
            </xsl:call-template>
          </a>
        </th>
      </tr>
      </thead>
      <tbody>
      <xsl:for-each select="data/record">
        <xsl:variable name="row" select="position() + 4" />
        <xsl:variable name="recordPosition" select="position() + number(/table/pager/@offset_start) - 1" />
        <xsl:variable name="id" select="@id" />
        <tr style="height:20;">

          <td>
            <xsl:if test="usr_requestFlg = ''">
             <input type="checkbox" class="uk-checkbox" name="smp-table-check-{/table/@tableId}" id="smp-table-check-{/table/@tableId}-{@id}" value="{@id}" onclick="SpiralTable.targetCheck({/table/@tableId}, {@id}, this.checked)" onkeydown="return SpiralTable.keyCheck(event);" />
            </xsl:if>
          </td>
          <td>
            <xsl:if test="usr_requestFlg != ''">
          <span class="">
            <xsl:attribute name="class">
              <xsl:choose>
                <xsl:when test='usr_requestFlg = "����"'>
                 uk-label uk-padding-small uk-label-success uk-padding-remove-vertical
                </xsl:when>
                <xsl:when test='usr_requestFlg = "�Ժ���"'>
                 uk-label uk-padding-small uk-label-danger uk-padding-remove-vertical
                </xsl:when>
                <xsl:otherwise>
                </xsl:otherwise>
              </xsl:choose>
            </xsl:attribute>
            <xsl:value-of select="usr_requestFlg" />
          	</span>
            </xsl:if>
          </td>
          <td>
            <xsl:value-of select="usr_makerName" />
          </td>
          <td>
            <xsl:value-of select="usr_catalogNo" />
          </td>
          <td>
            <xsl:value-of select="usr_serialNo" />
          </td>
          <td>
            <xsl:value-of select="usr_itemName" />
          </td>
          <td>
            <xsl:value-of select="usr_itemCode" />
          </td>
          <td>
            <xsl:value-of select="usr_itemStandard" />
          </td>
          <td>
            <xsl:value-of select="usr_itemJANCode" />
          </td>
          <td>
            <xsl:value-of select="usr_officialFlag" />
          </td>
          <td>
            <xsl:if test="usr_officialprice!= ''">
			<xsl:value-of select="format-number(usr_officialprice,'#,###.00')" />
			</xsl:if>
            <span class="unit uk-text-small">��</span>
          </td>
          <td>
            <xsl:if test="usr_officialpriceOld!= ''">
			<xsl:value-of select="format-number(usr_officialpriceOld,'#,###.00')" />
			</xsl:if>
            <span class="unit uk-text-small">��</span>
          </td>
          <td>
            <xsl:if test="usr_minPrice!= ''">
			<xsl:value-of select="format-number(usr_minPrice,'#,###.00')" />
			</xsl:if>
            <span class="unit uk-text-small">�� / 1<xsl:value-of select="usr_quantityUnit" /></span>
          </td>
          <td>
            <xsl:value-of select="usr_quantity" /><xsl:value-of select="usr_quantityUnit" />
          </td>
          <td>
            <xsl:value-of select="usr_quantityUnit" />
          </td>
          <td>
            <xsl:value-of select="usr_itemUnit" />
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
