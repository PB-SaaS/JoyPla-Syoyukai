    <style>
		
		a.top-to-icon {
			zoom : 1.4;
		}
		
		.page_title{
		}
		
		.shouhin-data{
		}
		
		.id{
			min-width: 24px;
			max-width: 24px;
		}
		
		.shouhin-data .itemName{
			font-size: 14px;
		}
		.shouhin-data .makerName{
			font-size: 12px;
			
		}
		.shouhin-data .tana{
			font-size: 8px;
		}
		.shouhin-data .itemCode{
			font-size: 8px;
			
		}
		.shouhin-data .constant{
			font-size: 8px;
			
		}
		.shouhin-data .quantity{
			font-size: 8px;
			
		}
		.shouhin-data .price{
			font-size: 8px;
			
		}
		.shouhin-data .JANCode{
			font-size: 8px;
			
		}
		.shouhin-data .officialFlag{
			font-size: 8px;
			
		}
		.itemCount{
			position: relative;
		}
		.itemCount->after {
		   content: attr(data-format); /* ここが重要!! */
		   width: 10%;
		   height: 20px;
		   position: absolute;
		   bottom: 4px;
		}
		.itemCountInput{
			width: 90%;
		}
		.uk-table th, .uk-table td{
			word-break: break-word;
			padding: 12px 8px;
			vertical-align: middle;
		}
		.uk-table tfoot tr{
			border-bottom: #e5e5e5 1px solid;
			border-top: #e5e5e5 1px solid;
		}
		
		table.uk-table {
			counter-reset: rowCount;
		}

		table.uk-table > tbody > tr {
			counter-increment: rowCount;
		}

		table.uk-table > tbody > tr > td:first-child::before {
			content: counter(rowCount);
		}

		.change {
			background : rgb(255, 204, 153);
			color : rgb(68, 68, 68);
		}

		.error {
			border : red 2px solid;
		}
		
    </style>
    <div id="app" class="animsition" uk-height-viewport="expand: true">
	  	<div class="uk-section uk-section-default uk-preserve-color uk-padding-remove uk-margin-top" id="page_top">
		    <div class="uk-container uk-container-expand">
		    	<ul class="uk-breadcrumb">
				    <li><a href="%url/rel:mpg:top%">TOP</a></li>
                	<li><a href="%url/rel:mpg:top%&page=page1">消費・発注</a></li>
				    <li><span>個別入荷内容入力</span></li>
				</ul>
		    	<h2 class="page_title">個別入荷内容入力</h2>
		    	<hr>
				<div uk-grid>
					<div class="uk-width-1-3@m">
						<label class="uk-form-label" > </label>
						<div class="uk-form-controls">
							<select class="uk-select" v-model="divisionId" v-bind:disabled="lists.length > 0">
								<option value="">----- 入荷先部署選択 -----</option>
								<option v-for="d in division" :value="d.divisionId">{{ d.divisionName }}</option>
							</select>
						</div>
						<span class="uk-text-danger"> </span>
					</div>
				</div>
				<div class="uk-margin-bottom uk-margin-top" uk-margin>
					<button class="uk-button uk-button-default" v-on:click="sanshouClick">商品マスタを開く</button>
					<button class="uk-button uk-button-default" type="button" onclick="window.print();return false;">印刷プレビュー</button>
					<button class="uk-button uk-button-primary" v-on:click="regist">入荷処理実行</button>
				</div>
				<div uk-sticky="sel-target: .uk-navbar-container; cls-active: uk-navbar-sticky" class="uk-padding-top uk-background-muted uk-padding-small">
					<form action='#' method="post"  onsubmit="app.barcodeSearch($('input[name=barcode]').val() , '' ,'' ,true);$('input[name=barcode]').val('') ; $('input[name=barcode]').focus();return false;">
						<input type="text" class="uk-input uk-width-4-5" placeholder="バーコード入力..." name="barcode" autocomplete="off">
						<button class="uk-button uk-button-primary uk-float-right uk-width-1-5 uk-padding-remove" type="submit">検索</button>
					</form>
				</div>
				<div class=" uk-width-expand uk-overflow-auto">
					<table class="uk-table uk-table-striped">
						<thead>
							<tr>
								<th class="uk-text-nowrap">id</th>
								<th class="uk-width-1-3">商品情報</th>
								<th class="uk-text-nowrap">入数</th>
								<th class="uk-text-nowrap">価格</th>
								<th class="uk-text-nowrap">ロット管理</th>
								<th class="uk-table-expand">ロット番号</th>
								<th class="uk-table-expand">使用期限</th>
								<th class="uk-text-nowrap">入庫数</th>
								<th></th>
								<th></th>
							</tr>
						</thead>
						<tbody>
							<tr v-for="(list, key) in lists" :id="'tr_' + key" v-bind:class="list.class">
								<td>{{list.text}}</td>
								<td>
									<div uk-grid margin="0">
                                            <div class="uk-width-1-4 uk-text-muted">メーカー</div>
                                            <div class="uk-width-3-4">{{ list.maker }}</div>
                                            <div class="uk-width-1-4 uk-text-muted">商品名</div>
                                            <div class="uk-width-3-4">{{ list.shouhinName }}</div>
                                            <div class="uk-width-1-4 uk-text-muted">製品コード</div>
                                            <div class="uk-width-3-4">{{ list.code }}</div>
                                            <div class="uk-width-1-4 uk-text-muted">規格</div>
                                            <div class="uk-width-3-4">{{ list.kikaku }}</div>
                                            <div class="uk-width-1-4 uk-text-muted">JANコード</div>
                                            <div class="uk-width-3-4">{{ list.jan }}</div>
                                        </div>
								</td>
								<td class="uk-text-nowrap">{{list.irisu}}{{list.unit}}</td>
								<td class="uk-text-nowrap">￥{{list.kakaku | number_format}}</td>
								<td class="uk-text-center">
									<span v-if="list.lotFlagBool == 1" class="uk-text-danger">必須</span>
									<span v-else >任意</span>
								</td>
								<td>
									<input type="text" maxlength="20" class="uk-input lot" v-model="list.lotNumber" style="width:180px" v-bind:class="{'change':(list.lotNumber), 'error':list.error_class.lotNumber }" v-on:change="list.error_class.lotNumber = false;">
								</td>
								<td>
									<input type="date" class="uk-input lotDate" v-model="list.lotDate" v-bind:class="{'change':(list.lotDate), 'error':list.error_class.lotDate }" v-on:change="list.error_class.lotDate = false;">
								</td>
								<td class="uk-text-nowrap">
									<input type="number" class="uk-input uk-width-small" style="width: 96px" v-model="list.count" v-bind:class="{'change':(list.count), 'error':list.error_class.count }" v-on:change="list.error_class.count = false;">
									<span class="uk-text-bottom">{{list.itemUnit}}</span>
								</td>
								<td>
									<input type="button" class="uk-button uk-button-danger uk-button-small" value="削除" v-on:click="deleteList(key)">
								</td>
								<td class="uk-text-nowrap">
									<input type="button" class="uk-button uk-button-default uk-button-small" value="追加" v-on:click="copyList(key)">
								</td>
							</tr>
						</tbody>
						<tfoot>
							<tr>
								<td colspan="19">&emsp;</td>
							</tr>
							<tr>
								<td colspan="19">&emsp;</td>
							</tr>
							<tr>
								<td colspan="19">&emsp;</td>
							</tr>
						</tfoot>
					</table>
				</div>
		    </div>
		</div>
	</div>
	
	<form action="<?php echo $api_url ?>&Action=payoutLabel" target="_blank" method="post" class="uk-hidden" name="LabelCreate">
		<input type="hidden" value="" name="payoutHistoryId" id="payoutHistoryId">
		<input type="hidden" value="payout" name="pattern">
	</form>
	
	
	<div id="modal-sections" class="uk-modal-container" uk-modal>
	    <div class="uk-modal-dialog">
	        <button class="uk-modal-close-default" type="button" uk-close></button>
	        <div class="uk-modal-header">
	            <h2 class="uk-modal-title">商品選択</h2>
	        </div>
	        <div class="uk-modal-body uk-width-expand uk-overflow-auto">
	         	<table class="uk-table uk-table-hover uk-table-striped uk-table-condensed uk-table-divider">
					<thead>
						<tr>
							<th class="uk-text-nowrap">id</th>
							<th class="uk-text-nowrap"></th>
							<th class="uk-table-expand">メーカー</th>
							<th class="uk-table-expand">商品名</th>
							<th class="uk-table-expand">製品コード</th>
							<th class="uk-table-expand">規格</th>
							<th class="uk-table-expand">JANコード</th>
							<th class="uk-table-expand">入数</th>
							<th class="uk-table-expand">価格</th>
							<th class="uk-table-expand">卸業者</th>
							<th class="uk-table-expand">ロット管理フラグ</th>
						</tr>
					</thead>
					<tbody>
						<tr v-for="(list , key) in select_items">
							<td></td>
							<td><button type="button" v-on:click="addObject(key)" class="uk-text-nowrap uk-button uk-button-primary uk-button-small">反映</button></td>
							<td class="uk-text-middle">{{list.maker}}</td>
							<td class="uk-text-middle">{{list.shouhinName}}</td>
							<td class="uk-text-middle">{{list.code}}</td>
							<td class="uk-text-middle">{{list.kikaku}}</td>
							<td class="uk-text-middle">{{list.jan}}</td>
							<td class="uk-text-middle">
							<span class="irisu uk-text-nowrap">{{list.irisu}}</span><span class="unit uk-text-small">{{list.unit}}</span>
							</td>
							<td class="uk-text-middle">￥{{list.kakaku}}</td>
							<td class="uk-text-middle">{{list.oroshi}}</td>
							<td class="uk-text-middle">{{list.lotFlag}}</td>
						</tr>
					</tbody>
				</table>   
	        </div>
	    </div>
	</div>
