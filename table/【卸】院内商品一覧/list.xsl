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
    <table class="uk-table uk-table-striped uk-table-condensed uk-text-nowrap uk-width-auto">
      <thead>
	      <tr>
	        <th class='uk-width-auto'>
	          <a href="{/table/fieldList/@idSort}">
	            <xsl:text>id</xsl:text>
	            <xsl:call-template name="sortText">
	              <xsl:with-param name="field" select="'id'" />
	            </xsl:call-template>
	          </a>
	        </th>
	        <th class="uk-table-shrink">�ܺ�</th>
	        <th class='uk-width-small f_01'>
	            <a href="{/table/fieldList/field[@title='notUsedFlag']/@sort}">
	            <xsl:text>���Ѿ���</xsl:text>
	            <xsl:call-template name="sortText">
              <xsl:with-param name="field" select="/table/fieldList/field[@title='notUsedFlag']/@code" />
	            </xsl:call-template>
	          </a>
	        </th>
	        <th class='uk-width-small f_02'>
	            <a href="{/table/fieldList/field[@title='registrationTime']/@sort}">
	            <xsl:text>��Ͽ����</xsl:text>
	            <xsl:call-template name="sortText">
              <xsl:with-param name="field" select="/table/fieldList/field[@title='registrationTime']/@code" />
	            </xsl:call-template>
	          </a>
	        </th>
	        <th class='uk-width-small f_03'>
	            <a href="{/table/fieldList/field[@title='updateTime']/@sort}">
	            <xsl:text>��������</xsl:text>
	            <xsl:call-template name="sortText">
              <xsl:with-param name="field" select="/table/fieldList/field[@title='updateTime']/@code" />
	            </xsl:call-template>
	          </a>
	        </th>
	        <th class='uk-width-small f_04'>
	            <a href="{/table/fieldList/field[@title='itemId']/@sort}">
	            <xsl:text>����ID</xsl:text>
	            <xsl:call-template name="sortText">
              <xsl:with-param name="field" select="/table/fieldList/field[@title='itemId']/@code" />
	            </xsl:call-template>
	          </a>
	        </th>
	        <th class='uk-width-large f_05'>
	            <a href="{/table/fieldList/field[@title='makerName']/@sort}">
	            <xsl:text>�᡼����̾</xsl:text>
	            <xsl:call-template name="sortText">
              <xsl:with-param name="field" select="/table/fieldList/field[@title='makerName']/@code" />
	            </xsl:call-template>
	          </a>
	        </th>
	        <th class='uk-width-large f_06'>
	            <a href="{/table/fieldList/field[@title='itemName']/@sort}">
	            <xsl:text>����̾</xsl:text>
	            <xsl:call-template name="sortText">
              <xsl:with-param name="field" select="/table/fieldList/field[@title='itemName']/@code" />
	            </xsl:call-template>
	          </a>
	        </th>
	        <th class='uk-width-large f_07'>
	            <a href="{/table/fieldList/field[@title='itemCode']/@sort}">
	            <xsl:text>���ʥ�����</xsl:text>
	            <xsl:call-template name="sortText">
              <xsl:with-param name="field" select="/table/fieldList/field[@title='itemCode']/@code" />
	            </xsl:call-template>
	          </a>
	        </th>
	        <th class='uk-width-small f_08'>
	          <a href="{/table/fieldList/field[@title='itemStandard']/@sort}">
	            <xsl:text>����</xsl:text>
	            <xsl:call-template name="sortText">
              <xsl:with-param name="field" select="/table/fieldList/field[@title='itemStandard']/@code" />
	            </xsl:call-template>
	          </a>
	        </th>
	        <th class='uk-width-large f_09'>
	          <a href="{/table/fieldList/field[@title='catalogNo']/@sort}">
	            <xsl:text>������No</xsl:text>
	            <xsl:call-template name="sortText">
              <xsl:with-param name="field" select="/table/fieldList/field[@title='catalogNo']/@code" />
	            </xsl:call-template>
	          </a>
	        </th>
	        <th class='uk-width-large f_10'>
	          <a href="{/table/fieldList/field[@title='serialNo']/@sort}">
	            <xsl:text>���ꥢ��No</xsl:text>
	            <xsl:call-template name="sortText">
              <xsl:with-param name="field" select="/table/fieldList/field[@title='serialNo']/@code" />
	            </xsl:call-template>
	          </a>
	        </th>
	        <th class='uk-width-small f_11'>
	          <a href="{/table/fieldList/field[@title='quantity']/@sort}">
	            <xsl:text>����</xsl:text>
	            <xsl:call-template name="sortText">
              <xsl:with-param name="field" select="/table/fieldList/field[@title='quantity']/@code" />
	            </xsl:call-template>
	          </a>
	        </th>
	        <th class='uk-width-small f_12'>
	          <a href="{/table/fieldList/field[@title='minPrice']/@sort}">
	            <xsl:text>�Ǿ�ñ�̤β���</xsl:text>
	            <xsl:call-template name="sortText">
              <xsl:with-param name="field" select="/table/fieldList/field[@title='minPrice']/@code" />
	            </xsl:call-template>
	          </a>
	        </th>
	        <th class='uk-width-small f_13'>
	          <a href="{/table/fieldList/field[@title='price']/@sort}">
	            <xsl:text>����</xsl:text>
	            <xsl:call-template name="sortText">
              <xsl:with-param name="field" select="/table/fieldList/field[@title='price']/@code" />
	            </xsl:call-template>
	          </a>
	        </th>
	        <th class='uk-width-small f_14'>
	          <a href="{/table/fieldList/field[@title='oldPrice']/@sort}">
	            <xsl:text>�����</xsl:text>
	            <xsl:call-template name="sortText">
              <xsl:with-param name="field" select="/table/fieldList/field[@title='oldPrice']/@code" />
	            </xsl:call-template>
	          </a>
	        </th>
	        <th class='uk-width-small f_15'>
	          <a href="{/table/fieldList/field[@title='itemJANCode']/@sort}">
	            <xsl:text>JAN������</xsl:text>
	            <xsl:call-template name="sortText">
              <xsl:with-param name="field" select="/table/fieldList/field[@title='itemJANCode']/@code" />
	            </xsl:call-template>
	          </a>
	        </th>
	        <th class='uk-width-small f_16'>
	          <a href="{/table/fieldList/field[@title='HPstock']/@sort}">
	            <xsl:text>�����Ҹ˺߸˿�</xsl:text>
	            <xsl:call-template name="sortText">
              <xsl:with-param name="field" select="/table/fieldList/field[@title='HPstock']/@code" />
	            </xsl:call-template>
	          </a>
	        </th>
	        <th class='uk-width-small f_17'>
	          <a href="{/table/fieldList/field[@title='officialFlag']/@sort}">
	            <xsl:text>���Բ��ʥե饰</xsl:text>
	            <xsl:call-template name="sortText">
              <xsl:with-param name="field" select="/table/fieldList/field[@title='officialFlag']/@code" />
	            </xsl:call-template>
	          </a>
	        </th>
	        <th class='uk-width-small f_18'>
	          <a href="{/table/fieldList/field[@title='officialprice']/@sort}">
	            <xsl:text>���Բ���</xsl:text>
	            <xsl:call-template name="sortText">
              <xsl:with-param name="field" select="/table/fieldList/field[@title='officialprice']/@code" />
	            </xsl:call-template>
	          </a>
	        </th>
	        <th class='uk-width-small f_19'>
	          <a href="{/table/fieldList/field[@title='officialpriceOld']/@sort}">
	            <xsl:text>����Բ���</xsl:text>
	            <xsl:call-template name="sortText">
              <xsl:with-param name="field" select="/table/fieldList/field[@title='officialpriceOld']/@code" />
	            </xsl:call-template>
	          </a>
	        </th>
	        <th class='uk-width-small f_20'>
	          <a href="{/table/fieldList/field[@title='distributorName']/@sort}">
	            <xsl:text>���ȼ�</xsl:text>
	            <xsl:call-template name="sortText">
              <xsl:with-param name="field" select="/table/fieldList/field[@title='distributorName']/@code" />
	            </xsl:call-template>
	          </a>
	        </th>
	        <th class='uk-width-small f_21'>
	          <a href="{/table/fieldList/field[@title='notice']/@sort}">
	            <xsl:text>�õ�����</xsl:text>
	            <xsl:call-template name="sortText">
              <xsl:with-param name="field" select="/table/fieldList/field[@title='notice']/@code" />
	            </xsl:call-template>
	          </a>
	        </th>
	      </tr>
	  </thead>
      <tbody>
      <xsl:for-each select="data/record">
        <xsl:variable name="row" select="position() + 3" />
        <xsl:variable name="recordPosition" select="position() + number(/table/pager/@offset_start) - 1" />
        <xsl:variable name="id" select="@id" />
        <tr>
          <td class="">
            <xsl:value-of select="@id" />
          </td>
          <td class="">

            <a class="uk-button uk-button-link" href="{/table/cardList/card[@title='page_267643'][@recordId=$id]}" target="_self">�ܺ�</a>
