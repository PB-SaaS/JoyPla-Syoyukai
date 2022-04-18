<div id="app">
<div class="animsition uk-margin-bottom no_print" uk-height-viewport="expand: true">
    <div class="uk-section uk-section-default uk-preserve-color uk-padding-remove uk-margin-top no_print" id="page_top">
        <div class="uk-container uk-container-expand">
            <ul class="uk-breadcrumb no_print">
                <li><a href="%url/rel:mpg:top%">TOP</a></li>
                <li><a href="<?php echo $link ?>&table_cache=true"><span><?php echo $link_title ?></span></a></li>
                <li><span>発注書</span></li>
            </ul>
                <div class="uk-child-width-1-2@m no_print uk-margin" uk-grid>
                    <div class="uk-text-left">
                        <input class="print_hidden uk-button uk-button-default" type="button" value="印刷プレビュー" onclick="window.print();return false;">
                        <?php if($user_info->isUser() || $user_info->isAdmin()): ?>
                        <input class="print_hidden uk-button uk-button-danger" type="button" value="発注書取消" v-on:click="slipDelete" v-bind:disabled="delete_disabled">
                        <?php endif ?>
                        <?php if($user_info->isApprover() || $user_info->isAdmin()): ?>
                        <input class="print_hidden uk-button uk-button-default" type="button" value="発注書訂正" v-on:click="correction">
                        <?php endif ?>
                        <?php if($user_info->isUser() || $user_info->isAdmin()): ?>
                        <input class="print_hidden uk-button uk-button-primary" type="button" value="納品照合" v-on:click="receiving">
                        <?php endif ?>
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
                        <table class="uk-table uk-table-hover uk-table-middle uk-table-divider" id="tbl-Items">
                            <thead>
                                <tr>
                                    <th class="uk-text-nowrap">No</th>
                                    <th class="uk-width-1-2">商品情報</th>
                                    <?php /*
                                    <th class="uk-table-expand" style="min-width:60px">メーカー</th>
                                    <th class="uk-table-expand" style="min-width:150px">商品名</th>
                                    <th class="uk-table-expand">製品コード</th>
                                    <th class="uk-table-expand">規格</th>
                                    <th class="uk-table-expand">JANコード</th>
                                    */ ?>
                                    <th class="uk-text-nowrap">価格</th>
                                    <th class="uk-text-nowrap">入数</th>
                                    <th class="uk-text-nowrap">発注数</th>
                                    <th class="uk-text-nowrap">入庫数</th>
                                    <th class="uk-text-nowrap">入庫可能数</th>
                                    <th class="uk-text-nowrap">今回入庫数</th>
                                    <th class="uk-text-nowrap">納期</th>
                                    <th class="uk-text-nowrap">金額</th>
                                    <?php if($user_info->isUser() || $user_info->isAdmin()): ?>
                                    <th class="uk-text-nowrap">入庫リストへ転記</th>
                                    <?php endif ?>
                                </tr>
                            </thead>
                            <tbody>
						        <tr v-for="(item, key) in items" :id="'tr_' + key" v-bind:class="item.class">
							        <td class="uk-text-nowrap">{{key + 1 }}</td>
                                    <?php /*
                                    <td>{{item.makerName}}</td>
                                    <td>{{item.itemName}}</td>
                                    <td>{{item.itemCode}}</td>
                                    <td>{{item.itemStandard}}</td>
                                    <td>{{item.itemJANCode}}</td>
                                    */ ?>
                                    <td>
                                        <div uk-grid margin="0">
                                            <div class="uk-width-1-4 uk-text-muted">メーカー</div>
                                            <div class="uk-width-3-4">{{ item.makerName }}</div>
                                            <div class="uk-width-1-4 uk-text-muted">商品名</div>
                                            <div class="uk-width-3-4">{{ item.itemName }}</div>
                                            <div class="uk-width-1-4 uk-text-muted">製品コード</div>
                                            <div class="uk-width-3-4">{{ item.itemCode }}</div>
                                            <div class="uk-width-1-4 uk-text-muted">規格</div>
                                            <div class="uk-width-3-4">{{ item.itemStandard }}</div>
                                            <div class="uk-width-1-4 uk-text-muted">JANコード</div>
                                            <div class="uk-width-3-4">{{ item.itemJANCode }}</div>
                                        </div>
                                    </td>
                                    <td class="uk-text-nowrap">￥{{item.price | number_format}}</td>
                                    <td class="uk-text-nowrap">{{item.quantity}}{{item.quantityUnit}}</td>
                                    <td class="uk-text-nowrap">{{item.orderQuantity}}{{item.itemUnit}}</td>
                                    <td class="uk-text-nowrap">{{item.receivingNum}}{{item.itemUnit}}</td>
                                    <td class="uk-text-nowrap">{{item.possibleNumber}}{{item.itemUnit}}</td>
                                    <td class="uk-text-nowrap" v-bind:class="item.nowCountClass">{{item.nowCount}}{{item.itemUnit}}</td>
                                    <td class="uk-text-nowrap">{{item.dueDate}}</td>
                                    <td class="uk-text-nowrap">￥{{item.orderPrice | number_format}}</td>
                                    <?php if($user_info->isUser() || $user_info->isAdmin()): ?>
                                    <td class="uk-text-nowrap"><button type="button" class="uk-button uk-button-primary" v-on:click="add(item)">転記</button></td>
                                    <?php endif ?>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </div>
                
                <?php if($user_info->isUser() || $user_info->isAdmin()): ?>
                <div class="uk-margin" id="receivingTable">
                    <p class="uk-text-bold uk-text-large">入庫リスト</p>
                    <div uk-sticky="sel-target: .uk-navbar-container; cls-active: uk-navbar-sticky" class="uk-padding-top uk-background-muted uk-padding-small">
                        <form action='#' method="post" onsubmit="gs1_128.check_gs1_128();$('input[name=barcode]').focus();return false;">
                            <input type="text" class="uk-input uk-width-4-5" placeholder="バーコード入力..." name="barcode" autocomplete="off" v-model="barcode">
                            <button class="uk-button uk-button-primary uk-float-right uk-width-1-5 uk-padding-remove" type="button" onclick="gs1_128.check_gs1_128()">検索</button>
                        </form>
                    </div>
                    
                    <div class="uk-overflow-auto">
                        <table class="uk-table uk-table-hover uk-table-middle uk-table-divider" id="tbl-Items">
                            <thead>
                                <tr>
                                    <th class="uk-text-nowrap">NO</th>
                                    <?php /*
                                    <th class="uk-table-expand" style="min-width:60px">メーカー</th>
                                    <th class="uk-table-expand" style="min-width:150px">商品名</th>
                                    <th class="uk-table-expand">製品コード</th>
                                    <th class="uk-table-expand">規格</th>
                                    <th class="uk-table-expand">JANコード</th>
                                    */ ?>
                                    <th class="uk-width-1-2">商品情報</th>
                                    <th class="uk-text-nowrap">価格</th>
                                    <th class="uk-text-nowrap">入数</th>
                                    <th class="uk-text-nowrap">入庫数</th>
                                    <th class="uk-text-nowrap">ロット管理</th>
                                    <th class="uk-table-expand">ロット番号</th>
                                    <th class="uk-table-expand">使用期限</th>
                                    <th></th>
                                </tr>
                            </thead>
                            <tbody>
						        <tr v-for="(list, key) in lists" :id="'tr_' + key" v-bind:class="list.class">
							        <td class="uk-text-nowrap">{{key + 1 }}</td>
                                    <?php /*
                                    <td>{{list.makerName}}</td>
                                    <td>{{list.itemName}}</td>
                                    <td>{{list.itemCode}}</td>
                                    <td>{{list.itemStandard}}</td>
                                    <td>{{list.itemJANCode}}</td>
                                    */ ?>
                                    <td>
                                        <div uk-grid margin="0">
                                            <div class="uk-width-1-4 uk-text-muted">メーカー</div>
                                            <div class="uk-width-3-4">{{ list.makerName }}</div>
                                            <div class="uk-width-1-4 uk-text-muted">商品名</div>
                                            <div class="uk-width-3-4">{{ list.itemName }}</div>
                                            <div class="uk-width-1-4 uk-text-muted">製品コード</div>
                                            <div class="uk-width-3-4">{{ list.itemCode }}</div>
                                            <div class="uk-width-1-4 uk-text-muted">規格</div>
                                            <div class="uk-width-3-4">{{ list.itemStandard }}</div>
                                            <div class="uk-width-1-4 uk-text-muted">JANコード</div>
                                            <div class="uk-width-3-4">{{ list.itemJANCode }}</div>
                                        </div>
                                    </td>
                                    <td class="uk-text-nowrap">￥{{list.price | number_format}}</td>
                                    <td class="uk-text-nowrap">{{list.quantity}}{{list.quantityUnit}}</td>
        							<td class="uk-text-nowrap">
        								<input type="number" step="1" class="uk-input" style="width: 96px;" v-bind:max="list.max" v-bind:min="list.min" v-bind:style="list.countStyle" v-model="list.countNum" v-bind:disabled="list.countNumDisabled" v-on:change="addCountStyle(key);changeCountNum(key)">
        								<span class="uk-text-bottom">{{list.itemUnit}}</span>
        							</td>
        							<td class="uk-text-nowrap">
        								<span v-if="list.lotManagement == 1" class="uk-text-danger">必須</span>
        								<span v-else >任意</span>
        							</td>
        							<td class="uk-text-nowrap">
        								<input type="text" maxlength="20" class="uk-input lot" style="width:180px" v-model="list.lotNumber" v-bind:style="list.lotNumberStyle" v-on:change="addLotNumberStyle(key)">
        							</td>
							        <td class="uk-text-nowrap">
                                        <input type="date" class="uk-input lotDate" v-model="list.lotDate" v-bind:style="list.lotDateStyle" v-on:change="addLotDateStyle(key)">
							        </td>
							        <td class="uk-text-nowrap">
								        <input type="button" class="uk-button uk-button-danger uk-button-small" value="削除" v-on:click="deleteList(key)">
                                    </td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </div>
                <?php endif ?>
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
                            <td>{{ item.distributorMCode }}</td>
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



