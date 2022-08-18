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
                	<li><a href="%url/rel:mpg:top%&path=payout">払出メニュー</a></li>
				    <li><span>払出予定商品入力</span></li>
				</ul>
		    	<h2 class="page_title">払出予定商品入力</h2>
		    	<hr>
				<div uk-grid>
					<div class="uk-width-1-3@m">
						<label class="uk-form-label" > </label>
						<div class="uk-form-controls">
							<select class="uk-select" v-model="source_division" v-bind:disabled="lists.length > 0">
								<option value="">----- 部署選択 -----</option>
								<?php
									foreach($source_division->data as $data)
									{
										if($data->divisionType === '1')
										{
											echo '<option value="'.$data->divisionId.'">'.$data->divisionName.'(大倉庫)</option>';
											echo '<option value="" disabled>--------------------</option>';
										}
									}
									foreach($source_division->data as $data)
									{
										if($data->divisionType === '2')
										{
											echo '<option value="'.$data->divisionId.'">'.$data->divisionName.'</option>';
										}
									}
								?>
							</select>
						</div>
						<span class="uk-text-danger"> </span>
					</div>
					<div class="uk-width-2-3@m uk-text-right">
						<div class="uk-form-controls">
							<input type="date" value="" class="uk-input uk-width-1-2@m" v-bind:class="payout_schedule_class" v-model="payout_schedule" v-on:change="payout_schedule_class.error = false">
						</div>
						<label class="uk-form-label" >払出予定日</label>
					</div>
				</div>
				<div class="uk-margin-bottom">
					<div>
						<div uk-margin>
							<button class="uk-button uk-button-default" v-on:click="sanshouClick">商品マスタを開く</button>
							<button class="uk-button uk-button-default" type="button" onclick="window.print();return false;">印刷プレビュー</button>
				    		<button class="uk-button uk-button-primary" v-on:click="regPayoutScheduledItems">払出予定商品登録</button>
						</div>
					</div>
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
								<th class="uk-table-expand">メーカー</th>
								<th class="uk-table-expand">商品名</th>
								<th class="uk-table-expand">製品コード</th>
								<th class="uk-table-expand">規格</th>
								<th class="uk-table-expand">JANコード</th>
								<th class="uk-text-nowrap">入数</th>
								<th class="uk-text-nowrap">価格</th>
								<th class="uk-text-nowrap">単価</th>
								<th class="uk-text-nowrap">払出先</th>
								<th class="uk-text-nowrap">払出数</th>
								<th class="uk-text-nowrap">カード番号</th>
								<th></th>
								<th></th>
							</tr>
						</thead>
						<tbody>
							<tr v-for="(list, key) in lists" :id="'tr_' + key" v-bind:class="list.class">
								<td>{{list.text}}</td>
								<td>{{list.maker}}</td>
								<td>{{list.shouhinName}}</td>
								<td>{{list.code}}</td>
								<td>{{list.kikaku}}</td>
								<td>{{list.jan}}</td>
								<td class="uk-text-nowrap">{{list.irisu}}{{list.unit}}</td>
								<td class="uk-text-nowrap">￥{{list.kakaku | number_format}}</td>
								<td class="uk-text-nowrap">
									￥<span v-if="useUnitPrice == 1">{{list.unitPrice | number_format}}</span>
									<span v-else>{{(list.kakaku / list.irisu)| number_format}}</span>
								</td>
								<td class="uk-text-nowrap">
									<select class="uk-select" v-model="list.target_division"  v-bind:class="{'change':list.change_class.target_division, 'error':list.error_class.target_division}" v-on:change="list.change_class.target_division = true;list.error_class.target_division = false" v-bind:disabled="list.exist_card">
										<option value="">----- 払出先選択 -----</option>
										<?php
											foreach($target_division->data as $data)
											{
												if($data->divisionType === '1')
												{
													echo '<option value="'.$data->divisionId.'">'.$data->divisionName.'(大倉庫)</option>';
												}
											}
											foreach($target_division->data as $data)
											{
												if($data->divisionType === '2')
												{
													echo '<option value="'.$data->divisionId.'">'.$data->divisionName.'</option>';
												}
											}
										?>
									</select>
								</td>
								<td class="uk-text-nowrap">
									<input type="number" class="uk-input uk-width-small" min="0" style="width: 96px" v-model="list.payoutCount" v-bind:class="{'change':list.change_class.payout_count, 'error':list.error_class.payout_count }" v-on:change="list.change_class.payout_count = true;list.error_class.payout_count = false" v-bind:disabled="list.exist_card">
									<span class="uk-text-bottom">{{list.unit}}</span>
								</td>
								<td>
									{{ list.cardNum }}
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

