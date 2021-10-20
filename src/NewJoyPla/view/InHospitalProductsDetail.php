<style>
    table.uk-table td, table.uk-table th {
        line-break: anywhere;  
        min-width:120px;
    }
</style>
<div class="animsition" uk-height-viewport="expand: true">
    <div class="uk-section uk-section-default uk-preserve-color uk-padding-remove uk-margin-top" id="page_top">
        <div class="uk-container uk-container-expand">
            <ul class="uk-breadcrumb no_print">
                <li><a href="%url/rel:mpg:top%">TOP</a></li>
                <li><a href="%url/rel:mpgt:Product%&Action=InHospitalItem&table_cache=true">院内商品マスタ</a></li>
                <li><span>院内商品情報</span></li>
            </ul>
            <div class="no_print uk-margin">
                <input class="print_hidden uk-button uk-button-default" type="submit" value="印刷プレビュー" onclick="window.print();return false;">

            <?php if ($userInfo->getUserPermission() == "1"): ?>

                <input class="print_hidden uk-button uk-button-primary" type="submit" value="院内商品情報変更" onclick="document.itemsChange.submit()">
                <form action="/regist/is" method="post" name="itemsChange" target="_blank" class="uk-hidden">
                    %SMPAREA%
                    <input type="hidden" name="SMPFORM" value="%smpform:330_inHpItemsC%">
                    <input type="hidden" name="hospitalId" value="%val:usr:hospitalId%">
                    <input type="hidden" name="makerName" value="%val:usr:makerName%">
                    <input type="hidden" name="itemId" value="%val:usr:itemId%">
                    <input type="hidden" name="itemName" value="%val:usr:itemName%">
                    <input type="hidden" name="itemCode" value="%val:usr:itemCode%">
                    <input type="hidden" name="itemStandard" value="%val:usr:itemStandard%">
                    <input type="hidden" name="itemJANCode" value="%val:usr:itemJANCode%">
                    <input type="hidden" name="price" value="%val:usr:price%">
                    <input type="hidden" name="inHospitalItemId" value="%val:usr:inHospitalItemId%">
                    <input type="hidden" name="authKey" value="%val:usr:authKey%">

                    <input type="hidden" name="notUsedFlag" value="%val:usr:notUsedFlag%">
                    <input type="hidden" name="distributorId" value="%val:usr:distributorId%">
                    <input type="hidden" name="catalogNo" value="%val:usr:catalogNo%">
                    <input type="hidden" name="serialNo" value="%val:usr:serialNo%">
                    <input type="hidden" name="quantity" value="%val:usr:quantity%">
                    <input type="hidden" name="quantityUnit" value="%val:usr:quantityUnit%">
                    <input type="hidden" name="itemUnit" value="%val:usr:itemUnit%">
                    <input type="hidden" name="price" value="%val:usr:price%">
                    <input type="hidden" name="priceId" value="%val:usr:priceId%">
                    <input type="hidden" name="medicineCategory" value="%val:usr:medicineCategory%">
                    <input type="hidden" name="homeCategory" value="%val:usr:homeCategory%">
                    <input type="hidden" name="HPstock" value="%val:usr:HPstock%">
                    <input type="hidden" name="notice" value="%val:usr:notice%">
                    <input type="hidden" name="unitPrice" value="%val:usr:unitPrice%">
                    <input type="hidden" name="measuringInst" value="%val:usr:measuringInst%">
                    <input type="hidden" name="oldPrice" value="%val:usr:price%">
                    <input type="hidden" name="oldUnitPrice" value="%val:usr:unitPrice%">
                </form>

            <?php endif ?>

                <a class="print_hidden uk-button uk-button-primary" href="#modal-label" uk-toggle>ラベル発行</a>
            </div>
            <div class="uk-width-1-1" uk-grid>
                <div class="uk-width-5-6@m uk-width-2-3">
                    <h2>院内商品情報</h2>
                </div>
            </div>
            <div class="uk-width-4-5@m uk-margin-auto uk-margin-remove-top">
                <table class="uk-table uk-table-divider uk-table-responsive">
                    <tr>
                        <td colspan="6">院内商品情報</td>
                    </tr>
                    <tr>
                        <th>不使用フラグ</th>
                        <td><span class="uk-label" id="notUsedFlag">%val:usr:notUsedFlag:v%</span></td>
                        <th>商品ID</th>
                        <td>%val:usr:itemId%</td>
                        <th>院内商品ID</th>
                        <td>%val:usr:inHospitalItemId%</td>
                    </tr>
                    <tr>
                        <th>JANコード</th>
                        <td>%val:usr:itemJANCode%</td>
                        <th>メーカー名</th>
                        <td colspan="3">%val:usr:makerName%</td>
                    </tr>
                    <tr>
                        <th>商品名</th>
                        <td colspan="5">%val:usr:itemName%</td>
                    </tr>
                    <tr>
                        <th>製品コード</th>
                        <td>%val:usr:itemCode%</td>
                        <th>規格</th>
                        <td colspan="3">%val:usr:itemStandard%</td>
                    </tr>
                    <tr>
                        <th>カタログNo</th>
                        <td>%val:usr:catalogNo%</td>
                        <th>シリアルNo</th>
                        <td colspan="3">%val:usr:serialNo%</td>
                    </tr>
                    <tr>
                        <th>ロット管理フラグ</th>
                        <td colspan="5">%val:usr:lotManagement:v%</td>
                    </tr>
                    <tr>
                        <th>保険請求分類（医科）</th>
                        <td>%val:usr:medicineCategory%</td>
                        <th>保険請求分類（在宅）</th>
                        <td colspan="3">%val:usr:homeCategory%</td>
                    </tr>
                    <tr>
                        <th>測定機器名</th>
                        <td colspan="5">%val:usr:measuringInst%</td>
                    </tr>
                    <tr>
                        <th>償還価格フラグ</th>
                        <td>%val:usr:officialFlag:v%</td>
                        <th>償還価格</th>
                        <td>
                            <script>price(fixed("%val:usr:officialprice%"))</script>円
                        </td>
                        <th>旧償還価格</th>
                        <td>
                            <script>price(fixed("%val:usr:officialpriceOld%"))</script>円
                        </td>
                    </tr>
                    <tr>
                        <th>卸業者</th>
                        <td colspan="5">%val:usr:distributorName%</td>
                    </tr>
                    <tr>
                        <th>入数</th>
                        <td>%val:usr:quantity% %val:usr:quantityUnit% / 1 %val:usr:itemUnit%</td>
                        <th>個数単位</th>
                        <td>%val:usr:itemUnit%</td>
                        <th>価格</th>
                        <td>￥<script>price(fixed("%val:usr:price%"))</script> / %val:usr:itemUnit%</td>
                        <!--
                      <th>旧価格</th>
                      <td><script>price(fixed("%val:usr:oldPrice%"))</script>円</td>
                      -->
                    </tr>
                    <tr>
                        <th>単価</th>
                        <td colspan="5">￥<script>price(fixed("%val:usr:unitPrice%"))</script>
                        </td>
                    </tr>
                    <!--
                  <tr>
                      <th>保険請求分類（医科）</th>
                      <td>%val:usr:medicineCategory%</td>
                      <th>保険請求分類（在宅）</th>
                      <td colspan="3">%val:usr:homeCategory%</td>
                  </tr>
                      -->
                    <tr>
                        <th class="uk-text-top">特記事項</th>
                        <td colspan="5">%val:usr:notice:br%</td>
                    </tr>
                </table>
            </div>

        </div>
    </div>
