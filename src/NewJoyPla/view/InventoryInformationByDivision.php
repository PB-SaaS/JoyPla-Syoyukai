
<?php
include_once 'NewJoyPla/lib/ApiSpiral.php';
include_once "NewJoyPla/lib/Define.php";
include_once "NewJoyPla/lib/SpiralDataBase.php";
include_once "NewJoyPla/lib/UserInfo.php";
include_once "NewJoyPla/api/GetCardInfo.php";
include_once "NewJoyPla/api/GetItemPayout.php";
include_once "NewJoyPla/api/RegInventoryEndHistory.php";

$session   = $SPIRAL->getSession();
$session->put("cardUrl", $_SERVER["REQUEST_URI"]);

$userInfo = new App\Lib\UserInfo($SPIRAL);

$spiralApiCommunicator = $SPIRAL->getSpiralApiCommunicator();
$spiralApiRequest = new SpiralApiRequest();
$spiralDataBase = new App\Lib\SpiralDataBase($SPIRAL,$spiralApiCommunicator,$spiralApiRequest);

$cardInfo = new App\Api\GetCardInfo($spiralDataBase);

$card = $cardInfo->select("NJ_InventoryEDB",$SPIRAL->getCardId(),"inventoryEndId","invEndAuthKey","inventoryStatus");
/*
$getItemPayout = new App\Api\GetItemPayout($spiralDataBase,$userInfo);
$payoutData = $getItemPayout->getItemPayout($card["data"][0][0]);

$payoutData = $spiralDataBase->arrayToNameArray($payoutData["data"],array("registrationTime","payoutHistoryId","price","itemId","itemName","itemCode","itemStandard","itemJANCode","quantityUnit","payoutQuantity","payoutAmount","deadlineMinimum","deadlineMax","itemUnit","quantity","makerName"));

foreach($payoutData as $record){
	$ItemsToJs[$record["inHospitalItemId"]] = array(
		"payoutQuantity"=> $record["payoutQuantity"]
		);
}
*/
$crypt   = $SPIRAL->getSpiralCryptOpenSsl();
$authKeyCrypt = $crypt->encrypt($card["data"][0][1], "JoyPla");
$class = "uk-label-warning" ;
$disabled = "";
if($card["data"][0][2] == 2){
	$class = "uk-label-success";
	$disabled = "disabled";
	$linkDisp = "uk-hidden";
} else {

	$regInventoryEndHistory = new App\Api\RegInventoryEndHistory($spiralDataBase,$userInfo);
	$result = $regInventoryEndHistory->updateHistory($card["data"][0][0]);
	
	if(!$result){
		echo json_encode(array("result"=>$result));
		exit;
	}

}
?>
<!DOCTYPE html>
<html>
  <head>
    <title>JoyPla 棚卸伝票</title>
	<?php include_once "NewJoyPla/src/Head.php"; ?>
  </head>
  <body>
    <?php include_once "NewJoyPla/src/HeaderForMypage.php"; ?>
    <div class="animsition uk-margin-bottom" uk-height-viewport="expand: true">
	  	<div class="uk-section uk-section-default uk-preserve-color uk-padding-remove uk-margin-top" id="page_top">
		    <div class="uk-container uk-container-expand">
		    	<ul class="uk-breadcrumb no_print">
				    <li><a href="%url/rel:mpg:top%">TOP</a></li>
				    <li><a href="%url/table:back%&table_cache=true"><span>棚卸履歴一覧</span></a></li>
				    <li><span>棚卸結果報告</span></li>
				</ul>
				<div class="no_print uk-margin" uk-margin>
					<input class="print_hidden uk-button uk-button-default" type="submit" value="印刷プレビュー" onclick="window.print();return false;">
					<?php if($card["data"][0][2] != 2 && $userInfo->getUserPermission() == "1"): ?>
					<input class="print_hidden uk-button uk-button-danger" type="submit" value="棚卸情報削除" onclick="inventoryDelete();return false;">
					<input class="print_hidden uk-button uk-button-primary" type="submit" value="棚卸終了" onclick="inventoryFinish();return false;">
					<?php endif ?>
				</div>
				<div class="no_print">
					<span class="uk-label uk-padding-small <?php echo $class ?> uk-padding-remove-vertical uk-text-large">%val:usr:inventoryStatus%</span>
				</div>
		    	<div class="uk-text-left uk-text-large">
		    		<p class="uk-text-bold" style="font-size: 32px">棚卸結果報告</p>
		    	<hr>
		    	</div>
			    <div class="uk-margin <?php echo $linkDisp ?> uk-hidden">
			    	<a href="#">計算上在庫が存在する商品を確認する</a>
		        </div>
		    	
		    	<div uk-grid>
			    	<div class="uk-width-1-2@m">
		    			<table class="uk-table uk-width-1-1 uk-width-2-3@m uk-table-divider">
		    				<tr>
		    					<td class="uk-text-bold">棚卸終了日時</td>
		    					<td class="uk-text-right">%val:usr:inventoryTime%</td>
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
		    	
		    	<div class="uk-margin" id="tablearea">
%sf:usr:search13:mstfilter:table%
		    	</div>
		    	
		    </div>
		</div>
	</div>
	<script>
	let canAjax = true;
	<?php 
	if($card["data"][0][2] == 2){
		echo "$('th.no_fix,td.no_fix').remove();";
	} else {
		echo "$('th.fix,td.fix').remove();";
	}
	?>
		//let itemsToJs = objectValueToURIencode( <?php echo json_encode($ItemsToJs); ?> );
		function inventoryDelete(){
			if(!canAjax) { 
				console.log('通信中');
				return;
			}
			UIkit.modal.confirm("伝票を取消します。<br>よろしいですか").then(function () {
				loading();
				
				canAjax = false; // これからAjaxを使うので、新たなAjax処理が発生しないようにする
				$.ajax({
					async: false,
					url:"%url/card:page_267247%",
					type:"POST",
					data:{
						inventoryEndId : "%val:usr:inventoryEndId%",
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
						location.href ="%url/table:back%&table_cache=true";
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
		
		function inventoryFinish(){
			if(!canAjax) { 
				console.log('通信中');
				return;
			}
			loading();
			canAjax = false; // これからAjaxを使うので、新たなAjax処理が発生しないようにする
			$.ajax({
				async: false,
                url:"%url/card:page_267244%",
                type:"POST",
                data:{
                	inventoryEndHistoryId : "%val:usr:inventoryEndId%",
                	inventoryEAuthkey : "<?php echo $authKeyCrypt ?>",
                },
                dataType: "json"
            })
            // Ajaxリクエストが成功した時発動
            .done( (data) => {
                if(! data.result){
            		UIkit.modal.alert("棚卸終了に失敗しました").then(function(){
						canAjax = true; // 再びAjaxできるようにする
					});
            		return false;
                }
                UIkit.modal.alert("棚卸を終了しました").then(function(){
					location.href ="%url/table:back%&table_cache=true";
				});
				
            })
            // Ajaxリクエストが失敗した時発動
            .fail( (data) => {
            	
        		UIkit.modal.alert("棚卸終了に失敗しました").then(function(){
					canAjax = true; // 再びAjaxできるようにする
				});
        		return false;
            })
            // Ajaxリクエストが成功・失敗どちらでも発動
            .always( (data) => {
            	loading_remove();
            });
		}
	</script>
  </body>
</html>