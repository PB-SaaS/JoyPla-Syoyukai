<?xml version="1.0" encoding="EUC-JP"?>
<xsl:stylesheet version="1.0" xmlns:xsl="http://www.w3.org/1999/XSL/Transform">
<xsl:output method="html" />

<xsl:template match="/">
  <xsl:apply-templates select="table" />
</xsl:template>

<!-- ソート状態のテキスト -->
<xsl:template name="sortText">
  <xsl:param name="field" />
  <xsl:variable name="appendSort" select="/table/data/@sort" />
  <xsl:choose>
    <xsl:when test="$appendSort = concat($field, '_down')"><xsl:text> ▼</xsl:text></xsl:when>
    <xsl:when test="$appendSort = concat($field, '_up')"><xsl:text> ▲</xsl:text></xsl:when>
  </xsl:choose>
</xsl:template>

<!-- 表示件数の切り替え -->
<xsl:template name="limiter">
  <xsl:param name="limit" />
    <div class="uk-width-2-3">
	    <select name="_limit_{/table/@tableId}" class=" uk-select">
	      <option value="10"><xsl:if test="$limit = '10'"><xsl:attribute name="selected">selected</xsl:attribute></xsl:if>10件</option>
	      <option value="50"><xsl:if test="$limit = '50'"><xsl:attribute name="selected">selected</xsl:attribute></xsl:if>50件</option>
	      <option value="100"><xsl:if test="$limit = '100'"><xsl:attribute name="selected">selected</xsl:attribute></xsl:if>100件</option>
	    </select>
    </div>
    <div class="uk-width-1-3">
        <input type="submit" name="smp-table-submit-button" class="uk-button uk-button-default" value="表示" />
    </div>
</xsl:template>

<!-- ページャー -->
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

<!-- テキストエリア型の改行を反映するためのXSLテンプレート -->
<xsl:template name="textareaHTML">
  <xsl:param name="content" />
  <!-- 改行を保持させる -->
  <xsl:variable name="match">\n</xsl:variable>
  <xsl:choose>
    <xsl:when test="contains($content,$match)">
      <xsl:value-of select="substring-before($content,$match)" />
      <br />
      <!-- 残りを変換 -->
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
  <!-- 改行を保持させる -->
  <xsl:variable name="match">\n</xsl:variable>
  <xsl:choose>
    <xsl:when test="contains($content,$match)">
      <xsl:value-of select="substring-before($content,$match)" />
<!-- 改行を表示 -->
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

