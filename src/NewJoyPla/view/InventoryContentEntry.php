
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
    <title>JoyPla 棚卸内容入力</title>
	<?php include_once 'NewJoyPla/src/Head.php'; ?>
    <style>
		
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
		let dataKey = ['id','maker','shouhinName','code','kikaku','irisu','count','kakaku','jan','oroshi'];
		
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
			let trElm = document.createElement("tr"); 
			let tdElm = '';
			for(let i = 0 ; i < dataKey.length; i++){
				tdElm = document.createElement("td");
				html = document.createTextNode('');
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
				    };
				    //input.step = listObject[object.recordId].irisu;
				    //<span class="uk-text-bottom">個</span>
				    span = document.createElement("span");
				    span.innerText = listObject[object.recordId].unit;
				    span.className = 'uk-text-bottom';
					html.appendChild(input);
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
					} else {
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
		}
		
		function onchangeCountNum(id,value){
			listObject[id].countNum = value;
			$('#hp_'+id).css({"color":"rgb(68, 68, 68)", "background-color":"rgb(255, 204, 153)", "width":"96px"});
			$('#hp_'+id).val(value);
			$(window).scrollTop($('#hp_'+id+':first').offset().top - 100);
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
            		UIkit.modal.alert("商品が見つかりませんでした").then(function(){
						canAjax = true; // 再びAjaxできるようにする
					});
            		return false;
                }
                data = data.data;
                addTr(data);
                value = parseInt(listObject[data.recordId].countNum) + parseInt(data.count);
                onchangeCountNum(data.recordId,value);
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
		
		function inventoryCheck(){
			if($('select[name="busyo"]').val()){
			} else {
				UIkit.modal.alert('部署を選択してください');
				return false ;
			}
			
			if(Object.keys(listObject).length === 0){
				UIkit.modal.alert('商品を選択してください');
				return false ;
			}
			/*
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
			*/
			return true;
			
		}
		
		function sendInventory(){
			if(!canAjax) { 
				console.log('通信中');
				return;
			}
            loading();
			if(! inventoryCheck()){
				loading_remove();
				return false;
			}
			
			canAjax = false; // これからAjaxを使うので、新たなAjax処理が発生しないようにする
			$.ajax({
				async: false,
                url:'%url/rel:mpgt:regInventory%',
                type:'POST',
                data:{
                	inventory : JSON.stringify( objectValueToURIencode(listObject) ),
                	divisionId : $('select[name="busyo"]').val()
                },
                dataType: 'json'
            })
            // Ajaxリクエストが成功した時発動
            .done( (data) => {
                if(! data.result){
            		UIkit.modal.alert("棚卸登録に失敗しました").then(function(){
						canAjax = true; // 再びAjaxできるようにする
					});
            		return false;
                }
                UIkit.modal.alert("棚卸登録が完了しました").then(function(){
					canAjax = true; // 再びAjaxできるようにする
					dataReset();
				});
            })
            // Ajaxリクエストが失敗した時発動
            .fail( (data) => {
                UIkit.modal.alert("棚卸登録に失敗しました").then(function(){
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
		
		
		
    </script>
  </head>
  <body>
    <?php include_once 'NewJoyPla/src/HeaderForMypage.php'; ?>
    <div class="animsition" uk-height-viewport="expand: true">
	  	<div class="uk-section uk-section-default uk-preserve-color uk-padding-remove uk-margin-top" id="page_top">
		    <div class="uk-container uk-container-expand">
		    	<ul class="uk-breadcrumb">
				    <li><a href="%url/rel:mpg:top%">TOP</a></li>
				    <li><span>棚卸内容入力</span></li>
				</ul>
		    	<h2 class="page_title">棚卸内容入力</h2>
		    	<hr>
		    	<div class="uk-width-1-3@m">
		    		<div class="uk-margin">
			            <select class="uk-select" name="busyo">
				                <option value="">----- 部署選択 -----</option>
				                <?php if($userInfo->getUserPermission() == '1'){ 
					                echo '<option value="'.$divisionData['store'][0][1].'">'.$divisionData['store'][0][3].'(大倉庫)</option>';
					                echo '<option value="" disabled>---------------------</option>';
					                foreach($divisionData['division'] as $divisiton){
				                		if($divisiton[5] == '1'){ continue; }
					                	echo '<option value="'.$divisiton[1].'">'.$divisiton[3].'</option>';
					                }
				                } else {
				                	if($divisionData['store'][0][1] == $userInfo->getDivisionId()){
				                		echo '<option value="'.$divisionData['store'][0][1].'">'.$divisionData['store'][0][3].'(大倉庫)</option>';
				                	} else {
						                foreach($divisionData['division'] as $divisiton){
				                			if($divisiton[1] != $userInfo->getDivisionId()){ continue; }
								            echo '<option value="'.$divisiton[1].'">'.$divisiton[3].'</option>';
						                }
				                	}
				                }?>
			            </select>
			        </div>
		    	</div>
		    	<div class="uk-margin-bottom" >
		    		<div class="" uk-margin>
			    		<button class="uk-button uk-button-default " onclick="sanshouClick()">商品マスタを開く</button>
			    		<button class="uk-button uk-button-default" type="submit" onclick="window.print();return false;">印刷プレビュー</button>
			    		<button class="uk-button uk-button-primary" onclick="sendInventory()">棚卸入力</button>
		    		</div>
		    		<?php /*
		    		<div class="uk-inline uk-width-1-2@m">
	    				<input type="text" class="uk-input uk-width-4-5" placeholder="バーコード入力..." autofocus="true"> 
		    			<button class="uk-button uk-button-default uk-float-right uk-width-1-5 uk-padding-remove">検索</button>
		    		</div>
		    		*/?>
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
		    					<th>id</th>
		    					<th>メーカー</th>
		    					<th>商品名</th>
		    					<th>製品コード</th>
		    					<th>規格</th>
		    					<th>入数</th>
		    					<th>棚卸数量</th>
		    					<th>価格</th>
		    					<th>JANコード</th>
		    					<th>卸業者</th>
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
		    				</tr>
		    			</tfoot>
		    		</table>
		    	</div>
		    </div>
		</div>
	</div>
  </body>
</html>
