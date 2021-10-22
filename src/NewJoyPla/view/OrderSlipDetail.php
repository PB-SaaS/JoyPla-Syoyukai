<div id="app" class="animsition uk-margin-bottom" uk-height-viewport="expand: true">
    <div class="uk-section uk-section-default uk-preserve-color uk-padding-remove uk-margin-top" id="page_top">
        <div class="uk-container uk-container-expand">
            <ul class="uk-breadcrumb no_print">
                <li><a href="%url/rel:mpg:top%">TOP</a></li>
                <li><a href="<?php echo $link ?>&table_cache=true"><span>発注書一覧</span></a></li>
                <li><span>発注書</span></li>
            </ul>
                <div class="uk-child-width-1-2@m no_print uk-margin" uk-grid>
                    <div class="uk-text-left">
                        <input class="print_hidden uk-button uk-button-default" type="button" value="印刷プレビュー" onclick="window.print();return false;">
                        <input class="print_hidden uk-button uk-button-danger" type="button" value="発注書取消" v-on:click="slipDelete" v-bind:disabled="delete_disabled">
                        <input class="print_hidden uk-button uk-button-primary" type="button" value="納品照合" v-on:click="receiving">
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
                                    <th>入庫可能数</th>
                                    <th>今回入庫数</th>
                                    <th>納期</th>
                                    <th>金額</th>
                                    <th>入庫リストへ転記</th>
                                </tr>
                            </thead>
                            <tbody>
						        <tr v-for="(item, key) in items" :id="'tr_' + key" v-bind:class="item.class">
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
                                    <td>{{item.possibleNumber}}{{item.itemUnit}}</td>
                                    <td v-bind:class="item.nowCountClass">{{item.nowCount}}{{item.itemUnit}}</td>
                                    <td>{{item.dueDate}}</td>
                                    <td>￥{{item.orderPrice | number_format}}</td>
                                    <td><button type="button" class="uk-button uk-button-primary" v-on:click="add(item)">転記</td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </div>
                
                <div class="uk-margin" id="receivingTable">
                    <p class="uk-text-bold uk-text-large">入庫リスト</p>
                    <div uk-sticky="sel-target: .uk-navbar-container; cls-active: uk-navbar-sticky" class="uk-padding-top uk-background-muted uk-padding-small">
                        <form action='#' method="post" onsubmit="gs1_128.check_gs1_128();$('input[name=barcode]').focus();return false;">
                            <input type="text" class="uk-input uk-width-4-5" placeholder="バーコード入力..." name="barcode" autocomplete="off" v-model="barcode">
                            <button class="uk-button uk-button-primary uk-float-right uk-width-1-5 uk-padding-remove" type="button" onclick="gs1_128.check_gs1_128()">検索</button>
                        </form>
                    </div>
                    
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
                                    <th>入庫数</th>
                                    <th>ロット管理</th>
                                    <th>ロット番号</th>
                                    <th>使用期限</th>
                                    <th></th>
                                </tr>
                            </thead>
                            <tbody>
						        <tr v-for="(list, key) in lists" :id="'tr_' + key" v-bind:class="list.class">
							        <td>{{key + 1 }}</td>
                                    <td>{{list.makerName}}</td>
                                    <td>{{list.itemName}}</td>
                                    <td>{{list.itemCode}}</td>
                                    <td>{{list.itemStandard}}</td>
                                    <td>{{list.itemJANCode}}</td>
                                    <td>￥{{list.price | number_format}}</td>
                                    <td>{{list.quantity}}{{list.quantityUnit}}</td>
        							<td>
        								<input type="number" step="1" class="uk-input" style="width: 96px;" v-bind:max="list.max" v-bind:min="list.min" v-bind:style="list.countStyle" v-model="list.countNum" v-bind:disabled="list.countNumDisabled" v-on:change="addCountStyle(key);changeCountNum(key)">
        								<span class="uk-text-bottom">{{list.itemUnit}}</span>
        							</td>
        							<td>
        								<span v-if="list.lotManagement == 1" class="uk-text-danger">必須</span>
        								<span v-else >任意</span>
        							</td>
        							<td>
        								<input type="text" class="uk-input lot" style="width:180px" v-model="list.lotNumber" v-bind:style="list.lotNumberStyle" v-on:change="addLotNumberStyle(key)">
        							</td>
							        <td>
                                        <input type="date" class="uk-input lotDate" v-model="list.lotDate" v-bind:style="list.lotDateStyle" v-on:change="addLotDateStyle(key)">
							        </td>
							        <td>
								        <input type="button" class="uk-button uk-button-danger uk-button-small" value="削除" v-on:click="deleteList(key)">
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
                                <td class="uk-width-4-5">
                                    %val:usr:ordercomment:br%
                                </td>
                            </tr>
                        </tbody>
                    </table>
                </div>
        </div>
    </div>
