<?php
include_once 'NewJoyPla/lib/ApiSpiral.php';
include_once "NewJoyPla/lib/Define.php";
include_once "NewJoyPla/lib/SpiralDataBase.php";
include_once "NewJoyPla/lib/UserInfo.php";
include_once "NewJoyPla/api/UpdateUnordered.php";
include_once "NewJoyPla/api/GetCardInfo.php";
include_once "NewJoyPla/api/GetItemReceipt.php";

include_once "NewJoyPla/lib/Func.php";


$userInfo = new App\Lib\UserInfo($SPIRAL);

$spiralApiCommunicator = $SPIRAL->getSpiralApiCommunicator();
$spiralApiRequest = new SpiralApiRequest();
$spiralDataBase = new App\Lib\SpiralDataBase($SPIRAL,$spiralApiCommunicator,$spiralApiRequest);

$cardInfo = new App\Api\GetCardInfo($spiralDataBase);
$card = $cardInfo->select("receiptHDB",$SPIRAL->getCardId(),"receivingHId","authKey","orderAuthKey","divisionId");

if($userInfo->getUserPermission() != "1" && $card["data"][0][3] != $userInfo->getDivisionId()){
	App\Lib\viewNotPossible();
	exit;
}

$getItemReceipt = new App\Api\GetItemReceipt($spiralDataBase,$userInfo);

$receiptItem = $getItemReceipt->getItemReceipt($card["data"][0][0]);
//$receivingData = $spiralDataBase->arrayToNameArray($receiptItem["data"],array("id","makerName","itemName","itemCode","itemStandard","quantity","quantityUnit","itemUnit","itemJANCode","orderQuantity","receivingCount","orderCNumber","inHospitalItemId"));
$receivingData = $receiptItem["data"];

foreach($receivingData as $record){
	$ItemsToJs[$record["receivingNumber"]] = array(
		"inHospitalItemId" => $record["inHospitalItemId"],
		"receivingCount" => $record["receivingCount"],
		"quantity" => $record["quantity"],
		"orderCNumber" => $record["orderCNumber"],
		"makerName" => $record["makerName"],
		"itemName" => $record["itemName"],
		"itemCode" => $record["itemCode"],
		"itemStandard" => $record["itemStandard"],
		"quantityUnit" => $record["quantityUnit"],
		"itemUnit" => $record["itemUnit"],
		"itemJANCode" => $record["itemJANCode"],
		"orderQuantity" => $record["orderQuantity"],
		"receivingNumber" => $record["receivingNumber"],
		"totalReturnCount" => $record["totalReturnCount"],
		"price" => $record["price"],
		"returnCount" => 0,
		"lotNumber" => $record["lotNumber"],
		"lotDate" => $record["lotDate"]
		);
}

$crypt   = $SPIRAL->getSpiralCryptOpenSsl();
$authKeyCrypt = $crypt->encrypt($card["data"][0][1], "JoyPla");
$orderAuthKey = $crypt->encrypt($card["data"][0][2], "JoyPla");

