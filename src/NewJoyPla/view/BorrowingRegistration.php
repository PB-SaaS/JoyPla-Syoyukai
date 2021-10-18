
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
<div class="animsition" uk-height-viewport="expand: true" id="app">
    <div class="uk-section uk-section-default uk-preserve-color uk-padding-remove" id="page_top">
        <div class="uk-container uk-container-expand uk-margin-top">
            <ul class="uk-breadcrumb">
                <li><a href="%url/rel:mpg:top%">TOP</a></li>
                <li><span>貸出内容入力</span></li>
            </ul>
            <h2 class="page_title">貸出内容入力</h2>
            <hr>
            <div class="uk-width-1-3@m">
                <div class="uk-margin">
                    <select class="uk-select" name="busyo" v-model="divisionId">
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
            </div>
            <div class="uk-margin-bottom">
                <div>
                    <div>
                        <button class="uk-button uk-button-default"  v-on:click="sanshouClick">商品マスタを開く</button>
                        <button class="uk-button uk-button-default" type="submit" onclick="window.print();return false;">印刷プレビュー</button>
                        <button class="uk-button uk-button-primary goodsBillingButton" v-on:click="borrowingRegist">貸出リスト登録</button>
                        <?php if(!$user_info->isDistributorUser()): ?>
                        <button class="uk-button uk-button-primary unorderedSlipButton"  v-on:click="usedReport">使用済み報告</button>
                        <?php endif ?>
                    </div>
                </div>
            </div>
            <div uk-sticky="sel-target: .uk-navbar-container; cls-active: uk-navbar-sticky" class="uk-padding-top uk-background-muted uk-padding-small">
                <form action='#' method="post" onsubmit="app.barcodeSearch($('input[name=barcode]').val() , '' ,'' ,true);$('input[name=barcode]').val('') ; $('input[name=barcode]').focus();return false;">
                    <input type="text" class="uk-input uk-width-4-5" placeholder="バーコード入力..." name="barcode" autocomplete="off"> 
                    <button class="uk-button uk-button-primary uk-float-right uk-width-1-5 uk-padding-remove" type="submit">検索</button>
                </form>	
            </div>
            <div class="shouhin-table uk-width-expand uk-overflow-auto">
                <table class="uk-table uk-table-striped uk-table-striped uk-table-condensed uk-text-nowrap">
                    <thead>
                        <tr>
                            <th class="uk-text-bottom">id</th>
                            <th class="uk-text-bottom">メーカー</th>
                            <th class="uk-text-bottom">商品名</th>
                            <th class="uk-text-bottom">製品コード</th>
                            <th class="uk-text-bottom">規格</th>
                            <th class="uk-text-bottom">JANコード</th>
                            <th class="uk-text-bottom">入数</th>
                            <th class="uk-text-bottom">価格</th>
                            <th class="uk-text-bottom">単価</th>
                            <th class="uk-text-bottom">卸業者</th>
                            <th class="uk-text-bottom">貸出数</th>
                            <th class="uk-text-bottom">ロット番号</th>
                            <th class="uk-text-bottom" style="width:146px">使用期限</th>
                            <th>
                                使用済み日<br>
                                <input type="date" class="uk-input uk-form-small" id="allUsedDate" style="width:184px" v-model="allUsedDate">
                                <div class="uk-text-right" style="width:184px">
                                    <button type="button" class="uk-button uk-button-primary uk-text-small uk-width-1-1" v-on:click='setUsedDate()'>一括反映</button>
                                </div>
                            </th>
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
							<td>{{list.jan}}</td>
							<td>{{list.irisu}}{{list.unit}}</td>
							<td>{{list.kakaku}}{{list.itemUnit}}</td>
							<td>{{list.unitPrice}}</td>
							<td>{{list.oroshi}}</td>
							<td>1{{ list.itemUnit }}</td>
							<td>
								<input type="text" class="uk-input lot" v-model="list.lotNumber" v-bind:style="list.lotNumberStyle" v-on:change="addLotNumberStyle(key)">
							</td>
							<td>
								<input type="date" class="uk-input lotDate" v-model="list.lotDate" v-bind:style="list.lotDateStyle" v-on:change="addLotDateStyle(key)">
							</td>
							<td>
								<input type="date" class="uk-input usedDate" v-model="list.usedDate" v-bind:style="list.usedDateStyle" v-on:change="addUsedDateStyle(key)">
							</td>
							<td uk-margin class="uk-text-center">
								<input type="button" class="uk-button uk-button-danger uk-button-small" value="削除" v-on:click="deleteList(key)">
							</td>
							<td uk-margin class="uk-text-center">
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
<script>
let now_date = '<?php echo date("Y-m-d") ?>';