</div>
<form action="%url/rel:mpgt:ReceivingLabel%" target="_blank" method="post" class="print_hidden uk-inline" id="createLabelForm">
    <!-- <input type="hidden" value="" name="itemsData" id="itemsData"> -->
    <input type="hidden" id="receivingId" name="receivingId">
    <input type="hidden" value="%val:usr:distributorName%" name="distributorName">
</form>

	<div id="modal-sections" class="uk-modal-container" uk-modal>
	    <div class="uk-modal-dialog">
	        <button class="uk-modal-close-default" type="button" uk-close></button>
	        <div class="uk-modal-header">
	            <h2 class="uk-modal-title">商品選択</h2>
	        </div>
	        <div class="uk-modal-body uk-width-expand uk-overflow-auto">
	         	<table class="uk-table uk-table-hover uk-table-striped uk-table-condensed uk-text-nowrap uk-table-divider">
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
                            <th>入庫可能数</th>
                            <th>今回入庫数</th>
                            <th>納期</th>
                            <th>金額</th>
                            <th>入庫リストへ転記</th>
						</tr>
					</thead>
					<tbody>
						<tr v-for="(list , key) in select_items" >
					        <td>{{key + 1 }}</td>
                            <td>{{list.makerName}}</td>
                            <td>{{list.itemName}}</td>
                            <td>{{list.itemCode}}</td>
                            <td>{{list.itemStandard}}</td>
                            <td>{{list.itemJANCode}}</td>
                            <td>￥{{list.price | number_format}}</td>
                            <td>{{list.quantity}}{{list.quantityUnit}}</td>
                            <td>{{list.orderQuantity}}{{list.itemUnit}}</td>
                            <td>{{list.receivingNum}}{{list.itemUnit}}</td>
                            <td>{{list.possibleNumber}}{{list.itemUnit}}</td>
                            <td v-bind:class="list.nowCountClass">{{list.nowCount}}{{list.itemUnit}}</td>
                            <td>{{list.dueDate}}</td>
                            <td>￥{{list.orderPrice | number_format}}</td>
                            <td><button type="button" class="uk-button uk-button-primary" v-on:click="addObject(key)">転記</td>
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
	    status: "%val:usr:orderStatus%",
	    delete_disabled: (this.status != 2),
		items: <?php echo json_encode($orderItems); ?>,
		lists: [],
		divisionId: '',
		division_disabled: false,
		barcode : '',
	},
	filters: {
        number_format: function(value) {
            if (! value ) { return false; }
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
                    changeObject.countStyle = { 'backgroundColor' : "rgb(255, 204, 153)" , 'color' : "rgb(68, 68, 68)"};
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
		    if(this.status != 2)
		    {
		        UIkit.modal.alert("取り消しは行えません");
		        return false;
		    }
            UIkit.modal.confirm("発注を取り消しますか").then(function () {
                
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
                        UIkit.modal.alert("発注取消に失敗しました");
                        return false;
                    }
                
                    UIkit.modal.alert("発注を取り消しました").then(function(){
                        location.href = "<?php echo $link ?>&table_cache=true";
                    });
                })
                // Ajaxリクエストが失敗した時発動
                .fail( (data) => {
                    UIkit.modal.alert("発注取消に失敗しました");
                })
                // Ajaxリクエストが成功・失敗どちらでも発動
                .always( (data) => {
    				loading_remove();
                });
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
			let regex = /^[0-9a-zA-Z]+$/;
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
			
			app.items.forEach(function (elem, index) {
			    if(elem.possibleNumber < elem.nowCount )
			    {
    				UIkit.modal.alert('入庫数が入庫可能数を上回っています');
    				return false ;
			    }
			});
			
			let checkflg = false;
			app.lists.forEach(function (elem, index) {
			    if(elem.countNum != 0)
			    {
			        checkflg = true;
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
                            UIkit.modal.alert("ラベル発行を行いますか").then(function(){
                                $("#receivingId").val(data.data.historyId);
                                $("#createLabelForm").submit();
                            });
                        }
                        location.reload();
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
            });
		}
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
            if (! value) { return false; }
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
			if(text == null){
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
        	let item = null ;
        	app.items.forEach(function(elem, index){
        	    if(elem.itemJANCode == searchJan && elem.possibleNumber != 0)
        	    {
        	        check_count++;
        	        elem.lotNumber = objLotNumber;
        	        elem.lotDate = objLotDate;
        		    modal_sections.addList(elem);
        	        item = elem;
        	    }
        	});
        	if(check_count == 0)
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
 generateBarcode('barcode_hacchu',order_num);
</script>