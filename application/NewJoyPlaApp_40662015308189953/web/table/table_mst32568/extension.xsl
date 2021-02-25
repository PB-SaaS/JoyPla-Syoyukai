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
    <table class="uk-table uk-table-hover uk-table-middle uk-table-divider uk-table-striped uk-table-condensed uk-text-nowrap">
     <thead>
      <tr>
        <th  class="uk-width-auto">
          <a href="{/table/fieldList/@idSort}">
            <xsl:text>id</xsl:text>
            <xsl:call-template name="sortText">
              <xsl:with-param name="field" select="'id'" />
            </xsl:call-template>
          </a>
        </th>
        <th>
          <a href="{/table/fieldList/field[@title='orderTime']/@sort}">
            <xsl:text>発注日時</xsl:text>
            <xsl:call-template name="sortText">
              <xsl:with-param name="field" select="'f002349741'" />
            </xsl:call-template>
          </a>
        </th>
        <th>
          <a href="{/table/fieldList/field[@title='orderNumber']/@sort}">
            <xsl:text>発注番号</xsl:text>
            <xsl:call-template name="sortText">
              <xsl:with-param name="field" select="'f002348980'" />
            </xsl:call-template>
          </a>
        </th>
        <th>
          <a href="{/table/fieldList/field[@title='hospitalName']/@sort}">
            <xsl:text>病院名</xsl:text>
            <xsl:call-template name="sortText">
              <xsl:with-param name="field" select="'f002349802'" />
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
          <a href="{/table/fieldList/field[@title='itemsNumber']/@sort}">
            <xsl:text>品目数</xsl:text>
            <xsl:call-template name="sortText">
              <xsl:with-param name="field" select="'f002349743'" />
            </xsl:call-template>
          </a>
        </th>
        <th>
          <a href="{/table/fieldList/field[@title='totalAmount']/@sort}">
            <xsl:text>合計金額</xsl:text>
            <xsl:call-template name="sortText">
              <xsl:with-param name="field" select="'f002349744'" />
            </xsl:call-template>
          </a>
        </th>
        <th>
          <a href="{/table/fieldList/field[@title='orderStatus']/@sort}">
            <xsl:text>ステータス</xsl:text>
            <xsl:call-template name="sortText">
              <xsl:with-param name="field" select="'f002349745'" />
            </xsl:call-template>
          </a>
        </th>
        <th class="uk-table-shrink">
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
            <xsl:value-of select="usr_orderTime/full_text" />
          </td>
          <td>
              <xsl:value-of select="usr_orderNumber" />
          </td>
          <td>
            <xsl:value-of select="usr_hospitalName" />
          </td>
          <td>
            <xsl:value-of select="usr_divisionName" />
          </td>
          <td>
            <xsl:value-of select="usr_itemsNumber" />
          </td>
          <td>
            <xsl:variable name="totalAmount" select="usr_totalAmount" />
            <xsl:if test="$totalAmount">￥<xsl:value-of select="format-number($totalAmount,'#,###')" />
			</xsl:if>
          </td>
          <td>
          	<span class="">
            <xsl:attribute name="class">
              <xsl:choose>
                <xsl:when test='usr_orderStatus = "発注完了"'>
                 uk-label uk-padding-small uk-label-danger uk-padding-remove-vertical
                </xsl:when>
                <xsl:when test='usr_orderStatus = "受注完了"'>
                 uk-label uk-padding-small uk-label-danger uk-padding-remove-vertical
                </xsl:when>
                <xsl:when test='usr_orderStatus = "納期報告済"'>
                 uk-label uk-padding-small uk-label-danger uk-padding-remove-vertical
                </xsl:when>
                <xsl:when test='usr_orderStatus = "一部入庫完了"'>
                 uk-label uk-padding-small uk-label-warning uk-padding-remove-vertical
                </xsl:when>
                <xsl:when test='usr_orderStatus = "入庫完了"'>
                 uk-label uk-padding-small uk-label-success uk-padding-remove-vertical
                </xsl:when>
                <xsl:when test='usr_orderStatus = "納品取消"'>
                 uk-label uk-padding-small uk-label-danger uk-padding-remove-vertical
                </xsl:when>
                <xsl:otherwise>
                  
                </xsl:otherwise>
              </xsl:choose>
            </xsl:attribute>
            <xsl:value-of select="usr_orderStatus" />
          	</span>
          </td>
          <td>
            <a href="{/table/cardList/card[@title='page_266218'][@recordId=$id]}" target="_self" class="uk-button uk-button-primary">
              詳細
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
