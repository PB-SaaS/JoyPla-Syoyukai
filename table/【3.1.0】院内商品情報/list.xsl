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
    <input type="button" value="ダウンロード" class="smp-table-button with-wrap uk-hidden" id="exportButton" onclick="SpiralTable.setDLFileName(this, {data/@limit});" />
    <xsl:apply-templates select="pager" />
    <div class="uk-overflow-auto">
    <table class="uk-table uk-table-striped uk-table-condensed uk-text-nowrap uk-width-auto">
     <thead>
      <tr>
        <th class="f_01">
          <a href="{/table/fieldList/@idSort}">
            <xsl:text>id</xsl:text>
            <xsl:call-template name="sortText">
              <xsl:with-param name="field" select="'id'" />
            </xsl:call-template>
          </a>
        </th>
        <th>
          詳細
        </th>
        <th class="f_02">
          <a href="{/table/fieldList/field[@title='notUsedFlag']/@sort}">
            <xsl:text>使用状況</xsl:text>
            <xsl:call-template name="sortText">
                <xsl:with-param name="field" select="/table/fieldList/field[@title='notUsedFlag']/@code" />
            </xsl:call-template>
          </a>
        </th>
        <th class="f_03">
          <a href="{/table/fieldList/field[@title='inHospitalItemId']/@sort}">
            <xsl:text>院内商品ID</xsl:text>
            <xsl:call-template name="sortText">
                <xsl:with-param name="field" select="/table/fieldList/field[@title='inHospitalItemId']/@code" />
            </xsl:call-template>
          </a>
        </th>
        <th class="f_04">
          <a href="{/table/fieldList/field[@title='registrationTime']/@sort}">
            <xsl:text>登録日時</xsl:text>
            <xsl:call-template name="sortText">
                <xsl:with-param name="field" select="/table/fieldList/field[@title='registrationTime']/@code" />
            </xsl:call-template>
          </a>
        </th>
        <th class="f_05">
          <a href="{/table/fieldList/field[@title='updateTime']/@sort}">
            <xsl:text>更新日時</xsl:text>
            <xsl:call-template name="sortText">
                <xsl:with-param name="field" select="/table/fieldList/field[@title='updateTime']/@code" />
            </xsl:call-template>
          </a>
        </th>
        <th class="f_06">
          <a href="{/table/fieldList/field[@title='itemId']/@sort}">
            <xsl:text>商品ID</xsl:text>
            <xsl:call-template name="sortText">
                <xsl:with-param name="field" select="/table/fieldList/field[@title='itemId']/@code" />
            </xsl:call-template>
          </a>
        </th>
        <th class="f_07">
          <a href="{/table/fieldList/field[@title='makerName']/@sort}">
            <xsl:text>メーカー</xsl:text>
            <xsl:call-template name="sortText">
                <xsl:with-param name="field" select="/table/fieldList/field[@title='makerName']/@code" />
            </xsl:call-template>
          </a>
        </th>
        <th class="f_08">
          <a href="{/table/fieldList/field[@title='itemName']/@sort}">
            <xsl:text>商品名</xsl:text>
            <xsl:call-template name="sortText">
                <xsl:with-param name="field" select="/table/fieldList/field[@title='itemName']/@code" />
            </xsl:call-template>
          </a>
        </th>
        <th class="f_09">
          <a href="{/table/fieldList/field[@title='itemCode']/@sort}">
            <xsl:text>製品コード</xsl:text>
            <xsl:call-template name="sortText">
                <xsl:with-param name="field" select="/table/fieldList/field[@title='itemCode']/@code" />
            </xsl:call-template>
          </a>
        </th>
        <th class="f_10">
          <a href="{/table/fieldList/field[@title='itemStandard']/@sort}">
            <xsl:text>規格</xsl:text>
            <xsl:call-template name="sortText">
                <xsl:with-param name="field" select="/table/fieldList/field[@title='itemStandard']/@code" />
            </xsl:call-template>
          </a>
        </th>
        <th class="f_11">
          <a href="{/table/fieldList/field[@title='itemJANCode']/@sort}">
            <xsl:text>JANコード</xsl:text>
            <xsl:call-template name="sortText">
                <xsl:with-param name="field" select="/table/fieldList/field[@title='itemJANCode']/@code" />
            </xsl:call-template>
          </a>
        </th>
        <th class="f_12">
          <a href="{/table/fieldList/field[@title='catalogNo']/@sort}">
            <xsl:text>カタログNo</xsl:text>
            <xsl:call-template name="sortText">
                <xsl:with-param name="field" select="/table/fieldList/field[@title='catalogNo']/@code" />
            </xsl:call-template>
          </a>
        </th>
        <th class="f_13">
          <a href="{/table/fieldList/field[@title='serialNo']/@sort}">
            <xsl:text>シリアルNo</xsl:text>
            <xsl:call-template name="sortText">
                <xsl:with-param name="field" select="/table/fieldList/field[@title='serialNo']/@code" />
            </xsl:call-template>
          </a>
        </th>
        <th class="f_14">
          <a href="{/table/fieldList/field[@title='medicineCategory']/@sort}">
            <xsl:text>保険請求分類（医科）</xsl:text>
            <xsl:call-template name="sortText">
                <xsl:with-param name="field" select="/table/fieldList/field[@title='medicineCategory']/@code" />
            </xsl:call-template>
          </a>
        </th>
        <th class="f_15">
          <a href="{/table/fieldList/field[@title='homeCategory']/@sort}">
            <xsl:text>保険請求分類（在宅）</xsl:text>
            <xsl:call-template name="sortText">
                <xsl:with-param name="field" select="/table/fieldList/field[@title='homeCategory']/@code" />
            </xsl:call-template>
          </a>
        </th>
        <th class="f_16">
          <a href="{/table/fieldList/field[@title='quantity']/@sort}">
            <xsl:text>入数</xsl:text>
            <xsl:call-template name="sortText">
                <xsl:with-param name="field" select="/table/fieldList/field[@title='quantity']/@code" />
            </xsl:call-template>
          </a>
        </th>
        <th class="f_17">
          <a href="{/table/fieldList/field[@title='quantityUnit']/@sort}">
            <xsl:text>入数単位</xsl:text>
            <xsl:call-template name="sortText">
                <xsl:with-param name="field" select="/table/fieldList/field[@title='quantityUnit']/@code" />
            </xsl:call-template>
          </a>
        </th>
        <th class="f_18">
          <a href="{/table/fieldList/field[@title='itemUnit']/@sort}">
            <xsl:text>個数単位</xsl:text>
            <xsl:call-template name="sortText">
                <xsl:with-param name="field" select="/table/fieldList/field[@title='itemUnit']/@code" />
            </xsl:call-template>
          </a>
        </th>
        <th class="f_19">
          <a href="{/table/fieldList/field[@title='price']/@sort}">
            <xsl:text>購買価格</xsl:text>
            <xsl:call-template name="sortText">
                <xsl:with-param name="field" select="/table/fieldList/field[@title='price']/@code" />
            </xsl:call-template>
          </a>
        </th>
        <th class="f_20">
          <a href="{/table/fieldList/field[@title='HPstock']/@sort}">
            <xsl:text>院内在庫数</xsl:text>
            <xsl:call-template name="sortText">
                <xsl:with-param name="field" select="/table/fieldList/field[@title='HPstock']/@code" />
            </xsl:call-template>
          </a>
        </th>
        <th class="f_21">
          <a href="{/table/fieldList/field[@title='officialFlag']/@sort}">
            <xsl:text>償還フラグ</xsl:text>
            <xsl:call-template name="sortText">
                <xsl:with-param name="field" select="/table/fieldList/field[@title='officialFlag']/@code" />
            </xsl:call-template>
          </a>
        </th>
        <th class="f_22">
          <a href="{/table/fieldList/field[@title='officialprice']/@sort}">
            <xsl:text>償還価格</xsl:text>
            <xsl:call-template name="sortText">
                <xsl:with-param name="field" select="/table/fieldList/field[@title='officialprice']/@code" />
            </xsl:call-template>
          </a>
        </th>
        <th class="f_23">
          <a href="{/table/fieldList/field[@title='officialpriceOld']/@sort}">
            <xsl:text>旧償還価格</xsl:text>
            <xsl:call-template name="sortText">
                <xsl:with-param name="field" select="/table/fieldList/field[@title='officialpriceOld']/@code" />
            </xsl:call-template>
          </a>
        </th>
        <th class="f_24">
          <a href="{/table/fieldList/field[@title='distributorName']/@sort}">
            <xsl:text>卸業者</xsl:text>
            <xsl:call-template name="sortText">
                <xsl:with-param name="field" select="/table/fieldList/field[@title='distributorName']/@code" />
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
          <xsl:attribute name="class">
            <xsl:text>smp-row-</xsl:text><xsl:value-of select="$row" />
            <xsl:text> smp-row-data</xsl:text>
            <xsl:if test="string(./*/@hasError) = 'true'">
              <xsl:text> smp-valid-err-row</xsl:text>
            </xsl:if>
          </xsl:attribute>
          <td class="f_01">
              <xsl:value-of select="@id" />
          </td>
          <td>
            <a class="smp-cell-id" href="{/table/cardList/card[@title='page_169055'][@recordId=$id]}" target="_self">
              詳細
            </a>
          </td>
          <td class="f_02">
            <xsl:if test="usr_notUsedFlag = '使用中'">
            	<span class="uk-label uk-label-success">使用中</span>
            </xsl:if>
            
            <xsl:if test="usr_notUsedFlag != '使用中'">
            	<span class="uk-label uk-label-danger">未使用</span>
            </xsl:if>
          </td>
          <td class="f_03">
            <xsl:value-of select="usr_inHospitalItemId" />
          </td>
          <td class="f_04">
            <xsl:value-of select="usr_registrationTime/full_text" />
          </td>
          <td class="f_05">
            <xsl:value-of select="usr_updateTime/full_text" />
          </td>
          <td class="f_06">
            <xsl:value-of select="usr_itemId" />
          </td>
          <td class="f_07">
            <xsl:value-of select="usr_makerName" />
          </td>
          <td class="f_08">
            <xsl:value-of select="usr_itemName" />
          </td>
          <td class="f_09">
            <xsl:value-of select="usr_itemCode" />
          </td>
          <td class="f_10">
            <xsl:value-of select="usr_itemStandard" />
          </td>
          <td class="f_11">
            <xsl:value-of select="usr_itemJANCode" />
          </td>
          <td class="f_12">
            <xsl:value-of select="usr_catalogNo" />
          </td>
          <td class="f_13">
            <xsl:value-of select="usr_serialNo" />
          </td>
          <td class="f_14">
            <xsl:value-of select="usr_medicineCategory" />
          </td>
          <td class="f_15">
            <xsl:value-of select="usr_homeCategory" />
          </td>
          <td class="f_16">
            <xsl:value-of select="usr_quantity" />
          </td>
          <td class="f_17">
            <xsl:value-of select="usr_quantityUnit" />
          </td>
          <td class="f_18">
            <xsl:value-of select="usr_itemUnit" />
          </td>
          <td class="f_19">
            <xsl:if test="usr_price!= ''">
              ￥<xsl:value-of select="format-number(usr_price, '#,##0.00')" />
            </xsl:if>
          </td>
          <td class="f_20">
            <xsl:value-of select="usr_HPstock" />
          </td>
          <td class="f_21">
            <xsl:value-of select="usr_officialFlag" />
          </td>
          <td class="f_22">
            <xsl:if test="usr_officialpriceOld!= ''">
              ￥<xsl:value-of select="format-number(usr_officialpriceOld, '#,##0.00')" />
            </xsl:if>
          </td>
          <td class="f_23">
            <xsl:if test="usr_officialpriceOld!= ''">
              ￥<xsl:value-of select="format-number(usr_officialpriceOld, '#,##0.00')" />
            </xsl:if>
          </td>
          <td class="f_24">
            <xsl:value-of select="usr_distributorName" />
          </td>
        </tr>
      </xsl:for-each>
                    </tbody>
                </table>
            </div>

            <xsl:apply-templates select="pager"/>
        </form>
</xsl:template>

</xsl:stylesheet>
