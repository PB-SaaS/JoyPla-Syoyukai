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
    <div class="no_print uk-margin">
    	<input id="smp-table-update-button" class=" uk-button uk-button-primary uk-margin-small-right" type="submit" name="smp-table-submit-button" value="更新" onclick="return SpiralTable.confirmation({/table/@tableId}, this);" />
    	<input id="smp-table-reset-button" class=" uk-button uk-button-default uk-margin-small-right" type="reset" value="リセット" onclick="SpiralTable.allReset({/table/@tableId});" /> 
    	<input id="smp-table-delete-button" class=" uk-button uk-button-danger uk-margin-small-right" type="submit" name="smp-table-submit-button" value="削除" onclick="return SpiralTable.confirmation({/table/@tableId}, this);" />
    </div>
    <xsl:apply-templates select="pager" />
    <p class="uk-text-danger uk-text-center">$table:action_err$</p>
    <div class="uk-overflow-auto">
    <table class="uk-table uk-table-hover uk-table-middle uk-table-divider uk-text-nowrap">
     <thead>
      <tr>
      	<th class="uk-table-shrink"></th>
        <th>
          <a href="{/table/fieldList/@idSort}">
            <xsl:text>No</xsl:text>
            <xsl:call-template name="sortText">
              <xsl:with-param name="field" select="'id'" />
            </xsl:call-template>
          </a>
        </th>
	        <th class="uk-table-shrink">
	            <a href="{/table/fieldList/field[@title='notUsedFlag']/@sort}">
	            <xsl:text>使用状況</xsl:text>
	            <xsl:call-template name="sortText">
	              <xsl:with-param name="field" select="'f002349016'" />
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
          <a href="{/table/fieldList/field[@title='rackName']/@sort}">
            <xsl:text>棚名</xsl:text>
            <xsl:call-template name="sortText">
              <xsl:with-param name="field" select="'f002349726'" />
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
          <a href="{/table/fieldList/field[@title='quantity']/@sort}">
            <xsl:text>入数</xsl:text>
            <xsl:call-template name="sortText">
              <xsl:with-param name="field" select="'f002348837'" />
            </xsl:call-template>
          </a>
        </th>
        <th class="uk-width-small">
          <a href="{/table/fieldList/field[@title='constantByDiv']/@sort}">
            <xsl:text>部署別定数</xsl:text>
            <xsl:call-template name="sortText">
              <xsl:with-param name="field" select="'f002349729'" />
            </xsl:call-template>
          </a>
        </th>
        <th>
          <a href="{/table/fieldList/field[@title='HPstock']/@sort}">
            <xsl:text>院内総在庫</xsl:text>
            <xsl:call-template name="sortText">
              <xsl:with-param name="field" select="'f002349769'" />
            </xsl:call-template>
          </a>
        </th>
        <th>
          <a href="{/table/fieldList/field[@title='stockQuantity']/@sort}">
            <xsl:text>在庫数</xsl:text>
            <xsl:call-template name="sortText">
              <xsl:with-param name="field" select="'f002349723'" />
            </xsl:call-template>
          </a>
        </th>
        <th class="uk-table-shrink">バーコード</th>
        <th class="uk-table-shrink"></th>
      </tr>
      </thead>
      <tbody>
      <xsl:for-each select="data/record">
        <xsl:variable name="row" select="position() + 3" />
        <xsl:variable name="recordPosition" select="position() + number(/table/pager/@offset_start) - 1" />
        <xsl:variable name="id" select="@id" />
        <tr>
        	<td>
        		<input type="checkbox" name="smp-table-check-{/table/@tableId}" id="smp-table-check-{/table/@tableId}-{@id}" class="uk-checkbox" value="{@id}" onclick="SpiralTable.targetCheck({/table/@tableId}, {@id}, this.checked)" onkeydown="return SpiralTable.keyCheck(event);" />
        	</td>
          <td>
              <xsl:value-of select="@id" />
          </td>
           <td>
            <xsl:if test="usr_notUsedFlag = '使用中'">
            	<span class="uk-label uk-label-success">使用中</span>
            </xsl:if>
            
            <xsl:if test="usr_notUsedFlag != '使用中'">
            	<span class="uk-label uk-label-danger">未使用</span>
            </xsl:if>
          </td>
          <td>
            <xsl:value-of select="usr_divisionName" />
          </td>
          <td>
          	<input type="text" name="smp-uf-{/table/fieldList/field[@title='rackName']/@code}-{@id}" onchange="SpiralTable.changeBC(this);" onfocus="SpiralTable.targetCheck({/table/@tableId},{@id});" onkeydown="return SpiralTable.keyCheck(event);" class="uk-input uk-width-small" value="{usr_rackName}">
<xsl:if test="string(usr_rackName/@hasError) = 't'">
			<xsl:attribute name="class">uk-form-danger</xsl:attribute>
</xsl:if>
</input>
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
            <span class="unit uk-text-small"><xsl:value-of select="usr_quantityUnit" /></span>
          </td>
          <td><input type="number" min="0" name="smp-uf-{/table/fieldList/field[@title='constantByDiv']/@code}-{@id}" onchange="SpiralTable.changeBC(this);" onfocus="SpiralTable.targetCheck({/table/@tableId},{@id});" onkeydown="return SpiralTable.keyCheck(event);" class="uk-input" style="width:80px" value="{usr_constantByDiv}">
			<xsl:if test="string(usr_constantByDiv/@hasError) = 't'">
			<xsl:attribute name="class">uk-form-danger</xsl:attribute>
			</xsl:if></input>
            <span class="unit uk-text-small"><xsl:value-of select="usr_quantityUnit" /></span>
          </td>
          <td>
            <xsl:value-of select="usr_HPstock" />
            <span class="unit uk-text-small"><xsl:value-of select="usr_quantityUnit" /></span>
          </td>
          <td>
            <xsl:value-of select="usr_stockQuantity" />
            <span class="unit uk-text-small"><xsl:value-of select="usr_quantityUnit" /></span>
          </td>
          <td>
            <p class="stock_barcode" style="width:270px">01<xsl:value-of select="usr_labelId" />0000</p>
          </td>
          <td class='labelCreate'>
            <button class="uk-button uk-button-small uk-button-primary" type="button" onclick="createModalOpen(this,'{usr_inHospitalItemId}');">
            ラベル発行
            </button>
          </td>
          <td class="uk-hidden json">
{
"<xsl:value-of select="usr_inHospitalItemId" />":{
"divisionName": "<xsl:value-of select="usr_divisionName" />",
"rackName": "<xsl:value-of select="usr_rackName" />",
"quantity":"<xsl:value-of select="usr_quantity" />",
"maker":"<xsl:value-of select="usr_makerName" />",
"itemName":"<xsl:value-of select="usr_itemName" />",
"code":"<xsl:value-of select="usr_itemCode" />",
"itemStandard":"<xsl:value-of select="usr_itemStandard" />",
"unit":"<xsl:value-of select="usr_quantityUnit" />",
"itemUnit":"<xsl:value-of select="usr_itemUnit" />",
"jan":"<xsl:value-of select="usr_itemJANCode" />",
"constantByDiv":"<xsl:value-of select="usr_constantByDiv" />",
"catalogNo":"<xsl:value-of select="usr_catalogNo" />",
"distributorName":"<xsl:value-of select="usr_distributorName" />",
"labelId":"<xsl:value-of select="usr_labelId" />"
}
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
