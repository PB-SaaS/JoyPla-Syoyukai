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
        <div class="uk-width-1-1 uk-grid">
	        <div class="uk-width-1-3@m uk-grid">
	            <xsl:call-template name="limiter"><xsl:with-param name="limit" select="data/@limit" /></xsl:call-template>
	        </div>
        </div>
    </div>
    <input type="hidden" value="����" class="uk-button uk-button-primary" onclick="SpiralTable.setDLFileName(this, 10);" id="exportButton"/>
    <xsl:apply-templates select="pager" />
    <div class="uk-overflow-auto">
    <table class="uk-table uk-table-striped uk-table-condensed uk-text-nowrap uk-margin-auto">
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
            <xsl:text>ȯ������</xsl:text>
            <xsl:call-template name="sortText">
              <xsl:with-param name="field" select="'f002349731'" />
            </xsl:call-template>
          </a>
        </th>
	    <th>
	      	<a href="{/table/fieldList/field[@title='orderNumber']/@sort}">
            <xsl:text>ȯ���ֹ�</xsl:text>
            <xsl:call-template name="sortText">
              <xsl:with-param name="field" select="'f002348980'" />
            </xsl:call-template>
          </a>
        </th>
	    <th>
	      	 <a href="{/table/fieldList/field[@title='divisionName']/@sort}">
            <xsl:text>����</xsl:text>
            <xsl:call-template name="sortText">
              <xsl:with-param name="field" select="'f002349796'" />
            </xsl:call-template>
          </a>
        </th>
	    <th>
	      	<a href="{/table/fieldList/field[@title='makerName']/@sort}">
            <xsl:text>�᡼����</xsl:text>
            <xsl:call-template name="sortText">
              <xsl:with-param name="field" select="'f002348835'" />
            </xsl:call-template>
          </a>
        </th>
	    <th>
	      	<a href="{/table/fieldList/field[@title='itemName']/@sort}">
            <xsl:text>����̾</xsl:text>
            <xsl:call-template name="sortText">
              <xsl:with-param name="field" select="'f002348831'" />
            </xsl:call-template>
          </a>
        </th>
	    <th>
	      	<a href="{/table/fieldList/field[@title='itemCode']/@sort}">
            <xsl:text>���ʥ�����</xsl:text>
            <xsl:call-template name="sortText">
              <xsl:with-param name="field" select="'f002348832'" />
            </xsl:call-template>
          </a>
        </th>
	    <th>
	      	<a href="{/table/fieldList/field[@title='itemStandard']/@sort}">
            <xsl:text>����</xsl:text>
            <xsl:call-template name="sortText">
              <xsl:with-param name="field" select="'f002348833'" />
            </xsl:call-template>
          </a>
        </th>
	    <th>
	      	<a href="{/table/fieldList/field[@title='quantity']/@sort}">
            <xsl:text>����</xsl:text>
            <xsl:call-template name="sortText">
              <xsl:with-param name="field" select="'f002348837'" />
            </xsl:call-template>
          </a>
        </th>
	    <th>
	      	<a href="{/table/fieldList/field[@title='orderQuantity']/@sort}">
            <xsl:text>ȯ����</xsl:text>
            <xsl:call-template name="sortText">
              <xsl:with-param name="field" select="'f002349736'" />
            </xsl:call-template>
          </a>
        </th>
	    <th>
	      	<a href="{/table/fieldList/field[@title='receivingTime']/@sort}">
            <xsl:text>�ǽ���������</xsl:text>
            <xsl:call-template name="sortText">
              <xsl:with-param name="field" select="'f002349733'" />
            </xsl:call-template>
          </a>
        </th>
	    <th>
	      	<a href="{/table/fieldList/field[@title='receivingNum']/@sort}">
            <xsl:text>���˿�</xsl:text>
            <xsl:call-template name="sortText">
              <xsl:with-param name="field" select="'f002349739'" />
            </xsl:call-template>
          </a>
        </th>
	    <th>
	      	<a href="{/table/fieldList/field[@title='orderStatus']/@sort}">
            <xsl:text>���ơ�����</xsl:text>
            <xsl:call-template name="sortText">
              <xsl:with-param name="field" select="'f002349745'" />
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
          <td>
              <xsl:value-of select="@id" />
          </td>
          <td>
            <xsl:value-of select="usr_registrationTime/full_text" />
          </td>
          <td>
            <xsl:value-of select="usr_orderNumber" />
          </td>
          <td>
            <xsl:value-of select="usr_divisionName" />
          </td>
          <td>
            <xsl:value-of select="usr_makerName" />
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
            <xsl:value-of select="usr_quantity" />
            <span class="uk-text-small"><xsl:value-of select="usr_quantityUnit" /></span>
          </td>
          <td>
            <xsl:value-of select="usr_orderQuantity" />
            <span class="uk-text-small"><xsl:value-of select="usr_itemUnit" /></span>
          </td>
          <td>
            <xsl:value-of select="usr_receivingTime/full_text" />
          </td>
          <td>
          	<xsl:if test="usr_receivingNum = ''">0</xsl:if>
            <xsl:value-of select="usr_receivingNum" />
            <span class="uk-text-small"><xsl:value-of select="usr_itemUnit" /></span>
          </td>
          <td>
          	
          	<span class="">
            <xsl:attribute name="class">
              <xsl:choose>
                <xsl:when test='usr_orderStatus = "̤ȯ��"'>
                 uk-label uk-padding-small uk-label-danger uk-padding-remove-vertical
                </xsl:when>
                <xsl:when test='usr_orderStatus = "ȯ����λ"'>
                 uk-label uk-padding-small uk-label-danger uk-padding-remove-vertical
                </xsl:when>
                <xsl:when test='usr_orderStatus = "������λ"'>
                 uk-label uk-padding-small uk-label-danger uk-padding-remove-vertical
                </xsl:when>
                <xsl:when test='usr_orderStatus = "Ǽ������"'>
                 uk-label uk-padding-small uk-label-danger uk-padding-remove-vertical
                </xsl:when>
                <xsl:when test='(usr_receivingNum - usr_orderQuantity) &lt; 0'>
                 uk-label uk-padding-small uk-label-warning uk-padding-remove-vertical
                </xsl:when>
                <xsl:when test='(usr_receivingNum - usr_orderQuantity) &gt;= 0'>
                 uk-label uk-padding-small uk-label-success uk-padding-remove-vertical
                </xsl:when>
                <xsl:otherwise>
                  
                </xsl:otherwise>
              </xsl:choose>
            </xsl:attribute>
            <xsl:if test='usr_orderStatus = "�������˴�λ"'>
                <xsl:if test='(usr_receivingNum - usr_orderQuantity) &gt;= 0'>
                	�������˴�λ
                </xsl:if>
                <xsl:if test='(usr_receivingNum - usr_orderQuantity) &lt; 0'>
            		�������˴�λ
                </xsl:if>
            </xsl:if>
            <xsl:if test='usr_orderStatus != "�������˴�λ"'>
              <xsl:value-of select="usr_orderStatus" />
            </xsl:if>
          	</span>
            <br/>
            <xsl:if test='usr_orderStatus = "�������˴�λ"'>
                <xsl:if test='(usr_receivingNum - usr_orderQuantity) &gt;= 0'>
                	<span class="uk-text-small">�����ξ��ʤΤ����˴�λ</span>
                </xsl:if>
            </xsl:if>

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