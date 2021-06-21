<?xml version="1.0" encoding="EUC-JP" ?>
<xsl:stylesheet version="1.0" xmlns:xsl="http://www.w3.org/1999/XSL/Transform">
<xsl:output method="html" />
<xsl:template match="/searchForm">
  <div class="">
    <form method="get" class="uk-form-stacked">
      <xsl:if test="/searchForm/@action">
        <xsl:attribute name="action">
          <xsl:value-of select="/searchForm/@action" />
        </xsl:attribute>
      </xsl:if>
      <!-- hidden -->
      $hidden:sf:extension$
      <div class="uk-width-3-4@m uk-margin-auto">
        <h3>検索</h3>
        <xsl:apply-templates select="fieldList/sys_multiSearch" />
        <xsl:apply-templates select="fieldList/usr_divisionName" />
        <xsl:apply-templates select="fieldList/usr_notUsedFlag" />
        <div class="uk-text-center">
          <input class="uk-margin-top uk-button uk-button-default" type="submit" name="{searchForm/submit/@name}" value="検索" />
        </div>
      </div>
    </form>
  </div>
</xsl:template>

<!-- 複数フィールドの検索 -->
<xsl:template match="sys_multiSearch">
  <div class="uk-margin">
    <label class="uk-form-label" for="form-stacked-text">
      フリー検索
    </label>
    <div class="uk-form-controls">
      <xsl:for-each select="option/exType/select">
        <label class="uk-margin-small-right">
          <input class="uk-radio" type="radio" name="{../@name}" value="{@value}">
            <xsl:choose>
              <xsl:when test="not(../@selected) and @value = '17'">
                <xsl:attribute name="checked">t</xsl:attribute>
              </xsl:when>
              <xsl:when test="../@selected = @value">
                <xsl:attribute name="checked">t</xsl:attribute>
              </xsl:when>
            </xsl:choose>
          </input>
          <xsl:value-of select="." />
        </label>
      </xsl:for-each>
      <input type="text" class="uk-input" name="{main/@name}">
        <xsl:if test=".">
          <xsl:attribute name="value">
            <xsl:value-of select="main" />
          </xsl:attribute>
        </xsl:if>
      </input>
      <input type="hidden" name="{option/action/@name}" value="0" />
      <span class="uk-text-meta uk-text-small">対象：メーカー名 商品名 製品コード 規格
</span>
    </div>
  </div>
</xsl:template>

<!-- 部署名 -->
<xsl:template match="usr_divisionName">
  <div class="uk-margin">
    <label class="uk-form-label" for="form-stacked-text">
      部署名
    </label>
    <div class="uk-form-controls" id="divisionNameDiv">
            <input type="hidden" name="{option/exType/@name}" value="16" />
              <input type="hidden" class="uk-input" id="divisionName" name="{main/@name}">
                <xsl:if test=".">
                  <xsl:attribute name="value">
                    <xsl:value-of select="main" />
                  </xsl:attribute>
                </xsl:if>
              </input>
            <input type="hidden" name="{option/action/@name}" value="0" />
    </div>
  </div>
</xsl:template>

<xsl:template match="usr_notUsedFlag">
  <div class="uk-margin">
    <label class="uk-form-label" for="form-stacked-text">使用フラグ</label>
    <div class="uk-form-controls uk-child-width-1-2@m uk-grid">
      <div class="uk-margin-small-bottom uk-margin uk-grid-small uk-child-width-auto uk-grid">
      <xsl:for-each select="main/select">
                <div>
                  <xsl:if test="not(@newLine = 't')">
                    <xsl:attribute name="style">float:left;</xsl:attribute>
                  </xsl:if>
                  <label>
                    <span>
                      <input type="checkbox" name="{../@name}" value="{@value}" class="uk-checkbox uk-margin-small-right">
                        <xsl:if test="@selected = 't'">
                          <xsl:attribute name="checked">t</xsl:attribute>
                        </xsl:if>
                      </input><xsl:value-of select="." />
                    </span>
                  </label>
                </div>
              </xsl:for-each>
            <input type="hidden" name="{option/action/@name}" value="0" />
      </div>
    </div>
  </div>
</xsl:template>
</xsl:stylesheet>








