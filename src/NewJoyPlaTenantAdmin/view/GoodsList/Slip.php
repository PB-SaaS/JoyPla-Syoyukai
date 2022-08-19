<div class="uk-section uk-section-default uk-preserve-color animsition uk-padding-remove">
    <div class="uk-container uk-container-expand">
        <div uk-grid>
            <div class="uk-width-1-2@m">
                <h1>商品情報詳細</h1>
            </div>
            <div class="uk-width-1-2@m uk-text-right">
                <form action="<?php echo $form_url ?>" method="post" uk-margin>
                    <button class="uk-button uk-button-primary" type="submit" value="itemUpdate" name="Action">商品情報変更</button>
                    <button class="uk-button uk-button-primary" type="submit" value="priceReg" name="Action">金額情報登録</button>
                    <button class="uk-button uk-button-primary" type="submit" value="inHospitalItemReg" name="Action">院内商品登録</button>
                </form>
            </div>
        </div>
        <div>
            <ul class="uk-child-width-expand uk-tab" >
                <li class="<?php echo $switch_1 ?>"><a href="#" onclick="location.href='<?php echo $base_url ?>&table_cache=true'">基本情報</a></li>
                <li class="<?php echo $switch_2 ?>"><a href="#" onclick="location.href='<?php echo $base_url ?>&Switcher=logs'">登録変更履歴</a></li>
                <li class="<?php echo $switch_3 ?>"><a href="#" onclick="location.href='<?php echo $base_url ?>&Switcher=priceList'">金額情報一覧</a></li>
                <li class="<?php echo $switch_4 ?>"><a href="#" onclick="location.href='<?php echo $base_url ?>&Switcher=InHospitalItems'">院内商品情報</a></li>
            </ul>
        </div>
        <?php if($switch_1 != ""): ?>
        <table class="uk-table uk-table-divider">
            <tbody>
            <tr>
                <th>
                    登録日時
                </th>
                <td>
                    %val:usr:registrationTime%
                </td>
            </tr>
            <tr>
                <th>
                    更新日時
                </th>
                <td>
                    %val:usr:updateTime%
                </td>
            </tr>
            <tr>
                <th>
                    商品ID
                </th>
                <td>
                    %val:usr:itemId%
                </td>
            </tr>
            <tr>
                <th>
                    商品名
                </th>
                <td>
                    %val:usr:itemName%
                </td>
            </tr>
            <tr>
                <th>
                    製品コード
                </th>
                <td>
                    %val:usr:itemCode%
                </td>
            </tr>
            <tr>
                <th>
                    規格
                </th>
                <td>
                    %val:usr:itemStandard%
                </td>
            </tr>
            <tr>
                <th>
                    JANコード
                </th>
                <td>
                    %val:usr:itemJANCode%
                </td>
            </tr>
            <tr>
                <th>
                    分類
                </th>
                <td>
                    %val:usr:category%
                </td>
            </tr>
            <tr>
                <th>
                    小分類
                </th>
                <td>
                    %val:usr:smallCategory%
                </td>
            </tr>
            <tr>
                <th>
                    メーカー名
                </th>
                <td>
                    %val:usr:makerName%
                </td>
            </tr>
            <tr>
                <th>
                    カタログNo
                </th>
                <td>
                    %val:usr:catalogNo%
                </td>
            </tr>
            <tr>
                <th>
                    シリアルNo
                </th>
                <td>
                    %val:usr:serialNo%
                </td>
            </tr>
            <tr>
                <th>
                    定価
                </th>
                <td>
                    %val:usr:minPrice%
                </td>
            </tr>
            <tr>
                <th>
                    償還価格フラグ
                </th>
                <td>
                    %val:usr:officialFlag:v%
                </td>
            </tr>
            <tr>
                <th>
                    償還価格
                </th>
                <td>
                    %val:usr:officialprice%
                </td>
            </tr>
            <tr>
                <th>
                    入数
                </th>
                <td>
                    %val:usr:quantity%
                </td>
            </tr>
            <tr>
                <th>
                    入数単位
                </th>
                <td>
                    %val:usr:quantityUnit%
                </td>
            </tr>
            <tr>
                <th>
                    個数単位
                </th>
                <td>
                    %val:usr:itemUnit%
                </td>
            </tr>
            <tr>
                <th>
                    ロット管理フラグ
                </th>
                <td>
                    %val:usr:lotManagement:v%
                </td>
            </tr>
            </tbody>
            <tfoot>
                <tr></tr>
            </tfoot>
        </table>
        <?php elseif($switch_2 != ""): ?>
		<div class="uk-margin spiral_table_area" style="display:none">
			%sf:usr:search21:table:mstfilter%
		</div>
        <?php elseif($switch_3 != ""): ?>
		<div class="uk-margin spiral_table_area" style="display:none">
            <div>
                <button class="uk-button uk-button-primary" type="button" name="Action" uk-toggle="target: #price-select-bulk-update">選択した情報を一括更新する</button>
            </div>
            <!-- This is the modal -->
            <div id="price-select-bulk-update" uk-modal>
                <div class="uk-modal-dialog uk-modal-body">
                    <h2 class="uk-modal-title">選択した情報の一括更新</h2>
                    <dl class="cf">
                        <dt class="title">
                            単価
                        </dt>
                        <dd class="data real">
                            <input
                                class="uk-input"
                                type="number"
                                name="unitPrice"
                                value=""
                                maxlength="20">
                            <br>
                        </dd>
                    </dl>
                    <dl class="cf">
                        <dt class="title">
                            購買価格
                        </dt>
                        <dd class="data real">
                            <input
                                class="uk-input"
                                type="number"
                                name="price"
                                value=""
                                maxlength="20">
                            <br>
                        </dd>
                    </dl>
                    <p class="uk-text-right">
                        <button class="uk-button uk-button-primary" type="button" onclick="update()">更新</button>
                    </p>
                </div>
            </div>

            <script>
                function update() {
                    let elems = $('input[id^=smp-table-check-]:checked');

                    if( elems.length === 0)
                    {
                        return UIkit.modal.alert("情報を選択してください");
                    }

                    let ids = [];
                    elems.each(function(index, element){ 
                        ids.push(element.value);
                    });

                    UIkit.modal.confirm(elems.length + "件の更新を行います。よろしいですか").then(function () {
                        
                        loading();
                        $.ajax({
                            async: false,
                            url: "<?php echo $price_api_url ?>",
                            type:'POST',
                            data:{
                                _csrf: "<?php echo $csrf_token ?>",  // CSRFトークンを送信
                                Action : "bulkPriceUpdate",
                                ids : JSON.stringify(ids),
                                unitPrice : $('input[name="unitPrice"]')[0].value,
                                price : $('input[name="price"]')[0].value,
                            },
                            dataType: 'json'
                        })
                        // Ajaxリクエストが成功した時発動
                        .done( (data) => {
                            if(data.code != 0)
                            {
                                UIkit.modal.alert('更新に失敗しました');
                            }
                            UIkit.modal.alert('更新しました。').then(function(){
                                location.reload();
                            });
                        })
                        // Ajaxリクエストが失敗した時発動
                        .fail( (data) => {
                            UIkit.modal.alert('更新に失敗しました');
                        })
                        // Ajaxリクエストが成功・失敗どちらでも発動
                        .always( (data) => {
                            loading_remove();
                        });

                    }, function () {
                    });
                }
            </script>
			%sf:usr:search29:table:mstfilter%
		</div>
        <?php elseif($switch_4 !== ""): ?>
    	<div class="uk-margin spiral_table_area" style="display:none">
    		%sf:usr:search47:table:mstfilter%
    	</div>
        <?php endif ?>
        <script>
            let elem = $('table tbody a');
            let param = "ItemSlip";
            if(param != "")
            {
            	for(let index = 0 ; index < elem.length ; index++){
            		elem[index].href += "&BACK="+param;
            	};
            }
        </script>
    </div>
</div>
<script>
$(document).ready(function () {
    loading();
    setTimeout(function () {
        loading_remove()
    }, 1000);
});
function loading() {
    if ($("#loading").length == 0) {
        $(".animsition").before(
            '<div style="z-index: 1;position: fixed;" id="loading" class="uk-position-cover' +
            ' uk-overlay uk-overlay-default uk-flex uk-flex-center uk-flex-middle"><span uk' +
            '-spinner="ratio: 4.5" class="uk-icon uk-spinner"></span></div>'
        );
    }
}

function loading_remove() {
    if ($("#loading").length != 0) {
        $('.animsition').css({opacity: "1"});
        $('#loading').remove();
    }
}
</script>