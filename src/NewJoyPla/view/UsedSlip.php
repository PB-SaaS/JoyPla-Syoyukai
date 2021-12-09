<div class="animsition uk-margin-bottom" uk-height-viewport="expand: true">
    <div class="uk-section uk-section-default uk-preserve-color uk-padding-remove uk-margin-top" id="page_top">
        <div class="uk-container uk-container-expand">
            <ul class="uk-breadcrumb no_print">
                <li><a href="%url/rel:mpg:top%">TOP</a></li>
                <li><a href="<?php echo $link ?>&table_cache=true"><span><?php echo $link_name ?></span></a></li>
                <li><span><?php echo $current_name ?></span></li>
            </ul>
            <div class="no_print uk-margin" uk-margin>
                <input class="print_hidden uk-button uk-button-default" type="button" value="印刷プレビュー" onclick="window.print();return false;">
                <?php if($used_slip_history->usedSlipStatus == '1'): ?>
                    <input class="print_hidden uk-button uk-button-default uk-button-danger" type="button" value="取消" onclick="used_slip.cancel()">
                    <?php if($user_info->isHospitalUser() && !$user_info->isUser()): ?>
                        <input class="print_hidden uk-button uk-button-default uk-button-primary" type="button" value="承認" onclick="used_slip.usedSlipApproval()">
                    <?php endif; ?>
                <?php endif; ?>
            </div>
            <div class="uk-text-center uk-text-large">
                <p class="uk-text-bold title_spacing" style="font-size: 32px"><?php echo $current_name ?></p>
            </div>
            <div uk-grid>
                <div class="uk-width-1-2@m">
                    <table class="uk-table uk-width-1-1 uk-width-2-3@m uk-table-divider">
                        <tr class="uk-text-large">
                            <td>ステータス</td>
                            <td class="uk-text-right">%val:usr:usedSlipStatus%</td>
                        </tr>
                        <tr class="uk-text-large">
                            <td>合計金額</td>
                            <td class="uk-text-right">￥<script>price('%val:usr:totalAmount%')</script> - </td>
                        </tr>
                    </table>
                </div>
                <div class="uk-width-1-2@m">
                    <div class="uk-float-right uk-width-2-3@m">
                        <table class="uk-table uk-width-1-1 uk-table-divider">
                            <tr>
                                <td>使用申請日時</td>
                                <td>%val:usr:registrationTime%</td>
                            </tr>
                            <tr>
                                <td>使用日</td>
                                <td>%val:usr:usedTime%</td>
                            </tr>
                            <tr>
                                <td>貸出伝票番号</td>
                                <td>%val:usr:usedSlipId%</td>
                            </tr>
                            <tr>
                                <td>申請者</td>
                                <td>%val:usr:reportPersonName%</td>
                            </tr>
                        </table>
                        <div class="uk-text-left">
                            <span>%val:usr:hospitalName%</span><br>
                            <span>〒%val:usr:postalCode%</span><br>
                            <span>%val:usr:prefectures% %val:usr:address%</span><br>
                            <span>電話番号：%val:usr:phoneNumber%</span><br>
                            <span>使用部署：%val:usr:divisionName%</span><br>
                        </div>
                    </div>
                    <div class="uk-clearfix"></div>
                </div>
            </div>
            
            <div class="uk-margin" id="tablearea">
                <form>
                    <div class="uk-overflow-auto">
                        <table class="uk-table uk-table-hover uk-table-middle uk-table-divider" id="tbl-Items">
                            <thead>
                                <tr>
                                    <th class="uk-text-nowrap">NO</th>
                                    <th class="uk-table-expand">メーカー</th>
                                    <th class="uk-table-expand">商品名</th>
                                    <th class="uk-table-expand">製品コード</th>
                                    <th class="uk-table-expand">規格</th>
                                    <th class="uk-table-expand">JANコード</th>
                                    <th class="uk-table-expand">入数</th>
                                    <th class="uk-table-expand">貸出数</th>
                                    <th class="uk-table-expand">ロット番号</th>
                                    <th class="uk-table-expand">使用期限</th>
                                    <th class="uk-text-nowrap">金額</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php
                                    $num = 1;
                                    foreach($borrowing as $record){
                                        echo "<tr>";
                                        echo "<td class='uk-text-nowrap'>".$num."</td>";
                                        echo "<td>".$record->makerName."</td>";
                                        echo "<td>".$record->itemName."</td>";
                                        echo "<td>".$record->itemCode."</td>";
                                        echo "<td>".$record->itemStandard."</td>";
                                        echo "<td>".$record->itemJANCode."</td>";
                                        echo "<td class='uk-text-nowrap'>".$record->quantity.$record->quantityUnit."</td>";
                                        echo "<td class='uk-text-nowrap'>".$record->borrowingNum.$record->itemUnit."</td>";
                                        echo "<td>".$record->lotNumber."</td>";
                                        echo "<td>".$record->lotDate."</td>";
                                        echo "<td class='uk-text-nowrap'>￥".number_format((int)$record->price * (int)$record->borrowingNum,2)."</td>";
                                        echo "</tr>";
                                        $num++;
                                    }
                                ?>
                            </tbody>
                            
                        </table>
                    </div>
                </form>
            </div>
            <div class="uk-margin">
                <?php if(count($association) > 0): ?>
                <p class="uk-text-bold">紐づく伝票情報</p>
                <div>
                    <table class="uk-table uk-table-hover uk-table-middle uk-table-divider uk-text-nowrap" id="tbl-Items">
                        <thead>
                            <tr>
                                <th>NO</th>
                                <th>発注番号</th>
                                <th>検収番号</th>
                                <th>消費番号</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php
                                $num = 1;
                                foreach($association as $record){
                                    echo "<tr>";
                                    echo "<td>".$num."</td>";
                                    echo "<td><a href='#' onclick='used_slip.search(\"" .$record->orderNumber ."\")'>".$record->orderNumber."</a></td>";
                                    echo "<td><a href='#' onclick='used_slip.search(\"" .$record->receivingHId ."\")'>".$record->receivingHId."</a></td>";
                                    echo "<td><a href='#' onclick='used_slip.search(\"" .$record->billingNumber ."\")'>".$record->billingNumber."</a></td>";
                                    echo "</tr>";
                                    $num++;
                                }
                            ?>
                        </tbody>
                    </table>
                </div>
                <?php endif; ?>
            </div>
        </div>
    </div>
