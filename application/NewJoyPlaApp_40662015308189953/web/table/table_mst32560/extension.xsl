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
          <a href="{/table/fieldList/field[@title='distributorName']/@sort}">
            <xsl:text>卸業者</xsl:text>
            <xsl:call-template name="sortText">
              <xsl:with-param name="field" select="'f002349777'" />
            </xsl:call-template>
          </a>
        </th>
        <th>
          <a href="{/table/fieldList/field[@title='makerName']/@sort}">
            <xsl:text>メーカー名</xsl:text>
            <xsl:call-template name="sortText">
              <xsl:with-param name="field" select="'f002348835'" />
            </xsl:call-template>
          </a>
        </th>
        <th>
          <a href="{/table/fieldList/field[@title='itemName']/@sort}">
            <xsl:text>商品名</xsl:text>
            <xsl:call-template name="sortText">
              <xsl:with-param name="field" select="'f002348831'" />
            </xsl:call-template>
          </a>
        </th>
        <th>
          <a href="{/table/fieldList/field[@title='itemCode']/@sort}">
            <xsl:text>商品コード</xsl:text>
            <xsl:call-template name="sortText">
              <xsl:with-param name="field" select="'f002348832'" />
            </xsl:call-template>
          </a>
        </th>
        <th>
          <a href="{/table/fieldList/field[@title='itemStandard']/@sort}">
            <xsl:text>規格</xsl:text>
            <xsl:call-template name="sortText">
              <xsl:with-param name="field" select="'f002348833'" />
            </xsl:call-template>
          </a>
        </th>
        <th>
          <a href="{/table/fieldList/field[@title='price']/@sort}">
            <xsl:text>購買価格</xsl:text>
            <xsl:call-template name="sortText">
              <xsl:with-param name="field" select="'f002348901'" />
            </xsl:call-template>
          </a>
        </th>
        <th>
          <a href="{/table/fieldList/field[@title='calculatingStock']/@sort}">
            <xsl:text>計算上在庫</xsl:text>
            <xsl:call-template name="sortText">
              <xsl:with-param name="field" select="'f002348933'" />
            </xsl:call-template>
          </a>
        </th>
        <th>
          <a href="{/table/fieldList/field[@title='inventryNum']/@sort}">
            <xsl:text>棚卸数量</xsl:text>
            <xsl:call-template name="sortText">
              <xsl:with-param name="field" select="'f002348934'" />
            </xsl:call-template>
          </a>
        </th>
        <th>
          <a href="{/table/fieldList/field[@title='inventryAmount']/@sort}">
            <xsl:text>棚卸金額</xsl:text>
            <xsl:call-template name="sortText">
              <xsl:with-param name="field" select="'f002348935'" />
            </xsl:call-template>
          </a>
        </th>
        <th>
          数量差分
        </th>
      </tr>
      </thead>
      <tbody>
      <xsl:for-each select="data/record">
        <xsl:variable name="row" select="position() + 4" />
        <xsl:variable name="recordPosition" select="position() + number(/table/pager/@offset_start) - 1" />
        <xsl:variable name="id" select="@id" />
        <tr>
          <td>
            <xsl:value-of select="@id" />
          </td>
          <td>
            <xsl:value-of select="usr_distributorName" />
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
            <xsl:if test="usr_price != ''">
              ￥<xsl:value-of select="format-number(usr_price, '###,###')" />
            </xsl:if>
          </td>
          <td>
            <xsl:value-of select="usr_calculatingStock" />
            <span class="uk-text-small"><xsl:value-of select="usr_quantityUnit" /></span>
          </td>
          <td>
            <xsl:value-of select="usr_inventryNum" />
            <span class="uk-text-small"><xsl:value-of select="usr_quantityUnit" /></span>
          </td>
          <td>
            <xsl:if test="usr_inventryAmount != ''">
              ￥<xsl:value-of select="format-number(usr_inventryAmount, '#,###.00')" />
            </xsl:if>
          </td>
          <td>
          	<xsl:value-of select="number(usr_inventryNum) - number(usr_calculatingStock)"/>
            <span class="uk-text-small"><xsl:value-of select="usr_quantityUnit" /></span>
          	
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
