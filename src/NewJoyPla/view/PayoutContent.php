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
    <script>
/*
		let canAjax = true;
		let gs1128_object = {};
		let listObject = {};
		let dataKey = ['id','maker','shouhinName','code','kikaku','irisu','count','symbol','labelCount','sumCount','jan'];
		let index = 1;
		
    	// 参照ボタンクリック
		function sanshouClick(){
			// 参照マスタを別ウィンドウで開く
			window.open('%url/rel:mpgt:page_175973%', '_blank','scrollbars=yes,width=1220,height=600');
		
		}
		
		function delTr(index , elm){
			elm.parentElement.parentElement.remove()
			delete listObject[index];
		}
		
		function chkNotLotRow(itemKey) {
			let index = null;
			$(document).find('.item_' + itemKey).each(function() {
				let lotNum = $(this).parents('tr').find('.lot_' + itemKey).val();
				let lotDate = $(this).parents('tr').find('.lotDate_' + itemKey).val();
				if (!lotNum && !lotDate) {
					let target = $(this).parents('tr');
					index = $(document).find('.shouhin-table table tbody tr').index(target);
					return false;
				}
			});
			return index;
		}
		
		function addTr0(object, type, count){
			if ( type === 1 ) { //商品マスタ
				let chk = chkNotLotRow(object.recordId);
				if ( chk !== null ) { return; }
			}
			if ( type === 2 ) { //バーコード検索
				let chk = chkNotLotRow(object.recordId);
				if ( chk !== null ) {
					let target = $('.shouhin-table table tbody tr').eq(chk);
					let targetCount = parseInt(target.find('.item_' + object.recordId).val());
					if(targetCount !== count) {
						UIkit.modal.alert('すでに記載されている商品と数量が異なります。<br>追加されている数量:'+targetCount + '<br>今回追加の数量:'+count+'<br>ご確認ください。');
						return;
					}
					let setLabelCount = parseInt(target.find('.labelCount_' + object.recordId).val()) + 1;
					target.find('.labelCount_' + object.recordId).val(setLabelCount).css({'color':'rgb(68, 68, 68)', 'background':'rgb(255, 204, 153)'});
					target.find('.sum').text(targetCount * setLabelCount);
					$(window).scrollTop($(target.find('.labelCount_' + object.recordId)).offset().top - 100);
					return;
				}
			}
			
			listObject[index] = object;
			listObject[index].no = index;
			listObject[index].countNum = count;
			listObject[index].countLabelNum = 1;

			let trElm = document.createElement('tr'); 
			trElm.id = 'tr_' + index;
			let tdElm = '';
			for(let i = 0 ; i < dataKey.length; i++){
				tdElm = document.createElement('td');
				html = document.createTextNode('');
				text = '';
				input = '';
				span = '';
				if(dataKey[i] === 'id'){
					//html = document.createTextNode(listObject[object.recordId].row);
				} else if(dataKey[i] === 'count'){
					//html = '<input type="number" class="uk-input" style="width:72px" step="10">';
				    html = document.createElement('div');
				    input = document.createElement('input');
				    input.type = 'number';
				    input.step = '1';
				    input.className = 'uk-input item_' + object.recordId;
				    input.name = 'count';
				    input.style = 'width:96px';
				    input.min = 0;
				    input.value = count;
				    //if ( count > 0 ) { 
				    //  input.style.backgroundColor = "rgb(255, 204, 153)";
				    //  input.style.color = "rgb(68, 68, 68)";
				    //}
				    input.onchange  = function () {  
						  changeForInputNumber(this);
				    };
				    //input.step = listObject[object.recordId].irisu;
				    //<span class="uk-text-bottom">個</span>
				    span = document.createElement('span');
				    span.innerText = listObject[index].unit;
				    span.className = 'uk-text-bottom';
					html.appendChild(input);
					html.appendChild(span);

					
				} else if(dataKey[i] === 'labelCount'){
					html = document.createElement('div');
					//html = '<input type="number" class="uk-input" style="width:72px" step="10">';\
				    input = document.createElement('input');
				    input.type = 'number';
				    input.step = '1';
				    input.className = 'uk-input labelCount_' + object.recordId;
				    input.name = 'labelCount';
				    input.style = 'width:96px';
				    input.min = 1;
				    input.value = 1;
				    
				    input.onchange  = function () {
						  changeForInputNumber(this);
				    };
				    //input.step = listObject[object.recordId].irisu;
				    //<span class="uk-text-bottom">個</span>
				    span = document.createElement('span');
				    span.innerText = '枚';
				    span.className = 'uk-text-bottom';
					html.appendChild(input);
					html.appendChild(span);
				} else if(dataKey[i] === 'symbol'){
					html = document.createTextNode('×');
				} else if(dataKey[i] === 'sumCount'){
					html = document.createElement('div');
				    span = document.createElement('span');
				    span.className = 'sum';
				    span.innerText = parseInt(listObject[index].countNum) * parseInt(listObject[index].countLabelNum);
					html.appendChild(span);
				    
					span = document.createElement('span');
				    span.innerText = listObject[index].unit;
				    span.className = 'uk-text-bottom';
					html.appendChild(span);
				} else {
					text = '';
					if(dataKey[i] === 'kakaku'){
						text += '￥';
						text += price_text(listObject[index][dataKey[i]]);
						text += '/'+ listObject[index].itemUnit;
					} else if(dataKey[i] === 'teisu' || dataKey[i] === 'irisu') {
						text += listObject[index][dataKey[i]];
						text += listObject[index].unit;
					} else if(listObject[index][dataKey[i]]){
						text += listObject[index][dataKey[i]];
					}
					html = document.createTextNode(text);
				}
				
				tdElm.appendChild(html);
				trElm.appendChild(tdElm);
			}

			tdElm = document.createElement('td');
			input = document.createElement('input');
			input.className = 'uk-input lot_' + object.recordId;
			input.name = 'lot';
			input.type = 'text';
			input.maxLength = 20;
			input.style.width = '184px';
			if ( type === 4 && object.lot ) {
				input.value = object.lot;
				input.style.backgroundColor = 'rgb(255, 204, 153)';
				input.style.color = 'rgb(68, 68, 68)';
			}
			input.onchange  = function () {  
			    	$(this).css({'background-color':'rgb(255, 204, 153)'});
			    };
			tdElm.appendChild(input); 
			trElm.appendChild(tdElm); 
			
			tdElm = document.createElement('td');
			input = document.createElement('input');
			input.className = 'uk-input lotDate_' + object.recordId;
			input.name = 'lotDate';
			input.type = 'date';
			input.style.width = '184px';
			if ( type === 4 && object.lotDate ) {
				input.value = object.lotDate;
				input.style.backgroundColor = 'rgb(255, 204, 153)';
				input.style.color = 'rgb(68, 68, 68)';
			}
			input.onchange  = function () {  
			    	$(this).css({'background-color':'rgb(255, 204, 153)'});
			    };
			tdElm.appendChild(input); 
			trElm.appendChild(tdElm);
			
			if ( type === 4 ) {
				delete object['lot'];
				delete object['lotDate'];
			}
			
			tdElm = document.createElement('td');
			input = document.createElement('input');
			input.type = 'button';
			input.value = '削除';
			input.className = 'uk-button uk-button-danger uk-button-small';
			input.onclick = function(){
				delTr(object.no, this)
			}
			
			tdElm.appendChild(input);
			trElm.appendChild(tdElm);
			
			tdElm = document.createElement('td');
			input = document.createElement('input');
			input.type = 'button';
			input.value = '追加';
			input.className = 'uk-button uk-button-default uk-button-small';
			input.onclick = function(){
				let copy = { ...object };
				addTr(copy, 3, 0);
			}
			tdElm.appendChild(input);
			trElm.appendChild(tdElm);
			
			if ( type === 3 ) { //追加ボタン
				var from = event.target;
				$('.shouhin-table table tbody').find(from).parents('tr').after(trElm);
			} else {
				$('.shouhin-table table tbody').append(trElm);
			}
			// 数量は変更しない
			// if ( count > 0 ) { $(window).scrollTop($(trElm).find(".item_" + object.recordId).offset().top - 100); }
			index++;
			return true;
		}
		
		//function onchangeDeadlineMinimum(id,value){
		//	$('input[name="deadlineMax_'+id+'"]').attr('min',value);
		//	listObject[id].deadlineMinimum = value;
		//}

		//function onchangeDeadlineMax(id,value){
		//	$('input[name="deadlineMinimum_'+id+'"]').attr('max',value);
		//	listObject[id].deadlineMax = value;
		//}
		
		
		$(document).on("change", "input[name='count'], input[name='labelCount']", function() {
			let count = parseInt($(this).parents("tr").find("input[name='count']").val());
			let label = parseInt($(this).parents("tr").find("input[name='labelCount']").val());
			$(this).parents("tr").find(".sum").text(count * label);
			$(this).css({"color":"rgb(68, 68, 68)", "background-color":"rgb(255, 204, 153)"});
			$(window).scrollTop($(this).offset().top - 100);
		});
		
		
		
		
		function barcodeSearch(){
			if(!canAjax) { 
				console.log('通信中');
				return;
			}
            loading();
          
			canAjax = false; // これからAjaxを使うので、新たなAjax処理が発生しないようにする
			$.ajax({
				async: false,
                url:'%url/rel:mpgt:labelBarcodeSAPI%',
                type:'POST',
                data:{
                	barcode : $('input[name="barcode"]').val(),
                },
                dataType: 'json'
            })
            // Ajaxリクエストが成功した時発動
            .done( (data) => {
            	let value = 0;
                if(data.code != 0 || data.data.length == 0){
            		UIkit.modal.alert("商品が見つかりませんでした");
					canAjax = true; // 再びAjaxできるようにする
            		return false;
                }
                data = data.data;
                addTr(data, 2, parseInt(data.count));
				canAjax = true; // 再びAjaxできるようにする
                $('input[name="barcode"]').val('');
            })
            // Ajaxリクエストが失敗した時発動
            .fail( (data) => {
                UIkit.modal.alert("商品が見つかりませんでした").then(function(){
					canAjax = true; // 再びAjaxできるようにする
				});
            })
            // Ajaxリクエストが成功・失敗どちらでも発動
            .always( (data) => {
				loading_remove();
            });
		}
		
		function payoutCheck(){
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
			
			if(Object.keys(listObject).length === 0){
				UIkit.modal.alert('商品を選択してください');
				return false ;
			}
			
			let checkflg = false;
			Object.keys(listObject).forEach(function (key) {
			  if(listObject[key].countNum !== 0){
			  	checkflg = true;
			  }
			});
			
			if(checkflg){
			} else {
				UIkit.modal.alert('数量を入力してください');
				return false ;
			}
			
			checkflg = false;
			Object.keys(listObject).forEach(function (key) {
			  if(listObject[key].countLabelNum !== 0){
			  	checkflg = true;
			  }
			});
			if(checkflg){
			} else {
				UIkit.modal.alert('個数を入力してください');
				return false ;
			}
			
			let chkLot = true;
			Object.keys(listObject).forEach(function (key) {
				if(listObject[key]['countNum'] > 0) {
					if((!listObject[key]['lotNumber'] && listObject[key]['lotDate']) || (listObject[key]['lotNumber'] && !listObject[key]['lotDate'])) {
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
			Object.keys(listObject).forEach(function (key) {
				if(listObject[key]['lotNumber']) {
					if((!regex.test(listObject[key]['lotNumber'])) ||
					   (encodeURI(listObject[key]['lotNumber']).replace(/%../g, '*').length > 20)) {
			    	chkLot = false;
					}
				}
			});
			
			if(!chkLot){
				UIkit.modal.alert('ロット番号の入力を確認してください');
				return false ;
			}
			
			return true;
			
		}
		
		function sendPayout(){
			if(!canAjax) { 
				console.log('通信中');
				return;
			}
            loading();
			setRegData();
			if(! payoutCheck()){
				loading_remove();
				return false;
			}
			
			canAjax = false; // これからAjaxを使うので、新たなAjax処理が発生しないようにする
			let payout = mergeLot();
			$.ajax({
				async: false,
                url:'%url/rel:mpgt:regPayout%',
                type:'POST',
                data:{
                	payout : JSON.stringify( objectValueToURIencode(payout) ),
                	sourceDivisionId : $('select[name="sourceDivision"]').val(),
                	sourceDivisionName : encodeURI($('select[name="sourceDivision"] option:selected').text()),
                	targetDivisionId : $('select[name="targetDivision"]').val(),
                	targetDivisionName : encodeURI($('select[name="targetDivision"] option:selected').text()),
                },
                dataType: 'json'
            })
            // Ajaxリクエストが成功した時発動
            .done( (data) => {
                if(! data.result){
            		UIkit.modal.alert("払出に失敗しました").then(function(){
						canAjax = true; // 再びAjaxできるようにする
					});
            		return false;
                }
                UIkit.modal.alert("払出が完了しました").then(function(){
					UIkit.modal.confirm("ラベルを発行しますか<br>※履歴から発行も可能です").then(function () {
						createLabel();
						dataReset();
						canAjax = true; // 再びAjaxできるようにする
					}, function() {
						canAjax = true; // 再びAjaxできるようにする
						dataReset();
					});
				});
            })
            // Ajaxリクエストが失敗した時発動
            .fail( (data) => {
                UIkit.modal.alert("払出に失敗しました").then(function(){
					canAjax = true; // 再びAjaxできるようにする
				});
            })
            // Ajaxリクエストが成功・失敗どちらでも発動
            .always( (data) => {
				loading_remove();
            });
		}
		
		
		function dataReset(){
            $('table.uk-table tbody tr').remove();
            listObject = {};
            loading_remove();
		}

		function createLabel(){
			let items = {};
			Object.keys(listObject).forEach(function (key) {
				if(parseInt(listObject[key].countNum) * parseInt(listObject[key].countLabelNum) > 0){
					items[key] = listObject[key];
					items[key].printCount = listObject[key].countLabelNum;　//ラベル枚数
					items[key].quantity = listObject[key].countNum; //入数
					items[key].distributorName = listObject[key].oroshi; //業者名
					items[key].itemName = listObject[key].shouhinName; //商品名
					items[key].itemStandard = listObject[key].kikaku; //規格
				}
			});

			$("#itemsData").val(JSON.stringify( objectValueToURIencode(items) ));
			$('input[name="sourceDivision"]').val($('select[name="sourceDivision"]').val());
			$('input[name="targetDivision"]').val($('select[name="targetDivision"]').val());
			document.createLabelForm.submit();
			return true;
		}

		*/
		
    </script>
    <div id="app" class="animsition" uk-height-viewport="expand: true">
	  	<div class="uk-section uk-section-default uk-preserve-color uk-padding-remove uk-margin-top" id="page_top">
		    <div class="uk-container uk-container-expand">
		    	<ul class="uk-breadcrumb">
				    <li><a href="%url/rel:mpg:top%">TOP</a></li>
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
		    	</div>
		    	<div class="uk-margin-bottom" uk-grid>
		    		<div class="uk-width-1-2@m" uk-margin>
			    		<button class="uk-button uk-button-default" v-on:click="sanshouClick">商品マスタを開く</button>
			    		<button class="uk-button uk-button-default" type="submit" onclick="window.print();return false;">印刷プレビュー</button>
			    		<button class="uk-button uk-button-primary uk-margin-small-top" onclick="sendPayout()">払出実行</button>
		    		</div>
		    		<?php /*
		    		<div class="uk-inline uk-width-1-2@m">
	    				<input type="text" class="uk-input uk-width-4-5" placeholder="バーコード入力..." autofocus="true"> 
		    			<button class="uk-button uk-button-default uk-float-right uk-width-1-5 uk-padding-remove">検索</button>
		    		</div>*/ ?>
		    	</div>
		    	
			    <div uk-sticky="sel-target: .uk-navbar-container; cls-active: uk-navbar-sticky" class="uk-padding-top uk-background-muted uk-padding-small">
		    		<form action='#' method="post" onsubmit="barcodeSearch(); return false">
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
									<input type="number" step="1" class="uk-input" min="0" style="width: 96px;" v-model="list.count" v-on:change="changeInputStyle">
									<span class="uk-text-bottom">{{list.unit}}</span>
								</td>
								<td>×</td>
								<td>
									<input type="number" step="1" class="uk-input" min="1" style="width: 96px;" v-model="list.labelCount" v-on:change="changeInputStyle">
									<span class="uk-text-bottom">枚</span>
								</td>
								<td>{{list.count * list.labelCount}}{{list.unit}}</td>
								<td>{{list.jan}}</td>
								<td><input type="text" class="uk-input lot" v-model="list.lotNumber" v-on:change="changeInputStyle"></td>
								<td><input type="date" class="uk-input lotDate" v-model="list.lotDate" v-on:change="changeInputStyle"></td>
								<td><input type="button" class="uk-button uk-button-danger uk-button-small" value="削除" v-on:click="deleteList(key)"></td>
								<td><input type="button" class="uk-button uk-button-default uk-button-small" value="追加" v-on:click="copyList(key)"></td>
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
	
