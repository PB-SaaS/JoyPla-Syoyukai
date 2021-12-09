<div class="uk-section uk-section-default uk-preserve-color uk-padding-remove">
    <div class="uk-container uk-container-expand">
        <div uk-grid>
            <div class="uk-width-1-2@m">
                <h1>商品登録更新詳細</h1>
            </div>
        </div>
        <table class="uk-table uk-table-divider">
            <thead>
                <tr>
                    <th>
                        登録変更日時
                    </th>
                    <td>
                        %val:usr:registrationTime%
                    </td>
                </tr>
                <tr>
                    <th>
                        商品ID
                    </th>
                    <td>
                        <script>
                            val = "%val:usr:itemId%";
                            if(val === "")
                            {
                                document.write('新規登録');
                            }
                            else
                            {
                                document.write('%val:usr:itemId%');
                            }
                        </script>
                        
                    </td>
                </tr>
                <tr>
                    <th>
                        変更内容
                    </th>
                    <td>
                        %val:usr:updateText:br%
                    </td>
                </tr>
                <tr>
                    <th></th>
                    <th>変更前</th>
                    <th>
                        <span uk-icon="arrow-right"></span>
                    </th>
                    <th>変更後</th>
                    <th class="uk-table-shrink"></th>
                </tr>
            <tbody>
                <tr>
                    <th>
                        メーカー名
                    </th>
                    <td>
                        %val:usr:o_makerName%
                    </td>
                    <td>
                        <span uk-icon="arrow-right"></span>
                    </td>
                    <td>
                        %val:usr:makerName%
                    </td>
                    <td>
                        <script>
                            old = "%val:usr:o_makerName%";
                            val = "%val:usr:makerName%";
                            if(old === val)
                            {
                                document.write('<span class="uk-label uk-label-success">変更なし</span>');
                            }
                            else
                            {
                                document.write('<span class="uk-label uk-label-warning">変更あり</span>');
                            }
                        </script>
                    </td>
                </tr>
                <tr>
                    <th>
                        商品名
                    </th>
                    <td>
                        %val:usr:o_itemName%
                    </td>
                    <td>
                        <span uk-icon="arrow-right"></span>
                    </td>
                    <td>
                        %val:usr:itemName%
                    </td>
                    <td>
                        <script>
                            old = "%val:usr:o_itemName%";
                            val = "%val:usr:itemName%";
                            if(old === val)
                            {
                                document.write('<span class="uk-label uk-label-success">変更なし</span>');
                            }
                            else
                            {
                                document.write('<span class="uk-label uk-label-warning">変更あり</span>');
                            }
                        </script>
                    </td>
                </tr>
                <tr>
                    <th>
                        分類
                    </th>
                    <td>
                        %val:usr:o_category%
                    </td>
                    <td>
                        <span uk-icon="arrow-right"></span>
                    </td>
                    <td>
                        %val:usr:category%
                    </td>
                    <td>
                        <script>
                            old = "%val:usr:o_category%";
                            val = "%val:usr:category%";
                            if(old === val)
                            {
                                document.write('<span class="uk-label uk-label-success">変更なし</span>');
                            }
                            else
                            {
                                document.write('<span class="uk-label uk-label-warning">変更あり</span>');
                            }
                        </script>
                    </td>
                </tr>
                <tr>
                    <th>
                        製品コード
                    </th>
                    <td>
                        %val:usr:o_itemCode%
                    </td>
                    <td>
                        <span uk-icon="arrow-right"></span>
                    </td>
                    <td>
                        %val:usr:itemCode%
                    </td>
                    <td>
                        <script>
                            old = "%val:usr:o_itemCode%";
                            val = "%val:usr:itemCode%";
                            if(old === val)
                            {
                                document.write('<span class="uk-label uk-label-success">変更なし</span>');
                            }
                            else
                            {
                                document.write('<span class="uk-label uk-label-warning">変更あり</span>');
                            }
                        </script>
                    </td>
                </tr>
                <tr>
                    <th>
                        規格
                    </th>
                    <td>
                        %val:usr:o_itemStandard%
                    </td>
                    <td>
                        <span uk-icon="arrow-right"></span>
                    </td>
                    <td>
                        %val:usr:itemStandard%
                    </td>
                    <td>
                        <script>
                            old = "%val:usr:o_itemStandard%";
                            val = "%val:usr:itemStandard%";
                            if(old === val)
                            {
                                document.write('<span class="uk-label uk-label-success">変更なし</span>');
                            }
                            else
                            {
                                document.write('<span class="uk-label uk-label-warning">変更あり</span>');
                            }
                        </script>
                    </td>
                </tr>
                <tr>
                    <th>
                        JANコード
                    </th>
                    <td>
                        %val:usr:o_itemJANCode%
                    </td>
                    <td>
                        <span uk-icon="arrow-right"></span>
                    </td>
                    <td>
                        %val:usr:itemJANCode%
                    </td>
                    <td>
                        <script>
                            old = "%val:usr:o_itemJANCode%";
                            val = "%val:usr:itemJANCode%";
                            if(old === val)
                            {
                                document.write('<span class="uk-label uk-label-success">変更なし</span>');
                            }
                            else
                            {
                                document.write('<span class="uk-label uk-label-warning">変更あり</span>');
                            }
                        </script>
                    </td>
                </tr>
                <tr>
                    <th>
                        カタログNo
                    </th>
                    <td>
                        %val:usr:o_catalogNo%
                    </td>
                    <td>
                        <span uk-icon="arrow-right"></span>
                    </td>
                    <td>
                        %val:usr:catalogNo%
                    </td>
                    <td>
                        <script>
                            old = "%val:usr:o_catalogNo%";
                            val = "%val:usr:catalogNo%";
                            if(old === val)
                            {
                                document.write('<span class="uk-label uk-label-success">変更なし</span>');
                            }
                            else
                            {
                                document.write('<span class="uk-label uk-label-warning">変更あり</span>');
                            }
                        </script>
                    </td>
                </tr>
                <tr>
                    <th>
                        シリアルNo
                    </th>
                    <td>
                        %val:usr:o_serialNo%
                    </td>
                    <td>
                        <span uk-icon="arrow-right"></span>
                    </td>
                    <td>
                        %val:usr:serialNo%
                    </td>
                    <td>
                        <script>
                            old = "%val:usr:o_serialNo%";
                            val = "%val:usr:serialNo%";
                            if(old === val)
                            {
                                document.write('<span class="uk-label uk-label-success">変更なし</span>');
                            }
                            else
                            {
                                document.write('<span class="uk-label uk-label-warning">変更あり</span>');
                            }
                        </script>
                    </td>
                </tr>
                <tr>
                    <th>
                        定価
                    </th>
                    <td>
                        %val:usr:o_minPrice%
                    </td>
                    <td>
                        <span uk-icon="arrow-right"></span>
                    </td>
                    <td>
                        %val:usr:minPrice%
                    </td>
                    <td>
                        <script>
                            old = "%val:usr:o_minPrice%";
                            val = "%val:usr:minPrice%";
                            if(old === val)
                            {
                                document.write('<span class="uk-label uk-label-success">変更なし</span>');
                            }
                            else
                            {
                                document.write('<span class="uk-label uk-label-warning">変更あり</span>');
                            }
                        </script>
                    </td>
                </tr>
                <tr>
                    <th>
                        ロット管理フラグ
                    </th>
                    <td>
                        %val:usr:o_lotManagement:v%
                    </td>
                    <td>
                        <span uk-icon="arrow-right"></span>
                    </td>
                    <td>
                        %val:usr:lotManagement:v%
                    </td>
                    <td>
                        <script>
                            old = "%val:usr:o_lotManagement:v%";
                            val = "%val:usr:lotManagement:v%";
                            if(old === val)
                            {
                                document.write('<span class="uk-label uk-label-success">変更なし</span>');
                            }
                            else
                            {
                                document.write('<span class="uk-label uk-label-warning">変更あり</span>');
                            }
                        </script>
                    </td>
                </tr>
                <tr>
                    <th>
                        償還価格フラグ
                    </th>
                    <td>
                        %val:usr:o_officialFlag:v%
                    </td>
                    <td>
                        <span uk-icon="arrow-right"></span>
                    </td>
                    <td>
                        %val:usr:officialFlag:v%
                    </td>
                    <td>
                        <script>
                            old = "%val:usr:o_officialFlag%";
                            val = "%val:usr:officialFlag%";
                            if(old === val)
                            {
                                document.write('<span class="uk-label uk-label-success">変更なし</span>');
                            }
                            else
                            {
                                document.write('<span class="uk-label uk-label-warning">変更あり</span>');
                            }
                        </script>
                    </td>
                </tr>
                <tr>
                    <th>
                        償還価格
                    </th>
                    <td>
                        %val:usr:o_officialprice%
                    </td>
                    <td>
                        <span uk-icon="arrow-right"></span>
                    </td>
                    <td>
                        %val:usr:officialprice%
                    </td>
                    <td>
                        <script>
                            old = "%val:usr:o_officialprice%";
                            val = "%val:usr:officialprice%";
                            if(old === val)
                            {
                                document.write('<span class="uk-label uk-label-success">変更なし</span>');
                            }
                            else
                            {
                                document.write('<span class="uk-label uk-label-warning">変更あり</span>');
                            }
                        </script>
                    </td>
                </tr>
                <tr>
                    <th>
                        入数
                    </th>
                    <td>
                        %val:usr:o_quantity%
                    </td>
                    <td>
                        <span uk-icon="arrow-right"></span>
                    </td>
                    <td>
                        %val:usr:quantity%
                    </td>
                    <td>
                        <script>
                            old = "%val:usr:o_quantity%";
                            val = "%val:usr:quantity%";
                            if(old === val)
                            {
                                document.write('<span class="uk-label uk-label-success">変更なし</span>');
                            }
                            else
                            {
                                document.write('<span class="uk-label uk-label-warning">変更あり</span>');
                            }
                        </script>
                    </td>
                </tr>
                <tr>
                    <th>
                        入数単位
                    </th>
                    <td>
                        %val:usr:o_quantityUnit%
                    </td>
                    <td>
                        <span uk-icon="arrow-right"></span>
                    </td>
                    <td>
                        %val:usr:quantityUnit%
                    </td>
                    <td>
                        <script>
                            old = "%val:usr:o_quantityUnit%";
                            val = "%val:usr:quantityUnit%";
                            if(old === val)
                            {
                                document.write('<span class="uk-label uk-label-success">変更なし</span>');
                            }
                            else
                            {
                                document.write('<span class="uk-label uk-label-warning">変更あり</span>');
                            }
                        </script>
                    </td>
                </tr>
                <tr>
                    <th>
                        個数単位
                    </th>
                    <td>
                        %val:usr:o_itemUnit%
                    </td>
                    <td>
                        <span uk-icon="arrow-right"></span>
                    </td>
                    <td>
                        %val:usr:itemUnit%
                    </td>
                    <td>
                        <script>
                            old = "%val:usr:o_itemUnit%";
                            val = "%val:usr:itemUnit%";
                            if(old === val)
                            {
                                document.write('<span class="uk-label uk-label-success">変更なし</span>');
                            }
                            else
                            {
                                document.write('<span class="uk-label uk-label-warning">変更あり</span>');
                            }
                        </script>
                    </td>
                </tr>
            </tbody>
            <tfoot>
                <tr></tr>
            </tfoot>
        </table>
    </div>
</div>