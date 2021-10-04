<?php
include_once 'NewJoyPla/lib/ApiSpiral.php';
include_once "NewJoyPla/lib/Define.php";
include_once "NewJoyPla/lib/Func.php";
include_once "NewJoyPla/lib/SpiralDataBase.php";
include_once "NewJoyPla/lib/UserInfo.php";
include_once "NewJoyPla/api/UpdateUnordered.php";
include_once "NewJoyPla/api/GetCardInfo.php";
include_once "NewJoyPla/api/GetItemReceipt.php";

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
	$ItemsToJs[$record["inHospitalItemId"]] = array(
		"receivingCount"=> $record["receivingCount"],
		"quantity"=> $record["quantity"],
		"orderCNumber" => $record["orderCNumber"],
		"makerName" => $record["makerName"],
		"itemName" => $record["itemName"],
		"itemCode" => $record["itemCode"],
		"itemStandard" => $record["itemStandard"],
		"quantityUnit" => $record["quantityUnit"],
		"itemUnit" => $record["itemUnit"],
		"itemJANCode" => $record["itemJANCode"],
		"orderQuantity" => $record["orderQuantity"],
		"labelId" => $record["labelId"],
		"officialFlag" => $record["officialFlag"],
		"totalReturnCount" => $record["totalReturnCount"],
		"lotNumber" => $record["lotNumber"],
		"lotDate" => $record["lotDate"]
		);
}

$crypt   = $SPIRAL->getSpiralCryptOpenSsl();
$authKeyCrypt = $crypt->encrypt($card["data"][0][1], "JoyPla");
if($card["data"][0][2] != ""){
	$orderAuthKey = $crypt->encrypt($card["data"][0][2], "JoyPla");
}

if($userInfo->getUserPermission() == "1"){
	$link = '%url/table:back%';
}else{
	$link = '%url/rel:mpgt:page_266908%';
} 
?>
<!DOCTYPE html>
<html>
  <head>
    <title>JoyPla 検収書</title>
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
				    <li><span>検収書</span></li>
				</ul>
				<div class="no_print uk-margin" uk-margin>
					<input class="print_hidden uk-button uk-button-default" type="submit" value="印刷プレビュー" onclick="window.print();return false;">
					<input class="print_hidden uk-button uk-button-danger" type="submit" value="返品伝票作成" onclick="location.href='%url/card:page_266904%'">
					<form action="%url/rel:@mpgt:createLabel%" target="_blank" method="post" class="print_hidden uk-inline">
						<input class="print_hidden uk-button uk-button-primary" type="submit" value="ラベル発行" onclick="createLabel()">
						<input type="hidden" value="" name="itemsData" id="itemsData">
						<input type="hidden" value="%val:usr:distributorName%" name="distributorName">
					</form>
				</div>
		    	<div class="uk-text-center uk-text-large">
		    		<p class="uk-text-bold" style="font-size: 32px">検　収　書</p>
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
		    				<div id="nouhin_barcode" class="uk-text-center">
	    						<span id="nouhin_num">%val:usr:receivingHId%</span>
	    					</div>
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
										<th>入荷状況</th>
				    				</tr>
				    			</thead>
				    			<tbody>
				    				<?php
				    					$num = 1;
										foreach($receivingData as $record){
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
				    						$text = "入庫完了";
				    						if((int)$record["orderQuantity"] > (int)$record["receivingCount"]){
				    							$text = "一部入庫(".$record["receivingCount"]." / ".$record["orderQuantity"].")";
				    						}
				    						
				    						$returnText = "";
				    						if((int)$record["totalReturnCount"] > 0){
				    							$returnText = "<span class='uk-text-danger'>返品(".$record["totalReturnCount"]." / ".$record["receivingCount"].")</span>";
				    						}
				    						echo "<td>".$text."<br>".$returnText."</td>";
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
		$(function(){
		 let nouhin_num = $("#nouhin_num").text();
		 //$("#hacchu_num").remove();
		 $("#nouhin_barcode").html("<svg id='barcode_nouhin'></svg>");
		 generateBarcode("barcode_nouhin",nouhin_num);
		 //$("td#order_barcode div").barcode({code:order_num, crc:false }, "int25",{barWidth: 3 ,barHeight: 40 , output: "css"});
		});
		
		let itemsToJs = objectValueToURIencode( <?php echo json_encode($ItemsToJs); ?> );
		
		function createLabel(){
			$("#itemsData").val(JSON.stringify( itemsToJs ));
			return true;
		}
		
	</script>
  </body>
</html>