<!-- データ部分 -->
<xsl:template match="/table">
  <script type="text/javascript" src="{@jsPath}" charset="{@jsEncode}"></script>
  <form method="post" action="{@action}">
    $hidden:table:extension$
    
    <div class="">
        <div class="uk-width-1-3@m">
            <font class="smp-offset-start"><xsl:value-of select="pager/@offset_start" /></font> - <font class="smp-offset-end"><xsl:value-of select="pager/@offset_end" /></font>件 / <font class="smp-count"><xsl:value-of select="data/@total" /></font>件
        </div>
        <div class="uk-width-1-3@m uk-grid">
            <xsl:call-template name="limiter"><xsl:with-param name="limit" select="data/@limit" /></xsl:call-template>
        </div>
    </div>
    <xsl:apply-templates select="pager" />
    <div class="uk-overflow-auto">
    <table class="uk-table uk-table-hover uk-table-striped uk-table-condensed uk-text-nowrap uk-table-divider">
      <thead>
	      <tr>
	        <th class="uk-table-shrink"></th>
	        <th>
	            <a href="{/table/fieldList/field[@title='makerName']/@sort}">
	            <xsl:text>メーカー</xsl:text>
	            <xsl:call-template name="sortText">
              <xsl:with-param name="field" select="/table/fieldList/field[@title='makerName']/@code" />
	            </xsl:call-template>
	          </a>
	        </th>
	        <th>
	            <a href="{/table/fieldList/field[@title='itemName']/@sort}">
	            <xsl:text>商品名</xsl:text>
	            <xsl:call-template name="sortText">
              <xsl:with-param name="field" select="/table/fieldList/field[@title='itemName']/@code" />
	            </xsl:call-template>
	          </a>
	        </th>
	        <th>
	            <a href="{/table/fieldList/field[@title='itemCode']/@sort}">
	            <xsl:text>製品コード</xsl:text>
	            <xsl:call-template name="sortText">
              <xsl:with-param name="field" select="/table/fieldList/field[@title='itemCode']/@code" />
	            </xsl:call-template>
	          </a>
	        </th>
	        <th>
	          <a href="{/table/fieldList/field[@title='itemStandard']/@sort}">
	            <xsl:text>規格</xsl:text>
	            <xsl:call-template name="sortText">
              <xsl:with-param name="field" select="/table/fieldList/field[@title='itemStandard']/@code" />
	            </xsl:call-template>
	          </a>
	        </th>
	        <th>
	          <a href="{/table/fieldList/field[@title='quantity']/@sort}">
	            <xsl:text>入数</xsl:text>
	            <xsl:call-template name="sortText">
              <xsl:with-param name="field" select="/table/fieldList/field[@title='quantity']/@code" />
	            </xsl:call-template>
	          </a>
	        </th>
	        <th>
	          <a href="{/table/fieldList/field[@title='price']/@sort}">
	            <xsl:text>価格</xsl:text>
	            <xsl:call-template name="sortText">
              <xsl:with-param name="field" select="/table/fieldList/field[@title='price']/@code" />
	            </xsl:call-template>
	          </a>
	        </th>
	        <th>
	          <a href="{/table/fieldList/field[@title='itemJANCode']/@sort}">
	            <xsl:text>JANコード</xsl:text>
	            <xsl:call-template name="sortText">
              <xsl:with-param name="field" select="/table/fieldList/field[@title='itemJANCode']/@code" />
	            </xsl:call-template>
	          </a>
	        </th>
	        <th>
	          <a href="{/table/fieldList/field[@title='distributorName']/@sort}">
	            <xsl:text>卸業者</xsl:text>
	            <xsl:call-template name="sortText">
              <xsl:with-param name="field" select="/table/fieldList/field[@title='distributorName']/@code" />
	            </xsl:call-template>
	          </a>
	        </th>
	        <th class="uk-hidden">
	            <xsl:text>id</xsl:text>
	        </th>
	        <th class="uk-hidden">
	            <xsl:text>個数単位</xsl:text>
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
          	<button type="button" onclick="hanei(this)" class="uk-button uk-button-primary uk-button-small">反映</button>
          </td>
          <td class="uk-text-middle">
            <xsl:value-of select="usr_makerName" />
          </td>
          <td class="uk-text-middle">
            <xsl:value-of select="usr_itemName" />
          </td>
          <td class="uk-text-middle">
            <xsl:value-of select="usr_itemCode" />
          </td>
          <td class="uk-text-middle">
            <xsl:value-of select="usr_itemStandard" />
          </td>
          <td class="uk-text-middle">
            <span class="irisu"><xsl:value-of select="usr_quantity" /></span>
            <span class="unit uk-text-small"><xsl:value-of select="usr_quantityUnit" /></span>
          </td>
          <td class="uk-text-middle">
            <xsl:if test="usr_price != ''">￥
			<xsl:value-of select="format-number(usr_price,'#,###.00')" />
			</xsl:if>
			
          </td>
          <td class="uk-text-middle">
            <xsl:value-of select="usr_itemJANCode" />
          </td>
          <td class="uk-text-middle">
            <xsl:value-of select="usr_distributorName" />
          </td>
          <td class="uk-hidden json">
{
"maker":"<xsl:value-of select="usr_makerName" />",
"shouhinName":"<xsl:value-of select="usr_itemName" />",
"code":"<xsl:value-of select="usr_itemCode" />",
"kikaku":"<xsl:value-of select="usr_itemStandard" />",
"irisu":"<xsl:value-of select="usr_quantity" />",
"kakaku":"<xsl:value-of select="usr_price " />",
"jan":"<xsl:value-of select="usr_itemJANCode" />",
"oroshi":"<xsl:value-of select="usr_distributorName" />",
"recordId":"<xsl:value-of select="usr_inHospitalItemId" />",
"unit":"<xsl:value-of select="usr_quantityUnit" />",
"itemUnit":"<xsl:value-of select="usr_itemUnit" />",
"labelId":"<xsl:value-of select="usr_labelId" />",
"catalogNo":"<xsl:value-of select="usr_catalogNo" />",
"distributorId":"<xsl:value-of select="usr_distributorId" />"
}
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
