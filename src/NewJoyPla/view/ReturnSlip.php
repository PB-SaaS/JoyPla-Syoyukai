<?php
include_once 'NewJoyPla/lib/ApiSpiral.php';
include_once "NewJoyPla/lib/Define.php";
include_once "NewJoyPla/lib/Func.php";
include_once "NewJoyPla/lib/SpiralDataBase.php";
include_once "NewJoyPla/lib/UserInfo.php";
include_once "NewJoyPla/api/GetCardInfo.php";
include_once "NewJoyPla/api/GetReturnData.php";

$userInfo = new App\Lib\UserInfo($SPIRAL);

$spiralApiCommunicator = $SPIRAL->getSpiralApiCommunicator();
$spiralApiRequest = new SpiralApiRequest();
$spiralDataBase = new App\Lib\SpiralDataBase($SPIRAL,$spiralApiCommunicator,$spiralApiRequest);

$cardInfo = new App\Api\GetCardInfo($spiralDataBase);
$card = $cardInfo->select("NJ_ReturnHDB",$SPIRAL->getCardId(),"returnHistoryID","divisionId");

if($userInfo->getUserPermission() != "1" && $card["data"][0][1] != $userInfo->getDivisionId()){
	App\Lib\viewNotPossible();
	exit;
}

$getReturnData = new App\Api\GetReturnData($spiralDataBase,$userInfo);

$returnItem = $getReturnData->select($card["data"][0][0]);
//$receivingData = $spiralDataBase->arrayToNameArray($receiptItem["data"],array("id","makerName","itemName","itemCode","itemStandard","quantity","quantityUnit","itemUnit","itemJANCode","orderQuantity","receivingCount","orderCNumber","inHospitalItemId"));
$returnItem = $returnItem["data"];

if($userInfo->getUserPermission() == "1"){
	$link = '%url/table:back%';
}else{
	$link = '%url/rel:mpgt:page_266910%';
} 
?>
<!DOCTYPE html>
<html>
  <head>
    <title>JoyPla 返品伝票</title>
	<?php include_once "NewJoyPla/src/Head.php"; ?>

  </head>
  <body>
    <?php include_once "NewJoyPla/src/HeaderForMypage.php"; ?>
    <div class="animsition uk-margin-bottom" uk-height-viewport="expand: true">
	  	<div class="uk-section uk-section-default uk-preserve-color uk-padding-remove uk-margin-top" id="page_top">
		    <div class="uk-container uk-container-expand">
		    	<ul class="uk-breadcrumb no_print">
				    <li><a href="%url/rel:mpg:top%">TOP</a></li>
				    <li><a href="<?php echo $link ?>&table_cache=true"><span>返品一覧</span></a></li>
				    <li><span>返品伝票</span></li>
				</ul>
				<div class="no_print uk-margin" uk-margin>
					<input class="print_hidden uk-button uk-button-default" type="submit" value="印刷プレビュー" onclick="window.print();return false;">
				</div>
		    	<div class="uk-text-center uk-text-large">
		    		<p class="uk-text-bold" style="font-size: 32px">返　品　伝　票</p>
		    	</div>
		    	<div uk-grid>
			    	<div class="uk-width-1-2@m">
		    			<table class="uk-table uk-width-1-1 uk-width-2-3@m uk-table-divider">
		    				<tr class="uk-text-large">
		    					<td colspan="2">
		    						<b>%val:usr:distributorName% 御中</b>
		    					</td>
		    				</tr>
		    				<tr class="uk-text-large">
		    					<td>
		    						返品合計金額
		    					</td>
		    					<td>
		    						￥-<script>price(fixed("%val:usr:returnTotalPrice%"));</script>
		    					</td>
		    				</tr>
		    			</table>
			    	</div>
			    	<div class="uk-width-1-2@m">
			    		<div class="uk-float-right uk-width-2-3@m">
			    			<table class="uk-table uk-width-1-1 uk-table-divider">
			    				<tr>
			    					<td>返品日時</td>
			    					<td>%val:usr:registrationTime%</td>
			    				</tr>
			    				<tr>
			    					<td>返品番号</td>
			    					<td>%val:usr:returnHistoryID%</td>
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
							<div id="return_barcode" class="uk-text-center">
								<span id="return_num">%val:usr:returnHistoryID%</span>
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
										<th>入数</th>
										<th>入庫数</th>
										<th>返品数</th>
										<th>返品金額</th>
				    				</tr>
				    			</thead>
				    			<tbody>
				    				<?php
				    					$num = 1;
										foreach($returnItem as $record){
				    						echo "<tr>";
				    						echo "<td>".$num."</td>";
				    						echo "<td>".$record["makerName"]."</td>";
				    						echo "<td>".$record["itemName"]."</td>";
				    						echo "<td>".$record["itemCode"]."</td>";
				    						echo "<td>".$record["itemStandard"]."</td>";
				    						echo "<td>".$record["quantity"].$record["quantityUnit"]."</td>";
				    						echo "<td>".$record["receivingCount"].$record["itemUnit"]."</td>";
				    						echo "<td>".$record["returnCount"].$record["itemUnit"]."</td>";
				    						echo "<td>￥<script>price(fixed(".$record["returnPrice"]."));</script></td>";
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
		 let return_num = $("#return_num").text();
		 //$("#hacchu_num").remove();
		 $("#return_barcode").html("<svg id='barcode_return'></svg>");
		 generateBarcode("barcode_return",return_num);
		 //$("td#order_barcode div").barcode({code:order_num, crc:false }, "int25",{barWidth: 3 ,barHeight: 40 , output: "css"});
		});
	</script>
  </body>
</html>