var app = new Vue({
	el: '#app',
	data: {
		lists: [],
		divisionId: '',
		allUsedDate: now_date,
	},
	methods: {
		addList: function(object) {
		    object.usedDate = ((object.usedDate == null)? now_date: object.usedDate); 
			object.countNum = 1;
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
			copy.countStyle = {};
			
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
		divisionCheck: function(){
			if(app.divisionId == ''){
				UIkit.modal.alert('部署を選択してください');
				return false ;
			}
			return true;
		},
		check: function(){
			
			if(app.lists.length === 0){
				UIkit.modal.alert('商品を選択してください');
				return false ;
			}
			
			return true;
		},
		setUsedDate: function()
		{
            UIkit.modal.confirm("使用済み日を一括反映しますか").then(function () {
				let original = JSON.parse(JSON.stringify(app.lists));
    			app.lists.splice(0, original.length);
                original.forEach(function(elem, index) {
                    elem.usedDate = app.allUsedDate;
			        elem.usedDateStyle = { 'backgroundColor' : "rgb(255, 204, 153)" , 'color' : "rgb(68, 68, 68)"};
    				app.addList(elem);
				});
            });
		},
		addUsedDateStyle: function(index){
			let changeObject = app.lists[index];
			changeObject.usedDateStyle = { 'backgroundColor' : "rgb(255, 204, 153)" , 'color' : "rgb(68, 68, 68)"};
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
		LotCheck: function(){
            let chkLot = true;
			app.lists.forEach(function (elem, index) {
                if(app.lists[index].countNum > 0) {
                    if((!app.lists[index].lotNumber && app.lists[index].lotDate) || (app.lists[index].lotNumber && ! app.lists[index].lotDate)) {
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
        usedDateCheck: function(){
            let usedDate = true;
            
			app.lists.forEach(function (elem, index) {
				if(app.lists[index].countNum > 0) {
                    if(app.lists[index].usedDate == '' || ! app.lists[index].usedDate ) {
                        usedDate = false;
                    }
				}
			});
            
            if(!usedDate){
                UIkit.modal.alert('使用済み日時の入力を確認してください');
                return false ;
            }

            return true;
            
        },
        borrowingRegist: function(){
            UIkit.modal.confirm('貸出商品登録を行いますか？').then(function () {
    			if(! app.divisionCheck()){
    			    return false;
    			}
    			if(! app.check()){
    				return false;
    			}
    			if(! app.LotCheck()){
    			    return false;
    			}
                loading();
                $.ajax({
                    async: false,
                    url: "<?php echo $api_url ?>",
                    type:'POST',
                    data:{
                        _csrf: "<?php echo $csrf_token ?>",  // CSRFトークンを送信
                        Action : "borrowingRegistApi",
                        borrowing : JSON.stringify( objectValueToURIencode(app.lists) ),
                        divisionId : app.divisionId
                    },
                    dataType: 'json'
                })
                // Ajaxリクエストが成功した時発動
                .done( (data) => {
                    if(data.code != 0){
                        UIkit.modal.alert('貸出商品登録に失敗しました').then(function(){
                        });
                        return false;
                    }
                    UIkit.modal.alert('貸出商品登録が完了しました').then(function () {
					    app.lists.splice(0, app.lists.length);
                    });
                })
                // Ajaxリクエストが失敗した時発動
                .fail( (data) => {
                    UIkit.modal.alert('貸出商品登録に失敗しました').then(function(){
                    });
                })
                // Ajaxリクエストが成功・失敗どちらでも発動
                .always( (data) => {
                    loading_remove();
                });
            
            }, function () {
            });
        },
        
        usedReport: function(){
            UIkit.modal.confirm('使用済み報告を行いますか？').then(function () {
                
    			if(! app.divisionCheck()){
    			    return false;
    			}
    			if(! app.check()){
    				return false;
    			}
    			if(! app.LotCheck()){
    			    return false;
    			}
    			if(! app.usedDateCheck()){
    			    return false;
    			}
                loading();
                $.ajax({
                    async: false,
                    url: "<?php echo $api_url ?>",
                    type:'POST',
                    data:{
                        _csrf: "<?php echo $csrf_token ?>",  // CSRFトークンを送信
                        Action : "<?php echo $borrowingAction ?>",
                        divisionId : app.divisionId,
                        borrowing : JSON.stringify( objectValueToURIencode(app.lists) ),
                    },
                    dataType: 'json'
                })
                // Ajaxリクエストが成功した時発動
                .done( (data) => {
                    if(data.code != 0){
                        UIkit.modal.alert('使用済み報告に失敗しました').then(function(){
                        });
                        return false;
                    }
                    UIkit.modal.alert('使用済み報告が完了しました').then(function () {
					    app.lists.splice(0, app.lists.length);
                    });
                })
                // Ajaxリクエストが失敗した時発動
                .fail( (data) => {
                    UIkit.modal.alert('使用済み報告に失敗しました').then(function(){
                    });
                })
                // Ajaxリクエストが成功・失敗どちらでも発動
                .always( (data) => {
                    loading_remove();
                });
            
            }, function () {
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
                	if(lotNumber != ''){
                		data.lotNumber = lotNumber;
                	}
                	if(lotDate != ''){
                		data.lotDate = lotDate;
                	}
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
			
			let searchJan = removeCheckDigit(obj["01"]);
			let objkey = null;
			let setObj = {};
			
			let objLotNumber = (obj["10"] === void 0) ? "" : obj["10"]; //lotNumber
			let objLotDate = (obj["17"] === void 0) ? "" : this.changeDate(obj["17"]); //lotDate
			let existflg = false;
			let changeObject = null;
			
			
			existflg = app.search(searchJan,objLotNumber,objLotDate);
			
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
	app.addList(JSON.parse(JSON.stringify(object)));
}
</script>