<?xml version="1.0" encoding="EUC-JP"?>

<xsl:stylesheet version="1.0" xmlns:xsl="http://www.w3.org/1999/XSL/Transform">

    <xsl:output method="html"/>

    <xsl:template match="/">
        <xsl:apply-templates select="table"/>
    </xsl:template>

    <!-- �����Ⱦ��֤Υƥ����� -->
    <xsl:template name="sortText">

        <xsl:param name="field"/>

        <xsl:variable name="appendSort" select="/table/data/@sort"/>

        <xsl:choose>

            <xsl:when test="$appendSort = concat($field, '_down')">
                <xsl:text> ��</xsl:text>
            </xsl:when>

            <xsl:when test="$appendSort = concat($field, '_up')">
                <xsl:text> ��</xsl:text>
            </xsl:when>
        </xsl:choose>
    </xsl:template>

    <!-- ɽ��������ڤ��ؤ� -->
    <xsl:template name="limiter">

        <xsl:param name="limit"/>
        <div class="uk-width-2-3">
            <select name="_limit_{/table/@tableId}" class=" uk-select">
                <option value="10">

                    <xsl:if test="$limit = '10'">

                        <xsl:attribute name="selected">selected</xsl:attribute>
                    </xsl:if>10��</option>
                <option value="50">

                    <xsl:if test="$limit = '50'">

                        <xsl:attribute name="selected">selected</xsl:attribute>
                    </xsl:if>50��</option>
                <option value="100">

                    <xsl:if test="$limit = '100'">

                        <xsl:attribute name="selected">selected</xsl:attribute>
                    </xsl:if>100��</option>
            </select>
        </div>
        <div class="uk-width-1-3">
            <input
                type="submit"
                name="smp-table-submit-button"
                class="uk-button uk-button-default"
                value="ɽ��"/>
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

    <!-- �ƥ����ȥ��ꥢ���β��Ԥ�ȿ�Ǥ��뤿���XSL�ƥ�ץ졼�� -->
    <xsl:template name="textareaHTML">

        <xsl:param name="content"/>
        <!-- ���Ԥ��ݻ������� -->

        <xsl:variable name="match">\n</xsl:variable>

        <xsl:choose>

            <xsl:when test="contains($content,$match)">

                <xsl:value-of select="substring-before($content,$match)"/>
                <br/>
                <!-- �Ĥ���Ѵ� -->

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
        <!-- ���Ԥ��ݻ������� -->

        <xsl:variable name="match">\n</xsl:variable>

        <xsl:choose>

            <xsl:when test="contains($content,$match)">

                <xsl:value-of select="substring-before($content,$match)"/>
                <!-- ���Ԥ�ɽ�� -->
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

    <!-- �ǡ�����ʬ -->
    <xsl:template match="/table">
    	<span class="uk-text-danger">���������ϤϺ��Ѥ��줿��ΰʳ����оݤǤ�</span>
        <script type="text/javascript" src="{@jsPath}" charset="{@jsEncode}"></script>
        <form method="post" action="{@action}">
            $hidden:table:extension$

            <div class="">
                <div class="uk-width-1-3@m">
                    <font class="smp-offset-start"><xsl:value-of select="pager/@offset_start"/></font>
                    -
                    <font class="smp-offset-end"><xsl:value-of select="pager/@offset_end"/></font>�� /
                    <font class="smp-count"><xsl:value-of select="data/@total"/></font>��
                </div>
                <div class="uk-width-1-3@m uk-grid">

                    <xsl:call-template name="limiter"><xsl:with-param name="limit" select="data/@limit"/></xsl:call-template>
                </div>
            </div>
            <input
                type="button"
                id="exportButton"
                value="���������"
                class="smp-table-button with-wrap uk-hidden"
                onclick="SpiralTable.setDLFileName(this, {data/@limit});"/>
            <p class="uk-text-danger uk-text-bold">$table:action_err$</p>

            <xsl:apply-templates select="pager"/>

            <div class="uk-overflow-auto">
                <table class="uk-table uk-table-striped uk-text-nowrap ">
                    <thead>
                        <tr>
                            <th class="uk-table-shrink">
                                <a href="{/table/fieldList/@idSort}">
                                    <xsl:text>ID</xsl:text>

                                    <xsl:call-template name="sortText">
                                        <xsl:with-param name="field" select="'id'"/>
                                    </xsl:call-template>
                                </a>
                            </th>
                            <th class="uk-table-shrink">
                                ��������
                            </th>
                            <th class="uk-table-shrink">
                                <a href="{/table/fieldList/field[@title='priceId']/@sort}">
                                    <xsl:text>��۴���ID</xsl:text>

                                    <xsl:call-template name="sortText">
                                        <xsl:with-param name="field" select="/table/fieldList/field[@title='priceId']/@code" />
                                    </xsl:call-template>
                                </a>
                            </th>
                            <th>
                                <a href="{/table/fieldList/field[@title='requestFlg']/@sort}">
                                    <xsl:text>���ѥե饰</xsl:text>

                                    <xsl:call-template name="sortText">
                                        <xsl:with-param name="field" select="/table/fieldList/field[@title='requestFlg']/@code" />
                                    </xsl:call-template>
                                </a>
                            </th>
                            <th>
                                <a href="{/table/fieldList/field[@title='makerName']/@sort}">
                                    <xsl:text>�᡼����</xsl:text>

                                    <xsl:call-template name="sortText">
                                        <xsl:with-param name="field" select="/table/fieldList/field[@title='makerName']/@code" />
                                    </xsl:call-template>
                                </a>
                            </th>
                            <th>
                                <a href="{/table/fieldList/field[@title='itemName']/@sort}">
                                    <xsl:text>����̾</xsl:text>

                                    <xsl:call-template name="sortText">
                                        <xsl:with-param name="field" select="/table/fieldList/field[@title='itemName']/@code" />
                                    </xsl:call-template>
                                </a>
                            </th>
                            <th>
                                <a href="{/table/fieldList/field[@title='itemCode']/@sort}">
                                    <xsl:text>���ʥ�����</xsl:text>

                                    <xsl:call-template name="sortText">
                                        <xsl:with-param name="field" select="/table/fieldList/field[@title='itemCode']/@code" />
                                    </xsl:call-template>
                                </a>
                            </th>
                            <th>
                                <a href="{/table/fieldList/field[@title='itemStandard']/@sort}">
                                    <xsl:text>����</xsl:text>

                                    <xsl:call-template name="sortText">
                                        <xsl:with-param name="field" select="/table/fieldList/field[@title='itemStandard']/@code" />
                                    </xsl:call-template>
                                </a>
                            </th>
                            <th>
                                <a href="{/table/fieldList/field[@title='itemJANCode']/@sort}">
                                    <xsl:text>JAN������</xsl:text>

                                    <xsl:call-template name="sortText">
                                        <xsl:with-param name="field" select="/table/fieldList/field[@title='itemJANCode']/@code" />
                                    </xsl:call-template>
                                </a>
                            </th>
                            <th>
                                <a href="{/table/fieldList/field[@title='distributorName']/@sort}">
                                    <xsl:text>���ȼ�</xsl:text>

                                    <xsl:call-template name="sortText">
                                        <xsl:with-param name="field" select="/table/fieldList/field[@title='distributorName']/@code" />
                                    </xsl:call-template>
                                </a>
                            </th>
                            <th>
                                <a href="{/table/fieldList/field[@title='quantity']/@sort}">
                                    <xsl:text>����</xsl:text>

                                    <xsl:call-template name="sortText">
                                        <xsl:with-param name="field" select="/table/fieldList/field[@title='quantity']/@code" />
                                    </xsl:call-template>
                                </a>
                            </th>
                            <th>
                                <a href="{/table/fieldList/field[@title='price']/@sort}">
                                    <xsl:text>�������</xsl:text>

                                    <xsl:call-template name="sortText">
                                        <xsl:with-param name="field" select="/table/fieldList/field[@title='price']/@code" />
                                    </xsl:call-template>
                                </a>
                            </th>
                            <th>
                                <a href="{/table/fieldList/field[@title='notice']/@sort}">
                                    <xsl:text>�õ�����</xsl:text>

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
                                    <xsl:value-of select="@id"/>
                                </td>
                                <td>
					          	<xsl:if test="usr_requestFlg/@id != '1'">
						            <a class="smp-cell-id" href="{/table/cardList/card[@title='page_169113'][@recordId=$id]}" target="_blank">
						              ��������
						            </a>
					            </xsl:if>
                                </td>
                                <td>
                                    <xsl:value-of select="usr_priceId"/>
                                </td>
                                <td>
					          	<xsl:if test="usr_requestFlg/@id = '1'">
					          		<span class="uk-label uk-label-success">
                                    <xsl:value-of select="usr_requestFlg"/>
                                    </span>
					            </xsl:if>
					          	<xsl:if test="usr_requestFlg/@id = '2'">
					          		<span class="uk-label uk-label-danger">
                                    <xsl:value-of select="usr_requestFlg"/>
                                    </span>
					            </xsl:if>
					          	<xsl:if test="usr_requestFlg/@id = '3'">
					          		<span class="uk-label uk-label-warning">
                                    <xsl:value-of select="usr_requestFlg"/>
                                    </span>
					            </xsl:if>
					          	<xsl:if test="usr_requestFlg/@id = '4'">
					          		<span class="uk-label uk-label-warning">
                                    <xsl:value-of select="usr_requestFlg"/>
                                    </span>
					            </xsl:if>
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
                                        ��<xsl:value-of select="format-number(usr_price, '###,##0.00')"/>
                                    </xsl:if>
                                </td>
                                <td>
            <xsl:call-template name="textareaHTML"><xsl:with-param name="content" select="usr_notice" /></xsl:call-template>

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