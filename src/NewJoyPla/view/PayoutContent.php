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
                	<li><a href="%url/rel:mpg:top%&path=payout">払出メニュー</a></li>
				    <li><span>払出内容入力</span></li>
				</ul>
		    	<h2 class="page_title">払出内容入力</h2>
		    	<hr>
		    	<div class="uk-child-width-1-3@m" uk-grid>
		    		<div>
		    			<label class="uk-form-label">払出元部署</label>
		    			<div class="uk-form-controls">
				            <select class="uk-width-3-4 uk-select uk-inline" name="sourceDivision">
				                <option value="">----- 部署選択 -----</option>
		                        <?php
								$selected = ($user_info->isUser())? $user_info->getDivisionId() : "" ;
		                        foreach($source_division->data as $data)
		                        {
		                            if($data->divisionType === '1')
		                            {
		                                echo '<option value="'.$data->divisionId.'" '. ($data->divisionId === $selected)? "selected" : "" .'>'.$data->divisionName.'(大倉庫)</option>';
		                                echo '<option value="" disabled>--------------------</option>';
		                            }
		                        }
		                        foreach($source_division->data as $data)
		                        {
		                            if($data->divisionType === '2')
		                            {
		                                echo '<option value="'.$data->divisionId.'" '. ($data->divisionId === $selected)? "selected" : "" .'>'.$data->divisionName.'</option>';
		                            }
		                        }
		                        ?>
				            </select>
			            </div>
			        </div>
		    		<div>
		    			<label class="uk-form-label" >払出先部署</label>
		    			<div class="uk-form-controls">
				            <select class="uk-select uk-width-3-4" name="targetDivision">
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
			        </div>
		    		<div>
		    			<label class="uk-form-label" >払出日指定</label>
		    			<div class="uk-form-controls">
				            <input type="date" value="" class="uk-input" name="payoutDate">
			            </div>
			            <span class="uk-text-danger">※入力なしで現在時刻を取得します</span>
			        </div>
		    	</div>
		    	<div class="uk-margin-bottom" uk-grid>
		    		<div class="uk-width-1-2@m" uk-margin>
			    		<button class="uk-button uk-button-default" v-on:click="sanshouClick">商品マスタを開く</button>
			    		<button class="uk-button uk-button-default" type="submit" onclick="window.print();return false;">印刷プレビュー</button>
			    		<button class="uk-button uk-button-primary uk-margin-small-top" v-on:click="sendPayout">払出実行</button>
		    		</div>
		    		<?php /*
		    		<div class="uk-inline uk-width-1-2@m">
	    				<input type="text" class="uk-input uk-width-4-5" placeholder="バーコード入力..." autofocus="true"> 
		    			<button class="uk-button uk-button-default uk-float-right uk-width-1-5 uk-padding-remove">検索</button>
		    		</div>*/ ?>
		    	</div>
		    	
			    <div uk-sticky="sel-target: .uk-navbar-container; cls-active: uk-navbar-sticky" class="uk-padding-top uk-background-muted uk-padding-small">
		            <form action='#' method="post" onsubmit="app.barcodeSearch($('input[name=barcode]').val() ,'' , '');$('input[name=barcode]').val('') ; $('input[name=barcode]').focus(); return false;">
	    				<input type="text" class="uk-input uk-width-4-5" placeholder="バーコード入力..." autofocus="true" name="barcode" autocomplete="off">  
		    			<button class="uk-button uk-button-primary uk-float-right uk-width-1-5 uk-padding-remove" type="submit">検索</button>
					</form>	
				</div>
		    	
					<div class="uk-margin uk-text-right">
						<button type="button" class="uk-button uk-button-primary"  uk-toggle="target: #gs1-128">GS1-128で照合</button>
					</div>
		    	
		    	<div class="shouhin-table uk-width-expand uk-overflow-auto">
		    		<table class="uk-table uk-table-striped uk-text-nowrap">
		    			<thead>
		    				<tr>
		    					<th class="uk-table-shrink">id</th>
		    					<th>メーカー</th>
		    					<th>商品名</th>
		    					<th>製品コード</th>
		    					<th>規格</th>
		    					<th>入数</th>
		    					<th class="uk-table-shrink">数量</th>
		    					<th class="uk-table-shrink"></th>
		    					<th class="uk-table-shrink">個数（ラベル枚数）</th>
		    					<th class="uk-table-shrink">合計払出数</th>
		    					<th>JANコード</th>
		    					<th>ロット番号</th>
		    					<th>使用期限</th>
		    					<th></th>
		    					<th></th>
		    				</tr>
		    			</thead>
		    			<tbody>
							<tr v-for="(list, key) in lists" :id="'tr_' + key">
								<td>{{list.text}}</td>
								<td>{{list.maker}}</td>
								<td>{{list.shouhinName}}</td>
								<td>{{list.code}}</td>
								<td>{{list.kikaku}}</td>
								<td>{{list.irisu}}{{list.unit}}</td>
								<td>
									<input type="number" step="1" class="uk-input" min="0" style="width: 96px;" v-bind:style="list.countStyle" v-model="list.countNum" v-on:change="addCountStyle(key)">
									<span class="uk-text-bottom">{{list.unit}}</span>
								</td>
								<td>×</td>
								<td>
									<input type="number" step="1" class="uk-input" min="1" style="width: 96px;" v-bind:style="list.labelCountStyle" v-model="list.countLabelNum" v-on:change="addLabelCountStyle(key)">
									<span class="uk-text-bottom">枚</span>
								</td>
								<td>{{list.countNum * list.countLabelNum}}{{list.unit}}</td>
								<td>{{list.jan}}</td>
								<td>
									<input type="text" class="uk-input lot" v-model="list.lotNumber" v-bind:style="list.lotNumberStyle" v-on:change="addLotNumberStyle(key)">
								</td>
								<td>
									<input type="date" class="uk-input lotDate" v-model="list.lotDate" v-bind:style="list.lotDateStyle" v-on:change="addLotDateStyle(key)">
								</td>
								<td>
									<input type="button" class="uk-button uk-button-danger uk-button-small" value="削除" v-on:click="deleteList(key)">
								</td>
								<td>
									<input type="button" class="uk-button uk-button-default uk-button-small" value="追加" v-on:click="copyList(key)">
								</td>
							</tr>
		    			</tbody>
		    			<tfoot>
		    				<tr>
		    					<td>&emsp;</td>
		    					<td>&emsp;</td>
		    					<td>&emsp;</td>
		    					<td>&emsp;</td>
		    					<td>&emsp;</td>
		    					<td>&emsp;</td>
		    					<td>&emsp;</td>
		    					<td>&emsp;</td>
		    					<td>&emsp;</td>
		    					<td>&emsp;</td>
		    					<td>&emsp;</td>
		    					<td>&emsp;</td>
		    					<td>&emsp;</td>
		    					<td>&emsp;</td>
		    					<td>&emsp;</td>
		    				</tr>
		    				<tr>
		    					<td>&emsp;</td>
		    					<td>&emsp;</td>
		    					<td>&emsp;</td>
		    					<td>&emsp;</td>
		    					<td>&emsp;</td>
		    					<td>&emsp;</td>
		    					<td>&emsp;</td>
		    					<td>&emsp;</td>
		    					<td>&emsp;</td>
		    					<td>&emsp;</td>
		    					<td>&emsp;</td>
		    					<td>&emsp;</td>
		    					<td>&emsp;</td>
		    					<td>&emsp;</td>
		    					<td>&emsp;</td>
		    				</tr>
		    				<tr>
		    					<td>&emsp;</td>
		    					<td>&emsp;</td>
		    					<td>&emsp;</td>
		    					<td>&emsp;</td>
		    					<td>&emsp;</td>
		    					<td>&emsp;</td>
		    					<td>&emsp;</td>
		    					<td>&emsp;</td>
		    					<td>&emsp;</td>
		    					<td>&emsp;</td>
		    					<td>&emsp;</td>
		    					<td>&emsp;</td>
		    					<td>&emsp;</td>
		    					<td>&emsp;</td>
		    					<td>&emsp;</td>
		    				</tr>
		    			</tfoot>
		    		</table>
		    	</div>
		    </div>
		</div>
	</div>
	
	<form action="%url/rel:mpgt:createLabel%" target="_blank" method="post" class="uk-hidden" name="createLabelForm">
		<input type="hidden" value="" name="itemsData" id="itemsData">
		<input type="hidden" value="" name="sourceDivision">
		<input type="hidden" value="" name="targetDivision">
		<input type="hidden" value="payout" name="pattern">
	</form>
	
	
	<!-- This is a button toggling the modal with the default close button -->
	<!-- This is the modal with the default close button -->
	<div id="gs1-128" uk-modal>
	    <div class="uk-modal-dialog uk-modal-body">
	    	<form onsubmit="gs1_128.check_gs1_128($('#GS1-128').val());return false;" action="#">
		        <button class="uk-modal-close" type="button" uk-close></button>
		        <h2 class="uk-modal-title">GS1-128 読取</h2>
		        <input type="text" class="uk-input" placeholder="GS1-128" id="GS1-128" autofocus="true">
				    <p class="uk-text-right">
		            <button class="uk-button uk-button-primary" type="button" onclick="gs1_128.check_gs1_128($('#GS1-128').val());">反映</button>
		        </p>
	        </form>
	    </div>
	</div>
	
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
							<td class="uk-text-middle">￥{{list.unitPrice}}</td>
							<td class="uk-text-middle">{{list.jan}}</td>
							<td class="uk-text-middle">{{list.oroshi}}</td>
						</tr>
					</tbody>
				</table>   
	        </div>
	    </div>
	</div>
