
<div class="uk-section uk-section-default uk-preserve-color uk-padding-remove" id="page_top">
    <div class="uk-container uk-container-expand">
        <div class="uk-width-1-1 uk-margin-auto">
            <form class="uk-form-stacked" name="myform" action="<?php echo $api_url; ?>" method="post" onsubmit="return billing_report.submitCheck()">
                <div class="uk-width-3-4@m uk-margin-auto">
                    <h3>検索</h3>
                    <div class="uk-form-controls uk-margin">
                        <label class="uk-form-label">日付</label>
                        <div class="uk-child-width-1-2@m" uk-grid>
                            <div>
                                <div>
                                    <input type="date" class="uk-input uk-width-4-5" name="startMonth" value="<?php echo $startMonth; ?>">
                                    <span class="uk-width-1-5'">から</span>
                                </div>
                            </div>
                            <div>
                                <div>
                                    <input type="date" class="uk-input uk-width-4-5" name="endMonth" value="<?php echo $endMonth; ?>">
                                    <span class="uk-width-1-5'">まで</span>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="uk-form-controls uk-margin">
                        <label class="uk-form-label">部署</label>
                        <div class="uk-child-width-1-1">
                            <div>
                                <select name="divisionId" class="uk-select">
                                    <option value="">----- 選択してください -----</option>
                                <?php
                                    foreach ($division->data as $data)
                                    {
                                        $selected = '';
                                        if ($divisionId == $data->divisionId) { $selected = 'selected'; }
                                        if ($data->divisionType === '1')
                                        {
                                            echo '<option value="'.$data->divisionId.'" ' .$selected .'>'.$data->divisionName.'(大倉庫)</option>';
                                            echo '<option value="" disabled>--------------------</option>';
                                        }
                                    }
                                    foreach ($division->data as $data)
                                    {
                                        $selected = '';
                                        if ($divisionId == $data->divisionId) { $selected = 'selected'; }
                                        if ($data->divisionType === '2')
                                        {
                                            echo '<option value="'.$data->divisionId.'" ' .$selected .'>'.$data->divisionName.'</option>';
                                        }
                                    }
                                ?>
                                </select>
                            </div>
                        </div>
                    </div>
                    <div class="uk-form-controls uk-margin">
                        <label class="uk-form-label">分類</label>
                        <div class="uk-child-width-1-1">
                            <div class="uk-form-controls">
                            <?php
                                foreach ($category as $key => $val)
                                {
                                    echo "<label class='uk-margin-small-right'>\n";
                                    echo "    <input type='checkbox' class='uk-checkbox uk-margin-small-right' name='category' value='{$key}' {$val['checked']}>\n";
                                    echo $val['label']."\n";
                                    echo "</label>\n";
                                }
                            ?>
                            </div>
                        </div>
                    </div>

                    <div class="uk-form-controls uk-margin">
                        <label class="uk-form-label">小分類</label>
                        <div class="uk-child-width-1-1">
                            <div>
                                <input type="text" class="uk-input" name="smallCategory" value="<?php echo $smallCategory; ?>">
                            </div>
                        </div>
                    </div>
                    <div class="uk-form-controls uk-margin">
                        <label class="uk-form-label">商品名</label>
                        <div class="uk-child-width-1-1">
                            <div>
                                <input type="text" class="uk-input" name="itemName" value="<?php echo $itemName; ?>">
                            </div>
                        </div>
                    </div>

                    <div class="uk-form-controls uk-margin">
                        <label class="uk-form-label">製品コード</label>
                        <div class="uk-child-width-1-1">
                            <div>
                                <input type="text" class="uk-input" name="itemCode" value="<?php echo $itemCode; ?>">
                            </div>
                        </div>
                    </div>

                    <div class="uk-form-controls uk-margin">
                        <label class="uk-form-label">規格</label>
                        <div class="uk-child-width-1-1">
                            <div>
                                <input type="text" class="uk-input" name="itemStandard" value="<?php echo $itemStandard; ?>">
                            </div>
                        </div>
                    </div>

                    <div class="uk-text-center">
                        <input class="uk-margin-top uk-button uk-button-default" type="submit" value="検索">
                    </div>
                </div>
                <div>
                    <table class="uk-table uk-width-1-2@m uk-width-1-4@m uk-table-divider">
                        <tbody>
                            <tr class="uk-text-large">
                                <td>合計金額</td>
                                <td class="uk-text-right">￥<?php echo number_format_jp($report['totalAmount']); ?></script> -</td>
                            </tr>
                        </tbody>
                    </table>
                </div>
                <div class="no_print uk-margin">
                    <input class="print_hidden uk-button uk-button-default" type="button" value="印刷プレビュー" onclick="window.print();return false;">
                    <input class="print_hidden uk-button uk-button-primary" type="button" value="出力" onclick="billing_report.listDl();return false;">
                </div>
                <?php \App\Lib\pager($page, $report['count'],$limit); ?>
                <div>
                    <div class="uk-width-1-3@m">
                        <span class="smp-offset-start">
                                <?php echo ($report['count'] > 0)? ($limit * ($page - 1)) + 1 : 0 ; ?></span> - <span class="smp-offset-end">
                            <?php echo ($limit * $page > $report['count']) ? $report['count'] : $limit * $page; ?></span>件 / <span class="smp-count">
                            <?php echo $report['count']; ?></span>件

                    </div>
                    <div class="uk-width-1-3@m" uk-grid>
                        <div class="uk-width-2-3">
                            <select name="limit" class=" uk-select">
                                <option value="10" <?php echo ($limit == '10') ? 'selected' : '10'; ?>>10件</option>
                                <option value="50" <?php echo ($limit == '50') ? 'selected' : ''; ?>>50件</option>
                                <option value="100" <?php echo ($limit == '100') ? 'selected' : ''; ?>>100件</option>
                            </select></div>
                        <div class="uk-width-1-3">
                            <input type="submit" name="smp-table-submit-button" class="uk-button uk-button-default" value="表示">
                        </div>
                    </div>
                    <div class="uk-overflow-auto">
                        <table class="uk-table uk-table-hover uk-table-middle uk-table-divider uk-text-nowrap">
                            <thead>
                                <tr>
                                    <th>No</th>
                                    <th>院内商品ID</th>
                                    <th>メーカー</th>
                                    <th>分類</th>
                                    <th>小分類</th>
                                    <th>商品名</th>
                                    <th>製品コード</th>
                                    <th>規格</th>
                                    <th>JANコード</th>
                                    <th>購買価格</th>
                                    <th>単価</th>
                                    <th>入数</th>
                                    <th>消費数</th>
                                    <th>合計金額</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php 
                                if ($report['count'] > 0) {
                                    foreach ($report['data'] as $record) {
                                        echo "<tr>";
                                        echo "<td>".$record['id']."</td>";
                                        echo "<td>".$record['inHospitalItemId']."</td>";
                                        echo "<td>".$record['makerName']."</td>";
                                        echo "<td>".$record['category']."</td>";
                                        echo "<td>".$record['smallCategory']."</td>";
                                        echo "<td>".$record['itemName']."</td>";
                                        echo "<td>".$record['itemCode']."</td>";
                                        echo "<td>".$record['itemStandard']."</td>";
                                            echo "<td>".$record['itemJANCode']."</td>";
                                        echo "<td>";
                                        foreach ($record['price'] as $price) {
                                            echo "￥".number_format_jp($price)."<br>";
                                        }
                                        echo "</td>";
                                        echo "<td>";
                                        foreach ($record['unitPrice'] as $unitPrice) {
                                            echo "￥".number_format_jp($unitPrice)."<br>";
                                        }
                                        echo "</td>";
                                        echo "<td>";
                                        foreach ($record['quantity'] as $key => $quantity) {
                                            echo number_format_jp($quantity).$record['quantityUnit'][$key]."<br>";
                                        }
                                        echo "</td>";
                                        echo "<td>";
                                        foreach ($record['billingQuantity'] as $key => $payoutQuantity) {
                                            echo number_format_jp($payoutQuantity).$record['quantityUnit'][$key]."<br>";
                                        }
                                        echo "</td>";
                                        echo "<td>";
                                        foreach ($record['totalAmount'] as $totalAmount) {
                                            echo "￥".number_format_jp($totalAmount)."<br>";
                                        }
                                        echo "</td>";
                                        echo "</tr>";
                                    }
                                } ?>
                            </tbody>
                        </table>
                    </div>
                </div>
                <input name="step" value="2" type="hidden">
                <input name="Action" value="GoodsBilling" type="hidden">
                <input name="hospitalId" value="<?php echo $hospitalId ?>" type="hidden">
                <input name="page" value="1" type="hidden">
                <?php \App\Lib\pager($page, $report['count'],$limit); ?>
            </form>
        </div>
    </div>