</div>
<div id="modal-label" uk-modal>
    <div class="uk-modal-dialog">
        <button class="uk-modal-close-default" type="button" uk-close></button>
        <div class="uk-modal-header">
            <h2 class="uk-modal-title">ラベル発行</h2>
        </div>
        <form action="%url/rel:@mpgt:createLabel%" target="_blank" method="post" class="uk-form-horizontal">
            <div class="uk-modal-body">
                <div class="uk-margin">
                    <label class="uk-form-label">入数指定</label>
                    <div class="uk-form-controls">
                        <input class="uk-input" type="number" value="%val:usr:quantity%" step="1" min="0" name="quantity">
                    </div>
                </div>
                <hr>
                <div class="uk-margin">
                    <label class="uk-form-label">印刷枚数</label>
                    <div class="uk-form-controls">
                        <input class="uk-input" type="number" step="1" value="1" min="0" name="printCount">
                    </div>
                </div>
                <hr>
                <div class="uk-margin">
                    <div class="uk-form-label">JANコード</div>
                    <div class="uk-form-controls uk-form-controls-text">
                        <span>%val:usr:itemJANCode%</span>
                    </div>
                </div>
                <hr>
                <div class="uk-margin">
                    <div class="uk-form-label">メーカー名</div>
                    <div class="uk-form-controls uk-form-controls-text">
                        <span>%val:usr:makerName%</span>
                    </div>
                </div>
                <hr>
                <div class="uk-margin">
                    <div class="uk-form-label">商品名</div>
                    <div class="uk-form-controls uk-form-controls-text">
                        <span>%val:usr:itemName%</span>
                    </div>
                </div>
                <hr>
                <div class="uk-margin">
                    <div class="uk-form-label">製品コード</div>
                    <div class="uk-form-controls uk-form-controls-text">
                        <span>%val:usr:itemCode%</span>
                    </div>
                </div>
                <hr>
                <div class="uk-margin">
                    <div class="uk-form-label">規格</div>
                    <div class="uk-form-controls uk-form-controls-text">
                        <span>%val:usr:itemStandard%</span>
                    </div>
                </div>
            </div>
            <div class="uk-modal-footer uk-text-right">
                <button class="uk-button uk-button-default uk-modal-close" type="button">閉じる</button>
                <input class="print_hidden uk-button uk-button-primary" type="submit" value="ラベル発行" onclick='return inHP_detail.createLabel("%val:usr:inHospitalItemId%")'>
                <input type="hidden" value="" name="itemsData" id="itemsData">
                <input type="hidden" value="%val:usr:distributorName%" name="distributorName">
            </div>
        </form>
    </div>
