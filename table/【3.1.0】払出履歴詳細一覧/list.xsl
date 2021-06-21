<?xml version="1.0" encoding="EUC-JP"?>

<xsl:stylesheet version="1.0" xmlns:xsl="http://www.w3.org/1999/XSL/Transform">

    <xsl:output method="html"/>

    <xsl:template match="/">
        <xsl:apply-templates select="table"/>
    </xsl:template>

    <!-- ソート状態のテキスト -->
    <xsl:template name="sortText">

        <xsl:param name="field"/>

        <xsl:variable name="appendSort" select="/table/data/@sort"/>

        <xsl:choose>

            <xsl:when test="$appendSort = concat($field, '_down')">
                <xsl:text> ▼</xsl:text>
            </xsl:when>

            <xsl:when test="$appendSort = concat($field, '_up')">
                <xsl:text> ▲</xsl:text>
            </xsl:when>
        </xsl:choose>
    </xsl:template>

    <!-- 表示件数の切り替え -->
    <xsl:template name="limiter">

        <xsl:param name="limit"/>
        <div class="uk-width-2-3">
            <select name="_limit_{/table/@tableId}" class=" uk-select">
                <option value="10">

                    <xsl:if test="$limit = '10'">

                        <xsl:attribute name="selected">selected</xsl:attribute>
                    </xsl:if>10件</option>
                <option value="50">

                    <xsl:if test="$limit = '50'">

                        <xsl:attribute name="selected">selected</xsl:attribute>
                    </xsl:if>50件</option>
                <option value="100">

                    <xsl:if test="$limit = '100'">

                        <xsl:attribute name="selected">selected</xsl:attribute>
                    </xsl:if>100件</option>
            </select>
        </div>
        <div class="uk-width-1-3">
            <input
                type="submit"
                name="smp-table-submit-button"
                class="uk-button uk-button-default"
                value="表示"/>
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
                            <span><xsl:value-of select="."/></span>
                        </xsl:when>

                        <xsl:when test="@omit = 'true'">

                            <xsl:attribute name="class">uk-disabled</xsl:attribute>
                            <span><xsl:value-of select="."/></span>
                        </xsl:when>

                        <xsl:otherwise>

                            <xsl:attribute name="class"></xsl:attribute>
                            <a href="{@url}"><xsl:value-of select="."/></a>
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
                    <font class="smp-offset-start"><xsl:value-of select="pager/@offset_start"/></font>
                    -
                    <font class="smp-offset-end"><xsl:value-of select="pager/@offset_end"/></font>件 /
                    <font class="smp-count"><xsl:value-of select="data/@total"/></font>件
                </div>
                <div class="uk-width-1-3@m uk-grid">

                    <xsl:call-template name="limiter"><xsl:with-param name="limit" select="data/@limit"/></xsl:call-template>
                </div>
            </div>
            <input
                type="button"
                value="出力"
                class=" uk-button uk-button-primary uk-margin-small-right uk-hidden"
                onclick="SpiralTable.setDLFileName(this, {data/@limit});"
                id="exportButton"/>
            <p class="uk-text-danger uk-text-bold">$table:action_err$</p>
            <div class="no_print uk-margin">
                <input
                    id="smp-table-update-button"
                    class=" uk-button uk-button-primary uk-margin-small-right"
                    type="submit"
                    name="smp-table-submit-button"
                    value="更新"
                    onclick="return SpiralTable.confirmation({/table/@tableId}, this);"/>
                <input
                    id="smp-table-reset-button"
                    class=" uk-button uk-button-default uk-margin-small-right"
                    type="reset"
                    value="リセット"
                    onclick="SpiralTable.allReset({/table/@tableId});"/>
            </div>

            <xsl:apply-templates select="pager"/>
            <div class="uk-overflow-auto">
                <table
                    class="uk-table uk-table-hover uk-table-middle uk-table-divider uk-text-nowrap">
                    <thead>
                        <tr>
                            <th>
                                <input
                                    type="checkbox"
                                    class="uk-checkbox"
                                    onclick="SpiralTable.allCheck({/table/@tableId}, this)"
                                    onkeydown="return SpiralTable.keyCheck(event);"/>
                            </th>
                            <th>
                                <a href="{/table/fieldList/@idSort}">
                                    <xsl:text>ID</xsl:text>

                                    <xsl:call-template name="sortText">
                                        <xsl:with-param name="field" select="'id'"/>
                                    </xsl:call-template>
                                </a>
                            </th>
                            <th>
                                <a href="{/table/fieldList/field[@title='registrationTime']/@sort}">
                                    <xsl:text>入荷日時</xsl:text>

                                    <xsl:call-template name="sortText">
                                        <xsl:with-param name="field" select="/table/fieldList/field[@title='registrationTime']/@code" />
                                    </xsl:call-template>
                                </a>
                            </th>
                            <th>
                                <a href="{/table/fieldList/field[@title='receivingHId']/@sort}">
                                    <xsl:text>検収番号</xsl:text>

                                    <xsl:call-template name="sortText">
                                        <xsl:with-param name="field" select="/table/fieldList/field[@title='receivingHId']/@code" />
                                    </xsl:call-template>
                                </a>
                            </th>
                            <th>
                                <a href="{/table/fieldList/field[@title='orderHistoryId']/@sort}">
                                    <xsl:text>発注番号</xsl:text>

                                    <xsl:call-template name="sortText">
                                        <xsl:with-param name="field" select="/table/fieldList/field[@title='orderHistoryId']/@code" />
                                    </xsl:call-template>
                                </a>
                            </th>
                            <th>
                                <a href="{/table/fieldList/field[@title='divisionName']/@sort}">
                                    <xsl:text>入荷先部署</xsl:text>

                                    <xsl:call-template name="sortText">
                                        <xsl:with-param name="field" select="/table/fieldList/field[@title='divisionName']/@code" />
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
                                <a href="{/table/fieldList/field[@title='receivingCount']/@sort}">
                                    <xsl:text>入荷数</xsl:text>

                                    <xsl:call-template name="sortText">
                                        <xsl:with-param name="field" select="/table/fieldList/field[@title='receivingCount']/@code" />
                                    </xsl:call-template>
                                </a>
                            </th>
                            <th>
                                <a href="{/table/fieldList/field[@title='receivingPrice']/@sort}">
                                    <xsl:text>入荷金額</xsl:text>

                                    <xsl:call-template name="sortText">
                                        <xsl:with-param name="field" select="/table/fieldList/field[@title='receivingPrice']/@code" />
                                    </xsl:call-template>
                                </a>
                            </th>
                            <th>
                                <a href="{/table/fieldList/field[@title='adjAmount']/@sort}">
                                    <xsl:text>調整額</xsl:text>

                                    <xsl:call-template name="sortText">
                                        <xsl:with-param name="field" select="/table/fieldList/field[@title='adjAmount']/@code" />
                                    </xsl:call-template>
                                </a>
                            </th>
                            <th>
                                <a href="{/table/fieldList/field[@title='priceAfterAdj']/@sort}">
                                    <xsl:text>調整後金額</xsl:text>

                                    <xsl:call-template name="sortText">
                                        <xsl:with-param name="field" select="/table/fieldList/field[@title='priceAfterAdj']/@code" />
                                    </xsl:call-template>
                                </a>
                            </th>
                        </tr>
                    </thead>
                    <tbody>

                        <xsl:for-each select="data/record">

                            <xsl:variable name="row" select="position() + 3"/>

                            <xsl:variable
                                name="recordPosition"
                                select="position() + number(/table/pager/@offset_start) - 1"/>

                            <xsl:variable name="id" select="@id"/>
                            <tr>
                                <td>
                                    <input
                                        type="checkbox"
                                        name="smp-table-check-{/table/@tableId}"
                                        id="smp-table-check-{/table/@tableId}-{@id}"
                                        class="smp-table-check uk-checkbox"
                                        value="{@id}"
                                        onclick="SpiralTable.targetCheck({/table/@tableId}, {@id}, this.checked)"
                                        onkeydown="return SpiralTable.keyCheck(event);"/>
                                </td>
                                <td>
                                        <xsl:value-of select="@id"/>
                                </td>
                                <td>

                                    <xsl:value-of select="usr_registrationTime/full_text"/>
                                </td>
                                <td>

                                    <xsl:value-of select="usr_receivingHId"/>
                                </td>
                                <td>

                                    <xsl:value-of select="usr_orderHistoryId"/>
                                </td>
                                <td>

                                    <xsl:value-of select="usr_divisionName"/>
                                </td>
                                <td>

                                    <xsl:value-of select="usr_distributorName"/>
                                </td>
                                <td>

                                    <xsl:value-of select="usr_makerName"/>
                                </td>
                                <td>

                                    <xsl:value-of select="usr_itemName"/>
                                </td>
                                <td>

                                    <xsl:value-of select="usr_itemCode"/>
                                </td>
                                <td>

                                    <xsl:value-of select="usr_itemStandard"/>
                                </td>
                                <td>

                                    <xsl:value-of select="usr_receivingCount"/>
                                    <span class="uk-text-small"><xsl:value-of select="usr_itemUnit"/></span>
                                </td>
                                <td>

                                    <xsl:if test="usr_receivingPrice!= ''">
                                        ￥<xsl:value-of select="format-number(usr_receivingPrice, '###,##0.00')"/>
                                    </xsl:if>
                                </td>
                                <td>
                                    ￥<input
                                        type="number"
                                        step="0.01"
                                        style="text-align:left;width:100px;"
                                        name="smp-uf-{/table/fieldList/field[@title='adjAmount']/@code}-{@id}"
                                        onchange="SpiralTable.changeBC(this);"
                                        onfocus="SpiralTable.targetCheck({/table/@tableId},{@id});"
                                        onkeydown="return SpiralTable.keyCheck(event);"
                                        class="uk-input uk-text-right"
                                        value="{usr_adjAmount}">

                                        <xsl:if test="string(usr_adjAmount/@hasError) = 't'">

                                            <xsl:attribute name="class">smp-valid-err-input</xsl:attribute>
                                        </xsl:if>
                                    </input>
                                </td>
                                <td>

                                    <xsl:if test="usr_priceAfterAdj!= ''">
                                        ￥<xsl:value-of select="format-number(usr_priceAfterAdj, '###,##0.00')"/>
                                    </xsl:if>
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