<form action="%url/rel:mpgt:ReceivingLabel%" target="_blank" method="post" class="no_print uk-inline" id="createLabelForm">
    <!-- <input type="hidden" value="" name="itemsData" id="itemsData"> -->
    <input type="hidden" id="receivingId" name="receivingId">
    <input type="hidden" value="%val:usr:distributorName%" name="distributorName">
</form>

<div id="modal-sections" class="uk-modal-container no_print" uk-modal>
    <div class="uk-modal-dialog">
        <button class="uk-modal-close-default" type="button" uk-close></button>
        <div class="uk-modal-header">
            <h2 class="uk-modal-title">商品選択</h2>
        </div>
        <div class="uk-modal-body uk-width-expand uk-overflow-auto">
            <table class="uk-table uk-table-hover uk-table-striped uk-table-condensed uk-table-divider">
                <thead>
                    <tr>
                        <th class="uk-text-nowrap">NO</th>
                        <th class="uk-table-expand" style="min-width:60px">メーカー</th>
                        <th class="uk-table-expand" style="min-width:150px">商品名</th>
                        <th class="uk-table-expand">製品コード</th>
                        <th class="uk-table-expand">規格</th>
                        <th class="uk-table-expand">JANコード</th>
                        <th class="uk-text-nowrap">価格</th>
                        <th class="uk-text-nowrap">入数</th>
                        <th class="uk-text-nowrap">発注数</th>
                        <th class="uk-text-nowrap">入庫数</th>
                        <th class="uk-text-nowrap">入庫可能数</th>
                        <th class="uk-text-nowrap">今回入庫数</th>
                        <th class="uk-text-nowrap">納期</th>
                        <th class="uk-text-nowrap">金額</th>
                        <th class="uk-text-nowrap">入庫リストへ転記</th>
                    </tr>
                </thead>
                <tbody>
                    <tr v-for="(list , key) in select_items" >
                        <td class="uk-text-nowrap">{{key + 1 }}</td>
                        <td class="uk-table-expand">{{list.makerName}}</td>
                        <td class="uk-table-expand">{{list.itemName}}</td>
                        <td class="uk-table-expand">{{list.itemCode}}</td>
                        <td class="uk-table-expand">{{list.itemStandard}}</td>
                        <td class="uk-table-expand">{{list.itemJANCode}}</td>
                        <td class="uk-text-nowrap">￥{{list.price | number_format}}</td>
                        <td class="uk-text-nowrap">{{list.quantity}}{{list.quantityUnit}}</td>
                        <td class="uk-text-nowrap">{{list.orderQuantity}}{{list.itemUnit}}</td>
                        <td class="uk-text-nowrap">{{list.receivingNum}}{{list.itemUnit}}</td>
                        <td class="uk-text-nowrap">{{list.possibleNumber}}{{list.itemUnit}}</td>
                        <td class="uk-text-nowrap" v-bind:class="list.nowCountClass">{{list.nowCount}}{{list.itemUnit}}</td>
                        <td class="uk-text-nowrap">{{list.dueDate}}</td>
                        <td class="uk-text-nowrap">￥{{list.orderPrice | number_format}}</td>
                        <td class="uk-text-nowrap"><button type="button" class="uk-button uk-button-primary" v-on:click="addObject(key)">転記</button></td>
                    </tr>
                </tbody>
            </table>   
        </div>
    </div>
