
<?php
include_once 'NewJoyPla/lib/ApiSpiral.php';
include_once "NewJoyPla/lib/Define.php";
include_once "NewJoyPla/lib/Func.php";
include_once "NewJoyPla/lib/SpiralDataBase.php";
include_once "NewJoyPla/lib/UserInfo.php";
include_once "NewJoyPla/api/GetCardInfo.php";
include_once "NewJoyPla/api/RegInventoryHistory.php";
include_once "NewJoyPla/api/RegInventoryEndHistory.php";

$userInfo = new App\Lib\UserInfo($SPIRAL);

$spiralApiCommunicator = $SPIRAL->getSpiralApiCommunicator();
$spiralApiRequest = new SpiralApiRequest();
$spiralDataBase = new App\Lib\SpiralDataBase($SPIRAL,$spiralApiCommunicator,$spiralApiRequest);

$cardInfo = new App\Api\GetCardInfo($spiralDataBase);


$session = $SPIRAL->getSession();
$cardUrl = $session->get("cardUrl");

$card = $cardInfo->select("inventoryByDiv",$SPIRAL->getCardId(),"inventoryHId","inventoryEndId","invHAuthKey","divisionId");

$InventoryHistoryId = $card["data"][0][0];
$inventoryEndHistoryId = $card["data"][0][1];

$crypt   = $SPIRAL->getSpiralCryptOpenSsl();
$authKeyCrypt = $crypt->encrypt($card["data"][0][2], "JoyPla");

$regInventoryHistory = new App\Api\RegInventoryHistory($spiralDataBase,$userInfo);

$result = $regInventoryHistory->updateHistory($InventoryHistoryId);

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
			    <div class="uk-margin" id="tablearea">
			    <?php if($userInfo->getUserPermission() == "1" || $userInfo->getDivisionId() == $card["data"][0][3]): ?>
			    　　%sf:usr:search15:mstfilter:table%
        		<?php else: ?>
                    %sf:usr:search17:mstfilter:table%
	        	<?php endif ?>
		        </div>
		    	
		    </div>
		</div>
	</div>
  </body>
</html>