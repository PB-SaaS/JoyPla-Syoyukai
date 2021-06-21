
<?php
include_once 'NewJoyPla/lib/ApiSpiral.php';
include_once "NewJoyPla/lib/Define.php";
include_once 'NewJoyPla/lib/SpiralDataBase.php';
include_once 'NewJoyPla/lib/UserInfo.php';
include_once 'NewJoyPla/api/GetDivision.php';


$userInfo = new App\Lib\UserInfo($SPIRAL);

$spiralApiCommunicator = $SPIRAL->getSpiralApiCommunicator();
$spiralApiRequest = new SpiralApiRequest();
$spiralDataBase = new App\Lib\SpiralDataBase($SPIRAL,$spiralApiCommunicator,$spiralApiRequest);

$getDivision = new App\Api\GetDivision($spiralDataBase,$userInfo);

$divisionData = $getDivision->select();

?>
<!DOCTYPE html>
<html> 
  <head>
    <title>JoyPla 払出内容入力</title>
	<?php include_once 'NewJoyPla/src/Head.php'; ?>
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
		let canAjax = true;
    	// 参照ボタンクリック
		function sanshouClick(){
			// 参照マスタを別ウィンドウで開く
			window.open('%url/rel:mpgt:page_262313%', '_blank','scrollbars=yes,width=1220,height=600');
		
		}
		
		let listObject = {};
		let dataKey = ['id','maker','shouhinName','code','kikaku','irisu','count','symbol','labelCount','sumCount','jan'];
		
		function delTr(object , elm){
			elm.parentElement.parentElement.remove()
			delete listObject[object.recordId];
		}

		function addTr(object){
			if(listObject[object.recordId]){
				return false;
			} else {
				listObject[object.recordId] = object;
				listObject[object.recordId].row = Object.keys(listObject).length;
			}
			listObject[object.recordId].countNum = 0;
			listObject[object.recordId].countLabelNum = 1;
			let trElm = document.createElement("tr"); 
			let tdElm = '';
			for(let i = 0 ; i < dataKey.length; i++){
				tdElm = document.createElement("td");
				html = document.createTextNode('');
				text = '';
				input = '';
				span = '';
				if(dataKey[i] === 'id'){
					//html = document.createTextNode(listObject[object.recordId].row);
				} else if(dataKey[i] === 'count'){
					//html = '<input type="number" class="uk-input" style="width:72px" step="10">';
				    html = document.createElement("div");
				    input = document.createElement("input");
				    input.type = 'number';
					input.step="1";
				    input.className = 'uk-input';
				    input.style = 'width:96px';
				    input.id = 'hp_' + object.recordId;
				    input.min = 0;
				    input.value = listObject[object.recordId].countNum;
				    
				    input.onchange  = function () {  
						changeForInputNumber(this);
				    	onchangeCountNum(object.recordId,this.value);
						onchangeSumCountNum(object.recordId);
				    };
				    //input.step = listObject[object.recordId].irisu;
				    //<span class="uk-text-bottom">個</span>
				    span = document.createElement("span");
				    span.innerText = listObject[object.recordId].unit;
				    span.className = 'uk-text-bottom';
					html.appendChild(input);
					html.appendChild(span);

					
				} else if(dataKey[i] === 'labelCount'){
					html = document.createElement("div");
					//html = '<input type="number" class="uk-input" style="width:72px" step="10">';\
				    input = document.createElement("input");
				    input.type = 'number';
					input.step="1";
				    input.className = 'uk-input';
				    input.style = 'width:96px';
				    input.id = 'labelCount_' + object.recordId;
				    input.min = 1;
				    input.value = 1;
				    
				    input.onchange  = function () {
						changeForInputNumber(this);
				    	onchangeCountLabelNum(object.recordId,parseInt(this.value));
						onchangeSumCountNum(object.recordId);
				    };
				    //input.step = listObject[object.recordId].irisu;
				    //<span class="uk-text-bottom">個</span>
				    span = document.createElement("span");
				    span.innerText = '枚';
				    span.className = 'uk-text-bottom';
					html.appendChild(input);
					html.appendChild(span);
				} else if(dataKey[i] === 'symbol'){
					html = document.createTextNode('×');
				} else if(dataKey[i] === 'sumCount'){
					html = document.createElement("div");
				    span = document.createElement("span");
				    span.id = 'sumCount_' + object.recordId;
				    span.innerText = parseInt(listObject[object.recordId].countNum) * parseInt(listObject[object.recordId].countLabelNum);
					html.appendChild(span);
				    
					span = document.createElement("span");
				    span.innerText = listObject[object.recordId].unit;
				    span.className = 'uk-text-bottom';
					html.appendChild(span);
				} else {
					text = '';
					if(dataKey[i] === 'kakaku'){
						text += '￥';
						text += price_text(listObject[object.recordId][dataKey[i]]);
						text += '/'+ listObject[object.recordId].itemUnit;
					} else if(dataKey[i] === 'teisu' || dataKey[i] === 'irisu') {
						text += listObject[object.recordId][dataKey[i]];
						text += listObject[object.recordId].unit;
					} else if(listObject[object.recordId][dataKey[i]]){
						text += listObject[object.recordId][dataKey[i]];
					}
					html = document.createTextNode(text);
				}
				
				tdElm.appendChild(html);
				trElm.appendChild(tdElm);
			}
			tdElm = document.createElement("td");

			input = document.createElement("input");
			input.type = 'button';
			input.value = '削除';
			input.className = 'uk-button uk-button-danger uk-button-small';
			input.onclick = function(){
				delTr(object,this)
			}
			
			tdElm.appendChild(input);
			trElm.appendChild(tdElm);
			
			$(".shouhin-table table tbody").append(trElm);
			return true;
		}
		/*
		function onchangeDeadlineMinimum(id,value){
			$('input[name="deadlineMax_'+id+'"]').attr('min',value);
			listObject[id].deadlineMinimum = value;
		}
		*/

		/*
		function onchangeDeadlineMax(id,value){
			$('input[name="deadlineMinimum_'+id+'"]').attr('max',value);
			listObject[id].deadlineMax = value;
		}
		*/
		function onchangeCountNum(id,value){
			listObject[id].countNum = value;
			$('#hp_'+id).css({"color":"rgb(68, 68, 68)", "background-color":"rgb(255, 204, 153)", "width":"96px"});
			$('#hp_'+id).val(value);
			$(window).scrollTop($('#hp_'+id+':first').offset().top - 100);
		}
		
		function onchangeCountLabelNum(id,value){
			listObject[id].countLabelNum = value;
			$('#labelCount_'+id).css({"color":"rgb(68, 68, 68)", "background-color":"rgb(255, 204, 153)", "width":"96px"});
			$('#labelCount_'+id).val(value);
			$(window).scrollTop($('#labelCount_'+id+':first').offset().top - 100);
		}

		function onchangeSumCountNum(id){
			value = parseInt(listObject[id].countLabelNum) * parseInt(listObject[id].countNum);
			$('#sumCount_'+id).text(value);
		}		
		
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
                let addFlag = addTr(data);
                //value = parseInt(listObject[data.recordId].countNum) + parseInt(data.count);
				value = parseInt(data.count);
				if(addFlag == false){
					if(parseInt(listObject[data.recordId].countNum) !== value){
						UIkit.modal.alert("すでに記載されている商品と数量が異なります。<br>追加されている数量:"+listObject[data.recordId].countNum + "<br>今回追加の数量:"+value+"<br>ご確認ください。").then(function(){
							canAjax = true; // 再びAjaxできるようにする
						});
						return false;
					}
					value = parseInt(listObject[data.recordId].countLabelNum) + 1;
					onchangeCountLabelNum(data.recordId,value);
				} else {
					onchangeCountNum(data.recordId,value);
				}

				onchangeSumCountNum(data.recordId);
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
			
			return true;
			
		}
		
		function sendPayout(){
			if(!canAjax) { 
				console.log('通信中');
				return;
			}
            loading();
			if(! payoutCheck()){
				loading_remove();
				return false;
			}
			
			canAjax = false; // これからAjaxを使うので、新たなAjax処理が発生しないようにする
			$.ajax({
				async: false,
                url:'%url/rel:mpgt:regPayout%',
                type:'POST',
                data:{
                	payout : JSON.stringify( objectValueToURIencode(listObject) ),
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

		
		
		
    </script>
  </head>
  <body>
    <?php include_once 'NewJoyPla/src/HeaderForMypage.php'; ?>
    <div class="animsition" uk-height-viewport="expand: true">
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
								$stringDom = '';
				                if($userInfo->getUserPermission() == '1') {
					                $stringDom .= '<option value="'.$divisionData['store'][0][1].'">'.$divisionData['store'][0][3].'(大倉庫)</option>';
					                $stringDom .= '<option value="" disabled>--------------------</option>';
					                foreach($divisionData['division'] as $divisiton){
					                	if($divisiton[5] == '1'){
					                		continue;
					                	}
										$stringDom .= '<option value="'.$divisiton[1].'">'.$divisiton[3].'</option>';
					                }
				                } else if($userInfo->getDivisionId() == $divisionData['store'][0][1]) {
					                $stringDom .= '<option value="'.$divisionData['store'][0][1].'">'.$divisionData['store'][0][3].'(大倉庫)</option>';
				                } else {
					                foreach($divisionData['division'] as $divisiton){
					                	if($divisiton[5] == '1'){
					                		continue;
					                	}
										$stringDom .= '<option value="'.$divisiton[1].'">'.$divisiton[3].'</option>';
					                }
								}
								echo $stringDom;
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
								$stringDom = '';
				                $stringDom .= '<option value="'.$divisionData['store'][0][1].'">'.$divisionData['store'][0][3].'(大倉庫)</option>';
				                $stringDom .= '<option value="" disabled>---------------------</option>';
				                foreach($divisionData['division'] as $divisiton){
									if($divisiton[5] == '1'){
										continue;
									}
									$stringDom .= '<option value="'.$divisiton[1].'">'.$divisiton[3].'</option>';
								}
								echo $stringDom;
				                ?>
				            </select>
			            </div>
			        </div>
		    	</div>
		    	<div class="uk-margin-bottom" uk-grid>
		    		<div class="uk-width-1-2@m" uk-margin>
			    		<button class="uk-button uk-button-default" onclick="sanshouClick()">商品マスタを開く</button>
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
		    					<th></th>
		    				</tr>
		    			</thead>
		    			<tbody>
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
	
  </body>
</html>