</div>

<script>
    
var app = new Vue({
	el: '#app',
	data: {
	    totalAmount: "%val:usr:totalAmount%",
	    delete_disabled: ("%val:usr:orderStatus:id%" != "2"),
		items: <?php echo json_encode($orderItems); ?>,
		lists: [],
		divisionId: '',
		division_disabled: false,
		barcode : '',
	},
	filters: {
        number_format: function(value) {
            if (! value ) { return 0; }
            return value.toString().replace( /([0-9]+?)(?=(?:[0-9]{3})+$)/g , '$1,' );
        },
    },
    watch: {
        lists: function() {
            this.$nextTick(function() {
                if($('.target').length > 0){
                    $(window).scrollTop($('.target').offset().top - 100);
                    app.lists.forEach(function(elem, index) {
                        let changeObject = null;
                        changeObject = app.lists[index];
        				changeObject.class.target = false;
        				app.$set(app.lists, index, changeObject);
    			    });
                }
            })
        }
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
	    add: function(item){
	        item = JSON.parse(JSON.stringify(item));
	        item.lotNumber = '';
	        item.lotDate = '';
	        item.countNum = 0;
	        this.addItemToList(item);
	    },
	    addItemToList : function(itemObject) {
	        let existflg = false;
			itemObject = JSON.parse(JSON.stringify(itemObject));
            app.lists.forEach(function(elem, index) {
                let changeObject = app.lists[index];
                if( !existflg &&
                    changeObject.orderCNumber == itemObject.orderCNumber && 
                    changeObject.lotDate == itemObject.lotDate && 
                    changeObject.lotNumber == itemObject.lotNumber)
                {
                    if( ( parseInt(changeObject.countNum) + parseInt(itemObject.countNum) ) !== parseInt(changeObject.countNum))
                    {
                        changeObject.countStyle = { 'backgroundColor' : "rgb(255, 204, 153)" , 'color' : "rgb(68, 68, 68)"};
                    }
                    changeObject.countNum = parseInt(changeObject.countNum) + parseInt(itemObject.countNum);
                    existflg = true;
           	        app.$set(app.lists, index, changeObject);
                }
		    });
			if(!existflg)
			{
			    this.addList(itemObject);
			}
			
           	this.nowCountUpdate();
	    },
		deleteList: function(key) {
			this.lists.splice(key, 1);
           	this.nowCountUpdate();
		},
		addList: function(object) {
			object.class = ((!object.class)? {'target' : true} : object.class);
			object.countNum = ((!object.countNum)? 0 : object.countNum);
			object.lotNumber = ((!object.lotNumber)? '': object.lotNumber); 
			object.lotDate = ((!object.lotDate)? '' : object.lotDate);
			object.countStyle =  ((!object.countStyle)? {} : object.countStyle);
			object.lotDateStyle =  ((!object.lotDateStyle)? {} : object.lotDateStyle);
			object.lotNumberStyle =  ((!object.lotNumberStyle)? {} : object.lotNumberStyle);
			this.lists.push(object);
		},
		changeCountNum : function(index){
            let changeObject = app.lists[index];
            if( ! ( parseInt(changeObject.countNum) >= parseInt(changeObject.min) && parseInt(changeObject.countNum) <= parseInt(changeObject.max) ) ) { 
                if(! ( parseInt(changeObject.countNum) >= parseInt(changeObject.min)) )
                {
                    changeObject.countNum = parseInt(changeObject.min) ;
                } 
                else if(! ( parseInt(changeObject.countNum) <= parseInt(changeObject.max)) )
                {
                    changeObject.countNum = parseInt(changeObject.max) ;
                }
            }
           	app.$set(app.lists, index, changeObject);
           	this.nowCountUpdate();
		},
		nowCountUpdate: function(){
            let changeItem = {};
            app.lists.forEach(function(elem, index) {
                if (! (elem.orderCNumber in changeItem) ){ changeItem[elem.orderCNumber] = 0 }
				changeItem[elem.orderCNumber] = changeItem[elem.orderCNumber] + parseInt(elem.countNum);
		    });
		    
            app.items.forEach(function(elem, index) {
                let changeObject = null;
                let num = 0
                Object.keys(changeItem).forEach(function (key) {
                    if(elem.orderCNumber == key )
                    {
                        num = changeItem[key];
                    }
                });
                changeObject = app.items[index];
                changeObject.nowCount = num;
                changeObject.nowCountClass = {'uk-text-danger' : false};
                if(
                    ( ( changeObject.possibleNumber >= 0 ) && ! (changeObject.possibleNumber >= changeObject.nowCount) ) ||
                    ( ( changeObject.possibleNumber < 0 ) && ! (changeObject.possibleNumber <= changeObject.nowCount) )
                )
                {
                    app.lists.forEach(function(list_elem, list_index) {
                        if(changeObject.orderCNumber === list_elem.orderCNumber)
                        {
                            list_elem.countStyle.border = 'red 2px solid';
				            app.$set(app.lists , list_index, list_elem);
                        }
        		    });
                    changeObject.nowCountClass = {'uk-text-danger' : true};
                    UIkit.modal.alert("今回入庫数が入庫可能数を上回っています。<br>ご確認ください。")
                }
				app.$set(app.items , index, changeObject);
            });
		},
		addCountStyle: function(index){
			let changeObject = app.lists[index];
			changeObject.countStyle = { 'backgroundColor' : "rgb(255, 204, 153)" , 'color' : "rgb(68, 68, 68)"};
			app.$set(app.lists, index, changeObject);
		},
		addLotNumberStyle: function(index){
			let changeObject = app.lists[index];
			changeObject.lotNumberStyle = { 'backgroundColor' : "rgb(255, 204, 153)" , 'color' : "rgb(68, 68, 68)"};
			app.$set(app.lists, index, changeObject);
		},
		addLotDateStyle: function(index){
			let changeObject = app.lists[index];
			changeObject.lotDateStyle = { 'backgroundColor' : "rgb(255, 204, 153)" , 'color' : "rgb(68, 68, 68)"};
			app.$set(app.lists, index, changeObject);
		},
		slipDelete: function(){
		    if(this.delete_disabled)
		    {
		        UIkit.modal.alert("取り消しは行えません");
		        return false;
		    }
            UIkit.modal.confirm("発注を取り消しますか").then(function () {
                loading();
    			$.ajax({
                    async: false,
                    url:'<?php echo $api_url ?>',
                    type:'POST',
                    data:{
                        _csrf: "<?php echo $csrf_token ?>",  // CSRFトークンを送信
                    	Action : "orderedDeleteAPI",
                    },
                    dataType: 'json'
                })
                // Ajaxリクエストが成功した時発動
                .done( (data) => {
                    if(data.code != 0){
                        UIkit.modal.alert("発注の取り消しに失敗しました");
                        return false;
                    }
                
                    UIkit.modal.alert("発注を取り消しました").then(function(){
                        location.href = "<?php echo $link ?>&table_cache=true";
                    });
                })
                // Ajaxリクエストが失敗した時発動
                .fail( (data) => {
                    UIkit.modal.alert("発注の取り消しに失敗しました");
                })
                // Ajaxリクエストが成功・失敗どちらでも発動
                .always( (data) => {
                    loading_remove();
                });
            }, function () {
                UIkit.modal.alert("中止します");
            });
		},
        correction: function(){
            UIkit.modal.confirm("発注書を訂正しますか").then(function(){
                location.href = "%url/card:page_263320%&Action=correction";
            });
        },
		lotCheck: function(){
			
			let chkLot = true;
			app.lists.forEach(function (elem, index) {
				elem.lotNumberStyle.border = '';
				elem.lotDateStyle.border = '';
				if(app.lists[index].lotManagement == 1) {
					if( !( app.lists[index].lotNumber && app.lists[index].lotDate)) {
						let changeObject = app.lists[index];
						changeObject.lotNumberStyle.border = 'red 2px solid';
						changeObject.lotDateStyle.border = 'red 2px solid';
						app.$set(app.lists, index, changeObject);
				    	chkLot = false;
					}
				}
			});
			if(!chkLot){
				UIkit.modal.alert('ロット管理が必須のものはすべてロット情報を入力してください');
				return false ;
			}
			
			chkLot = true;
			app.lists.forEach(function (elem, index) {
				elem.lotNumberStyle.border = '';
				elem.lotDateStyle.border = '';
				if(app.lists[index].countNum != 0) {
					if(!app.lists[index].lotNumber && app.lists[index].lotDate){
						let changeObject = app.lists[index];
						changeObject.lotNumberStyle.border = 'red 2px solid';
						app.$set(app.lists, index, changeObject);
			    		chkLot = false;
					}
					else if(app.lists[index].lotNumber && !app.lists[index].lotDate) {
						let changeObject = app.lists[index];
						changeObject.lotDateStyle.border = 'red 2px solid';
						app.$set(app.lists, index, changeObject);
			    		chkLot = false;
					}
				}
			});
			
			if(!chkLot){
				UIkit.modal.alert('ロット情報を入力してください');
				return false ;
			}
			
			chkLot = true;
			let regex = /^[a-zA-Z0-9!-/:-@¥[-`{-~]+$/;
			app.lists.forEach(function (elem, index) {
				elem.lotNumberStyle.border = '';
				if(app.lists[index].lotNumber) {
					if((!regex.test(app.lists[index].lotNumber)) ||
					   (encodeURI(app.lists[index].lotNumber).replace(/%../g, '*').length > 20)) {
						let changeObject = app.lists[index];
						changeObject.lotNumberStyle.border = 'red 2px solid';
						app.$set(app.lists, index, changeObject);
			    		chkLot = false;
					}
				}
			});
			
			if(!chkLot){
				UIkit.modal.alert('ロット番号の入力を確認してください');
				return false ;
			}
			
			return true;
        },
		check: function(){
			
			if(app.lists.length === 0){
				UIkit.modal.alert('入庫リストに商品を追加してください');
				return false ;
			}
			
			let checkflg = false;
			app.items.forEach(function (elem, index) {
			    if(Math.abs(elem.possibleNumber) < Math.abs(elem.nowCount) )
			    {
			        checkflg = true;
			    }
			});
			
			
			if(checkflg){
			    UIkit.modal.alert('入庫数が入庫可能数を上回っています');
				return false ;
			}
			
			checkflg = true;
			app.lists.forEach(function (elem, index) {
			    if(elem.countNum == 0)
			    {
                    let changeObject = app.lists[index];
    				changeObject.countStyle = {'border': 'red 2px solid'};
    				app.$set(app.lists, index, changeObject);
			        checkflg = false;
			    }
			});
			
			if(!checkflg){
				UIkit.modal.alert('入庫数を確認してください');
				return false ;
			}
			
			return true;
		},
		label_create_check: function(){
			let checkflg = false;
			app.lists.forEach(function (elem, index) {
			    if(elem.countNum > 0)
			    {
			        checkflg = true; // 一つでも+があればOK
			    }
			});
			return checkflg;
		},
		receiving: function(){
            UIkit.modal.confirm("納品照合を行いますか").then(function () {
                if(!app.check())
                {
                    return false;
                }
                if(!app.lotCheck())
                {
                    return false;
                }
                loading();
    			$.ajax({
                    async: false,
                    url:'<?php echo $api_url ?>',
                    type:'POST',
                    data:{
                        _csrf: "<?php echo $csrf_token ?>",  // CSRFトークンを送信
                    	Action : "receivingAPI",
                        receiving : JSON.stringify( objectValueToURIencode(app.lists) ),
                    },
                    dataType: 'json'
                })
                // Ajaxリクエストが成功した時発動
                .done( (data) => {
                    if(data.code != 0){
                        UIkit.modal.alert("納品照合に失敗しました");
                        return false;
                    }
                
                    UIkit.modal.alert("納品照合が完了しました").then(function(){
                        if(app.label_create_check()){
                            UIkit.modal.confirm("ラベル発行を行いますか").then(function(){
                                $("#receivingId").val(data.data.historyId);
                                $("#createLabelForm").submit();
                                location.reload();
                            },function(){
                                location.reload();
                            });
                        } else {
                            location.reload();
                        }
                    });
                })
                // Ajaxリクエストが失敗した時発動
                .fail( (data) => {
                    UIkit.modal.alert("納品照合に失敗しました");
                })
                // Ajaxリクエストが成功・失敗どちらでも発動
                .always( (data) => {
    				loading_remove();
                });
                }, function () {
                    UIkit.modal.alert("中止します");
            });
		},
	},
}); 


var modal_sections = new Vue({
	el:	'#modal-sections',
	data: {
		select_items: [],
        useUnitPrice: parseInt(<?php echo json_encode($useUnitPrice); ?>),
	},
	filters: {
        number_format: function(value) {
            if (! value ) { return 0; }
            return value.toString().replace( /([0-9]+?)(?=(?:[0-9]{3})+$)/g , '$1,' );
        },
    },
	methods: {
		clear: function(){
			let original = JSON.parse(JSON.stringify(this.select_items));
			this.select_items.splice(0, original.length);
		},
		addList: function(object){
			this.select_items.push(object);
		},
		addObject: function(index){
		    if(this.select_items[index].possibleNumber > 0){
		        this.select_items[index].countNum = 1;
		    } 
		    else if(this.select_items[index].possibleNumber < 0){
		        this.select_items[index].countNum = -1;
		    }
		    this.select_items[index].countStyle = { 'backgroundColor' : "rgb(255, 204, 153)" , 'color' : "rgb(68, 68, 68)"};
		    this.select_items[index].lotDateStyle = { 'backgroundColor' : "rgb(255, 204, 153)" , 'color' : "rgb(68, 68, 68)"};
		    this.select_items[index].lotNumberStyle = { 'backgroundColor' : "rgb(255, 204, 153)" , 'color' : "rgb(68, 68, 68)"};
			app.addItemToList(JSON.parse(JSON.stringify(this.select_items[index])));
		},
		openModal: function(){
        	UIkit.modal('#modal-sections').show();
		}
	}
});

var gs1_128 = new Vue({
	data: {
		gs1_128: {}
	},
	methods: {
		changeDate: function (text){
			if(!text){
				return "";
			}
			if(text.length == "6"){
				text = 20 + text;
			}
			let date = text.slice(6, 8);
			if(parseInt(text.slice(6, 8)) == 0){
				date = '01';
			}
			return text.slice(0, 4) + "-" + text.slice(4, 6) + "-" + date;
		},
		check_gs1_128: function ()
		{
		    gs1128 = app.barcode;
		    app.barcode = '';
		    
	        let searchJan = '';
	        let objLotNumber = '';
	        let objLotDate = '';
		    if(gs1128.length != 13){
    		        
    		    if(gs1128 == ''){
    		        return false;
    		    }
    			if(gs1128.indexOf("]C1") !== 0){
    				gs1128 = "]C1"+gs1128;
    			}
    			
    			gs1128 = gs1128.slice( 3 );
    			let obj = check_gs1128(gs1128);
    				
    			if(!obj.hasOwnProperty("01")){
    				UIkit.modal.alert("商品情報が含まれておりませんでした。").then(function(){
    				});
    				return;
    			}
                searchJan = gs1_01_to_jan(obj["01"]);
                objLotNumber = (obj["10"] === void 0) ? "" : obj["10"]; //lotNumber
                objLotDate = (obj["17"] === void 0) ? "" : this.changeDate(obj["17"]); //lotDate
		    } else {
		        searchJan = gs1128;
		        objLotNumber = '';
		        objLotDate = '';
		    }
			
        	modal_sections.clear();
        	let check_count = 0;
        	let isPossible = true;
        	let item = null ;
        	app.items.forEach(function(elem, index){
        	    if(elem.itemJANCode == searchJan && elem.possibleNumber != 0)
        	    {
        	        check_count++;
        	        isPossible = true;
        	        elem.lotNumber = objLotNumber;
        	        elem.lotDate = objLotDate;
        		    modal_sections.addList(elem);
        	        item = elem;
        	    }
        	    else if(elem.itemJANCode == searchJan && elem.possibleNumber == 0)
        	    {
        	        isPossible = false;
        	    }
        	    
        	});
        	if(!isPossible)
        	{
        	    UIkit.modal.alert("すでに入庫が完了しています");
        	}
        	else if(check_count == 0)
        	{
        	    UIkit.modal.alert("一致する商品が見つかりませんでした");
        	}
        	else if(check_count > 1)
        	{
        		UIkit.modal.alert("複数の商品が見つかりました").then(function(){
        			modal_sections.openModal();
        		});
        	}
        	else if(check_count == 1)
        	{
			    item.countStyle = { 'backgroundColor' : "rgb(255, 204, 153)" , 'color' : "rgb(68, 68, 68)"};
			    item.lotDateStyle = { 'backgroundColor' : "rgb(255, 204, 153)" , 'color' : "rgb(68, 68, 68)"};
			    item.lotNumberStyle = { 'backgroundColor' : "rgb(255, 204, 153)" , 'color' : "rgb(68, 68, 68)"};
			    item.countNum = 1;
        	    app.addItemToList(JSON.parse(JSON.stringify(item)));
        	}
        	
		}
	}
});

 let order_num = $('#hacchu_num').text();
 //$('#hacchu_num').remove();
 $('#order_barcode').html('<svg id="barcode_hacchu"></svg>');
 $('#order_barcode_print').html('<svg id="barcode_hacchu_print"></svg>');
 generateBarcode('barcode_hacchu',order_num);
 JsBarcode("#barcode_hacchu_print",order_num,{format: "ITF", width: 1.4, height: 30,fontSize: 12});

</script>