</td>
          <td class=" f_01">
            <xsl:if test="usr_notUsedFlag = '������'">
            	<span class="uk-label uk-label-success">������</span>
            </xsl:if>
            
            <xsl:if test="usr_notUsedFlag != '������'">
            	<span class="uk-label uk-label-danger">̤����</span>
            </xsl:if>
          </td>
          <td class=" f_02">
            <xsl:value-of select="usr_registrationTime/full_text" />
          </td>
          <td class=" f_03">
            <xsl:value-of select="usr_updateTime/full_text" />
          </td>
          <td class=" f_04">
            <xsl:value-of select="usr_itemId" />
          </td>
          <td class=" f_05">
            <xsl:value-of select="usr_makerName" />
          </td>
          <td class=" f_06">
            <xsl:value-of select="usr_itemName" />
          </td>
          <td class=" f_07">
            <xsl:value-of select="usr_itemCode" />
          </td>
          <td class=" f_08">
            <xsl:value-of select="usr_itemStandard" />
          </td>
          <td class=" f_09">
            <xsl:value-of select="usr_catalogNo" />
          </td>
          <td class=" f_10">
            <xsl:value-of select="usr_serialNo" />
          </td>
          <td class=" f_11">
            <span class="irisu"><xsl:value-of select="usr_quantity" /></span>
            <span class="unit uk-text-small"><xsl:value-of select="usr_quantityUnit" /></span>
          </td>
          <td class=" f_12">
            <xsl:if test="usr_minPrice != ''">
			<xsl:value-of select="format-number(usr_minPrice,'#,###.00')" />
			</xsl:if>
            <span class="unit uk-text-small">�� / 1<xsl:value-of select="usr_quantityUnit" /></span>
			
          </td>
          <td class=" f_13">
            <xsl:if test="usr_price != ''">
			<xsl:value-of select="format-number(usr_price,'#,###.00')" />
			</xsl:if>
            <span class="unit uk-text-small">��</span>
			
          </td>
          <td class=" f_14">
            <xsl:if test="usr_oldPrice != ''">
			<xsl:value-of select="format-number(usr_oldPrice,'#,###.00')" />
			</xsl:if>
            <span class="unit uk-text-small">��</span>
			
          </td>
          <td class=" f_15">
            <xsl:value-of select="usr_itemJANCode" />
          </td>
          <td class=" f_16">
            <xsl:value-of select="usr_HPstock" />
            <span class="unit uk-text-small"><xsl:value-of select="usr_quantityUnit" /></span>
          </td>
          <td class=" f_17">
            <xsl:value-of select="usr_officialFlag" />
          </td>
          <td class=" f_18">
            <xsl:if test="usr_officialprice != ''">
			<xsl:value-of select="format-number(usr_officialprice ,'#,###.00')" />
			</xsl:if>
            <span class="unit uk-text-small">��</span>
          </td>
          <td class=" f_19">
            <xsl:if test="usr_officialpriceOld!= ''">
			<xsl:value-of select="format-number(usr_officialpriceOld,'#,###.00')" />
			</xsl:if>
            <span class="unit uk-text-small">��</span>
          </td>
          <td class=" f_20">
            <xsl:value-of select="usr_distributorName" />
          </td>
          <td class="f_21">
            <xsl:call-template name="textareaHTML"><xsl:with-param name="content" select="usr_notice" /></xsl:call-template>
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