</div>
<script>
class GoodBillingMR
{
    constructor()
    {
        this.list = <?php echo json_encode($report['data']); ?>;
    }

    submitCheck()
    {
        if ($('input[name="startMonth"]').val() == "")
        {
            UIkit.modal.alert('日付検索の開始日は必須です');
            return false;
        }
        return true;
    }
    
    pageSubmit(page)
    {
        $('input[name="page"]').val(page);
        document.myform.submit();
    }
     
    exportCSV(records)
    {
        let remakeArray = new Array();

        let k = 0;
        remakeArray[k] = records[0];
        for (let i = 1; i < records.length; i++) {
            for (let j = 0; j < records[i][9].length; j++) {
                k = k + 1;
                remakeArray[k] = new Array();
                remakeArray[k][0] = records[i][0];
                remakeArray[k][1] = records[i][1];
                remakeArray[k][2] = records[i][2];
                remakeArray[k][3] = records[i][3];
                remakeArray[k][4] = records[i][4];
                remakeArray[k][5] = records[i][5];
                remakeArray[k][6] = records[i][6];
                remakeArray[k][7] = records[i][7];
                remakeArray[k][8] = records[i][8];
                remakeArray[k][9] = records[i][9][j];
                remakeArray[k][10] = records[i][10][j];
                remakeArray[k][11] = records[i][11][j];
                remakeArray[k][12] = records[i][12][j];
                remakeArray[k][13] = records[i][13][j];
                remakeArray[k][14] = records[i][14][j];
            }
        }
        let data = remakeArray.map((record) => record.join('\t')).join('\r\n');
        data = Encoding.stringToCode(data);
        let shiftJisCodeList = Encoding.convert(data, 'sjis', 'unicode');
        let uInt8List = new Uint8Array(shiftJisCodeList);
        
        //let bom = new Uint8Array([0xEF, 0xBB, 0xBF]);
        let blob = new Blob([uInt8List], {type: 'text/tab-separated-values'});
        let url = (window.URL || window.webkitURL).createObjectURL(blob);
        let link = document.createElement('a');
        link.download = 'ConsumeMonthlyReport_<?php echo date('Ymd'); ?>.tsv';
        link.href = url;
        document.body.appendChild(link);
        link.click();
        document.body.removeChild(link);
    };
    
    listDl()
    {
        let tmp = this;
        let result = [];
        for (let i = 0; i < tmp.list.length; i++) {
            result[i] = [];
            Object.keys(tmp.list[i]).forEach(function(key) {
                result[i].push(tmp.list[i][key]);
            });
        }
        
        result.unshift(['id', '院内商品ID', 'メーカー', '分類', '小分類', '商品名', '製品コード', '規格', 'JANコード', '購買価格', '単価', '入数', '入数単位', '消費数', '合計金額']);
    
        this.exportCSV(result);
    }

}

    let billing_report = new GoodBillingMR();
    
function pageSubmit(page)
{
    billing_report.pageSubmit(page);
}
</script>