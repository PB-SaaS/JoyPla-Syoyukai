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
        <xsl:apply-templates select="fieldList/usr_registrationTime" />
        <xsl:apply-templates select="fieldList/usr_payoutHistoryId" />
        <div class="uk-child-width-1-2 uk-grid">
        <xsl:apply-templates select="fieldList/usr_sourceDivision" />
        <xsl:apply-templates select="fieldList/usr_targetDivision" />
        </div>
        <div class="uk-text-center">
          <input class="uk-margin-top uk-button uk-button-default" type="submit" name="{searchForm/submit/@name}" value="検索" />
        </div>
      </div>
    </form>
  </div>
</xsl:template>

<!-- 登録日時 -->
<xsl:template match="usr_registrationTime">
  <div class="uk-margin">
    <label class="uk-form-label" for="form-stacked-text">入庫日時</label>
    <input type="hidden" name="{option/exType/@name}" value="16" />
    <div class="uk-form-controls uk-child-width-1-2@m uk-grid">
      <div class="uk-margin-small-bottom">
      <input class="uk-input uk-width-3-4 uk-margin-small-right" type="date" name="{main/value1/@name}">
        <xsl:if test=".">
          <xsl:attribute name="value">
            <xsl:value-of select="main/value1" />
          </xsl:attribute>
        </xsl:if>
      </input>
      <span class="uk-text-bottom">から</span>
      </div>
      <div class="uk-margin-small-bottom">
      <input class="uk-input uk-width-3-4 uk-margin-small-right" type="date" name="{main/value2/@name}">
        <xsl:if test=".">
          <xsl:attribute name="value">
            <xsl:value-of select="main/value2" />
          </xsl:attribute>
        </xsl:if>
      </input>
      <span class="uk-text-bottom">まで</span>
      </div>
    </div>
    <input type="hidden" name="{option/action/@name}" value="0" />
  </div>
</xsl:template>


<!-- 払出履歴ID -->
<xsl:template match="usr_payoutHistoryId">
  <div class="uk-margin">
    <label class="uk-form-label" for="form-stacked-text">
      払出番号
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
    </div>
  </div>
</xsl:template>


<xsl:template match="usr_sourceDivision">
  <div>
    <label class="uk-form-label" for="form-stacked-text">
      払出元部署名
    </label>
    <div class="uk-form-controls">
            <input type="hidden" name="{option/exType/@name}" value="16" />
              <input type="text" class="uk-input" name="{main/@name}">
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


<xsl:template match="usr_targetDivision">
  <div>
    <label class="uk-form-label" for="form-stacked-text">
      払出先部署名
    </label>
    <div class="uk-form-controls">
            <input type="hidden" name="{option/exType/@name}" value="16" />
              <input type="text" class="uk-input" name="{main/@name}">
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


</xsl:stylesheet>












