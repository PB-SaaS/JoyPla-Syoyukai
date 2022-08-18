<div class="animsition" uk-height-viewport="expand: true">
    <div class="uk-section uk-section-default uk-preserve-color uk-padding-remove uk-margin-top" id="page_top">
        <div class="uk-container uk-container-expand">
            <ul class="uk-breadcrumb">
                <li><a href="%url/rel:mpg:top%">TOP</a></li>
                	<li><a href="%url/rel:mpg:top%&path=monthlyreport">月次レポートメニュー</a></li>
                <li><span>月次レポート【入荷照合】</span></li>
            </ul>
            <div class="no_print uk-margin">
                <input class="print_hidden uk-button uk-button-default" type="button" value="印刷プレビュー" onclick="window.print();return false;">
                <input class="print_hidden uk-button uk-button-primary" type="button" value="出力" onclick="receiving_report.listDl();return false;">
            </div>
            <h2 class="page_title uk-margin-remove">月次レポート【入荷照合】</h2>
            <hr>
            <div class="uk-width-1-1 uk-margin-auto">
                <form class="uk-form-stacked" name="myform" action="<?php echo $api_url; ?>" method="post" onsubmit="return receiving_report.submitCheck()">
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
                            <label class="uk-form-label">卸業者</label>
                            <div class="uk-child-width-1-1">
                                <div>
                                    <select name="distributorId" class="uk-select">
                                        <option value="">----- 選択してください -----</option>
                                        <?php
                                            foreach ($distributor as $record)
                                            {
                                                $selected = '';
                                                if ($distributorId == $record->distributorId) { $selected = 'selected'; }
                                                echo '<option value="'.$record->distributorId.'"' .$selected .'>'.$record->distributorName.'</option>';
                                            }
                                        ?>
                                    </select>
                                </div>
                            </div>
                        </div>

                        <?php if(!$userInfo->isUser()): ?>
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
                        <?php else: ?>
                        <div class="uk-form-controls uk-margin">
                            <label class="uk-form-label">部署</label>
                            <div class="uk-child-width-1-1">
                                <div>
                                    <?php
                                        foreach ($division->data as $data)
                                        {
                                            if ($divisionId == $data->divisionId) { echo $data->divisionName; }
                                        }
                                    ?>
                                </div>
                            </div>
                        </div>
                        <?php endif ?>

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
                                    <td class="uk-text-right">￥<script>price(fixed('<?php echo $report['totalAmount']; ?>'))</script> -</td>
                                </tr>
                            </tbody>
                        </table>
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
                                    <option value="10" <?php echo ($limit == '10') ? 'selected' : ''; ?>>10件</option>
                                    <option value="50" <?php echo ($limit == '50') ? 'selected' : ''; ?>>50件</option>
                                    <option value="100" <?php echo ($limit == '100') ? 'selected' : ''; ?>>100件</option>
                                </select></div>
                            <div class="uk-width-1-3">
                                <input type="submit" name="smp-table-submit-button" class="uk-button uk-button-default" value="表示">
                            </div>
                        </div>
                        <div class="uk-overflow-auto">
                            <table class="uk-table uk-table-hover uk-table-middle uk-table-divider">
                                <thead>
                                    <tr>
                                        <th class="uk-text-nowrap">No</th>
                                        <th class="uk-table-expand">院内商品ID</th>
                                        <th class="uk-table-expand">卸業者</th>
                                        <th class="uk-table-expand">メーカー</th>
                                        <th class="uk-table-expand">分類</th>
                                        <th class="uk-table-expand">商品名</th>
                                        <th class="uk-table-expand">製品コード</th>
                                        <th class="uk-table-expand">規格</th>
                                        <th class="uk-table-expand">JANコード</th>
                                        <th class="uk-text-nowrap">価格</th>
                                        <th class="uk-text-nowrap">入庫数</th>
                                        <th class="uk-text-nowrap">返品数</th>
                                        <th class="uk-text-nowrap">合計金額</th>
                                        <th class="uk-text-nowrap">調整額</th>
                                        <th class="uk-text-nowrap">調整後金額</th>
                                    </tr>
                                </thead>
                                <tbody>
                                <?php 
                                    if ($report['count'] > 0) {
                                        foreach ($report['data'] as $record) {
                                            echo "<tr>";
                                            echo "<td>".$record['id']."</td>";
                                            echo "<td>".$record['inHospitalItemId']."</td>";
                                            echo "<td>";
                                            foreach ($record['distributorName'] as $distributorName) {
                                                echo $distributorName."<br>";
                                            }
                                            echo "</td>";
                                            echo "<td>".$record['makerName']."</td>";
                                            echo "<td>".$record['category']."</td>";
                                            echo "<td>".$record['itemName']."</td>";
                                            echo "<td>".$record['itemCode']."</td>";
                                            echo "<td>".$record['itemStandard']."</td>";
                                            echo "<td>".$record['itemJANCode']."</td>";
                                            echo "<td>";
                                            foreach ($record['price'] as $price) {
                                                echo "￥<script>price(fixed('".$price."'))</script><br>";
                                            }
                                            echo "</td>";
                                            echo "<td>";
                                            foreach ($record['receivingCount'] as $key => $receivingCount) {
                                                echo $receivingCount." ".$record['itemUnit'][$key]."<br>";
                                            }
                                            echo "</td>";
                                            echo "<td>";
                                            foreach ($record['totalReturnCount'] as $key => $totalReturnCount) {
                                                echo "<span class=\"uk-text-danger\">".$totalReturnCount." ".$record['itemUnit'][$key]."</span><br>";
                                            }
                                            echo "</td>";
                                            echo "<td>";
                                            foreach ($record['totalAmount'] as $totalAmount) {
                                                echo "￥<script>price(fixed('".$totalAmount."'))</script><br>";
                                            }
                                            echo "</td>";
                                            echo "<td>";
                                            foreach ($record['adjAmount'] as $adjAmount) {
                                                echo "￥<script>price(fixed('".$adjAmount."'))</script><br>";
                                            }
                                            echo "</td>";
                                            echo "<td>";
                                            foreach ($record['priceAfterAdj'] as $priceAfterAdj) {
                                                echo "￥<script>price(fixed('".$priceAfterAdj."'))</script><br>";
                                            }
                                            echo "</td>";
                                            echo "</tr>";
                                        }
                                    } 
                                ?>
                                </tbody>
                            </table>
                        </div>
                    </div>
                    <input name="page" value="1" type="hidden">
                    <?php \App\Lib\pager($page, $report['count'],$limit); ?>
                </form>
            </div>
        </div>
    </div>
