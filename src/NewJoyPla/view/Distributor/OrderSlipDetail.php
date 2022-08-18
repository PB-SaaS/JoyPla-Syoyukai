<div id="app">
    <div class="animsition uk-margin-bottom no_print" uk-height-viewport="expand: true">
        <div class="uk-section uk-section-default uk-preserve-color uk-padding-remove uk-margin-top" id="page_top">
            <div class="uk-container uk-container-expand">
                <ul class="uk-breadcrumb no_print">
                    <li><a href="%url/rel:mpg:top%">TOP</a></li>
                	<li><a href="%url/rel:mpg:top%&page=page1">消費・発注</a></li>
                    <li><a href="<?php echo $link ?>&table_cache=true"><span><?php echo $link_title ?></span></a></li>
                    <li><span>発注書</span></li>
                </ul>
                    <div class="uk-child-width-1-2@m no_print uk-margin" uk-grid>
                        <div class="uk-text-left">
                            <input class="print_hidden uk-button uk-button-default" type="button" value="印刷プレビュー" onclick="window.print();return false;">
                            <input class="print_hidden uk-button uk-button-danger" type="button" value="受注取消" v-on:click="orderReset" v-bind:disabled="orderResetDisabled">
                            <input class="print_hidden uk-button uk-button-primary" type="button" value="受注確定" v-on:click="orderFixing" v-bind:disabled="orderFixingDisabled">
                        </div>
                    </div>
                    <div class="uk-text-center uk-text-large">
                        <p class="uk-text-bold title_spacing" style="font-size: 32px">発注書</p>
                    </div>
                    <div uk-grid>
                        <div class="uk-width-1-2@m">
                            <table class="uk-table uk-width-1-1 uk-width-2-3@m uk-table-divider">
                                <tr class="uk-text-large">
                                    <td colspan="2">
                                        <b>%val:usr:distributorName% 御中</b>
                                    </td>
                                </tr>
                                <tr class="uk-text-large">
                                    <td>合計金額</td>
                                    <td class="uk-text-right">￥{{ totalAmount | number_format }} - </td>
                                </tr>
                            </table>
                        </div>
                        <div class="uk-width-1-2@m">
                            <div class="uk-float-right uk-width-2-3@m">
                                <table class="uk-table uk-width-1-1 uk-table-divider">
                                    <tr>
                                        <td>発注日時</td>
                                        <td>%val:usr:orderTime%</td>
                                    </tr>
                                    <tr>
                                        <td>発注番号</td>
                                        <td>%val:usr:orderNumber%</td>
                                    </tr>
                                </table>
                                <div id="order_barcode" class="uk-text-center">
                                    <span id="hacchu_num">%val:usr:orderNumber%</span>
                                </div>
                                <div class="uk-text-left">
                                    <span>%val:usr:hospitalName%</span><br>
                                    <span>〒%val:usr:postalCode%</span><br>
                                    <span>%val:usr:prefectures% %val:usr:address%</span><br>
                                    <span>電話番号：%val:usr:phoneNumber%</span><br>
                                    <span>発注担当者：%val:usr:ordererUserName%</span><br>
                                    <span>発注部署：%val:usr:divisionName%</span><br>
                                </div>
                            </div>
                            <div class="uk-clearfix"></div>
                        </div>
                    </div>

                    <div class="" id="tablearea">
                        <div class="uk-overflow-auto">
                            <table class="uk-table uk-table-hover uk-table-middle uk-table-divider uk-text-nowrap" id="tbl-Items">
                                <thead>
                                    <tr>
                                        <th>NO</th>
                                        <th style="min-width:60px">メーカー</th>
                                        <th style="min-width:150px">商品名</th>
                                        <th>製品コード</th>
                                        <th>規格</th>
                                        <th>JANコード</th>
                                        <th>価格</th>
                                        <th>入数</th>
                                        <th>発注数</th>
                                        <th>入庫数</th>
                                        <th>金額</th>
                                        <th>
                                            納期<br>
                                            <input type="date" class="uk-input uk-form-small" id="allSetDate" style="width:184px" v-model="setDate">
                                            <div class="uk-text-right" style="width:184px">
                                                <button type="button" class="uk-button uk-button-primary uk-text-small uk-width-1-1" v-on:click='setUsedDate()'>一括反映</button>
                                            </div>
                                        </th>
                                        <th>ステータス</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <tr v-for="(item, key) in lists" :id="'tr_' + key" v-bind:class="item.class">
                                        <td>{{key + 1 }}</td>
                                        <td>{{item.makerName}}</td>
                                        <td>{{item.itemName}}</td>
                                        <td>{{item.itemCode}}</td>
                                        <td>{{item.itemStandard}}</td>
                                        <td>{{item.itemJANCode}}</td>
                                        <td>￥{{item.price | number_format}}</td>
                                        <td>{{item.quantity}}{{item.quantityUnit}}</td>
                                        <td>{{item.orderQuantity}}{{item.itemUnit}}</td>
                                        <td>{{item.receivingNum}}{{item.itemUnit}}</td>
                                        <td>￥{{item.orderPrice | number_format}}</td>
                                        <td><input type="date" class="uk-input uk-form-small" v-model="item.dueDate" v-bind:style="item.dueDateStyle" v-on:change="addDueDateStyle(key)"></td>
                                        <td>
                                            <span v-if="parseInt(item.orderQuantity) === parseInt(item.receivingNum)">入庫済み</span>
                                            <span v-else-if="parseInt(item.receivingNum) == 0">未入庫</span>
                                            <span v-else-if="parseInt(item.orderQuantity) > parseInt(item.receivingNum)">一部入庫({{ item.receivingNum }} / {{ item.orderQuantity }})</span>
                                            
                                        </td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>
                    </div>
                    
                    <div class="uk-width-3-4 uk-margin">
                        <table class="uk-table uk-table-middle uk-table-divider">
                            <thead>
                                <tr>
                                    <td colspan="2">備考</td>
                                </tr>
                            </thead>
                            <tbody>
                                <tr>
                                    <td class="uk-width-1-5">
                                        %val:usr:divisionName%
                                    </td>
                                    <td class="uk-width-4-5 uk-text-break">
                                        %val:usr:ordercomment:br%
                                    </td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
            </div>
        </div>
    </div>
    
    <!-- print Only -->
    <div id="detail-sheet">
        <section class="sheet">
            <p class="uk-text-center print-text-xlarge title_spacing">発注書</p>
            <div>
                <div uk-grid>
                    <div class="uk-width-1-2">
                        <p class="uk-text-bold print-text-large">%val:usr:distributorName% 御中</p>
                        <p class="uk-text-bold print-text-large">合計金額 &yen; {{ totalAmount | number_format }} - </p>
                        <div>
                            <span>備考</span><br>
                            <div class="print-text-small uk-text-break" style="border: gray 1.2px solid; min-height: 90pt;line-height: normal; padding: 2pt">
                            %val:usr:ordercomment:br%
                            </div>
                        </div>
                    </div>
                    <div class="uk-width-1-2">
                        <div class="print-text-default">
                            発注日時 %val:usr:orderTime%<br>
                            発注番号 %val:usr:orderNumber%
                        </div>
                        <div id="order_barcode_print" class="uk-text-center uk-padding-remove">
                        </div>
                        <table class="print-text-small uk-text-left" style="line-height: normal;">
                            <tr><td colspan="2">%val:usr:hospitalName%</td></tr>
                            <tr><td style="width: 45pt">〒%val:usr:postalCode%</td><td>%val:usr:prefectures% %val:usr:address%</td></tr>
                            <tr><td>電話番号</td><td>%val:usr:phoneNumber%</td></tr>
                            <tr><td>発注担当者</td><td>%val:usr:ordererUserName%</td></tr>
                            <tr><td>発注部署</td><td>%val:usr:divisionName%</td></tr>
                            <tr><td>納品部署</td><td><?php echo $receipt_division ?></td></tr>
                        </table>
                    </div>
                </div>
                <table class="print-table" style="line-height: normal;">
                    <thead>
                        <tr class="print-text-default">
                            <th class="uk-table-shrink">No</th>
                            <th class="uk-text-left" style="width:200pt">商品情報</th>
                            <th style="min-width:100pt">JANコード</th>
                            <th style="min-width:80pt">卸業者管理コード</th>
                            <th>価格</th>
                            <th>入数</th>
                            <th>発注数</th>
                            <th>金額</th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr v-for="(item, key) in items" :id="'tr_' + key" v-bind:class="item.class" class="uk-text-center" style="word-break: break-all;">
                            <td class="uk-text-nowrap">{{key + 1 }}</td>
                            <td  class="uk-text-left">
                                <div uk-grid>
                                    <div class="uk-width-1-3">メーカー</div>
                                    <div class="uk-width-2-3 uk-padding-remove">{{ item.makerName }}</div>
                                    <div class="uk-width-1-3">商品名</div>
                                    <div class="uk-width-2-3 uk-padding-remove">{{ item.itemName }}</div>
                                    <div class="uk-width-1-3">製品コード</div>
                                    <div class="uk-width-2-3 uk-padding-remove">{{ item.itemCode }}</div>
                                    <div class="uk-width-1-3">規格</div>
                                    <div class="uk-width-2-3 uk-padding-remove">{{ item.itemStandard }}</div>
                                </div>
                            </td>
                            <td>
                                <svg :id="'print_jan_'+(key+1)"></svg>
                            </td>
                            <td>{{ item.itemJANCode }}</td>
                            <td>&yen;{{item.price | number_format}}</td>
                            <td>{{item.quantity}}{{item.quantityUnit}}</td>
                            <td>{{item.orderQuantity}}{{item.itemUnit}}</td>
                            <td>&yen;{{item.orderPrice | number_format}}</td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </section>
    </div>
