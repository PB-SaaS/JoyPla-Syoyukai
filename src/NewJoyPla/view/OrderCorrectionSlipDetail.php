<div id="app">
<div class="animsition uk-margin-bottom no_print" uk-height-viewport="expand: true">
    <div class="uk-section uk-section-default uk-preserve-color uk-padding-remove uk-margin-top no_print" id="page_top">
        <div class="uk-container uk-container-expand">
            <ul class="uk-breadcrumb no_print">
                <li><a href="%url/rel:mpg:top%">TOP</a></li>
                <li><a href="<?php echo $link ?>&table_cache=true"><span><?php echo $link_title ?></span></a></li>
                <li><a href="%url/card:page_263320%&table_cache=true"><span>発注書</span></a></li>
                <li><span>発注書訂正</span></li>
            </ul>
                <div class="uk-child-width-1-2@m no_print uk-margin" uk-grid>
                    <div class="uk-text-left">
                        <input class="print_hidden uk-button uk-button-primary" type="button" value="訂正確定" v-on:click="success">
                    </div>
                </div>
                <div class="uk-text-center uk-text-large">
                    <p class="uk-text-bold title_spacing" style="font-size: 32px">発注書</p>
                </div>

                <div class="" id="tablearea">
                    <div class="uk-overflow-auto">
                        <table class="uk-table uk-table-hover uk-table-middle uk-table-divider" id="tbl-Items">
                            <thead>
                                <tr>
                                    <th class="uk-text-nowrap">No</th>
                                    <th class="uk-text-nowrap">商品情報</th>
                                    <th class="uk-text-nowrap">価格</th>
                                    <th class="uk-text-nowrap">入数</th>
                                    <th class="uk-text-nowrap">発注数</th>
                                    <th class="uk-text-nowrap">変更したい数</th>
                                    <th class="uk-text-nowrap">入庫数</th>
                                    <th class="uk-text-nowrap">金額</th>
                                </tr>
                            </thead>
                            <tbody>
						        <tr v-for="(item, key) in items" :id="'tr_' + key" v-bind:class="item.class">
							        <td class="uk-text-nowrap">{{key + 1 }}</td>
                                    <td>
                                        <div class="uk-width-medium uk-flex uk-flex-wrap uk-flex-wrap-around ">
                                            <span class="uk-width-1-3">メーカー</span>
                                            <span class="uk-width-2-3">{{ item.makerName }}</span><br>
                                            <span class="uk-width-1-3">商品名</span>
                                            <span class="uk-width-2-3">{{ item.itemName }}</span><br>
                                            <span class="uk-width-1-3">製品コード</span>
                                            <span class="uk-width-2-3">{{ item.itemCode }}</span><br>
                                            <span class="uk-width-1-3">規格</span>
                                            <span class="uk-width-2-3">{{ item.itemStandard }}</span><br>
                                            <span class="uk-width-1-3">JANコード</span>
                                            <span class="uk-width-2-3">{{ item.itemJANCode }}</span>
                                        </div>
                                    </td>
                                    <td class="uk-text-nowrap">￥{{item.price | number_format}}</td>
                                    <td class="uk-text-nowrap">{{item.quantity}}{{item.quantityUnit}}</td>
                                    <td class="uk-text-nowrap">{{item.orderQuantity}}{{item.itemUnit}}</td>
                                    <td class="uk-text-nowrap">
                                        <input type="number" :max="item.max" :min="item.min"  v-model="item.orderQuantityCorrection" class="uk-input uk-width-small joypla-333" :class="{'change': ( item.orderQuantityCorrection != item.orderQuantity) ,'error': ( error[key] )}" >{{item.itemUnit}}
                                    </td>
                                    <td class="uk-text-nowrap">{{item.receivingNum}}{{item.itemUnit}}</td>
                                    <td class="uk-text-nowrap">￥{{item.orderPrice | number_format}}</td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                    <div class="uk-width-expand uk-margin">
                        <table class="uk-table uk-table-middle uk-table-divider">
                            <thead>
                                <tr>
                                    <td>備考</td>
                                </tr>
                            </thead>
                            <tbody>
                                <tr>
                                    <td>
                                        <textarea class="uk-textarea uk-width-1-1" rows="10" name="ordercomment" v-model="comment" placeholder="備考を記入..." :class="{'change': ( comment != raw_commnt) ,'error': ( error['comment'] )}"></textarea>
                                    </td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
    
</div>


<script>
    
var app = new Vue({
	el: '#app',
	data: {
		items: <?php echo json_encode($order_items); ?>,
        raw_commnt: `%val:usr:ordercomment%`,
        comment: `%val:usr:ordercomment%`,
        error: [],
	},
	filters: {
        number_format: function(value) {
            if (! value ) { return 0; }
            return value.toString().replace( /([0-9]+?)(?=(?:[0-9]{3})+$)/g , '$1,' );
        },
    },
    watch: {
    },
	methods: {
        success: function(){
            UIkit.modal.confirm('この内容で訂正しますか').then(function(){
                $.ajax({
                    async: false,
                    url: "%url/card:page_263320%",
                    type:'POST',
                    data:{
                        _csrf: "<?php echo $csrf_token ?>",  // CSRFトークンを送信
                        Action : "orderItemUpdate",
                        items : JSON.stringify(app.items),
                        comment: encodeURI(app.comment),
                    },
                    dataType: 'json'
                })
                // Ajaxリクエストが成功した時発動
                .done( (data) => {
                })
                // Ajaxリクエストが失敗した時発動
                .fail( (data) => {
                })
                // Ajaxリクエストが成功・失敗どちらでも発動
                .always( (data) => {
                    if(data.code === 102)
                    {
                        app.error = data.data;
                    }
                    UIkit.modal.alert(data.message).then(function()
                    {
                        if(data.code === 0)
                        {
                            location.href = "%url/card:page_263320%";
                        }
                    });
                });
            });
        }
	},
}); 

</script>