var app = new Vue({
	el: '#app',
	data: {
		lists: [],
		change_class: {'payout_count':false,'target_division':false},
		error_class: {'payout_count':false,'target_division':false},
		source_division: "<?php echo ($user_info->isUser())? $user_info->getDivisionId() : "" ; ?>",
		payout_schedule : '',
		payout_schedule_class : {'error': false},
        useUnitPrice: parseInt(<?php echo json_encode($useUnitPrice); ?>),
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
			object.source_division = this.source_division;
			object.change_class = JSON.parse(JSON.stringify(this.change_class));
			object.error_class = JSON.parse(JSON.stringify(this.error_class));
			this.lists.push(object);
		},
		copyList: function(key) {
			let copy = JSON.parse(JSON.stringify(this.lists[key]));
			copy.payoutCountStyle = {};
			copy.payoutCount = 0;
			copy.cardNum = '';
			copy.exist_card = false;
			copy.source_division = this.source_division;
			copy.target_division = '';
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
			if(! app.source_division){
				UIkit.modal.alert('払出元部署を選択してください');
				return false ;
			}
			return true;
		},
		validCheck: function(){
			let errorflg = true;
			
			if(app.lists.length === 0){
				UIkit.modal.alert('商品を選択してください');
				return false ;
			}
			
			app.payout_schedule_class = {'error': false};
			if(app.payout_schedule == ''){
				app.payout_schedule_class = {'error': true};
				UIkit.modal.alert('払出予定日を入力してください');
				return false ;
			}
			
			errorflg = false;
			app.lists.forEach(function (elem, index) {
				elem.error_class.payout_count = false;
				elem.error_class.payout_count = (app.lists[index].payoutCount <= 0);
				if(!errorflg)
				{
					errorflg = (app.lists[index].payoutCount <= 0);
				}
			});
			
			if(errorflg){
				UIkit.modal.alert('払出数は1以上で入力してください');
				return false ;
			}

			
			errorflg = false;
			app.lists.forEach(function (elem, index) {
				elem.error_class.target_division = false;
				elem.error_class.target_division = (app.lists[index].target_division == '');
				if(!errorflg)
				{
					errorflg = (app.lists[index].target_division == '');
				}
			});
			
			if(errorflg){
				UIkit.modal.alert('払出先部署を選択してください');
				return false ;
			}

			errorflg = false;
			app.lists.forEach(function (elem, index) {
				elem.error_class.target_division = (app.lists[index].target_division == app.lists[index].source_division);
				if(!errorflg)
				{
					errorflg = (app.lists[index].target_division == app.lists[index].source_division);
				}
			});
			
			if(errorflg){
				UIkit.modal.alert('払出元部署と払出先部署は同一のものを選択しないでください');
				return false ;
			}
			return true;
		},
		regPayoutScheduledItems: function(){
			
			UIkit.modal.confirm('払出予定商品を登録します<br>よろしいですか').then(function(){
				if(! app.validCheck()){
					return false;
				}
				if(! app.divisionCheck()){
					return false;
				}
				
				app.lists.forEach(function (elem, index) {
					app.lists[index].payout_schedule = app.payout_schedule;
				});
				
				$.ajax({
	                url: "<?php echo $api_url ?>",
					type:'POST',
					data:{
	                	Action : 'regPayoutScheduledApi',
						_csrf: "<?php echo $csrf_token ?>",  // CSRFトークンを送信
	                	items : JSON.stringify( objectValueToURIencode(app.lists) ),
					},
					dataType: 'json'
				})
				// Ajaxリクエストが成功した時発動
				.done( (data) => {
	                if(data.code != '0'){
	            		UIkit.modal.alert("払出予定商品登録に失敗しました");
	            		return false;
	                }
	                UIkit.modal.alert("払出予定商品登録が完了しました").then(function(){
						app.lists.splice(0, app.lists.length);
					});
				})
				// Ajaxリクエストが失敗した時発動
				.fail( (data) => {
	                UIkit.modal.alert("払出予定商品登録に失敗しました");
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
			if(barcode.indexOf("90") === 0 && barcode.length == 18)
			{
				exist_card = true;
				let exist = false;
				app.lists.forEach(function(elem, index) {
					if(app.lists[index].cardNum == barcode)
					{
						exist = true;
					}
				});
				if(exist)
				{
					UIkit.modal.alert("読み込み済みのカード情報です");
					return false;
				}
			}
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
					data.exist_card = exist_card;
					data.payoutCount = data.count;
					if(exist_card)
					{
						if(app.source_division == data.divisionId )
						{
							UIkit.modal.alert("払出元部署のカード情報です");
							return false;
						}
						data.cardNum = barcode;
						data.target_division = data.divisionId;
					}
                	if(lotNumber != ''){
                		data.lotNumber = lotNumber;
                	}
                	if(lotDate != ''){
                		data.lotDate = lotDate;
                	}
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