</div>
<script>
class ReceivingMR
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
            for (let j = 0; j < records[i][8].length; j++) {
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
                remakeArray[k][8] = records[i][8][j];
                remakeArray[k][9] = records[i][9][j];
                remakeArray[k][10] = records[i][10][j];
                remakeArray[k][11] = records[i][11][j];
                remakeArray[k][12] = records[i][12][j];
                remakeArray[k][13] = records[i][13][j];
                remakeArray[k][14] = records[i][14][j];
                remakeArray[k][15] = records[i][15][j];
                remakeArray[k][15] = records[i][16][j];
            }
        }
        let data = remakeArray.map((record) => record.join('\t')).join('\r\n');
        data = Encoding.stringToCode(data);
        let shiftJisCodeList = Encoding.convert(data, 'sjis', 'unicode');
        let uInt8List = new Uint8Array(shiftJisCodeList);
        
        //let bom = new Uint8Array([0xEF, 0xBB, 0xBF]);
        let blob = new Blob([uInt8List], {
            type: 'text/tab-separated-values'
        });
        let url = (window.URL || window.webkitURL).createObjectURL(blob);
        let link = document.createElement('a');
        link.download = 'ReceivingMonthlyReport_<?php echo date('Ymd'); ?>.tsv';
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

        result.unshift(['id', 'inHospitalItemId', 'makerName', 'category', 'itemName', 'itemCode', 'itemStandard', 'itemJANCode', 'distributorName', 'quantity', 'price', 'receivingCount', 'totalAmount', 'itemUnit', 'totalReturnCount', 'adjAmount', 'priceAfterAdj']);

        this.exportCSV(result);
    }
}

    let receiving_report = new ReceivingMR();

function pageSubmit(page)
{
    receiving_report.pageSubmit(page);
}
</script>