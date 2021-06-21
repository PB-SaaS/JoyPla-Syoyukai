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
        <h3>����</h3>
        <xsl:apply-templates select="fieldList/usr_registrationTime" />
        <xsl:apply-templates select="fieldList/usr_makerName" />
        <xsl:apply-templates select="fieldList/usr_itemName" />
        <xsl:apply-templates select="fieldList/usr_itemCode" />
        <xsl:apply-templates select="fieldList/usr_itemStandard" />
        <xsl:apply-templates select="fieldList/usr_sourceDivision" />
        <xsl:apply-templates select="fieldList/usr_targetDivision" />
        <div class="uk-text-center">
        <input type="submit" name="{searchForm/submit/@name}"  class="uk-margin-top uk-button uk-button-default" value="����" />
      </div>
      </div>
    </form>
  </div>
</xsl:template>


<!-- ��Ͽ���� -->
<xsl:template match="usr_registrationTime">
  <div class="uk-margin">
    <label class="uk-form-label" for="form-stacked-text">ʧ������</label>
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
      <span class="uk-text-bottom">����</span>
      </div>
      <div class="uk-margin-small-bottom">
      <input class="uk-input uk-width-3-4 uk-margin-small-right" type="date" name="{main/value2/@name}">
        <xsl:if test=".">
          <xsl:attribute name="value">
            <xsl:value-of select="main/value2" />
          </xsl:attribute>
        </xsl:if>
      </input>
      <span class="uk-text-bottom">�ޤ�</span>
      </div>
    </div>
    <input type="hidden" name="{option/action/@name}" value="0" />
  </div>
</xsl:template>


<!-- �᡼����̾ -->
<xsl:template match="usr_makerName">
  <div class="uk-margin">
    <label class="uk-form-label" for="form-stacked-text">
      �᡼����
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

<!-- ����̾ -->
<xsl:template match="usr_itemName">
  <div class="uk-margin">
    <label class="uk-form-label" for="form-stacked-text">
      ����̾
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

<!-- ���ʥ����� -->
<xsl:template match="usr_itemCode">
  <div class="uk-margin">
    <label class="uk-form-label" for="form-stacked-text">
      ���ʥ�����
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

<!-- ���ʵ��� -->
<xsl:template match="usr_itemStandard">
  <div class="uk-margin">
    <label class="uk-form-label" for="form-stacked-text">
      ����
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

<!-- ʧ�и�����̾ -->
<xsl:template match="usr_sourceDivision">
  <div class="uk-margin">
    <label class="uk-form-label" for="form-stacked-text">
      ʧ�и�����̾
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

<!-- ʧ��������̾ -->
<xsl:template match="usr_targetDivision">
  <div class="uk-margin">
    <label class="uk-form-label" for="form-stacked-text">
      ʧ��������̾
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


