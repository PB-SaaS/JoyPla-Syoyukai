<style>
    .itemInfo.table.uk-table td, .itemInfo.table.uk-table th {
        line-break: anywhere;
        min-width:120px;
    }
</style>
<div class="animsition uk-margin-bottom" uk-height-viewport="expand: true">
    <div class="uk-section uk-section-default uk-preserve-color uk-padding-remove uk-margin-top" id="page_top">
        <div class="uk-container uk-container-expand">
            <ul class="uk-breadcrumb no_print">
                <li><a href="%url/rel:mpg:top%">TOP</a></li>
                <li><a href="%url/rel:mpg:top%&path=product">商品メニュー</a></li>
                <li><a href="%url/rel:mpgt:Product%&Action=Item&table_cache=true">商品一覧</a></li>
                <li><span>商品情報詳細</span></li>
            </ul>
            <div class="no_print uk-margin" uk-margin>
                <input class="print_hidden uk-button uk-button-default" type="button" value="印刷プレビュー" onclick="window.print();return false;">


                <?php if ($tenantKind == "1" && $userInfo->isAdmin()): ?>
                <input class="print_hidden uk-button uk-button-primary" type="submit" value="商品情報変更" onclick="document.itemsChange.submit()">
                <form action="<?php echo $api_url ?>" method="post" name="itemsChange" class="uk-hidden">
                    <input type="hidden" value="itemChangeForm" name="Action">
                </form>
                <?php endif ?>
                <?php if($tenantKind == "1" && ($userInfo->isAdmin() || $userInfo->isApprover())): ?>
                <input class="print_hidden uk-button uk-button-primary" type="submit" value="金額情報登録" onclick="document.priceReg.submit()">
                <form action="<?php echo $api_url ?>" method="post" name="priceReg" class="uk-hidden">
                    <input type="hidden" value="priceRegist" name="Action">
                </form>
                <?php endif ?>
                <?php if($userInfo->isAdmin() || $userInfo->isApprover()): ?>

                <input class="print_hidden uk-button uk-button-primary" type="submit" value="院内商品として追加" onclick="inHPItemsRegConfirm()">
                <form action="<?php echo $api_url ?>"  method="post" name="inHPItemsReg" class="uk-hidden">
                    <input type="hidden" name="Action" value="inHospitalItemRegist">
                </form>
                <?php endif ?>
            </div>

            <div class="uk-width-1-1" uk-grid>
                <div class="uk-width-5-6@m uk-width-2-3">
                    <h2>商品情報詳細</h2>
                </div>
            </div>
            <div class="uk-width-4-5@m uk-margin-auto uk-margin-remove-top print-width-1-1 itemInfo">
                <table class="uk-table uk-table-divider uk-table-responsive">
                    <tr>
                        <td colspan="6">商品基本情報</td>
                    </tr>
                    <tr>
                        <th>商品ID</th>
                        <td>%val:usr:itemId%</td>
                        <th>JANコード</th>
                        <td colspan="3">%val:usr:itemJANCode%</td>
                    </tr>
                    <tr>
                        <th>メーカー名</th>
                        <td>%val:usr:makerName%</td>
                        <th>カタログNo</th>
                        <td>%val:usr:catalogNo%</td>
                        <th>シリアルNo</th>
                        <td>%val:usr:serialNo%</td>
                    </tr>
                    <tr>
                        <th>商品名</th>
                        <td>%val:usr:itemName%</td>
                        <th>製品コード</th>
                        <td>%val:usr:itemCode%</td>
                        <th>規格</th>
                        <td>%val:usr:itemStandard%</td>
                    </tr>
                    <tr>
                        <th>分類</th>
                        <td>%val:usr:category%</td>
                        <th>小分類</th>
                        <td>%val:usr:smallCategory%</td>
                        <th>ロット管理フラグ</th>
                        <td colspan="2">%val:usr:lotManagement:v%</td>
                    </tr>
                    <tr>
                        <th>償還価格フラグ</th>
                        <td>%val:usr:officialFlag:v%</td>
                        <th>償還価格</th>
                        <td>￥<script>price(fixed("%val:usr:officialprice%"))</script>
                        </td>
                        <th>旧償還価格</th>
                        <td>￥<script>price(fixed("%val:usr:officialpriceOld%"))</script>
                        </td>
                    </tr>
                    <tr>
                        <th>入数</th>
                        <td>%val:usr:quantity% %val:usr:quantityUnit%</td>
                        <th>個数単位</th>
                        <td>%val:usr:itemUnit%</td>
                        <th>定価</th>
                        <td>￥<script>price(fixed("%val:usr:minPrice%"))</script>
                        </td>
                    </tr>
                </table>
            </div>

            <div class="uk-width-1-1" uk-grid>
                <div class="uk-width-5-6@m uk-width-2-3">
                    <h2>金額情報</h2>
                </div>
            </div>
            <div class="uk-width-1-1 uk-margin-top price_list">
                %sf:usr:search36:table:mstfilter%
            </div>
            <script>
            	let elem = $('.price_list table tbody a');
            	for(let index = 0 ; index < elem.length ; index++){
            		elem[index].href += "&Action=itemSlip";
            	};
            </script>

            <div uk-grid>
                <div class="uk-width-3-4@m">
                    <h2>使用中の院内商品情報</h2>
                </div>
                <div class="uk-width-1-4@m no_print">
                    <ul uk-accordion class="uk-background-muted uk-padding-small">
                        <li>
                            <a class="uk-accordion-title" href="#">表示項目選択</a>
                            <div class="uk-accordion-content" hidden>
                                <a class="uk-button uk-button-secondary uk-button-small uk-width-1-1" href="#" onclick="item_detail.table_field_selector()">反映</a>
                                <ul class="uk-list uk-list-striped">

                                    <li>
                                        <div class="uk-margin uk-grid-small uk-child-width-auto uk-grid">
                                            <label><input class="uk-checkbox chk_01" type="checkbox"> id</label>
                                        </div>
                                    </li>
                                    <li>
                                        <div class="uk-margin uk-grid-small uk-child-width-auto uk-grid">
                                            <label><input class="uk-checkbox chk_02" type="checkbox"> 使用状況</label>
                                        </div>
                                    </li>
                                    <li>
                                        <div class="uk-margin uk-grid-small uk-child-width-auto uk-grid">
                                            <label><input class="uk-checkbox chk_03" type="checkbox"> 院内商品ID</label>
                                        </div>
                                    </li>
                                    <li>
                                        <div class="uk-margin uk-grid-small uk-child-width-auto uk-grid">
                                            <label><input class="uk-checkbox chk_04" type="checkbox"> 登録日時</label>
                                        </div>
                                    </li>
                                    <li>
                                        <div class="uk-margin uk-grid-small uk-child-width-auto uk-grid">
                                            <label><input class="uk-checkbox chk_05" type="checkbox"> 更新日時</label>
                                        </div>
                                    </li>
                                    <li>
                                        <div class="uk-margin uk-grid-small uk-child-width-auto uk-grid">
                                            <label><input class="uk-checkbox chk_06" type="checkbox"> 商品ID</label>
                                        </div>
                                    </li>
                                    <li>
                                        <div class="uk-margin uk-grid-small uk-child-width-auto uk-grid">
                                            <label><input class="uk-checkbox chk_07" type="checkbox"> メーカー</label>
                                        </div>
                                    </li>
                                    <li>
                                        <div class="uk-margin uk-grid-small uk-child-width-auto uk-grid">
                                            <label><input class="uk-checkbox chk_08" type="checkbox"> 分類</label>
                                        </div>
                                    </li>
                                    <li>
                                        <div class="uk-margin uk-grid-small uk-child-width-auto uk-grid">
                                            <label><input class="uk-checkbox chk_29" type="checkbox"> 小分類</label>
                                        </div>
                                    </li>
                                    <li>
                                        <div class="uk-margin uk-grid-small uk-child-width-auto uk-grid">
                                            <label><input class="uk-checkbox chk_09" type="checkbox"> 商品名</label>
                                        </div>
                                    </li>
                                    <li>
                                        <div class="uk-margin uk-grid-small uk-child-width-auto uk-grid">
                                            <label><input class="uk-checkbox chk_10" type="checkbox"> 製品コード</label>
                                        </div>
                                    </li>
                                    <li>
                                        <div class="uk-margin uk-grid-small uk-child-width-auto uk-grid">
                                            <label><input class="uk-checkbox chk_11" type="checkbox"> 規格</label>
                                        </div>
                                    </li>
                                    <li>
                                        <div class="uk-margin uk-grid-small uk-child-width-auto uk-grid">
                                            <label><input class="uk-checkbox chk_12" type="checkbox"> JANコード</label>
                                        </div>
                                    </li>
                                    <li>
                                        <div class="uk-margin uk-grid-small uk-child-width-auto uk-grid">
                                            <label><input class="uk-checkbox chk_13" type="checkbox"> カタログNO</label>
                                        </div>
                                    </li>
                                    <li>
                                        <div class="uk-margin uk-grid-small uk-child-width-auto uk-grid">
                                            <label><input class="uk-checkbox chk_14" type="checkbox"> シリアルNO</label>
                                        </div>
                                    </li>
                                    <li>
                                        <div class="uk-margin uk-grid-small uk-child-width-auto uk-grid">
                                            <label><input class="uk-checkbox chk_15" type="checkbox"> ロット管理フラグ</label>
                                        </div>
                                    </li>
                                    <li>
                                        <div class="uk-margin uk-grid-small uk-child-width-auto uk-grid">
                                            <label><input class="uk-checkbox chk_16" type="checkbox"> 保険請求分類（医科）</label>
                                        </div>
                                    </li>
                                    <li>
                                        <div class="uk-margin uk-grid-small uk-child-width-auto uk-grid">
                                            <label><input class="uk-checkbox chk_17" type="checkbox"> 保険請求分類（在宅）</label>
                                        </div>
                                    </li>
                                    <li>
                                        <div class="uk-margin uk-grid-small uk-child-width-auto uk-grid">
                                            <label><input class="uk-checkbox chk_18" type="checkbox"> 入数</label>
                                        </div>
                                    </li>
                                    <li>
                                        <div class="uk-margin uk-grid-small uk-child-width-auto uk-grid">
                                            <label><input class="uk-checkbox chk_19" type="checkbox"> 入数単位</label>
                                        </div>
                                    </li>
                                    <li>
                                        <div class="uk-margin uk-grid-small uk-child-width-auto uk-grid">
                                            <label><input class="uk-checkbox chk_20" type="checkbox"> 個数単位</label>
                                        </div>
                                    </li>
                                    <li>
                                        <div class="uk-margin uk-grid-small uk-child-width-auto uk-grid">
                                            <label><input class="uk-checkbox chk_21" type="checkbox"> 購買価格</label>
                                        </div>
                                    </li>
                                    <li>
                                        <div class="uk-margin uk-grid-small uk-child-width-auto uk-grid">
                                            <label><input class="uk-checkbox chk_22" type="checkbox"> 単価</label>
                                        </div>
                                    </li>
                                    <li>
                                        <div class="uk-margin uk-grid-small uk-child-width-auto uk-grid">
                                            <label><input class="uk-checkbox chk_23" type="checkbox"> 院内在庫数</label>
                                        </div>
                                    </li>
                                    <li>
                                        <div class="uk-margin uk-grid-small uk-child-width-auto uk-grid">
                                            <label><input class="uk-checkbox chk_24" type="checkbox"> 償還フラグ</label>
                                        </div>
                                    </li>
                                    <li>
                                        <div class="uk-margin uk-grid-small uk-child-width-auto uk-grid">
                                            <label><input class="uk-checkbox chk_25" type="checkbox"> 償還価格</label>
                                        </div>
                                    </li>
                                    <li>
                                        <div class="uk-margin uk-grid-small uk-child-width-auto uk-grid">
                                            <label><input class="uk-checkbox chk_26" type="checkbox"> 旧償還価格</label>
                                        </div>
                                    </li>
                                    <li>
                                        <div class="uk-margin uk-grid-small uk-child-width-auto uk-grid">
                                            <label><input class="uk-checkbox chk_27" type="checkbox"> 卸業者</label>
                                        </div>
                                    </li>
                                    <li>
                                        <div class="uk-margin uk-grid-small uk-child-width-auto uk-grid">
                                            <label><input class="uk-checkbox chk_28" type="checkbox"> 購買価格</label>
                                        </div>
                                    </li>
                                </ul>
                            </div>
                        </li>
                    </ul>
                </div>
            </div>

            <div class="uk-width-1-1 uk-margin-top" id="inHP">
                %sf:usr:search23:mstfilter:table%
            </div>

        </div>
    </div>
