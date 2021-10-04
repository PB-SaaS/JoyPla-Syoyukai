
<?php
include_once 'NewJoyPla/lib/ApiSpiral.php';
include_once "NewJoyPla/lib/Define.php";
include_once "NewJoyPla/lib/Func.php";
include_once "NewJoyPla/lib/SpiralDataBase.php";
include_once "NewJoyPla/lib/UserInfo.php";
include_once "NewJoyPla/api/GetCardInfo.php";
include_once "NewJoyPla/api/RegInventoryHistory.php";
include_once "NewJoyPla/api/RegInventoryEndHistory.php";
include_once "NewJoyPla/api/GetItemInventory.php";
include_once "NewJoyPla/api/GetStock.php";

$userInfo = new App\Lib\UserInfo($SPIRAL);

$spiralApiCommunicator = $SPIRAL->getSpiralApiCommunicator();
$spiralApiRequest = new SpiralApiRequest();
$spiralDataBase = new App\Lib\SpiralDataBase($SPIRAL,$spiralApiCommunicator,$spiralApiRequest);

$cardInfo = new App\Api\GetCardInfo($spiralDataBase);


$session = $SPIRAL->getSession();
$cardUrl = $session->get("cardUrl");

$card = $cardInfo->select("inventoryByDiv",$SPIRAL->getCardId(),"inventoryHId","inventoryEndId","invHAuthKey","divisionId","itemsNumber","totalAmount");

$InventoryHistoryId = $card["data"][0][0];
$inventoryEndHistoryId = $card["data"][0][1];

$crypt   = $SPIRAL->getSpiralCryptOpenSsl();
$authKeyCrypt = $crypt->encrypt($card["data"][0][2], "JoyPla");


$itemsNumber = $card["data"][0][4];
$totalAmount = $card["data"][0][5];

$regInventoryHistory = new App\Api\RegInventoryHistory($spiralDataBase,$userInfo);

$result = $regInventoryHistory->updateHistory($InventoryHistoryId,$itemsNumber,$totalAmount);

if(!$result){
	echo json_encode(array("result"=>$result));
	exit;
}

$regInventoryEndHistory = new App\Api\RegInventoryEndHistory($spiralDataBase,$userInfo);
$result = $regInventoryEndHistory->updateHistory($inventoryEndHistoryId);


if(!$result){
	echo json_encode(array("result"=>$result));
	exit;
}


$getItemInventory = new App\Api\GetItemInventory($spiralDataBase,$userInfo);
$inventoryData = $getItemInventory->getItemInventory($InventoryHistoryId);
if($inventoryData['code'] != '0'){
	echo json_encode(array('result'=>$result));
	exit;
}

$inventoryData = $spiralDataBase->arrayToNameArray($inventoryData["data"],array('registrationTime','inHospitalItemId','distributorName','makerName','itemName','itemCode','itemStandard','quantityUnit','price','inventryNum','inventryAmount','unitPrice'));

$inventoryItemData = [];
foreach ($inventoryData as $data) {
	if (array_key_exists($data['inHospitalItemId'], $inventoryItemData)) {
		$inventoryItemData[$data['inHospitalItemId']]['inventryNum'] += (int)$data['inventryNum'];
		$inventoryItemData[$data['inHospitalItemId']]['inventryAmount'] += (int)$data['inventryAmount'];
	} else {
		$inventoryItemData[$data['inHospitalItemId']] = $data;
	}
}

$getStock = new App\Api\GetStock($spiralDataBase,$userInfo);
$stockData = $getStock->getStockData($inventoryItemData,$card["data"][0][3]);
if($stockData['code'] != '0'){
	echo json_encode(array('result'=>$result));
	exit;
}

foreach ($inventoryItemData as $key => $val) {
	foreach ($stockData['data'] as $stock) {
		if ($key == $stock[0]) {
			$inventoryItemData[$key]['stockQuantity'] = $stock[1];
		}
	}
}

