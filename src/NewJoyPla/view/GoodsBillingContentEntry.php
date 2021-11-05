<style>
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

        .uk-button.goodsBillingButton{
            background: #EB8400;
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
    <div class="uk-section uk-section-default uk-preserve-color uk-padding-remove" id="page_top">
        <div class="uk-container uk-container-expand uk-margin-top">
            <ul class="uk-breadcrumb">
                <li><a href="%url/rel:mpg:top%">TOP</a></li>
                <li><span>消費登録・個別発注 内容入力</span></li>
            </ul>
            <h2 class="page_title">消費登録・個別発注 内容入力</h2>
            <hr>
            <div uk-grid>
                <div class="uk-width-1-3@m">
    			    <label class="uk-form-label" > </label>
                    <div class="uk-form-controls">
                        <select class="uk-select" name="busyo" v-model="divisionId" v-bind:disabled="division_disabled">
                            <option value="">----- 部署選択 -----</option>
                        <?php
                            foreach($divisionData->data as $data)
                            {
                                if($data->divisionType === '1')
                                {
                                    echo '<option value="'.$data->divisionId.'">'.$data->divisionName.'(大倉庫)</option>';
                                    echo '<option value="" disabled>--------------------</option>';
                                }
                            }
                            foreach($divisionData->data as $data)
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
    		            <input type="date" value="" class="uk-input uk-width-1-2@m" v-model="consumeDate">
    	            </div>
        			<label class="uk-form-label" >消費日指定</label>
    	            <span class="uk-text-danger">※入力なしで現在時刻を取得します</span>
                </div>
            </div>
            <div class="uk-margin-bottom">
                <div>
                    <div>
                        <button class="uk-button uk-button-default" v-on:click="sanshouClick">商品マスタを開く</button>
                        <button class="uk-button uk-button-default" type="submit" onclick="window.print();return false;">印刷プレビュー</button>
                        <button class="uk-button uk-button-primary goodsBillingButton" v-on:click="sendGoodsBilling">消費登録</button>
                        <button class="uk-button uk-button-primary unorderedSlipButton" v-on:click="sendUnorderedSlip(false)">未発注伝票作成</button>
                    </div>
                </div>
            </div>
            <div uk-sticky="sel-target: .uk-navbar-container; cls-active: uk-navbar-sticky" class="uk-padding-top uk-background-muted uk-padding-small">
                <form action='#' method="post"  onsubmit="app.barcodeSearch($('input[name=barcode]').val() , '' ,'' ,true);$('input[name=barcode]').val('') ; $('input[name=barcode]').focus();return false;">
                    <input type="text" class="uk-input uk-width-4-5" placeholder="バーコード入力..." name="barcode" autocomplete="off">
                    <button class="uk-button uk-button-primary uk-float-right uk-width-1-5 uk-padding-remove" type="submit">検索</button>
                </form>
            </div>

            <div class="shouhin-table uk-width-expand uk-overflow-auto">
                <table class="uk-table uk-table-striped uk-table-striped uk-table-condensed uk-text-nowrap">
                    <thead>
                        <tr>
                            <th>id</th>
                            <th>メーカー</th>
                            <th>商品名</th>
                            <th>製品コード</th>
                            <th>規格</th>
                            <th>JANコード</th>
                            <th>卸業者</th>
                            <th>入数</th>
                            <th>価格</th>
                            <th>単価</th>
                            <th>数量
		    						<input type="button" class="uk-button uk-button-default uk-button-small" v-on:click="countToIrisu" value="入数を反映" >
		    				</th>
                            <th>ロット管理</th>
                            <th>ロット番号</th>
                            <th>使用期限</th>
                            <th></th>
                            <th></th>
                        </tr>
                    </thead>
                    <tbody>
						<tr v-for="(list, key) in lists" :id="'tr_' + key" v-bind:class="list.class">
							<td></td>
							<td>{{list.maker}}</td>
							<td>{{list.shouhinName}}</td>
							<td>{{list.code}}</td>
							<td>{{list.kikaku}}</td>
							<td>{{list.jan}}</td>
							<td>{{list.oroshi}}</td>
							<td>{{list.irisu}}{{list.unit}}</td>
							<td>￥{{list.kakaku | number_format}}</td>
							<td>
							    ￥<span v-if="useUnitPrice == 1">{{list.unitPrice | number_format}}</span>
							    <span v-else>{{(list.kakaku / list.irisu)| number_format}}</span>
							</td>
							<td>
								<input type="number" step="1" class="uk-input" style="width: 96px;" v-bind:style="list.countStyle" v-model="list.countNum" v-bind:disabled="list.countNumDisabled" v-on:change="addCountStyle(key)">
								<span class="uk-text-bottom">{{list.unit}}</span>
							</td>
							<td>
								<span v-if="list.lotFlagBool == 1" class="uk-text-danger">必須</span>
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
							<th class="uk-table-shrink">id</th>
							<th class="uk-table-shrink"></th>
							<th>メーカー</th>
							<th>商品名</th>
							<th>製品コード</th>
							<th>規格</th>
							<th>入数</th>
							<th>価格</th>
							<th>単価</th>
							<th>JANコード</th>
							<th>卸業者</th>
							<th>ロット管理フラグ</th>
						</tr>
					</thead>
					<tbody>
						<tr v-for="(list , key) in select_items" >
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
		divisionId: '',
		consumeDate : '',
		division_disabled: false,
        useUnitPrice: parseInt(<?php echo json_encode($useUnitPrice); ?>),
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
		addList: function(object) {
			object.class = ((object.class == null)? {'target' : true} : object.class);
			object.countNum = ((object.countNum == null)? 0 : object.countNum);
			object.lotNumber = ((object.lotNumber == null)? '': object.lotNumber); 
			object.lotDate = ((object.lotDate == null)? '' : object.lotDate);
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
			copy.lotNumber = null;
			copy.lotDate = null;
			copy.countStyle = null;
			copy.lotDateStyle =  null;
			copy.lotNumberStyle =  null;
			
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
		
		divisionCheck : function(){
			if(!this.divisionId){
				UIkit.modal.alert('部署を選択してください');
				return false ;
			}
			return true;
		},
		countToIrisu: function(){
			UIkit.modal.confirm("数量に入数を自動挿入しますか。<br>数量0に設定されている商品が対象です").then(function () {
				app.lists.forEach(function(elem, index) {
					let changeObject = null;
				    if(app.lists[index].countNum == 0){
					    changeObject = app.lists[index];
    					changeObject.countNum = changeObject.irisu;
    					app.$set(app.lists, index, changeObject);
                		app.addCountStyle(index);
				    }
				});
			}, function() {
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
		isCard: function(barcode){
		    return (barcode.length == 18 && barcode.startsWith('90'));
		},
		
		cardCheck: function(barcode){
		    let exist = false;
		    app.lists.forEach(function(elem, index) {
				let changeObject = null;
			    if(app.lists[index].card == barcode){
			        exist = true;
			    }
			});
			return exist;
		},
		barcodeSearch: function(barcode , lotNumber , lotDate , gs1_128_search_flg) {
			if(! this.divisionCheck()){
				return false;
			}
			
			if(this.isCard(barcode)){
			    if(this.cardCheck(barcode)){
			       UIkit.modal.alert("すでに読み込んでいるカードです");
			       return false;
			    }
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
	            this.division_disabled = true;
                if(data.count == 1)
                {
                	data = data.data;
                	
                	if(data.divisionId != "" && this.divisionId != data.divisionId )
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
                	data.countNum = data.count;
					data.labelCountStyle = { 'backgroundColor' : "rgb(255, 204, 153)" , 'color' : "rgb(68, 68, 68)"};
					data.countStyle = { 'backgroundColor' : "rgb(255, 204, 153)" , 'color' : "rgb(68, 68, 68)"};
					data.lotNumberStyle = { 'backgroundColor' : "rgb(255, 204, 153)" , 'color' : "rgb(68, 68, 68)"};
					data.lotDateStyle = { 'backgroundColor' : "rgb(255, 204, 153)" , 'color' : "rgb(68, 68, 68)"};
        			if(app.isCard(barcode)){
        			    data.card = barcode;
        			}
                	this.addList(data);
	                
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
		},
		
        goodsBillingValidationCheck:function(){
            if (app.lists.length == 0) {
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
				elem.countStyle.border = '';
				if(app.lists[index].countNum < 0){
					let changeObject = app.lists[index];
					changeObject.countStyle.border = 'red 2px solid';
					app.$set(app.lists, index, changeObject);
					checkflg = false;
				}
			});
			
			if(!checkflg){
				UIkit.modal.alert('消費の場合はプラスの値を入力してください');
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
        listsCheck:function(){
            if (app.lists.length == 0) {
                UIkit.modal.alert('商品を選択してください');
                return false ;
            }
          
            return true;
        },
        
        sendGoodsBilling: function(){
            UIkit.modal.confirm('消費登録を行いますか？').then(function () {
                loading();
                if (!app.goodsBillingValidationCheck()) {
                    loading_remove();
                    return false;
                }
                $.ajax({
                    async: false,
                    url:'<?php echo $api_url ?>',
                    type:'POST',
                    data:{
                        _csrf: "<?php echo $csrf_token ?>",  // CSRFトークンを送信
                        Action : "regGoodsBillingAPI",
                        consumeDate : app.consumeDate,
                        billing : JSON.stringify( objectValueToURIencode(app.lists) ),
                        divisionId : app.divisionId
                    },
                    dataType: 'json'
                })
                // Ajaxリクエストが成功した時発動
                .done( (data) => {
                    if (data.code != 0) {
                        UIkit.modal.alert('消費登録に失敗しました');
                        return false;
                    }
                    UIkit.modal.alert('消費登録が完了しました').then(function () {
                        app.sendUnorderedSlip(true);
                    });
                })
                // Ajaxリクエストが失敗した時発動
                .fail( (data) => {
                    UIkit.modal.alert('消費登録に失敗しました');
                })
                // Ajaxリクエストが成功・失敗どちらでも発動
                .always( (data) => {
                    loading_remove();
                });
          
            }, function () {
            });
        },
        
        sendUnorderedSlip: function(goodsFlg){
            UIkit.modal.confirm('未発注伝票を作成しますか？').then(function () {
                if (!app.listsCheck()) {
                    loading_remove();
                    return false;
                }
                loading();
            
                $.ajax({
                    async: false,
                    url:'<?php echo $order_api_url ?>',
                    type:'POST',
                    data:{
                        _csrf: "<?php echo $csrf_token ?>",  // CSRFトークンを送信
                        Action : "regUnorderedAPI",
                        ordered : JSON.stringify( objectValueToURIencode(app.lists) ),
                        divisionId : app.divisionId
                    },
                    dataType: 'json'
                })
                // Ajaxリクエストが成功した時発動
                .done( (data) => {
                    if (data.code != 0) {
                        UIkit.modal.alert('未発注伝票の作成に失敗しました');
                        return false;
                    }
                    if (data.count == 0) {
                        UIkit.modal.alert('登録するデータがありませんでした');
                        return false;
                    }
              
                    UIkit.modal.alert('未発注伝票を作成しました').then(function(){
                        $('.goodsBillingButton').prop('disabled', false);
						app.lists.splice(0, app.lists.length);
                    });
                })  
                // Ajaxリクエストが失敗した時発動
                .fail( (data) => {
                    UIkit.modal.alert('未発注伝票の作成に失敗しました');
                })
                // Ajaxリクエストが成功・失敗どちらでも発動
                .always( (data) => {
                    loading_remove();
                });
          
            }, function () {
                if(goodsFlg == true){
					app.lists.splice(0, app.lists.length);
                }
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

$(document).on('change', 'input[type="number"]', function() {
    $(this).css({'color':'rgb(68, 68, 68)', 'background-color':'rgb(255, 204, 153)'});
    $(window).scrollTop($(this).offset().top - 100);
});

function addTr(object, type, count)
{
	app.addList(object);
}
</script>