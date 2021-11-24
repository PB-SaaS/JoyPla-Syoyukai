<div class="uk-section uk-section-default uk-preserve-color uk-padding-remove uk-margin-top">
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