if($userInfo->getUserPermission() == "1"){
	$link = '%url/table:back%';
}else{
	$link = '%url/rel:mpgt:page_266908%';
} 
?>
<!DOCTYPE html>
<html>
  <head>
    <title>JoyPla 返品伝票入力</title>
	<?php include_once "NewJoyPla/src/Head.php"; ?>

  </head>
  <body>
    <?php include_once "NewJoyPla/src/HeaderForMypage.php"; ?>
    <div class="animsition uk-margin-bottom" uk-height-viewport="expand: true">
	  	<div class="uk-section uk-section-default uk-preserve-color uk-padding-remove uk-margin-top" id="page_top">
		    <div class="uk-container uk-container-expand">
		    	<ul class="uk-breadcrumb no_print">
				    <li><a href="%url/rel:mpg:top%">TOP</a></li>
				    <li><a href="<?php echo $link ?>&table_cache=true"><span>検収書一覧</span></a></li>
				    <li><a href="%url/card:page_266892%"><span>検収書</span></a></li>
				    <li><span>返品伝票入力</span></li>
				</ul>
				<div class="no_print uk-margin" uk-margin>
					<input class="print_hidden uk-button uk-button-default" type="submit" value="印刷プレビュー" onclick="window.print();return false;">
					<input class="print_hidden uk-button uk-button-danger" type="submit" value="返品伝票登録" onclick="returnReg();return false;">
				</div>
		    	<div class="uk-text-center uk-text-large">
		    		<p class="uk-text-bold" style="font-size: 32px">返　品　伝　票　入　力</p>
		    	</div>
		    	<div uk-grid>
			    	<div class="uk-width-1-2@m">
		    			<table class="uk-table uk-width-1-1 uk-width-2-3@m uk-table-divider">
		    				<tr class="uk-text-large">
		    					<td colspan="2">
		    						<b>%val:usr:distributorName% 御中</b>
		    					</td>
		    				</tr>
		    			</table>
			    	</div>
			    	<div class="uk-width-1-2@m">
			    		<div class="uk-float-right uk-width-2-3@m">
			    			<table class="uk-table uk-width-1-1 uk-table-divider">
			    				<tr>
			    					<td>検収日時</td>
			    					<td>%val:usr:registrationTime%</td>
			    				</tr>
			    				<tr>
			    					<td>検収番号</td>
			    					<td>%val:usr:receivingHId%</td>
			    				</tr>
			    				<tr>
			    					<td>発注番号</td>
			    					<td>%val:usr:orderHistoryId%</td>
			    				</tr>
			    			</table>
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
		    	
		    	<div class="uk-margin" id="tablearea">
		    		<form>
			    		<div class="uk-overflow-auto">
				    		<table class="uk-table uk-table-hover uk-table-middle uk-table-divider uk-text-nowrap" id="tbl-Items">
				    			<thead>
				    				<tr>
										<th>NO</th>
										<th style="min-width:150px">メーカー</th>
										<th style="min-width:150px">商品名</th>
										<th>製品コード</th>
										<th>規格</th>
										<th>ロット番号</th>
										<th>使用期限</th>
										<th>入数</th>
										<th>発注数</th>
										<th>入庫数</th>
										<th>返品数</th>
										<th>今回返品数</th>
				    				</tr>
				    			</thead>
				    			<tbody>
				    				<?php
										$num = 1;
										foreach($receivingData as $record){
											$max = 0;
											if($record["receivingCount"] == ''){
												$record["receivingCount"] = 0;
											}
											if($record["totalReturnCount"] == ''){
												$record["totalReturnCount"] = 0;
											}
											if(((int)$record["receivingCount"] - (int)$record["totalReturnCount"]) > 0){
												$max = (int)$record["receivingCount"] - (int)$record["totalReturnCount"];
											}
				    						echo "<tr>";
				    						echo "<td>".$num."</td>";
				    						echo "<td>".$record["makerName"]."</td>";
				    						echo "<td>".$record["itemName"]."</td>";
				    						echo "<td>".$record["itemCode"]."</td>";
				    						echo "<td>".$record["itemStandard"]."</td>";
				    						echo "<td>".$record["lotNumber"]."</td>";
				    						echo "<td>".$record["lotDate"]."</td>";
				    						echo "<td>".$record["quantity"].$record["quantityUnit"]."</td>";
				    						echo "<td>".$record["orderQuantity"].$record["itemUnit"]."</td>";
				    						echo "<td>".$record["receivingCount"].$record["itemUnit"]."</td>";
				    						echo "<td>".$record["totalReturnCount"].$record["itemUnit"]."</td>";
				    						echo "<td><input type='number' step='1' class='uk-input' style='width:100px' min='0' max='".$max."' value='0' onchange='returnCount(this,\"".$record["receivingNumber"]."\")'><span class='uk-text-small uk-text-middle'>".$record["itemUnit"]."</span></td>";
				    						echo "</tr>";
				    						$num++;
				    					}
				    				?>
				    			</tbody>
				    			
				    		</table>
				    	</div>
			    	
		    		</form>
		    	</div>
		    </div>
		</div>
	</div>
	<script>
    	let canAjax = true;
		let itemsToJs = objectValueToURIencode( <?php echo json_encode($ItemsToJs); ?> );
		function returnReg(){
			if(!canAjax) { 
				console.log('通信中');
				return;
			}
			let errflag = false;
			let checked = false;
			Object.keys(itemsToJs).forEach(function (inhpkey) {
				if(!checked){
					if(parseInt(itemsToJs[inhpkey].returnCount) <= 0){
						errflag = true;	
					} else {
						errflag = false;
						checked = true;
					}
				}
			});
			
			if(errflag){
				UIkit.modal.alert("返品対象がありません");
				return;
			}
			
			errflag = false;
			Object.keys(itemsToJs).forEach(function (inhpkey) {
				if(parseInt(itemsToJs[inhpkey].receivingCount) < parseInt(itemsToJs[inhpkey].returnCount)){
					errflag = true;	
				}
			});
			
			if(errflag){
				UIkit.modal.alert("返品数が入庫数を超えています");
				return;
			}
			
			UIkit.modal.confirm("返品を行います。<br>よろしいですか").then(function () {
				UIkit.modal.alert("注意:卸業者へのご連絡はシステムでは行いません。").then(function(){
					loading();
					canAjax = false; // これからAjaxを使うので、新たなAjax処理が発生しないようにする
					$.ajax({
						async: false,
						url:"%url/rel:@mpgt:regReturn%",
						type:"POST",
						data:{
							divisionId: "%val:usr:divisionId%",
							receivingHistoryId : "%val:usr:receivingHId%",
							distributorId: "%val:usr:distributorId%",
							returnData : JSON.stringify( itemsToJs ),
						},
						dataType: "json"
					})
					// Ajaxリクエストが成功した時発動
					.done( (data) => {
						
						if(! data.result){
							UIkit.modal.alert("返品伝票登録に失敗しました").then(function(){
								canAjax = true; // 再びAjaxできるようにする
							});
							return false;
						}
						UIkit.modal.alert("返品伝票登録が完了しました").then(function(){
							location.href ="%url/card:page_263469%";
						});
						
					})
					// Ajaxリクエストが失敗した時発動
					.fail( (data) => {
						
						UIkit.modal.alert("返品伝票登録に失敗しました").then(function(){
							canAjax = true; // 再びAjaxできるようにする
						});
						return false;
					})
					// Ajaxリクエストが成功・失敗どちらでも発動
					.always( (data) => {
						loading_remove();
					});
				});
			}, function () {
				UIkit.modal.alert("中止します");
			});
		}
		
		function returnCount(elm,receivingNumber){
			itemsToJs[receivingNumber].returnCount = elm.value;
			$(elm).css({"color":"rgb(68, 68, 68)", "background-color":"rgb(255, 204, 153)", "width":"100px"});
		}
		
	</script>
  </body>
</html>