</div>
<script>
class Item
{
    constructor()
    {
        let storage = JSON.parse(localStorage.getItem("joypla_inHpItemsList"));
        let dispObj = {};
        if (!storage) {
            for (let i = 1 ; i <= 29 ; i++) {
                dispObj[i] = true;
            }
        } else {
            dispObj = storage;
        }
        this.dispSet(dispObj);
    }

    table_field_selector() {
        let count = $("input[class^='uk-checkbox chk_']").length;
        let disp = {};
        for (let i = 1; i <= count; i++) {
            disp[i] = false;
            if ($("input.chk_" + ("00" + i).slice(-2)).is(":checked")) {
                disp[i] = true;
            }
        }
        localStorage.setItem("joypla_inHpItemsList", JSON.stringify(disp));
        location.reload();
    }

    dispSet(settingObj) {
        Object.keys(settingObj).forEach(function(key) {
            $(".chk_" + ("00" + key).slice(-2)).prop("checked", settingObj[key]);
            if (settingObj[key]) {
                $(".f_" + ("00" + key).slice(-2)).show();
            } else {
                $(".f_" + ("00" + key).slice(-2)).hide();
            }
         });
    }

}

let item_detail = new Item();
let is_inHPItemsRegChecked = false;
function inHPItemsRegConfirm()
{
    if($("#inHP .smp-row-data").length > 0 && is_inHPItemsRegChecked === false){
        UIkit.modal.confirm('院内商品情報は登録済みです。追加で登録しますか？').then(function(){
            console.log("登録する");
            document.inHPItemsReg.submit();
        }, function(){
            console.log("登録しない");
        });
        is_inHPItemsRegChecked = true;
    }else{
        document.inHPItemsReg.submit()
    }
}
</script>