<script>

let resister = {
	'division' : <?php echo json_encode($division) ?>,
};

var app = new Vue({
	el: '#app',
	data: {
		lists: [],
		change_class: {'count':false,'lotDate':false,'lotNumber':false},
		error_class: {'count':false,'lotDate':false,'lotNumber':false},
		division: resister.division,
		divisionId: "<?php echo ($user_info->isUser())? $user_info->getDivisionId() : "" ; ?>",
	},
	filters: {
        number_format: function(value) {
            if (! value ) { return 0; }
            return new Intl.NumberFormat('ja-JP').format(value);
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
		addList: function(object) {
			object.count = (object.count)? object.count : 0 ;
			object.change_class = JSON.parse(JSON.stringify(this.change_class));
			object.error_class = JSON.parse(JSON.stringify(this.error_class));
			this.lists.push(object);
		},
		copyList: function(key) {
			let copy = JSON.parse(JSON.stringify(this.lists[key]));
			copy.lotNumber = "";
			copy.lotDate = "";
			copy.count = "";
			copy.change_class = JSON.parse(JSON.stringify(this.change_class));
			copy.error_class = JSON.parse(JSON.stringify(this.error_class));
			this.lists.splice(( key + 1 ), 0, copy);
		},
		deleteList: function(key) {
			this.lists.splice(key, 1);
		},
		sanshouClick: function() {
			if(! this.divisionCheck()){
				return false;
			}
			window.open('%url/rel:mpgt:page_175973%', '_blank','scrollbars=yes,width=1220,height=600');
		},
		divisionCheck : function(){
			if(! this.divisionId){
				UIkit.modal.alert('入荷先部署を選択してください');
				return false ;
			}
			return true;
		},
        validate:function(){
            if (app.lists.length == 0) {
                UIkit.modal.alert('商品を選択してください');
                return false ;
            }
          
			let checkflg = true;
			app.lists.forEach(function (elem, index) {
				let changeObject = app.lists[index];
				changeObject.error_class.count = false;
				if(app.lists[index].count == 0){
					changeObject.error_class.count = true;
					checkflg = false;
				}
				app.$set(app.lists, index, changeObject);
			});
			
			if(!checkflg){
				UIkit.modal.alert('数量を入力してください');
				return false ;
			}
          
			let chkLot = true;
			app.lists.forEach(function (elem, index) {
				let changeObject = app.lists[index];
				changeObject.error_class.lotNumber = false;
				changeObject.error_class.lotDate = false;
				if(app.lists[index].count > 0 && app.lists[index].lotFlagBool == 1) {
					if( !( app.lists[index].lotNumber && app.lists[index].lotDate)) {
						changeObject.error_class.lotDate = true;
						changeObject.error_class.lotNumber = true;
				    	chkLot = false;
					}
				}
				app.$set(app.lists, index, changeObject);	
			});
			if(!chkLot){
				UIkit.modal.alert('ロット管理が必須のものはすべてロット情報を入力してください');
				return false ;
			}
			
			chkLot = true;
			app.lists.forEach(function (elem, index) {
				let changeObject = app.lists[index];
				changeObject.error_class.lotNumber = false;
				changeObject.error_class.lotDate = false;
				if(app.lists[index].count > 0) {
					if(!app.lists[index].lotNumber && app.lists[index].lotDate){
						changeObject.error_class.lotNumber = true;
			    		chkLot = false;
					}
					else if(app.lists[index].lotNumber && !app.lists[index].lotDate) {
						changeObject.error_class.lotDate = true;
			    		chkLot = false;
					}
				}
				app.$set(app.lists, index, changeObject);
			});
			
			if(!chkLot){
				UIkit.modal.alert('ロット情報を入力してください');
				return false ;
			}
			
          
			chkLot = true;
			let regex = /^[a-zA-Z0-9!-/:-@¥[-`{-~]+$/;
			app.lists.forEach(function (elem, index) {
				let changeObject = app.lists[index];
				changeObject.error_class.lotNumber = false;
				if(app.lists[index].lotNumber) {
					if((!regex.test(app.lists[index].lotNumber)) ||
					   (encodeURI(app.lists[index].lotNumber).replace(/%../g, '*').length > 20)) {
						changeObject.error_class.lotNumber = true;
			    		chkLot = false;
					}
				}
				app.$set(app.lists, index, changeObject);
			});
			
			if(!chkLot){
				UIkit.modal.alert('ロット番号の入力を確認してください');
				return false ;
			}
			
			return true;
          
        },
		regist: function(){
			UIkit.modal.confirm('入荷処理を実行します<br>よろしいですか').then(function(){
				if(! app.validate()){
					return false;
				}
				$.ajax({
	                url: "<?php echo $api_url ?>",
					type:'POST',
					data:{
	                	Action : 'individualRegistApi',
						_csrf: "<?php echo $csrf_token ?>",  // CSRFトークンを送信
	                	divisionId : app.divisionId,
	                	items : JSON.stringify( objectValueToURIencode(app.lists) ),
					},
					dataType: 'json'
				})
				// Ajaxリクエストが成功した時発動
				.done( (data) => {
	                if(data.code == '1'){
	            		UIkit.modal.alert(data.message);
	            		return false;
	                }
	                if(data.code != '0'){
	            		UIkit.modal.alert("入荷処理に失敗しました");
	            		return false;
	                }

	                UIkit.modal.alert(data.message).then(function(){
						UIkit.modal.confirm('ラベル発行しますか').then(function(){
							//別タブ　%url/rel:@mpgt:ReceivingLabel%&receivingId=
							const url = '%url/rel:mpgt:ReceivingLabel%&receivingId='+(Object.keys(data.data)).join(',')
							window.open(url, '_blank')
						});
						app.lists.splice(0, app.lists.length);
					});
				})
				// Ajaxリクエストが失敗した時発動
				.fail( (data) => {
	                UIkit.modal.alert("入荷処理に失敗しました");
				})
				// Ajaxリクエストが成功・失敗どちらでも発動
				.always( (data) => {
				});
				
			},function(){
				UIkit.modal.alert("中止しました");
			});
		},
		barcodeSearch: function(barcode , lotNumber , lotDate , gs1_128_search_flg) {
			if(! this.divisionCheck()){
				return false;
			}
			let exist_card = false;
			$.ajax({
				async: false,
                url:'%url/rel:mpgt:labelBarcodeSAPI%',
                type:'POST',
                data:{
					_csrf: "<?php echo $csrf_token ?>",  // CSRFトークンを送信
                	divisionId : app.source_division,
                	barcode : barcode,
                },
                dataType: 'json'
            })
            // Ajaxリクエストが成功した時発動
            .done( (data) => {
            	let value = 0;
                if(data.code != 0 || data.data.length == 0){
                	if(gs1_128_search_flg)
                	{
						gs1_128.check_gs1_128(barcode);
                	} else {
            			UIkit.modal.alert("商品が見つかりませんでした");
                	}
            		return false;
                }
                if(data.count == 1)
                {
                	data = data.data;
                	if(lotNumber != ''){
                		data.lotNumber = lotNumber;
                	}
                	if(lotDate != ''){
                		data.lotDate = lotDate;
                	}
	                data.count = 1;
                	app.addList(data);
	                
	                $('input[name="barcode"]').val('');
                } else {
                	data = data.data;
                	modal_sections.clear();
                	for(let num = 0 ; num < data.length ; num++)
                	{
						if(exist_card)
						{
							data[num].cardNum = barcode;
						}
	                	data[num].lotNumber = lotNumber;
	                	data[num].lotDate = lotDate;
	                	data[num].count = 1;
                		modal_sections.addList(data[num]);
                	}
            		UIkit.modal.alert("複数の商品が見つかりました").then(function(){
            			modal_sections.openModal();
            		});
                }
            })
            // Ajaxリクエストが失敗した時発動
            .fail( (data) => {
                UIkit.modal.alert("商品が見つかりませんでした");
            })
            // Ajaxリクエストが成功・失敗どちらでも発動
            .always( (data) => {
				loading_remove();
            });
		}
	}
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
            return new Intl.NumberFormat('ja-JP').format(value);
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
			app.addList(JSON.parse(JSON.stringify(this.select_items[index])));
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
		check_gs1_128: function (gs1128)
		{
			if(gs1128.indexOf("]C1") !== 0){
				//UIkit.modal.alert("GS1-128ではありません");
				//return ;
				return this.check_gs1_128("]C1"+gs1128);
			}
			
			gs1128 = gs1128.slice( 3 );
			let obj = check_gs1128(gs1128);
				
			if(!obj.hasOwnProperty("01")){
				UIkit.modal.alert("商品情報が含まれておりませんでした。").then(function(){
				});
				return;
			}
			
			let searchJan = gs1_01_to_jan(obj["01"]);
			let objkey = null;
			let setObj = {};
			
			let objLotNumber = (obj["10"] === void 0) ? "" : obj["10"]; //lotNumber
			let objLotDate = (obj["17"] === void 0) ? "" : this.changeDate(obj["17"]); //lotDate
			let existflg = false;
			let changeObject = null;
			
			
			if(!existflg){
				app.barcodeSearch(searchJan,objLotNumber,objLotDate , false);
				//UIkit.modal.alert("対象の発注商品が見つかりませんでした。").then(function(){
				//	UIkit.modal($('#gs1-128')).show();
				//});
			}
		}
	}
});

function addTr(object, type, count){
	object.payoutCountStyle = {};
	object.payoutCount = 0;
	object.cardNum = '';
	object.exist_card = false;
	object.target_division = '';
	app.addList(object);
}
</script>