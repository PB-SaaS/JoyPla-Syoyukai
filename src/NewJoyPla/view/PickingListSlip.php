<div id="app" class="animsition" uk-height-viewport="expand: true">
    <div class="uk-section uk-section-default uk-preserve-color uk-padding-remove uk-margin-top" id="page_top">
        <div class="uk-container uk-container-expand">
            <ul class="uk-breadcrumb no_print">
                <li><a href="%url/rel:mpg:top%">TOP</a></li>
                	<li><a href="%url/rel:mpg:top%&path=payout">払出メニュー</a></li>
                <li><a :href="picking_list_url">ピッキングリスト一覧</a></li>
                <li><span>ピッキングリスト</span></li>
            </ul>
            <div class="no_print uk-margin-bottom" uk-margin>
                <input class="print_hidden uk-button uk-button-default" type="button" value="印刷プレビュー" onclick="window.print();return false;">
                <input class="print_hidden uk-button uk-button-danger" type="button" value="ピッキングリスト削除" v-on:click="picking_slip_delete" v-if="picking_status == '1'">
                <input class="print_hidden uk-button uk-button-primary" type="button" value="ピッキング確定" v-on:click="picking_slip_commit" v-if="picking_status == '1'">
            </div>
            <div class="uk-text-center uk-text-large">
                <p class="uk-text-bold title_spacing uk-text-large">ピッキングリスト</p>
            </div>
            <div uk-grid>
                <div class="uk-width-1-3@m uk-width-1-2@s">
                    <table class="uk-table uk-width-1-1 uk-table-divider">
                        <tr>
                            <td class="uk-text-bold">登録日時</td>
                            <td class="uk-text-right">%val:usr:registrationTime%</td>
                        </tr>
                        <tr>
                            <td class="uk-text-bold">払出元部署</td>
                            <td class="uk-text-right">{{ division_name }}</td>
                        </tr>
                        <tr>
                            <td class="uk-text-bold">品目数</td>
                            <td class="uk-text-right">{{ stocks.length }}</td>
                        </tr>
                    </table>
                </div>
                <div class="uk-width-1-3@m uk-visible@m"></div>
                <div class="uk-width-1-3@m uk-width-1-2@s uk-text-center">
                    <svg id='barcode'></svg>
                </div>
            </div>
            <div class="uk-margin" v-if="picking_status != '2'">
                <div>
                    <p class="uk-text-bold uk-text-large uk-margin-remove">理論在庫数</p>
                </div>
                <template v-if="stocks_sort.length > 0">
                    <div class="uk-overflow-auto">  
                        <table class="uk-table uk-table-middle uk-table-divider" id="tbl-Items">
                            <thead>
                                <tr>
                                    <th class="uk-table-shrink">No</th>
                                    <th class="uk-width-1-2">商品情報</th>
                                    <th class="uk-text-nowrap">棚名</th>
                                    <th class="uk-text-nowrap">理論在庫数</th>
                                    <th class="uk-text-nowrap">ピックアップ総数</th>
                                </tr>
                            </thead>
                            <tbody>
                                <tr v-for="(stock , index ) in stocks_sort">
                                    <td class="uk-table-shrink">{{ ( index + 1 ) }}</td>
                                    <!--
                                    <td>{{ stock.makerName }}</td>
                                    <td>{{ stock.itemName }}</td>
                                    <td>{{ stock.itemCode }}</td>
                                    <td>{{ stock.itemStandard }}</td>
                                    <td>{{ stock.itemJANCode }}</td>
                                    -->
                                    <td>
                                        <div uk-grid margin="0">
                                            <div class="uk-width-1-4 uk-text-muted">メーカー</div>
                                            <div class="uk-width-3-4">{{ stock.makerName }}</div>
                                            <div class="uk-width-1-4 uk-text-muted">商品名</div>
                                            <div class="uk-width-3-4">{{ stock.itemName }}</div>
                                            <div class="uk-width-1-4 uk-text-muted">製品コード</div>
                                            <div class="uk-width-3-4">{{ stock.itemCode }}</div>
                                            <div class="uk-width-1-4 uk-text-muted">規格</div>
                                            <div class="uk-width-3-4">{{ stock.itemStandard }}</div>
                                            <div class="uk-width-1-4 uk-text-muted">JANコード</div>
                                            <div class="uk-width-3-4">{{ stock.itemJANCode }}</div>
                                        </div>
                                    </td>
                                    <td>{{ stock.rackName }}</td>
                                    <td>{{ stock.stockQuantity }}{{ stock.quantityUnit }}</td>
                                    <td>{{ get_total_payout_num(stock.inHospitalItemId) }}{{ stock.quantityUnit }}</td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </template>
                <template v-else>
                    <p class="uk-text-center">表示する情報がありません</p>
                </template>
            </div>
            <div class="uk-margin">
                <div>
                    <p class="uk-text-bold uk-text-large uk-margin-remove">ピッキング商品</p>
                </div>
                <template v-if="payout_schedule_items_sort.length > 0">
                    <div class="uk-overflow-auto">
                        <table class="uk-table uk-table-middle uk-table-divider uk-table-justify uk-table-small">
                            <thead>
                                <tr>
                                    <th class="uk-table-shrink">No</th>
                                    <th class="uk-width-1-2">商品情報</th>
                                    <th class="uk-text-nowrap">払出先<br>カードID</th>
                                    <th class="uk-text-nowrap" v-if="picking_status != '2'">理論在庫<br>(合算値)</th>
                                    <th class="uk-text-nowrap">ピックアップ数</th>
                                    <th class="uk-text-nowrap">ステータス</th>
                                </tr>
                            </thead>
                            <tbody>
                                <tr v-for="(p , index ) in payout_schedule_items_sort">
                                    <td class="uk-table-shrink">{{ ( index + 1 ) }}</td>
                                    <td>
                                        <div uk-grid margin="0">
                                            <div class="uk-width-1-4 uk-text-muted">メーカー</div>
                                            <div class="uk-width-3-4">{{ get_stock_object(p.inHospitalItemId).makerName }}</div>
                                            <div class="uk-width-1-4 uk-text-muted">商品名</div>
                                            <div class="uk-width-3-4">{{ get_stock_object(p.inHospitalItemId).itemName }}</div>
                                            <div class="uk-width-1-4 uk-text-muted">製品コード</div>
                                            <div class="uk-width-3-4">{{ get_stock_object(p.inHospitalItemId).itemCode }}</div>
                                            <div class="uk-width-1-4 uk-text-muted">規格</div>
                                            <div class="uk-width-3-4">{{ get_stock_object(p.inHospitalItemId).itemStandard }}</div>
                                            <div class="uk-width-1-4 uk-text-muted">JANコード</div>
                                            <div class="uk-width-3-4">{{ get_stock_object(p.inHospitalItemId).itemJANCode }}</div>
                                        </div>
                                    </td>
                                    <td>{{ p.targetDivision }}<br><span v-if="p.cardId">{{ p.cardId }}</span></td>
                                    <td v-if="picking_status != '2'">
                                        <span >{{ get_calc_stock_num(p.inHospitalItemId, index)  }}{{ get_stock_object(p.inHospitalItemId).quantityUnit }}</span>
                                    </td>
                                    <td>{{ p.payoutQuantity }}{{ get_stock_object(p.inHospitalItemId).quantityUnit }}</td>
                                    <td class="uk-text-nowrap">
                                        <template v-if="picking_status == '1'">
                                            <select v-model="p.outOfStockStatus" class="uk-select" :class="{change : p.outOfStockStatusChange }" @change="p.outOfStockStatusChange = true" >
                                                <option value="2">払出可能</option>
                                                <option value="3">欠品</option>
                                            </select>
                                        </template>
                                        <template v-if="picking_status == '2'">
                                            <span>ピッキング済み</span>
                                        </template>
                                    </td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </template>
                <template v-else>
                    <p class="uk-text-center">表示する情報がありません</p>
                </template>
            </div>
        </div>
    </div>
