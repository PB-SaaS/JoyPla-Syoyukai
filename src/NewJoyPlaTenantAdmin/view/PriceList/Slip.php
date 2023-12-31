<div class="uk-section uk-section-default uk-preserve-color uk-padding-remove">
    <div class="uk-container uk-container-expand">
        <div uk-grid>
            <div class="uk-width-1-2@m">
                <h1>金額情報詳細</h1>
            </div>
            <div class="uk-width-1-2@m uk-text-right">
                <form action="<?php echo $form_url ?>" method="post">
                    <input type="hidden" name="distributorId" value="%val:usr:distributorId%">
                    <button class="uk-button uk-button-primary" type="submit" value="update" name="Action">金額情報変更</button>
                    <button class="uk-button uk-button-primary" type="submit" value="registInHospitalItem" name="Action">院内商品登録</button>
                </form>
            </div>
        </div>
        <div>
            <ul class="uk-child-width-expand uk-tab" >
                <li class="<?php echo $switch_1 ?>"><a href="#" onclick="location.href='<?php echo $base_url ?>&table_cache=true'">基本情報</a></li>
                <li class="<?php echo $switch_2 ?>"><a href="#" onclick="location.href='<?php echo $base_url ?>&Switcher=InHospitalItem'">使用中の院内商品</a></li>
            </ul>
        </div>
        <?php if($switch_1 !== ""): ?>
        <table class="uk-table uk-table-divider">
            <tbody>
                <tr>
                    <th>
                        金額管理ID
                    </th>
                    <td>
                        %val:usr:priceId%
                    </td>
                </tr>
                <tr>
                    <th>
                        病院名
                    </th>
                    <td>
                        %val:usr:hospitalName%
                    </td>
                </tr>
                <tr>
                    <th>
                        卸業者名
                    </th>
                    <td>
                        %val:usr:distributorName%
                    </td>
                </tr>
                <tr>
                    <th>
                        不使用フラグ
                    </th>
                    <td>
                        %val:usr:notUsedFlag:v%
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
                        メーカー名
                    </th>
                    <td>
                        %val:usr:makerName%
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
                        購買価格
                    </th>
                    <td>
                        %val:usr:price%
                    </td>
                </tr>
                <tr>
                    <th>
                        特記事項
                    </th>
                    <td>
                        %val:usr:notice:br%
                    </td>
                </tr>
            </tbody>
            <tfoot>
                <tr></tr>
            </tfoot>
        </table>
        <?php elseif($switch_2 !== ""): ?>
    	<div class="uk-margin spiral_table_area" style="display:none">
    		%sf:usr:search47:table:mstfilter%
    	</div>
        <?php endif ?>
        <script>
            let elem = $('table tbody a');
            let param = "<?php echo $back_key ?>";
            if(param != "")
            {
            	for(let index = 0 ; index < elem.length ; index++){
            		elem[index].href += "&BACK="+param;
            	};
            }
        </script>
    </div>
</div>