</div>

<script>
    class UsedSlip
    {
        constructor()
        {
            this.canAjax = true;
            this.gs1128_object = {};
            this.listObject = {};
            this.dataKey = ['id','maker','shouhinName','code','kikaku','irisu','kakaku','unitPrice','jan','oroshi','count'];
            this.index = 1;
        }
        cancel(){
            let tmp = this;
            UIkit.modal.confirm('取消を行います。よろしいでしょうか').then(function () {
                loading();
                $.ajax({
                    async: false,
                    url: "<?php echo $api_url ?>",
                    type:'POST',
                    data:{
                        _csrf: "<?php echo $csrf_token ?>",  // CSRFトークンを送信
                        Action : "cancelApi",
                    },
                    dataType: 'json'
                })
                // Ajaxリクエストが成功した時発動
                .done( (data) => {
                    if(data.code != 0){
                        UIkit.modal.alert('使用申請の取り消しに失敗しました').then(function(){
                        });
                        return false;
                    }
                    UIkit.modal.alert('使用申請を取り消しました').then(function () {
                        location.href = "<?php echo $link ?>&table_cache=true";
                    });
                })
                // Ajaxリクエストが失敗した時発動
                .fail( (data) => {
                    UIkit.modal.alert('使用申請の取り消しに失敗しました').then(function(){
                    });
                })
                // Ajaxリクエストが成功・失敗どちらでも発動
                .always( (data) => {
                    loading_remove();
                });
            
            }, function () {
            });
        }

        usedSlipApproval(){
            let tmp = this;
            UIkit.modal.confirm('承認を行います。よろしいでしょうか<br>※発注書・検収書・消費伝票が自動で生成されます').then(function () {
                loading();
                $.ajax({
                    async: false,
                    url: "<?php echo $api_url ?>",
                    type:'POST',
                    data:{
                        _csrf: "<?php echo $csrf_token ?>",  // CSRFトークンを送信
                        Action : "usedSlipApprovalApi",
                    },
                    dataType: 'json'
                })
                // Ajaxリクエストが成功した時発動
                .done( (data) => {
                    if(data.code != 0){
                        UIkit.modal.alert('使用申請の承認に失敗しました').then(function(){
                        });
                        return false;
                    }
                    UIkit.modal.alert('使用申請を承認しました').then(function () {
                        location.reload();
                    });
                })
                // Ajaxリクエストが失敗した時発動
                .fail( (data) => {
                    UIkit.modal.alert('使用申請の承認に失敗しました').then(function(){
                    });
                })
                // Ajaxリクエストが成功・失敗どちらでも発動
                .always( (data) => {
                    loading_remove();
                });
            
            }, function () {
            });
        }
        
        search(slipnum){
            loading();
            $.ajax({
                async: false,
                url:'%url/rel:mpgt:barcodeSearchAPI%',
                type:'POST',
                data:{
                    searchValue :slipnum
                },
                dataType: 'json'
            })
            // Ajaxリクエストが成功した時発動
            .done( (data) => {
                if(data.code != 0){
                    UIkit.modal.alert("URLが見つかりませんでした").then(function(){
                        tmp.canAjax = true; // 再びAjaxできるようにする
                    });
                    return false;
                }
                location.href=data.urls[0];
            })
            // Ajaxリクエストが失敗した時発動
            .fail( (data) => {
                UIkit.modal.alert("URLが見つかりませんでした").then(function(){
                });
            })
            // Ajaxリクエストが成功・失敗どちらでも発動
            .always( (data) => {
                loading_remove();
            });
        }
    }

    let used_slip = new UsedSlip();
    
</script>