</div>
	
<script>

var app = new Vue({
	el: '#app',
	data: {
		lists: []
	},
	methods: {
		addList: function(object) {
			object.countNum = ((object.count == null)? 0 : object.count); 
			object.countLabelNum = ((object.countLabelNum == null)? 1 : object.countLabelNum);
			object.lotNumber = ((object.lotNumber == null)? '': object.lotNumber); 
			object.lotDate = ((object.lotDate == null)? '' : object.lotDate);
			
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
			copy.countLabelNum = null;
			copy.lotNumber = null;
			copy.lotDate = null;
			copy.countStyle = {};
			copy.labelCountStyle = {};
			copy.lotNumberStyle = {};
			copy.lotDateStyle = {};
			
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
			window.open('%url/rel:mpgt:page_175973%', '_blank','scrollbars=yes,width=1220,height=600');
		},
		sendPayout: function(){
			if(! this.payoutCheck()){
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
					UIkit.modal.confirm("ラベルを発行しますか<br>※履歴から発行も可能です").then(function () {
						app.lists.splice(0, app.lists.length);
					}, function() {
						app.lists.splice(0, app.lists.length);
					});
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
		payoutCheck: function(){
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
			
			if(app.lists.length === 0){
				UIkit.modal.alert('商品を選択してください');
				return false ;
			}
			
			let checkflg = false;
			app.lists.forEach(function (elem, index) {
			  if(app.lists[index].countNum !== 0){
			  	checkflg = true;
			  }
			});
			
			if(checkflg){
			} else {
				UIkit.modal.alert('数量を入力してください');
				return false ;
			}
			
			checkflg = false;
			app.lists.forEach(function (elem, index) {
			  if(app.lists[index].countLabelNum !== 0){
			  	checkflg = true;
			  }
			});
			if(checkflg){
			} else {
				UIkit.modal.alert('個数を入力してください');
				return false ;
			}
			
			let chkLot = true;
			app.lists.forEach(function (elem, index) {
				if(app.lists[index].countNum > 0) {
					if((!app.lists[index].lotNumber && app.lists[index].lotDate) || (app.lists[index].lotNumber && !app.lists[index].lotDate)) {
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
				if(app.lists[index].lotNumber) {
					if((!regex.test(app.lists[index].lotNumber)) ||
					   (encodeURI(app.lists[index].lotNumber).replace(/%../g, '*').length > 20)) {
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
		search: function(searchJan,objLotNumber,objLotDate){
			//商品が存在するかつロットが同一
			let existflg = false;
			app.lists.forEach(function(elem, index) {
				let changeObject = null;
				if(!existflg){
					if(searchJan == app.lists[index].jan && app.lists[index].lotNumber == objLotNumber && app.lists[index].lotDate == objLotDate ){
						changeObject = app.lists[index];
						changeObject.countLabelNum++;
						app.$set(app.lists, index, changeObject);
						app.addLabelCountStyle(index);
						existflg = true;
						$(window).scrollTop($("#tr_"+index).offset().top - 100);

					}
				}
			});
			
			//商品が存在するかつロットの記入がされていない
			if(!existflg){
				app.lists.forEach(function(elem, index) {
					let changeObject = null;
					if(!existflg){
						if(searchJan == app.lists[index].jan && app.lists[index].lotNumber == null && app.lists[index].lotDate == null ){
							changeObject = app.lists[index];
							changeObject.lotNumber = objLotNumber;
							changeObject.lotDate = objLotDate;
							
							app.$set(app.lists, index, changeObject);
							app.addLotNumberStyle(index);
							app.addLotDateStyle(index);
							existflg = true;
							$(window).scrollTop($("#tr_"+index).offset().top - 100);

						}
					}
				});
			}
			
			//商品をaddする
			if(!existflg){
				app.lists.forEach(function(elem, index) {
					let changeObject = null;
					if(!existflg){
						if(searchJan == app.lists[index].jan){
							changeObject = app.lists[index];
							changeObject.lotNumber = objLotNumber;
							changeObject.lotDate = objLotDate;
							
							app.addList(changeObject);
							app.addLotNumberStyle(index);
							app.addLotDateStyle(index);
							existflg = true;
							$(window).scrollTop($("#tr_"+index).offset().top - 100);
						}
					}
				});
			}
			
			return existflg;
		},
		labelSearch: function(object) {
			let existflg = false;
			app.lists.forEach(function(elem, index) {
				let changeObject = null;
				if(!existflg){
					if(object.jan == app.lists[index].jan && app.lists[index].countNum == object.countNum){
						if(object.lotNumber == app.lists[index].lotNumber && object.lotDate == app.lists[index].lotDate){
							changeObject = app.lists[index];
							changeObject.countLabelNum++;
							
							app.$set(app.lists, index, changeObject);
							app.addLabelCountStyle(index);
							existflg = true;
							$(window).scrollTop($("#tr_"+index).offset().top - 100);
						}
					}
				}
			});
			return existflg;
		},
		barcodeSearch: function(barcode , lotNumber , lotDate) {
			if(barcode.length > 14)
			{
				gs1_128.check_gs1_128(barcode);
				return ;
			}
			$.ajax({
				async: false,
                url:'%url/rel:mpgt:labelBarcodeSAPI%',
                type:'POST',
                data:{
					_csrf: "<?php echo $csrf_token ?>",  // CSRFトークンを送信
                	barcode : barcode,
                },
                dataType: 'json'
            })
            // Ajaxリクエストが成功した時発動
            .done( (data) => {
            	let value = 0;
                if(data.code != 0 || data.data.length == 0){
            		UIkit.modal.alert("商品が見つかりませんでした");
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
	                data.countNum = data.irisu;
	                let existflg = this.labelSearch(data);
	                if(!existflg){
						data.labelCountStyle = { 'backgroundColor' : "rgb(255, 204, 153)" , 'color' : "rgb(68, 68, 68)"};
						data.countStyle = { 'backgroundColor' : "rgb(255, 204, 153)" , 'color' : "rgb(68, 68, 68)"};
						data.lotNumberStyle = { 'backgroundColor' : "rgb(255, 204, 153)" , 'color' : "rgb(68, 68, 68)"};
						data.lotDateStyle = { 'backgroundColor' : "rgb(255, 204, 153)" , 'color' : "rgb(68, 68, 68)"};
	                	this.addList(data);
	                }
	                
	                $('input[name="barcode"]').val('');
                } else {
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
			app.addList(this.select_items[index]);
		},
		openModal: function(){
        	UIkit.modal('#modal-sections').show();
		}
	}
});

var gs1_128 = new Vue({
	el: '#gs1-128',
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
					UIkit.modal($('#gs1-128')).show();
				});
				return;
			}
			
			let searchJan = removeCheckDigit(obj["01"]);
			let objkey = null;
			let setObj = {};
			
			let objLotNumber = (obj["10"] === void 0) ? "" : obj["10"]; //lotNumber
			let objLotDate = (obj["17"] === void 0) ? "" : this.changeDate(obj["17"]); //lotDate
			let existflg = false;
			let changeObject = null;
			
			
			existflg = app.search(searchJan,objLotNumber,objLotDate);
			
			if(!existflg){
				app.barcodeSearch(searchJan,objLotNumber,objLotDate);
				//UIkit.modal.alert("対象の発注商品が見つかりませんでした。").then(function(){
				//	UIkit.modal($('#gs1-128')).show();
				//});
				return;
			} 
			
			$("#GS1-128").val("");
			document.getElementById("GS1-128").focus();
		}
	}
});

function addTr(object, type, count){
	app.addList(object);
}
</script>