</div>
<script>
    
var app = new Vue({
	el: '#app',
	data: {
		orderResetDisabled: (<?php echo $orderResetButton ?> != 1) ,
		orderFixingDisabled: (<?php echo $orderFixingButton ?> != 1) ,
		setDate: "<?php echo date('Y-m-d') ?>",
		totalAmount: "%val:usr:totalAmount%",
		lists: <?php echo json_encode($orderItems); ?>,
		items: <?php echo json_encode($orderItems); ?>,
	},
	filters: {
        number_format: function(value) {
            if (! value ) { return 0; }
            return new Intl.NumberFormat('ja-JP').format(value);
        },
    },
    watch: {
    },
    mounted: function()
    {
        this.items.forEach(function(elem, index) {
            if(isJanCheckDigit(elem.itemJANCode))
            {
                JsBarcode("#print_jan_"+(index+1),elem.itemJANCode,{format: "EAN13", width: 1, height: 25,fontSize: 20});
            }
            else
            {
                //$("#print_jan_"+(index+1)).before('<span>'+elem.itemJANCode+'</span>');
                //$("#print_jan_"+(index+1)).remove();
                JsBarcode("#print_jan_"+(index+1),elem.itemJANCode,{format: "codabar", width: 1, height: 30,fontSize: 12});
            }
        });
    },
	methods: {
		orderReset: function(){
            UIkit.modal.confirm("受注取消を行います。よろしいですか").then(function () {
                $.ajax({
                    async: false,
                    url: "<?php echo $api_url ?>",
                    type:'POST',
                    data:{
                        _csrf: "<?php echo $csrf_token ?>",  // CSRFトークンを送信
                        Action : "orderResetApi",
                    },
                    dataType: 'json'
                })
                // Ajaxリクエストが成功した時発動
                .done( (data) => {
                    if(data.code != 0){
                        UIkit.modal.alert('受注取消に失敗しました');
                        return false;
                    }
                    UIkit.modal.alert('受注取消が完了しました').then(function () {
                        location.reload();
                    });
                })
                // Ajaxリクエストが失敗した時発動
                .fail( (data) => {
                    UIkit.modal.alert('受注取消に失敗しました');
                })
                // Ajaxリクエストが成功・失敗どちらでも発動
                .always( (data) => {
                });
                
            }, function () {
            });
		},
		orderFixing: function(){
            UIkit.modal.confirm("受注確定を行います。よろしいですか").then(function () {
                $.ajax({
                    async: false,
                    url: "<?php echo $api_url ?>",
                    type:'POST',
                    data:{
                        _csrf: "<?php echo $csrf_token ?>",  // CSRFトークンを送信
                        Action : "orderFixingApi",
                        lists : JSON.stringify(app.lists),
                    },
                    dataType: 'json'
                })
                // Ajaxリクエストが成功した時発動
                .done( (data) => {
                    if(data.code != 0){
                        UIkit.modal.alert('受注確定に失敗しました');
                        return false;
                    }
                    UIkit.modal.alert('受注確定が完了しました').then(function () {
                        location.reload();
                    });
                })
                // Ajaxリクエストが失敗した時発動
                .fail( (data) => {
                    UIkit.modal.alert('受注確定に失敗しました');
                })
                // Ajaxリクエストが成功・失敗どちらでも発動
                .always( (data) => {
                });
                
            }, function () {
            });
		},
		setUsedDate: function(){
			UIkit.modal.confirm("納期を一括で反映します。<br>未入力のものが対象です").then(function(){
				app.lists.forEach(function(elem, index){
					if(elem.dueDate == ''){
						let changeObject = app.lists[index];
						changeObject.dueDate = app.setDate;
						changeObject.dueDateStyle = { 'backgroundColor' : "rgb(255, 204, 153)" , 'color' : "rgb(68, 68, 68)"};
						app.$set(app.lists, index, changeObject);
					}	
				});
			});
		},
		addDueDateStyle: function(index){
			let changeObject = app.lists[index];
			changeObject.dueDateStyle = { 'backgroundColor' : "rgb(255, 204, 153)" , 'color' : "rgb(68, 68, 68)"};
			app.$set(app.lists, index, changeObject);
		}
	},
}); 

 let order_num = $('#hacchu_num').text();
 //$('#hacchu_num').remove();
 $('#order_barcode').html('<svg id="barcode_hacchu"></svg>');
 $('#order_barcode_print').html('<svg id="barcode_hacchu_print"></svg>');
 generateBarcode('barcode_hacchu',order_num);
 JsBarcode("#barcode_hacchu_print",order_num,{width: 1.4, height: 30,fontSize: 12});
</script>