</div>
<script src="https://i02.smp.ne.jp/u/joypla/new/components/pagination.js"></script>
<script src="https://i02.smp.ne.jp/u/joypla/new/components/table-offset.js"></script>
<script src="https://i02.smp.ne.jp/u/joypla/new/components/limit-select.js"></script>
<script src="https://i02.smp.ne.jp/u/joypla/new/components/sort-link.js"></script>
<script>
let register_data = {
     csrf_token: "<?php echo $csrf_token ?>",
     stocks: <?php echo json_encode($stock) ?>,
     pay_schedule_items: <?php echo json_encode($pay_schedule_items) ?>,
     picking_list_url: "<?php echo $picking_list_url ?>&Action=pickingList&table_cache=true",
     division_name: "<?php echo $division_name ?>",
     picking_id: "<?php echo $picking_id ?>",
     picking_status : "<?php echo $picking_status ?>",
};
var app = new Vue({
	el: '#app',
	data: {
        csrf_token: register_data.csrf_token,
        picking_list_url: register_data.picking_list_url,
        stocks: register_data.stocks,
        division_name : register_data.division_name,
        picking_id : register_data.picking_id,
        pay_schedule_items : register_data.pay_schedule_items,
        picking_status : register_data.picking_status,
    },
    created(){
        JsBarcode("#barcode",this.picking_id,{width: 1.8, height: 40,fontSize: 14});
        let vm = this;
        if(vm.picking_status == '1')
        {
            vm.payout_schedule_items_sort.forEach(function (elm, i) {
                vm.set_calc_stock_status(elm.inHospitalItemId, i);
            });
        }
    },
	filters: {
        number_format: function(value) {
            if (! value ) { return 0; }
            return new Intl.NumberFormat('ja-JP').format(value);
        },
        date_format: function(value)
        {
            let arr = [{'format':'年','replace':'-'},{'format':'月','replace':'-'},{'format':'日','replace':''},{'format':'時','replace':':'},{'format':'分','replace':':'},{'format':'秒','replace':''}]
            let str = value;
            for (const element of arr) {
                str = str.replace(element.format, element.replace);
            }

            let today = new Date(str);
            let year = today.getFullYear();
            let month = today.getMonth() + 1;
            let day = today.getDate();
            return ( year + '年' + month + '月' + day + '日');
        },
    },
    computed: {
		stocks_sort() {
			return this.stocks.sort((a, b) => {
				return a.inHospitalItemId - b.inHospitalItemId;
			});
		},
		payout_schedule_items_sort() {
			return this.pay_schedule_items.sort((a, b) => {
				return a.inHospitalItemId - b.inHospitalItemId;
			});
		},
    },
    methods:{
        get_stock_object : function(in_hospital_id)
        {
            if(!in_hospital_id){ return {} }
            return this.stocks_sort.find(stock => stock.inHospitalItemId === in_hospital_id);
        },
        get_total_payout_num : function(in_hospital_id)
        {
            let stock = 0;
            
			this.payout_schedule_items_sort.forEach(function (elm, i) {
                if(in_hospital_id === elm.inHospitalItemId){
                    stock += parseInt(elm.payoutQuantity);
                }
            });

            return stock;
        },
        set_calc_stock_status : function(in_hospital_id, index)
        {
            let stock = this.get_calc_stock_num(in_hospital_id, index);
            if( ( stock - this.payout_schedule_items_sort[index].payoutQuantity ) >= 0 )
            {
                this.payout_schedule_items_sort[index].outOfStockStatus = 2;
            }
            else
            {
                this.payout_schedule_items_sort[index].outOfStockStatus = 3;
            }
        },
        get_calc_stock_num : function(in_hospital_id, index)
        {
            let stock = this.get_stock_object(in_hospital_id).stockQuantity;
            
			this.payout_schedule_items_sort.forEach(function (elm, i) {
                if(index > i && in_hospital_id === elm.inHospitalItemId){
                    stock -= elm.payoutQuantity;
                }
            });

            return stock;
        },
        picking_slip_delete: function()
        {
            UIkit.modal.confirm('ピッキングリストを削除します。よろしいですか<br><p class="uk-text-danger">【注意】ピッキング商品は払出予定商品に戻します</p>').then(function () {
                $.ajax({
                    async: true,
                    url : "<?php echo $api_url ?>",
                    type:'POST',
                    data:{
                        Action: 'pickingSlipDelete',
                        _csrf: app.csrf_token,
                        picking_id: app.picking_id
                    },
                    dataType: 'json'
                })
                // Ajaxリクエストが成功した時発動
                .done( (data) => {
                    if(data.code != 0){
                        UIkit.modal.alert('ピッキングリスト削除に失敗しました').then(function(){
                            location.reload();
                        })
                        return false;
                    }
                    UIkit.modal.alert('ピッキングリスト削除が完了しました').then(function () {
                        location.href = app.picking_list_url;
                    });
                })
                // Ajaxリクエストが失敗した時発動
                .fail( (data) => {
                        UIkit.modal.alert('ピッキングリスト削除に失敗しました');
                });
            },function(){
                UIkit.modal.alert('ピッキングリスト削除を中止しました');
                return false;
            });
        },
        picking_slip_commit: function()
        {
            UIkit.modal.confirm('ピッキングを確定します。よろしいですか<br><p class="uk-text-danger">【注意】欠品のものは払出予定商品に戻します</p>').then(function () {
                $.ajax({
                    async: true,
                    url : "<?php echo $api_url ?>",
                    type:'POST',
                    data:{
                        Action: 'pickingSlipCommit',
                        _csrf: app.csrf_token,
                        picking_id: app.picking_id,
                        pay_schedule_items: JSON.stringify( objectValueToURIencode(app.pay_schedule_items) ),
                    },
                    dataType: 'json'
                })
                // Ajaxリクエストが成功した時発動
                .done( (data) => {
                    if(data.code != 0){
                        UIkit.modal.alert('ピッキング確定に失敗しました').then(function(){
                            location.reload();
                        })
                        return false;
                    }
                    UIkit.modal.alert('ピッキング確定が完了しました').then(function () {
                        location.reload();
                    });
                })
                // Ajaxリクエストが失敗した時発動
                .fail( (data) => {
                        UIkit.modal.alert('ピッキング確定に失敗しました');
                });
            },function(){
                UIkit.modal.alert('ピッキング確定を中止しました');
                return false;
            });
        }
    }
});
</script>