ksort($inventoryItemData);
?>
<!DOCTYPE html>
<html>
  <head>
    <title>JoyPla 棚卸伝票</title>
	<?php include_once "NewJoyPla/src/Head.php"; ?>

        <script>
		let canAjax = true;
		<?php if($userInfo->getUserPermission() == "1" || $userInfo->getDivisionId() == $card["data"][0][3]): ?>
		function deleteInvForDiv(){
			if(!canAjax) { 
				console.log('通信中');
				return;
			}
			UIkit.modal.confirm("伝票を取消します。<br>よろしいですか").then(function () {
				loading();
				canAjax = false; // これからAjaxを使うので、新たなAjax処理が発生しないようにする
				$.ajax({
					async: false,
					url:"%url/card:page_267254%",
					type:"POST",
					data:{
						inventoryHId : "%val:usr:inventoryHId%",
						authKey : "<?php echo $authKeyCrypt ?>",
					},
					dataType: "json"
				})
				// Ajaxリクエストが成功した時発動
				.done( (data) => {
					
					if(! data.result){
						UIkit.modal.alert("取消に失敗しました").then(function(){
							canAjax = true; // 再びAjaxできるようにする
						});
						return false;
					}
					UIkit.modal.alert("取消しました").then(function(){
						location.href ="<?php echo $cardUrl ?>";
					});
					
				})
				// Ajaxリクエストが失敗した時発動
				.fail( (data) => {
					
					UIkit.modal.alert("取消に失敗しました").then(function(){
						canAjax = true; // 再びAjaxできるようにする
					});
					return false;
				})
				// Ajaxリクエストが成功・失敗どちらでも発動
				.always( (data) => {
					loading_remove();
				});
			}, function () {
				UIkit.modal.alert("中止します");
			});
		}
		<?php endif ?>
		
		</script>
  </head>
  <body>
    <?php include_once "NewJoyPla/src/HeaderForMypage.php"; ?>
    <div class="animsition uk-margin-bottom" uk-height-viewport="expand: true">
	  	<div class="uk-section uk-section-default uk-preserve-color uk-padding-remove uk-margin-top" id="page_top">
		    <div class="uk-container uk-container-expand">
		    	<ul class="uk-breadcrumb no_print">
				    <li><a href="%url/rel:mpg:top%">TOP</a></li>
				    <li><a href="%url/rel:mpgt:page_263631%&table_cache=true"><span>棚卸履歴一覧</span></a></li>
				    <li><a href="<?php echo $cardUrl ?>"><span>棚卸結果報告</span></a></li>
				    <li><span>%val:usr:divisionName% 棚卸結果報告</span></li>
				</ul>
				<div class="no_print uk-margin" uk-margin>
					<input class="print_hidden uk-button uk-button-default" type="submit" value="印刷プレビュー" onclick="window.print();return false;">
					
			    	<?php if($userInfo->getUserPermission() == "1" || $userInfo->getDivisionId() == $card["data"][0][3]): ?>
					<input class="print_hidden uk-button uk-button-danger" type="submit" value="%val:usr:divisionName% 棚卸取消" onclick="deleteInvForDiv();return false;">
			    	<?php endif ?>
				</div>
		    	<div class="uk-text-left uk-text-large">
		    		<p class="uk-text-bold" style="font-size: 32px">%val:usr:divisionName% 棚卸結果報告</p>
		    	<hr>
		    	</div>
		        
			    <div class="uk-margin">
			    	<!-- <a href="#">計算上在庫が存在する商品を確認する</a> -->
		        </div>
		    	
		    	<div uk-grid>
			    	<div class="uk-width-1-2@m">
		    			<table class="uk-table uk-width-1-1 uk-width-2-3@m uk-table-divider">
		    				<tr>
		    					<td class="uk-text-bold">棚卸登録日時</td>
		    					<td class="uk-text-right">%val:usr:registrationTime%</td>
		    				</tr>
		    				<tr>
		    					<td class="uk-text-bold">棚卸更新日時</td>
		    					<td class="uk-text-right">%val:usr:updateTime%</td>
		    				</tr>
		    				<tr>
		    					<td class="uk-text-bold">部署名</td>
		    					<td class="uk-text-right">%val:usr:divisionName%</td>
		    				</tr>
		    				<tr>
		    					<td class="uk-text-bold">品目数</td>
		    					<td class="uk-text-right">%val:usr:itemsNumber%</td>
		    				</tr>
		    				<tr>
		    					<td class="uk-text-bold">合計金額</td>
		    					<td class="uk-text-right">￥<script>price("%val:usr:totalAmount%")</script> - </td>
		    				</tr>
		    			</table>
			    	</div>
			    </div>

		    	<div class="uk-margin" style="margin-bottom: 50px;">
		    		<h3>棚卸総数</h3>
			  		<div class="uk-margin">
			  			<div class="uk-overflow-auto">
			  				<table class="uk-table uk-table-hover uk-table-middle uk-table-divider uk-text-nowrap">
			  					<thead>
			  						<tr>
			  							<th>NO</th>
			  							<th>卸業者</th>
			  							<th>メーカー名</th>
			  							<th>商品名</th>
			  							<th>商品コード</th>
			  							<th>規格</th>
			  							<th>購買価格</th>
			  							<th>単価</th>
			  							<th>計算上在庫</th>
			  							<th>棚卸数量</th>
			  							<th>棚卸金額</th>
			  							<th>数量差分</th>
			  						</tr>
			  					</thead>
			  					<tbody>
				    				<?php
				    					$num = 1;
											foreach ($inventoryItemData as $record) {
				    						echo "<tr>";
				    						echo "<td>".$num."</td>";
				    						echo "<td>".$record["distributorName"]."</td>";
				    						echo "<td>".$record["makerName"]."</td>";
				    						echo "<td>".$record["itemName"]."</td>";
				    						echo "<td>".$record["itemCode"]."</td>";
				    						echo "<td>".$record["itemStandard"]."</td>";
				    						echo "<td>￥<script>price(\"".$record["price"]."\")</script></td>";
				    						echo "<td>￥<script>price(\"".$record["unitPrice"]."\")</script></td>";
				    						echo "<td>".$record["stockQuantity"]."<span class='uk-text-small'>".$record["quantityUnit"]."</span></td>";
				    						echo "<td>".$record["inventryNum"]."<span class='uk-text-small'>".$record["quantityUnit"]."</span></td>";
				    						echo "<td>￥<script>price(\"".$record["inventryAmount"]."\")</script></td>";
				    						echo "<td>".((int)$record["stockQuantity"] - (int)$record["inventryNum"])."<span class='uk-text-small'>".$record["quantityUnit"]."</span></td>";
				    						echo "</tr>";
				    						$num++;
											}
				    				?>
			  					</tbody>
			  				</table>
			  			</div>
			  		</div>
		    	</div>

			    <div class="uk-margin" id="tablearea">
			    <?php if($userInfo->getUserPermission() == "1" || $userInfo->getDivisionId() == $card["data"][0][3]): ?>
			      %sf:usr:search24:mstfilter:table%
        		<?php else: ?>
              %sf:usr:search25:mstfilter:table%
	        	<?php endif ?>
		        </div>
		    	
		    </div>
		</div>
	</div>
  </body>
</html>