<script>
var app = new Vue({
	el: '#app',
	data: {
		lists: []
	},
	methods: {
		addList: function(object) {
			object.count = ((object.count == null)? 0 : object.count); 
			object.labelCount = ((object.labelCount == null)? 1 : object.labelCount);
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
			copy.count = null;
			copy.labelCount = null;
			copy.lotNumber = null;
			copy.lotDate = null;
			
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
		changeInputStyle: function(elm){
			elm.currentTarget.style.backgroundColor = "rgb(255, 204, 153)";
			elm.currentTarget.style.color = "rgb(68, 68, 68)";
		},
		search: function(searchJan,lotNumber,lotDate){
			//商品が存在するかつロットが同一
			let existflg = false;
			this.lists.forEach(function(elem, index) {
				changeObject = null;
				if(!existflg){
					if(searchJan == this.lists[index].jan && this.lists[index].lotNumber == objLotNumber && this.lists[index].lotDate == objLotDate ){
						changeObject = this.lists[index];
						changeObject.labelCount++;
						
						this.$set(this.lists, index, changeObject);
						existflg = true;							
						$("#tr_"+index+" input[type='text']").css({'backgroundColor' : 'rgb(255, 204, 153)','color' : 'rgb(68, 68, 68)'});
						$("#tr_"+index+" input[type='number']").css({'backgroundColor' : 'rgb(255, 204, 153)','color' : 'rgb(68, 68, 68)'});
						$("#tr_"+index+" input[type='date']").css({'backgroundColor' : 'rgb(255, 204, 153)','color' : 'rgb(68, 68, 68)'});
						$(window).scrollTop($("#tr_"+index).offset().top - 100);

					}
				}
			});
			
			//商品が存在するかつロットの記入がされていない
			if(!existflg){
				this.lists.forEach(function(elem, index) {
					changeObject = null;
					if(!existflg){
						if(searchJan == this.lists[index].jan && this.lists[index].lotNumber == null && this.lists[index].lotDate == null ){
							changeObject = this.lists[index];
							changeObject.lotNumber = objLotNumber;
							changeObject.lotDate = objLotDate;
							
							this.$set(this.lists, index, changeObject);
							existflg = true;							
							$("#tr_"+index+" input[type='text']").css({'backgroundColor' : 'rgb(255, 204, 153)','color' : 'rgb(68, 68, 68)'});
							$("#tr_"+index+" input[type='number']").css({'backgroundColor' : 'rgb(255, 204, 153)','color' : 'rgb(68, 68, 68)'});
							$("#tr_"+index+" input[type='date']").css({'backgroundColor' : 'rgb(255, 204, 153)','color' : 'rgb(68, 68, 68)'});
							$(window).scrollTop($("#tr_"+index).offset().top - 100);

						}
					}
				});
			}
			
			//商品をaddする
			if(!existflg){
				this.lists.forEach(function(elem, index) {
					changeObject = null;
					if(!existflg){
						if(searchJan == this.lists[index].jan){
							changeObject = this.lists[index];
							changeObject.lotNumber = objLotNumber;
							changeObject.lotDate = objLotDate;
							
							this.addList(changeObject);
							existflg = true;
							$("#tr_"+index+" input").css({'backgroundColor' : 'rgb(255, 204, 153)','color' : 'rgb(68, 68, 68)'});
							$("#tr_"+index+" input[type='number']").css({'backgroundColor' : 'rgb(255, 204, 153)','color' : 'rgb(68, 68, 68)'});
							$("#tr_"+index+" input[type='date']").css({'backgroundColor' : 'rgb(255, 204, 153)','color' : 'rgb(68, 68, 68)'});
							$(window).scrollTop($("#tr_"+index).offset().top - 100);
						}
					}
				});
			}
			
			return existflg;
		},
		labelSearch: function(object) {
			let existflg = false;
			this.lists.forEach(function(elem, index) {
				changeObject = null;
				if(!existflg){
					if(object.jan == this.lists[index].jan && this.lists[index].count == object.count){
						if(object.lotNumber == this.lists[index].lotNumber && object.objLotDate == this.lists[index].objLotDate){
							changeObject = this.lists[index];
							changeObject.labelCount++;
							
							this.$set(this.lists, index, changeObject);
							existflg = true;							
							$("#tr_"+index+" input[type='text']").css({'backgroundColor' : 'rgb(255, 204, 153)','color' : 'rgb(68, 68, 68)'});
							$("#tr_"+index+" input[type='number']").css({'backgroundColor' : 'rgb(255, 204, 153)','color' : 'rgb(68, 68, 68)'});
							$("#tr_"+index+" input[type='date']").css({'backgroundColor' : 'rgb(255, 204, 153)','color' : 'rgb(68, 68, 68)'});
							$(window).scrollTop($("#tr_"+index).offset().top - 100);
						}
					}
				}
			});
			if(!existflg){
				this.lists.forEach(function(elem, index) {
					changeObject = null;
					if(!existflg){
						if(object.jan == this.lists[index].jan){
							this.addList(object);
							existflg = true;
							$(window).scrollTop($("#tr_"+index).offset().top - 100);
						}
					}
				});
			}
			return existflg;
		},
		barcodeSearch: function() {
			$.ajax({
				async: false,
                url:'%url/rel:mpgt:labelBarcodeSAPI%',
                type:'POST',
                data:{
                	barcode : $('input[name="barcode"]').val(),
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
                data = data.data;
                this.labelSearch(data);
                $('input[name="barcode"]').val('');
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

var gs1_128 = new Vue({
	el: '#gs1-128',
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
			date = text.slice(6, 8);
			if(parseInt(text.slice(6, 8)) == 0){
				date = '01';
			}
			return text.slice(0, 4) + "-" + text.slice(4, 6) + "-" + date;
		},
		check_gs1_128: function (gs1128)
		{
			if(app.lists.length === 0)
			{
				UIkit.modal.alert('先に商品を選択してください。');
				return false ;
			}
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
				UIkit.modal.alert("対象の発注商品が見つかりませんでした。").then(function(){
					UIkit.modal($('#gs1-128')).show();
				});
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
/*
    	function gs1_128(gs1128){
    		gs1128_object = {};
				
			if(Object.keys(listObject).length === 0){
				UIkit.modal.alert('先に商品を選択してください。');
				return false ;
			}
				
			if(gs1128.indexOf("]C1") !== 0){
				//UIkit.modal.alert("GS1-128ではありません");
				//return ;
				return gs1_128("]C1"+gs1128);
			} else {
				gs1128 = gs1128.slice( 3 );
				let obj = check_gs1128(gs1128);
				let objkey = null;
				let setObj = {};
				
				if(!obj.hasOwnProperty("01")){
					UIkit.modal.alert("商品情報が含まれておりませんでした。").then(function(){
						UIkit.modal($('#modal-gs1128')).show();
					});
					return;
				}
				
				searchJan = addCheckDigit(obj["01"]);
				console.log(searchJan);
				Object.keys(listObject).forEach(function (key) {
					if(searchJan == listObject[key]["jan"]){
						objkey = listObject[key]["recordId"];
						setObj = { ...listObject[key] };
					}
				});
				
				if(!objkey){
					UIkit.modal.alert("対象の発注商品が見つかりませんでした。").then(function(){
						UIkit.modal($('#modal-gs1128')).show();
					});
					return;
				}

				let existflg = false;
				let objLot = (obj["10"] === void 0) ? "" : obj["10"]; //lotNumber
				let objLotDate = (obj["17"] === void 0) ? "" : changeDate(obj["17"]); //lotDate

				$(document).find(".lot_" + objkey).each(function() {
					let addRowLot = $(this).val();
					let addRowLotDate = $(this).parents("tr").find(".lotDate_" + objkey).val();
					let addRowNum = parseInt($(this).parents("tr").find(".item_" + objkey).val());

					if (addRowNum === 0 && !addRowLot && !addRowLotDate) {
						$(this).val(objLot).css({"color":"rgb(68, 68, 68)", "background" : "rgb(255, 204, 153)"});
						$(this).parents("tr").find(".lotDate_" + objkey).val(objLotDate).css({"color":"rgb(68, 68, 68)", "background" : "rgb(255, 204, 153)"});
//						$(this).parents("tr").find(".item_" + objkey).val(parseInt(setObj.irisu)).css({"color":"rgb(68, 68, 68)", "background" : "rgb(255, 204, 153)"});
						$(window).scrollTop($(this).offset().top - 100);
						existflg = true;
						return false;
					}
					if ((addRowLot == objLot) && (addRowLotDate == objLotDate)) {
//						let num = addRowNum + parseInt(setObj.irisu);
//						$(this).parents("tr").find(".item_" + objkey).val(num).css({"color":"rgb(68, 68, 68)", "background" : "rgb(255, 204, 153)"});
//						let itemIndex = $.trim(($(this).parents("tr").attr("id")).replace("tr_", ""));
//						$(window).scrollTop($(this).offset().top - 100);
						existflg = true;
						return false;
					}
				});

				if (!existflg) {
					setObj.lot = objLot;
					setObj.lotDate = objLotDate;
					addTr(setObj, 4, 0);
				}

				$(".select_items").hide();
				$(".select_items select").val("");
				$("#GS1-128").val("");
				document.getElementById("GS1-128").focus();
			}
		}

		function changeDate(text){
			if(text == null){
				return "";
			}
			if(text.length == "6"){
				text = 20 + text;
			}
			date = text.slice(6, 8);
			if(parseInt(text.slice(6, 8)) == 0){
				date = '01';
			}
			return text.slice(0, 4) + "-" + text.slice(4, 6) + "-" + date;
		}

		function setRegData() {
			$(document).find('.shouhin-table table tbody tr').each(function() {
				let row = $.trim(($(this).attr('id')).replace('tr_', ''));
				let count = $(this).find('input[name="count"]').val();
				let labelCount = $(this).find('input[name="labelCount"]').val();
				let lot = $(this).find('input[name="lot"]').val();
				let date = $(this).find('input[name="lotDate"]').val();
				listObject[row].countNum = parseInt(count);
				listObject[row].countLabelNum = parseInt(labelCount);
				listObject[row].lotNumber = lot;
				listObject[row].lotDate = date;
			});
		}

		function mergeLot() {
			let regObj = {};
			Object.keys(listObject).forEach(function (key) {
				let itemId = listObject[key]['recordId'];
				if (regObj[itemId] === void 0) { regObj[itemId] = {}; }
				let lotKey = listObject[key].lotNumber + listObject[key].lotDate;
				let temp = Object.entries(regObj[itemId]);
				let chkLotDup = temp.findIndex(([id, data]) => data.lotNumber == listObject[key].lotNumber && data.lotDate == listObject[key].lotDate);
				if (chkLotDup === -1) {
					regObj[itemId][lotKey] = { ...listObject[key] };
				} else {
					regObj[itemId][lotKey]['countNum'] = regObj[itemId][lotKey]['countNum'] + listObject[key].countNum;
					regObj[itemId][lotKey]['countLabelNum'] = regObj[itemId][lotKey]['countLabelNum'] + listObject[key].countLabelNum;
				}
				
				if (!listObject[key].lotNumber && !listObject[key].lotDate) {
					if (regObj[itemId][0] === void 0) {
						regObj[itemId][0] = {};
						regObj[itemId][0] = { ...listObject[key] };
					} else {
						regObj[itemId][0]['countNum'] = regObj[itemId][0]['countNum'] + listObject[key].countNum;
						regObj[itemId][0]['countLabelNum'] = regObj[itemId][0]['countLabelNum'] + listObject[key].countLabelNum;
					}
				}
			});
			return regObj;
		}
*/
</script>