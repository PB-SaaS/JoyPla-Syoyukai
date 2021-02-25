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
    <table class="uk-table uk-text-nowrap uk-table-divider">
      <thead>
	<tr>
        <th class="uk-table-shrink">
          採用結果
        </th>
        <th>
          <a href="{/table/fieldList/field[@title='makerName']/@sort}">
            <xsl:text>メーカー</xsl:text>
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
          <a href="{/table/fieldList/field[@title='itemJANCode']/@sort}">
            <xsl:text>JANコード</xsl:text>
            <xsl:call-template name="sortText">
              <xsl:with-param name="field" select="'f002348834'" />
            </xsl:call-template>
          </a>
        </th>
        <th>
          <a href="{/table/fieldList/field[@title='minPrice']/@sort}">
            <xsl:text>最小入数単位の価格</xsl:text>
            <xsl:call-template name="sortText">
              <xsl:with-param name="field" select="'f002348840'" />
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
          <a href="{/table/fieldList/field[@title='quantityUnit']/@sort}">
            <xsl:text>入数単位</xsl:text>
            <xsl:call-template name="sortText">
              <xsl:with-param name="field" select="'f002348838'" />
            </xsl:call-template>
          </a>
        </th>
        <th>
          <a href="{/table/fieldList/field[@title='itemUnit']/@sort}">
            <xsl:text>個数単位</xsl:text>
            <xsl:call-template name="sortText">
              <xsl:with-param name="field" select="'f002348839'" />
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
            <xsl:if test="usr_requestFlg != ''">
          <span class="">
            <xsl:attribute name="class">
              <xsl:choose>
                <xsl:when test='usr_requestFlg = "採用"'>
                 uk-label uk-padding-small uk-label-success uk-padding-remove-vertical
                </xsl:when>
                <xsl:when test='usr_requestFlg = "不採用"'>
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
            <xsl:if test="usr_minPrice!= ''">
			<xsl:value-of select="format-number(usr_minPrice,'#,###.00')" />
			</xsl:if>
            <span class="unit uk-text-small">円 / 1<xsl:value-of select="usr_quantityUnit" /></span>
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
