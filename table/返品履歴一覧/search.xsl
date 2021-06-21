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
        <xsl:apply-templates select="fieldList/usr_receivingHId" />
        <xsl:apply-templates select="fieldList/usr_returnHistoryID" />
        <div class="uk-text-center">
          <input class="uk-margin-top uk-button uk-button-default" type="submit" name="{searchForm/submit/@name}" value="検索" />
        </div>
      </div>
    </form>
  </div>
</xsl:template>

<!-- 返品日時 -->
<xsl:template match="usr_registrationTime">
  <div class="uk-margin">
    <label class="uk-form-label" for="form-stacked-text">
      返品日時
    </label>
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

<!-- 入庫履歴ID -->
<xsl:template match="usr_receivingHId">
  <div class="uk-margin">
    <label class="uk-form-label" for="form-stacked-text">
      入庫履歴ID
    </label>
    <div class="uk-form-controls">
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

<!-- 返品履歴ID -->
<xsl:template match="usr_returnHistoryID">
  <div class="uk-margin">
    <label class="uk-form-label" for="form-stacked-text">
      返品履歴ID
    </label>
    <div class="uk-form-controls">
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


