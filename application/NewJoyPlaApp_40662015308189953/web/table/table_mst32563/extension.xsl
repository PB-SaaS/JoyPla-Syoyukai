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
    <table class="uk-table uk-table-hover uk-table-middle uk-table-divider uk-table-condensed uk-text-nowrap ">
     <thead>
      <tr>
        <th>
          <a href="{/table/fieldList/@idSort}">
            <xsl:text>id</xsl:text>
            <xsl:call-template name="sortText">
              <xsl:with-param name="field" select="'id'" />
            </xsl:call-template>
          </a>
        </th>
        <th>
          <a href="{/table/fieldList/field[@title='divisionName']/@sort}">
            <xsl:text>部署名</xsl:text>
            <xsl:call-template name="sortText">
              <xsl:with-param name="field" select="'f002349796'" />
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
            <xsl:text>製品コード</xsl:text>
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
          <a href="{/table/fieldList/field[@title='distributorName']/@sort}">
            <xsl:text>卸業者</xsl:text>
            <xsl:call-template name="sortText">
              <xsl:with-param name="field" select="'f002349777'" />
            </xsl:call-template>
          </a>
        </th>
        <th>
          <a href="{/table/fieldList/field[@title='quantity']/@sort}">
            <xsl:text>入数</xsl:text>
            <xsl:call-template name="sortText">
              <xsl:with-param name="field" select="'f002348837'" />
            </xsl:call-template>
          </a>
        </th>
        <th>
          <a href="{/table/fieldList/field[@title='stockQuantity']/@sort}">
            <xsl:text>部署在庫数</xsl:text>
            <xsl:call-template name="sortText">
              <xsl:with-param name="field" select="'f002349723'" />
            </xsl:call-template>
          </a>
        </th>
        <th>
          <a href="{/table/fieldList/field[@title='orderWithinCount']/@sort}">
            <xsl:text>発注中数</xsl:text>
            <xsl:call-template name="sortText">
              <xsl:with-param name="field" select="'f002349725'" />
            </xsl:call-template>
          </a>
        </th>
        <th>
          <a href="{/table/fieldList/field[@title='planInventoryCnt']/@sort}">
            <xsl:text>予定在庫</xsl:text>
            <xsl:call-template name="sortText">
              <xsl:with-param name="field" select="'f002349728'" />
            </xsl:call-template>
          </a><br/>
          <span class="uk-text-small uk-text-meta">部署在庫数+発注中数</span>
        </th>
        <th>
          <a href="{/table/fieldList/field[@title='constantByDiv']/@sort}">
            <xsl:text>部署別定数</xsl:text>
            <xsl:call-template name="sortText">
              <xsl:with-param name="field" select="'f002349729'" />
            </xsl:call-template>
          </a>
        </th>
        <th>
          <a href="{/table/fieldList/field[@title='requiredOrderNum']/@sort}">
            <xsl:text>必要在庫数</xsl:text>
            <xsl:call-template name="sortText">
              <xsl:with-param name="field" select="'f002349730'" />
            </xsl:call-template>
          </a><br/>
          <span class="uk-text-small uk-text-meta">部署別定数-予定在庫</span>
        </th>
        <th class="uk-width-small">
          発注数
        </th>
        <th class="uk-width-small">
          発注入数<br/>
          <span class="uk-text-small uk-text-meta">発注数×入数</span>
        </th>
        <th>
          調整後在庫数<br/>
          <span class="uk-text-small uk-text-meta">予定在庫+発注入数</span>
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
            <xsl:value-of select="usr_distributorName" />
          </td>
          <td>
            <span id="quantity_{$id}">
            <xsl:value-of select="usr_quantity" />
            </span>
            <span class="unit uk-text-small"><xsl:value-of select="usr_quantityUnit" /></span>
          </td>
          
          <td>
            <xsl:value-of select="usr_stockQuantity" />
            <span class="unit uk-text-small"><xsl:value-of select="usr_quantityUnit" /></span>
          </td>
          
          <td>
            <xsl:value-of select="usr_orderWithinCount" />
            <span class="unit uk-text-small"><xsl:value-of select="usr_quantityUnit" /></span>
          </td>
          
          <td>
            <span id="planInventoryCnt_{$id}">
             <xsl:value-of select="usr_planInventoryCnt" />
            </span>
            <span class="unit uk-text-small"><xsl:value-of select="usr_quantityUnit" /></span>
          </td>
          <td>
            <span id="constantByDiv_{$id}">
            <xsl:value-of select="usr_constantByDiv" />
            </span>
            <span class="unit uk-text-small"><xsl:value-of select="usr_quantityUnit" /></span>
          </td>
          <td>
            <span id="requiredOrderNum_{$id}">
            <xsl:value-of select="usr_requiredOrderNum" />
            </span>
            <span class="unit uk-text-small"><xsl:value-of select="usr_quantityUnit" /></span>
            
          </td>
          <td>
            <input type="number" class="uk-input" style="width:72px" onchange="active(this,{$id},{usr_planInventoryCnt},{usr_quantity},'{usr_divisionId}','{usr_inHospitalItemId}')" id="orderQuantity_{$id}">
              <xsl:attribute name="value">
                <xsl:value-of select="ceiling(usr_requiredOrderNum div usr_quantity)" />
              </xsl:attribute>
            </input>
            <span class="unit uk-text-small uk-text-middle uk-width-auto"><xsl:value-of select="usr_itemUnit" /></span>
          </td>
          <td>
            <span id="orderQuantityPerCarton_{$id}">
            <xsl:value-of select="ceiling(usr_requiredOrderNum div usr_quantity) * usr_quantity" />
            </span>
            <span class="unit uk-text-small"><xsl:value-of select="usr_quantityUnit" /></span>
          </td>
          <td>
            <span id="adjustmentStock_{$id}">
            <xsl:value-of select="usr_planInventoryCnt + (ceiling(usr_requiredOrderNum div usr_quantity) * usr_quantity)" />
            </span>
            <span class="unit uk-text-small"><xsl:value-of select="usr_quantityUnit" /></span>
          </td>

          <td class="uk-hidden json">
{
"id":"<xsl:value-of select="@id" />",
"divisionName":"<xsl:value-of select="usr_divisionName" />",
"makerName":"<xsl:value-of select="usr_makerName" />",
"itemName":"<xsl:value-of select="usr_itemName" />",
"itemCode":"<xsl:value-of select="usr_itemCode" />",
"itemStandard":"<xsl:value-of select="itemStandard" />",
"distributorName":"<xsl:value-of select="usr_distributorName" />",
"irisu":"<xsl:value-of select="usr_quantity" />",
"kakaku":"<xsl:value-of select="usr_price" />",
"unit":"<xsl:value-of select="usr_quantityUnit" />",
"itemUnit":"<xsl:value-of select="usr_itemUnit" />",
"stockQuantity":"<xsl:value-of select="usr_stockQuantity" />",
"orderWithinCount":"<xsl:value-of select="usr_orderWithinCount" />",
"planInventoryCnt":"<xsl:value-of select="usr_planInventoryCnt" />",
"constantByDiv":"<xsl:value-of select="usr_constantByDiv" />",
"requiredOrderNum":"<xsl:value-of select="usr_requiredOrderNum" />",
"orderQuantity":"<xsl:value-of select="ceiling(usr_requiredOrderNum div usr_quantity)" />",
"countNum":"<xsl:value-of select="ceiling(usr_requiredOrderNum div usr_quantity) * usr_quantity" />",
"adjustmentStock":"<xsl:value-of select="usr_planInventoryCnt + (ceiling(usr_requiredOrderNum div usr_quantity) * usr_quantity)" />",
"inHospitalItemId":"<xsl:value-of select="usr_inHospitalItemId" />",
"divisionId":"<xsl:value-of select="usr_divisionId" />",
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
