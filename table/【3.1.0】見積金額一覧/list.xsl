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

    <!-- テキストエリア型の改行を反映するためのXSLテンプレート -->
    <xsl:template name="textareaHTML">

        <xsl:param name="content"/>
        <!-- 改行を保持させる -->

        <xsl:variable name="match">\n</xsl:variable>

        <xsl:choose>

            <xsl:when test="contains($content,$match)">

                <xsl:value-of select="substring-before($content,$match)"/>
                <br/>
                <!-- 残りを変換 -->

                <xsl:call-template name="textareaHTML">
                    <xsl:with-param name="content" select="substring-after($content,$match)"/>
                </xsl:call-template>
            </xsl:when>

            <xsl:otherwise>
                <xsl:value-of select="$content"/>
            </xsl:otherwise>
        </xsl:choose>
    </xsl:template>

    <xsl:template name="textareaContent">

        <xsl:param name="content"/>
        <!-- 改行を保持させる -->

        <xsl:variable name="match">\n</xsl:variable>

        <xsl:choose>

            <xsl:when test="contains($content,$match)">

                <xsl:value-of select="substring-before($content,$match)"/>
                <!-- 改行を表示 -->
                <xsl:text>
</xsl:text>

                <xsl:call-template name="textareaContent">
                    <xsl:with-param name="content" select="substring-after($content,$match)"/>
                </xsl:call-template>
            </xsl:when>

            <xsl:otherwise>
                <xsl:value-of select="$content"/>
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
                id="exportButton"
                value="ダウンロード"
                class="smp-table-button with-wrap uk-hidden"
                onclick="SpiralTable.setDLFileName(this, {data/@limit});"/>
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
                <input
                    id="smp-table-delete-button"
                    class=" uk-button uk-button-danger uk-margin-small-right"
                    type="submit"
                    name="smp-table-submit-button"
                    value="削除"
                    onclick="return SpiralTable.confirmation({/table/@tableId}, this);"/></div>

            <xsl:apply-templates select="pager"/>

            <div class="uk-overflow-auto">
                <table class="uk-table uk-table-striped uk-text-nowrap ">
                    <thead>
                        <tr>
                            <th class="uk-table-shrink">
                                <input
                                    type="checkbox"
                                    class="uk-checkbox"
                                    onclick="SpiralTable.allCheck({/table/@tableId}, this)"
                                    onkeydown="return SpiralTable.keyCheck(event);"/>

                            </th>
                            <th class="uk-table-shrink">
                                <a href="{/table/fieldList/@idSort}">
                                    <xsl:text>ID</xsl:text>

                                    <xsl:call-template name="sortText">
                                        <xsl:with-param name="field" select="'id'"/>
                                    </xsl:call-template>
                                </a>
                            </th>
                            <th class="uk-table-shrink">
                                <a href="{/table/fieldList/field[@title='priceId']/@sort}">
                                    <xsl:text>金額管理ID</xsl:text>

                                    <xsl:call-template name="sortText">
                                        <xsl:with-param name="field" select="/table/fieldList/field[@title='priceId']/@code" />
                                    </xsl:call-template>
                                </a>
                            </th>
                            <th>
                                <a href="{/table/fieldList/field[@title='requestFlg']/@sort}">
                                    <xsl:text>採用フラグ</xsl:text>

                                    <xsl:call-template name="sortText">
                                        <xsl:with-param name="field" select="/table/fieldList/field[@title='requestFlg']/@code" />
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
                                    <xsl:text>購買価格</xsl:text>

                                    <xsl:call-template name="sortText">
                                        <xsl:with-param name="field" select="/table/fieldList/field[@title='price']/@code" />
                                    </xsl:call-template>
                                </a>
                            </th>
                            <th>
                                <a href="{/table/fieldList/field[@title='notice']/@sort}">
                                    <xsl:text>特記事項</xsl:text>

                                    <xsl:call-template name="sortText">
                                        <xsl:with-param name="field" select="/table/fieldList/field[@title='notice']/@code" />
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
	                                        class="uk-checkbox smp-table-check"
	                                        value="{@id}"
	                                        onclick="SpiralTable.targetCheck({/table/@tableId}, {@id}, this.checked)"
	                                        onkeydown="return SpiralTable.keyCheck(event);"/>
                                	
                                </td>
                                <td>
                                    <xsl:value-of select="@id"/>
                                </td>
                                <td>
                                    <xsl:value-of select="usr_priceId"/>
                                </td>
                                <td>
                                    <select
                                        class="uk-select"
                                        name="smp-uf-{/table/fieldList/field[@title='requestFlg']/@code}-{@id}"
                                        onchange="SpiralTable.changeBC(this);"
                                        onfocus="SpiralTable.targetCheck({/table/@tableId},{@id});"
                                        style="font-size:10pt;color:#444444;">

                                        <xsl:if test="string(usr_requestFlg/@hasError) = 't'">

                                            <xsl:attribute name="class">smp-valid-err-input</xsl:attribute>
                                        </xsl:if>

                                        <xsl:variable name="selected" select="usr_requestFlg/@id"/>

                                        <xsl:for-each select="/table/fieldList/field[@title='requestFlg']/label">
                                            <option value="{@value}">

                                                <xsl:if test="string(@value) != ''">

                                                    <xsl:attribute name="title"><xsl:value-of select="."/></xsl:attribute>
                                                </xsl:if>

                                                <xsl:if test="@value = $selected">

                                                    <xsl:attribute name="selected">selected</xsl:attribute>
                                                </xsl:if>
                                                
                                                
                                                <xsl:if test="$selected = 3 and @value != 3">
                                                    <xsl:attribute name="disabled">disabled</xsl:attribute>
                                                </xsl:if>
                                                
                                                <xsl:if test="$selected != 3 and @value = 3">
                                                    <xsl:attribute name="disabled">disabled</xsl:attribute>
                                                </xsl:if>
                                                
                                                

                                                <xsl:value-of select="."/>
                                            </option>
                                        </xsl:for-each>
                                    </select>
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
                                    <xsl:value-of select="usr_itemJANCode"/>
                                </td>
                                <td>
                                    <xsl:value-of select="usr_distributorName"/>
                                </td>
                                <td>

                                    <xsl:value-of select="usr_quantity"/>

                                    <xsl:value-of select="usr_quantityUnit"/>
                                    /

                                    <xsl:value-of select="usr_itemUnit"/>
                                </td>
                                <td>

                                    <xsl:if test="usr_price!= ''">
                                        ￥<xsl:value-of select="format-number(usr_price, '###,##0.00')"/>
                                    </xsl:if>
                                </td>
                                <td>
                                    <textarea
                                        class="uk-textarea"
                                        name="smp-uf-{/table/fieldList/field[@title='notice']/@code}-{@id}"
                                        onchange="SpiralTable.changeBC(this);"
                                        onfocus="SpiralTable.targetCheck({/table/@tableId},{@id});"
                                        style="font-size:10pt;color:#444444;">

                                        <xsl:if test="string(usr_notice/@hasError) = 't'">

                                            <xsl:attribute name="class">smp-valid-err-input</xsl:attribute>
                                        </xsl:if>

                                        <xsl:call-template name="textareaContent"><xsl:with-param name="content" select="usr_notice"/></xsl:call-template>
                                    </textarea>
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