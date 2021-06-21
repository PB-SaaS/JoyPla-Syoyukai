<?xml version="1.0" encoding="EUC-JP" ?>
<xsl:stylesheet version="1.0" xmlns:xsl="http://www.w3.org/1999/XSL/Transform">
<xsl:output method="html" />
<xsl:template match="/searchForm">
  <div>
    <form method="get">
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
        <xsl:apply-templates select="fieldList/usr_makerName" />
        <xsl:apply-templates select="fieldList/usr_itemName" />
        <xsl:apply-templates select="fieldList/usr_itemCode" />
        <xsl:apply-templates select="fieldList/usr_itemStandard" />
        <xsl:apply-templates select="fieldList/usr_sourceDivision" />
        <xsl:apply-templates select="fieldList/usr_targetDivision" />
        <div class="uk-text-center">
        <input type="submit" name="{searchForm/submit/@name}"  class="uk-margin-top uk-button uk-button-default" value="検索" />
      </div>
      </div>
    </form>
  </div>
</xsl:template>


<!-- 登録日時 -->
<xsl:template match="usr_registrationTime">
  <div class="uk-margin">
    <label class="uk-form-label" for="form-stacked-text">払出日時</label>
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


<!-- メーカー名 -->
<xsl:template match="usr_makerName">
  <div class="uk-margin">
    <label class="uk-form-label" for="form-stacked-text">
      メーカー
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

<!-- 商品名 -->
<xsl:template match="usr_itemName">
  <div class="uk-margin">
    <label class="uk-form-label" for="form-stacked-text">
      商品名
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

<!-- 商品コード -->
<xsl:template match="usr_itemCode">
  <div class="uk-margin">
    <label class="uk-form-label" for="form-stacked-text">
      製品コード
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

<!-- 商品規格 -->
<xsl:template match="usr_itemStandard">
  <div class="uk-margin">
    <label class="uk-form-label" for="form-stacked-text">
      規格
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

<!-- 払出元部署名 -->
<xsl:template match="usr_sourceDivision">
  <div class="uk-margin">
    <label class="uk-form-label" for="form-stacked-text">
      払出元部署名
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

<!-- 払出先部署名 -->
<xsl:template match="usr_targetDivision">
  <div class="uk-margin">
    <label class="uk-form-label" for="form-stacked-text">
      払出先部署名
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