</div>
<script>
class InHospitalItemDetail
{
    constructor()
    {
        let label = "";
        let notUsedFlag = "%val:usr:notUsedFlag:id%";
        if (notUsedFlag == "0") {
            label = "uk-label-success";
        } else {
            label = "uk-label-danger";
        }
    
        $("#notUsedFlag").addClass(label);

        let itemsToJs = {
            "%val:usr:inHospitalItemId%":{
            "receivingCount":"0",
            "quantity":"%val:usr:quantity%",
            "makerName":"%val:usr:makerName%",
            "itemName":"%val:usr:itemName%",
            "itemCode":"%val:usr:itemCode%",
            "itemStandard":"%val:usr:itemStandard%",
            "quantityUnit":"%val:usr:quantityUnit%",
            "itemUnit":"%val:usr:itemUnit%",
            "itemJANCode":"%val:usr:itemJANCode%",
            "totalReturnCount":"0",
            "labelId":"%val:usr:labelId%",
            }
        };
    }

    createLabel(inHospitalItemId)
    {
        if (!this.setVal())
        {
            UIkit.modal.alert('入力値に不正があります。<br>ご確認ください');
            return false;
        }
        this.itemsToJs[inHospitalItemId].receivingCount = $('input[name="printCount"]').val();
        this.itemsToJs[inHospitalItemId].quantity = $('input[name="quantity"]').val();
      
        $("#itemsData").val(JSON.stringify( objectValueToURIencode(this.itemsToJs) ));
        return true;
    }

    setVal()
    {
        $('input[name="printCount"]').removeClass('uk-form-danger');
        $('input[name="quantity"]').removeClass('uk-form-danger');
      
        let flg = true;
        if ($('input[name="printCount"]').val() == '' || $('input[name="printCount"]').val() < 1 )
        {
            $('input[name="printCount"]').addClass('uk-form-danger');
            flg = false;
        }
        if ($('input[name="quantity"]').val() == '' || $('input[name="quantity"]').val() < 1 )
        {
            $('input[name="quantity"]').addClass('uk-form-danger');
            flg = false;
        }
        return flg;
    }
}

let inHP_detail = new InHospitalItemDetail();
</script>