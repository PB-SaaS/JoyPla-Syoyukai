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
    </style>
    <div id="app" class="animsition" uk-height-viewport="expand: true">
	  	<div class="uk-section uk-section-default uk-preserve-color uk-padding-remove uk-margin-top" id="page_top">
		    <div class="uk-container uk-container-expand">
		    	<ul class="uk-breadcrumb">
				    <li><a href="%url/rel:mpg:top%">TOP</a></li>
				    <li><span>払出内容入力（カード対応版）</span></li>
				</ul>
		    	<h2 class="page_title">払出内容入力（カード対応版）</h2>
		    	<hr>
	    		<div class="uk-width-auto uk-text-right">
	    			<div class="uk-form-controls">
			            <input type="date" value="" class="uk-input uk-width-1-3@m" name="payoutDate">
		            </div>
	    			<label class="uk-form-label" >払出日指定</label>
		            <span class="uk-text-danger">※入力なしで現在時刻を取得します</span>
		        </div>
		    	<div class="shouhin-table uk-width-expand">
		    		<div uk-sticky="sel-target: .uk-navbar-container; cls-active: uk-navbar-sticky" class="uk-padding-top uk-background-muted uk-padding-small">
		    			<div uk-grid>
			    			<div class="uk-width-1-2">
			    				<label class="uk-form-label">払出元部署</label>
				    			<div class="uk-form-controls">
						            <select class="uk-width-4-5 uk-select uk-inline" v-model="sourceDivision" name="sourceDivision">
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
					            <form action='#' method="post" onsubmit="app.barcodeSearch($('input[name=barcode]').val() , '' ,'' ,true);$('input[name=barcode]').val('') ; $('input[name=barcode]').focus();return false;">
			    					<input type="text" class="uk-input uk-width-4-5" placeholder="バーコード入力...(GS1-128も可能)" autofocus="true" name="barcode" autocomplete="off">  
					    			<button class="uk-button uk-button-primary uk-float-right uk-width-1-5 uk-padding-remove" type="submit">検索</button>
								</form>
			    			</div>
			    			<div class="uk-width-1-2">
				    			<label class="uk-form-label" >払出先部署</label>
				    			<div class="uk-form-controls">
			    				   <select class="uk-select uk-width-4-5" v-model="targetDivision" name="targetDivision">
						                <option value="">----- 部署選択 -----</option>
				                        <?php
				                        foreach($target_division->data as $data)
				                        {
				                            if($data->divisionType === '1')
				                            {
				                                echo '<option value="'.$data->divisionId.'">'.$data->divisionName.'(大倉庫)</option>';
				                                echo '<option value="" disabled>--------------------</option>';
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
						        </div>
					            <form action='#' method="post" onsubmit="app.cardSearch($('input[name=barcode2]').val()); $('input[name=barcode2]').val('') ; $('input[name=barcode2]').focus(); return false;">
			    					<input type="text" class="uk-input uk-width-4-5" placeholder="カードのバーコード入力..." autofocus="true" name="barcode2" autocomplete="off">  
					    			<button class="uk-button uk-button-primary uk-float-right uk-width-1-5 uk-padding-remove" type="submit">検索</button>
								</form>
								<span class="uk-text-danger uk-text-small">カードを紐づける場合は払出数を0、枚数を1にしてください</span>
			    			</div>
		    			</div>
    				</div>
    				<div class="uk-margin">
			    		<div uk-margin>
				    		<button class="uk-button uk-button-default" v-on:click="sanshouClick">商品マスタを開く</button>
				    		<button class="uk-button uk-button-default" type="submit" onclick="window.print();return false;">印刷プレビュー</button>
				    		<button class="uk-button uk-button-primary uk-margin-small-top" v-on:click="sendPayout">払出実行</button>
			    		</div>
    				</div>
    				<div class=" uk-width-expand uk-overflow-auto">
			    		<table class="uk-table uk-table-striped uk-text-nowrap">
			    			<thead>
			    				<tr>
			    					<th colspan="9" class="uk-width-1-2">
			    						払出元情報
			    					</th>
			    					<th colspan="6" class="uk-width-1-2">
			    						払出先情報
		    						</th>
			    				</tr>
		    				</thead>
		    				<thead>
			    				<tr>
			    					<th>id</th>
			    					<th>メーカー</th>
			    					<th>商品名</th>
			    					<th>製品コード</th>
			    					<th>規格</th>
			    					<th>JANコード</th>
			    					<th>入数</th>
	                            	<th>価格</th>
	                            	<th>単価</th>
			    					<th>ロット管理</th>
			    					<th>ロット番号</th>
			    					<th>使用期限</th>
			    					<th>払出数
			    						<input type="button" class="uk-button uk-button-default uk-button-small" v-on:click="countToIrisu" value="入数を反映" >
			    					</th>
			    					<th>個数（ラベル枚数）</th>
			    					<th>合計払出数</th>
			    					<th>カード番号</th>
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
									<td>{{list.irisu}}{{list.unit}}</td>
									<td>￥{{list.kakaku | number_format}}</td>
									<td>
									    ￥<span v-if="useUnitPrice == 1">{{list.unitPrice | number_format}}</span>
									    <span v-else>{{(list.kakaku / list.irisu)| number_format}}</span>
									</td>
									<td class="uk-text-center">
										<span v-if="list.lotFlagBool == 1" class="uk-text-danger">必須</span>
										<span v-else >任意</span>
									</td>
									<td>
										<input type="text" class="uk-input lot" v-model="list.lotNumber" style="width:180px" v-bind:style="list.lotNumberStyle" v-on:change="addLotNumberStyle(key)">
									</td>
									<td>
										<input type="date" class="uk-input lotDate" v-model="list.lotDate" v-bind:style="list.lotDateStyle" v-on:change="addLotDateStyle(key)">
									</td>
									<td>
										<input type="number" step="1" class="uk-input" min="0" style="width: 96px;" v-bind:style="list.countStyle" v-model="list.countNum" v-bind:disabled="list.countNumDisabled" v-on:change="addCountStyle(key)">
										<span class="uk-text-bottom">{{list.unit}}</span>
									</td>
									<td>×
										<input type="number" step="1" class="uk-input" min="1" style="width: 96px;" v-bind:style="list.labelCountStyle" v-model="list.countLabelNum" v-bind:disabled="list.countLabelNumDisabled" v-on:change="addLabelCountStyle(key)">
										<span class="uk-text-bottom">枚</span>
									</td>
									<td>{{list.countNum * list.countLabelNum}}{{list.unit}}</td>
									<td>
										<input type="text" class="uk-input card" v-model="list.cardNum" readonly v-bind:style="list.cardStyle" >
									</td>
									<td>
										<input type="button" class="uk-button uk-button-danger uk-button-small" value="削除" v-on:click="deleteList(key)">
									</td>
									<td>
										<input type="button" class="uk-button uk-button-default uk-button-small" value="払出元情報複製" v-on:click="copyList(key)">
									</td>
								</tr>
			    			</tbody>
			    		</table>
			    	</div>
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
	        <div class="uk-modal-body">
	         	<table class="uk-table uk-table-hover uk-table-striped uk-table-condensed uk-text-nowrap uk-table-divider">
					<thead>
						<tr>
							<th class="uk-table-shrink">id</th>
							<th class="uk-table-shrink"></th>
							<th>メーカー</th>
							<th>商品名</a></th>
							<th>製品コード</a></th>
							<th>規格</a></th>
							<th>入数</a></th>
							<th>価格</a></th>
							<th>単価</a></th>
							<th>JANコード</a></th>
							<th>卸業者</a></th>
							<th>ロット管理フラグ</a></th>
						</tr>
					</thead>
					<tbody>
						<tr v-for="(list , key) in select_items">
							<td></td>
							<td><button type="button" v-on:click="addObject(key)" class="uk-button uk-button-primary uk-button-small">反映</button></td>
							<td class="uk-text-middle">{{list.maker}}</td>
							<td class="uk-text-middle">{{list.shouhinName}}</td>
							<td class="uk-text-middle">{{list.code}}</td>
							<td class="uk-text-middle">{{list.kikaku}}</td>
							<td class="uk-text-middle">
							<span class="irisu">{{list.irisu}}</span><span class="unit uk-text-small">{{list.unit}}</span>
							</td>
							<td class="uk-text-middle">￥{{list.kakaku}}</td>
							<td>
							    ￥<span v-if="useUnitPrice == 1">{{list.unitPrice | number_format}}</span>
							    <span v-else>{{(list.kakaku / list.irisu)| number_format}}</span>
							</td>
							<td class="uk-text-middle">{{list.jan}}</td>
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
		sourceDivision: '',
		sourceDivisionReadonly: false,
		targetDivision: '',
		targetDivisionReadonly: false,
        useUnitPrice: parseInt(<?php echo json_encode($useUnitPrice); ?>),
	},
	filters: {
        number_format: function(value) {
            if (! value) { return false; }
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
		setSourceDivision : function(elm) {
			this.sourceDivision = elm.value;
		},
		setTargetDivision : function(elm) {
			this.targetDivision = elm.value;
		},
		lock1: function(){
			this.sourceDivisionReadonly = true;	
		},
		lock2: function(){
			this.targetDivisionReadonly = true;	
		},
		addList: function(object) {
			object.class = ((object.class == null)? {'target' : true} : object.class);
			object.countNum = ((object.countNum == null)? 0 : object.countNum);
			object.countLabelNum = ((object.countLabelNum == null)? 1 : object.countLabelNum);
			object.lotNumber = ((object.lotNumber == null)? '': object.lotNumber); 
			object.lotDate = ((object.lotDate == null)? '' : object.lotDate);
			object.labelCountStyle =  ((object.labelCountStyle == null)? {} : object.labelCountStyle);
			object.countStyle =  ((object.countStyle == null)? {} : object.countStyle);
			object.lotDateStyle =  ((object.lotDateStyle == null)? {} : object.lotDateStyle);
			object.lotNumberStyle =  ((object.lotNumberStyle == null)? {} : object.lotNumberStyle);
			this.lists.push(object);
		},
		copyList: function(key) {
			let original = JSON.parse(JSON.stringify(this.lists));
			this.lists.splice(0, original.length);
			let num = 0;
			for(num ; num <= key ; num++)
			{
				this.addList(JSON.parse(JSON.stringify(original[num])));
			}
			
			let copy = JSON.parse(JSON.stringify(original[key]));
			copy.countNum = 0;
			copy.class.target = true;
			copy.countLabelNum = null;
			copy.cardNum = '';
			
			this.addList(copy); //コピー
			
			for(num ; num < original.length ; num++)
			{
				this.addList(JSON.parse(JSON.stringify(original[num])));
			}
			
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
		sendPayout: function(){
			if(! this.payoutCheck()){
				return false;
			}
			if(! this.divisionCheck()){
				return false;
			}
			
			
            loading();
			canAjax = false; // これからAjaxを使うので、新たなAjax処理が発生しないようにする
			$.ajax({
				async: false,
                url: "<?php echo $api_url ?>",
                type:'POST',
                data:{
                    _csrf: "<?php echo $csrf_token ?>",  // CSRFトークンを送信
                    payoutDate : $('input[name=payoutDate]').val(),
                	Action : 'payoutRegistApi',
                	payout : JSON.stringify( objectValueToURIencode(this.lists) ),
                	sourceDivisionId : $('select[name="sourceDivision"]').val(),
                	sourceDivisionName : encodeURI($('select[name="sourceDivision"] option:selected').text()),
                	targetDivisionId : $('select[name="targetDivision"]').val(),
                	targetDivisionName : encodeURI($('select[name="targetDivision"] option:selected').text()),
                },
                dataType: 'json'
            })
            // Ajaxリクエストが成功した時発動
            .done( (data) => {
                if(data.code != '0'){
            		UIkit.modal.alert("払出に失敗しました").then(function(){
					});
            		return false;
                }
                UIkit.modal.alert("払出が完了しました").then(function(){
                	if(data.data.labelCreateFlg)
                	{
						UIkit.modal.confirm("ラベルを発行しますか<br>※履歴から発行も可能です").then(function () {
							$('input[name=payoutHistoryId]').val(data.data.payoutHistoryId);
							$('form[name=LabelCreate]').submit();
							app.lists.splice(0, app.lists.length);
						}, function() {
							app.lists.splice(0, app.lists.length);
						});
                	} else 
                	{
						app.lists.splice(0, app.lists.length);
                	}
				});
            })
            // Ajaxリクエストが失敗した時発動
            .fail( (data) => {
                UIkit.modal.alert("払出に失敗しました").then(function(){
				});
            })
            // Ajaxリクエストが成功・失敗どちらでも発動
            .always( (data) => {
				loading_remove();
            });
		},
		divisionCheck : function(){
			if($('select[name="sourceDivision"]').val()){
			} else {
				UIkit.modal.alert('払出元部署を選択してください');
				return false ;
			}
			
			
			if($('select[name="targetDivision"]').val()){
			} else {
				UIkit.modal.alert('払出先部署を選択してください');
				return false ;
			}
			
			
			if($('select[name="sourceDivision"]').val() != $('select[name="targetDivision"]').val() ){
			} else {
				UIkit.modal.alert('払出元部署と払出先部署は同一のものを選択しないでください');
				return false ;
			}	
			return true;
		},
		payoutCheck: function(){
			
			if(app.lists.length === 0){
				UIkit.modal.alert('商品を選択してください');
				return false ;
			}
			
			let checkflg = true;
			app.lists.forEach(function (elem, index) {
				elem.countStyle.border = '';
				if(app.lists[index].countNum == 0){
					let changeObject = app.lists[index];
					changeObject.countStyle.border = 'red 2px solid';
					app.$set(app.lists, index, changeObject);
					checkflg = false;
				}
			});
			
			if(!checkflg){
				UIkit.modal.alert('数量を入力してください');
				return false ;
			}
			
			checkflg = true;
			app.lists.forEach(function (elem, index) {
				elem.labelCountStyle.border = '';
			  if(app.lists[index].countLabelNum == 0){
				let changeObject = app.lists[index];
				changeObject.labelCountStyle.border = 'red 2px solid';
				app.$set(app.lists, index, changeObject);
		  		checkflg = false;
			  }
			});
			if(!checkflg){
				UIkit.modal.alert('個数を入力してください');
				return false ;
			}
			
			let chkLot = true;
			app.lists.forEach(function (elem, index) {
				elem.lotNumberStyle.border = '';
				elem.lotDateStyle.border = '';
				if(app.lists[index].countNum > 0 && app.lists[index].lotFlagBool == 1) {
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
				if(app.lists[index].countNum > 0) {
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
		addLabelCountStyle: function(index){
			let changeObject = app.lists[index];
			changeObject.labelCountStyle = { 'backgroundColor' : "rgb(255, 204, 153)" , 'color' : "rgb(68, 68, 68)"};
			app.$set(app.lists, index, changeObject);
		},
		addCountStyle: function(index){
			let changeObject = app.lists[index];
			changeObject.countStyle = { 'backgroundColor' : "rgb(255, 204, 153)" , 'color' : "rgb(68, 68, 68)"};
			app.$set(app.lists, index, changeObject);
		},
		addCardStyle: function(index){
			let changeObject = app.lists[index];
			changeObject.cardStyle = { 'backgroundColor' : "rgb(255, 204, 153)" , 'color' : "rgb(68, 68, 68)"};
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
		countToIrisu: function(){
			UIkit.modal.confirm("払出数に入数を自動挿入しますか。カードが設定済みのもの以外すべて上書きます。").then(function () {
				app.lists.forEach(function(elem, index) {
					if(!app.lists[index].cardNum)
					{
						let changeObject = null;
						changeObject = app.lists[index];
						changeObject.countNum = changeObject.irisu;
						app.$set(app.lists, index, changeObject);
	            		app.addCountStyle(index);
					}
				});
			}, function() {
			});
		},
		cardSearch: function(barcode)
		{
			if(! this.divisionCheck()){
				return false;
			}
			if(barcode.length != 18)
			{
				UIkit.modal.alert("カードのバーコードではありません");
				return false;
			}
        	let exist = false;
        	app.lists.forEach(function(elem, index) {
        		if(app.lists[index].cardNum == barcode)
        		{
        			exist = true;
        		}
        	});
        	if(exist)
        	{
            	UIkit.modal.alert("すでに紐づいているカード情報です");
            	return false;
        	}
			$.ajax({
				async: false,
                url:'%url/rel:mpgt:labelBarcodeSAPI%',
                type:'POST',
                data:{
                	divisionId : $('select[name="targetDivision"]').val(),
                	barcode : barcode,
                },
                dataType: 'json'
            })
            // Ajaxリクエストが成功した時発動
            .done( (data) => {
            	if(data.count > 0)
            	{
	            	$('select[name="targetDivision"]').attr('disabled',true);
	            	let checked = false;
	            	app.lists.forEach(function(elem, index) {
	            		if(
	            			data.data.recordId == app.lists[index].recordId && 
	            			app.lists[index].countNum == 0 && app.lists[index].countLabelNum == 1 && !app.lists[index].cardNum &&
	            			!checked
	            		)
	            		{
							let changeObject = null;
							changeObject = app.lists[index];
							changeObject.countNum = data.data.count;
							changeObject.cardNum = barcode;
							changeObject.countNumDisabled = true;
							changeObject.countLabelNumDisabled = true;
							app.$set(app.lists, index, changeObject);
		            		app.addCountStyle(index);
		            		checked = true;
	            		}
	            	});
	            	if(!checked)
	            	{
	                	UIkit.modal.alert("紐づける対象の商品がリストにありませんでした");
	            	}
            	} else {
                	UIkit.modal.alert("カード情報が取得できませんでした。カード情報と払出先部署が一致しているか確認してください。");
                	return false;
            	}
            })
            // Ajaxリクエストが失敗した時発動
            .fail( (data) => {
                UIkit.modal.alert("カード情報が取得できませんでした。カード情報と払出先部署が一致しているか確認してください。");
            })
            // Ajaxリクエストが成功・失敗どちらでも発動
            .always( (data) => {
				loading_remove();
            });
			
		},
		barcodeSearch: function(barcode , lotNumber , lotDate , gs1_128_search_flg) {
			if(! this.divisionCheck()){
				return false;
			}
			$.ajax({
				async: false,
                url:'%url/rel:mpgt:labelBarcodeSAPI%',
                type:'POST',
                data:{
                	divisionId : $('select[name="sourceDivision"]').val(),
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
	            	$('select[name="sourceDivision"]').attr('disabled',true);
                	data = data.data;
                	
                	if(data.divisionId != "" && $('select[name="sourceDivision"]').val() != data.divisionId )
                	{
	            		UIkit.modal.alert("読み込んだバーコードの部署が払出元の部署と一致しません");
	            		return false;
                	}
                	if(lotNumber != ''){
                		data.lotNumber = lotNumber;
                	}
                	if(lotDate != ''){
                		data.lotDate = lotDate;
                	}
					data.labelCountStyle = { 'backgroundColor' : "rgb(255, 204, 153)" , 'color' : "rgb(68, 68, 68)"};
					data.countStyle = { 'backgroundColor' : "rgb(255, 204, 153)" , 'color' : "rgb(68, 68, 68)"};
					data.lotNumberStyle = { 'backgroundColor' : "rgb(255, 204, 153)" , 'color' : "rgb(68, 68, 68)"};
					data.lotDateStyle = { 'backgroundColor' : "rgb(255, 204, 153)" , 'color' : "rgb(68, 68, 68)"};
                	this.addList(data);
	                
	                $('input[name="barcode"]').val('');
                } else {
	            	$('select[name="sourceDivision"]').attr('disabled',true);
                	data = data.data;
                	modal_sections.clear();
                	for(let num = 0 ; num < data.length ; num++)
                	{
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
	app.addList(object);
}
</script>