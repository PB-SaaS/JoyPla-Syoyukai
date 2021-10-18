<?php
include_once 'NewJoyPla/lib/ApiSpiral.php';
include_once "NewJoyPla/lib/Define.php";
include_once "NewJoyPla/lib/Func.php";
include_once "NewJoyPla/lib/SpiralDataBase.php";
include_once "NewJoyPla/lib/UserInfo.php";
include_once "NewJoyPla/api/GetCardInfo.php";
include_once "NewJoyPla/api/GetItemPayout.php";

$userInfo = new App\Lib\UserInfo($SPIRAL);

$spiralApiCommunicator = $SPIRAL->getSpiralApiCommunicator();
$spiralApiRequest = new SpiralApiRequest();
$spiralDataBase = new App\Lib\SpiralDataBase($SPIRAL,$spiralApiCommunicator,$spiralApiRequest);

$cardInfo = new App\Api\GetCardInfo($spiralDataBase);
$card = $cardInfo->select("NJ_PayoutHDB",$SPIRAL->getCardId(),"payoutHistoryId","payoutAuthKey","sourceDivisionId");

if($userInfo->getUserPermission() != "1" && $card["data"][0][2] != $userInfo->getDivisionId()){
	App\Lib\viewNotPossible();
	exit;
}

$getItemPayout = new App\Api\GetItemPayout($spiralDataBase,$userInfo);
$payoutData = $getItemPayout->getItemPayout($card["data"][0][0]);

$payoutData = $spiralDataBase->arrayToNameArray($payoutData["data"],array("registrationTime","payoutHistoryId","price","itemId","itemName","itemCode","itemStandard","itemJANCode","quantityUnit","payoutQuantity","payoutAmount","itemUnit","quantity","makerName","inHospitalItemId","payoutCount","payoutLabelCount","distributorId","catalogNo","labelId","lotNumber","lotDate","unitPrice"));

foreach($payoutData as $record){
	$ItemsToJs[] = array(
		'inHospitalItemId' => $record['inHospitalItemId'],
		'payoutQuantity' => $record['payoutQuantity'],
		'lotNumber' => $record['lotNumber'],
		'lotDate' => $record['lotDate']
		);
}

$crypt   = $SPIRAL->getSpiralCryptOpenSsl();
$payoutAuthKey = $crypt->encrypt($card["data"][0][1], "JoyPla");

if($userInfo->getUserPermission() == "1"){
	$link = '%url/table:back%';
}else{
	$link = '%url/rel:mpgt:page_267442%';
} 
?>
<!DOCTYPE html>
<html>
  <head>
    <title>JoyPla 払出伝票</title>
	<?php include_once "NewJoyPla/src/Head.php"; ?>

  </head>
  <body>
    <?php include_once "NewJoyPla/src/HeaderForMypage.php"; ?>
    <div class="animsition uk-margin-bottom" uk-height-viewport="expand: true">
	  	<div class="uk-section uk-section-default uk-preserve-color uk-padding-remove uk-margin-top" id="page_top">
		    <div class="uk-container uk-container-expand">
		    	<ul class="uk-breadcrumb no_print">
				    <li><a href="%url/rel:mpg:top%">TOP</a></li>
				    <li><a href="<?php echo $link ?>&table_cache=true"><span>払出履歴一覧</span></a></li>
				    <li><span>払出伝票</span></li>
				</ul>
				<div class="no_print uk-margin" uk-margin>
					<input class="print_hidden uk-button uk-button-default" type="button" value="印刷プレビュー" onclick="window.print();return false;">
					<input class="print_hidden uk-button uk-button-danger" type="button" value="払出伝票取消" onclick="payoutDelete();return false;">
					<input class="print_hidden uk-button uk-button-primary" type="button" value="払出ラベル発行" onclick="createLabel();return false;">
				</div>
		    	<div class="uk-text-center uk-text-large">
		    		<p class="uk-text-bold" style="font-size: 32px">払　出　伝　票</p>
		    	</div>
		    	<div uk-grid>
			    	<div class="uk-width-1-2@m">
		    			<table class="uk-table uk-width-1-1 uk-width-2-3@m uk-table-divider">
		    				<tr>
		    					<td class="uk-text-bold">払出日時</td>
		    					<td class="uk-text-right">%val:usr:registrationTime%</td>
		    				</tr>
		    				<tr>
		    					<td class="uk-text-bold">払出部署</td>
		    					<td class="uk-text-right">%val:usr:sourceDivision%<span uk-icon="icon: arrow-right"></span>%val:usr:targetDivision%</td>
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
										<th>価格</th>
										<th>単価</th>
										<th class="uk-table-shrink">数量</th>
										<th class="uk-table-shrink"></th>
										<th class="uk-table-shrink">個数（ラベル枚数）</th>
										<th class="uk-table-shrink">合計払出数</th>
										<th>払出金額</th>
				    				</tr>
				    			</thead>
				    			<tbody>
				    				<?php
				    					$num = 1;
										foreach($payoutData as $record){
				    						echo "<tr>";
				    						echo "<td>".$num."</td>";
				    						echo "<td>".$record["makerName"]."</td>";
				    						echo "<td>".$record["itemName"]."</td>";
				    						echo "<td>".$record["itemCode"]."</td>";
				    						echo "<td>".$record["itemStandard"]."</td>";
				    						echo "<td>".$record["lotNumber"]."</td>";
				    						echo "<td>".$record["lotDate"]."</td>";
				    						echo "<td>".$record["quantity"].$record["quantityUnit"]."</td>";
				    						echo "<td><script>price(\"".($record["price"] / $record["quantity"] )."\")</script>円 / ".$record["quantityUnit"]."</td>";
				    						echo "<td>￥<script>price(\"".$record["unitPrice"]."\")</script></td>";
											echo "<td>".$record["payoutCount"].$record["quantityUnit"]."</td>";
											
											echo "<td>×</td>";
											echo "<td>".$record["payoutLabelCount"]."枚</td>";

				    						echo "<td>".$record["payoutQuantity"].$record["quantityUnit"]."</td>";
				    						echo "<td>￥<script>price(\"".$record["payoutAmount"]."\")</script></td>";
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
	<form action="%url/rel:@mpgt:Payout%&Action=payoutLabel" target="_blank" method="post" class="uk-hidden" name="LabelCreate">
		<input type="hidden" value="%val:usr:payoutHistoryId%" name="payoutHistoryId" id="payoutHistoryId">
		<input type="hidden" value="payout" name="pattern">
	</form>
	<script>
		let canAjax = true;
		let itemsToJs = objectValueToURIencode( <?php echo json_encode($ItemsToJs); ?> );
		let lotdata = {};

		function payoutDelete(){
			if(!canAjax) { 
				console.log('通信中');
				return;
			}
			
			let flg = true;
			
			UIkit.modal.confirm("払出情報を削除します。<br>よろしいですか<br>注意:払出元へ在庫数を戻します。").then(function () {
				loading();
				canAjax = false; // これからAjaxを使うので、新たなAjax処理が発生しないようにする
				$.ajax({
					async: false,
	                url:"%url/card:page_267245%",
	                type:"POST",
	                data:{
	                	sourceDivisionId:"%val:usr:sourceDivisionId%",
	                	targetDivisionId:"%val:usr:targetDivisionId%",
	                	payoutData : JSON.stringify( itemsToJs ),
	                	payoutAuthKey: "<?php echo $payoutAuthKey ?>",
	                	payoutHistoryId: "%val:usr:payoutHistoryId%",
	                },
	                dataType: "json"
	            })
	            // Ajaxリクエストが成功した時発動
	            .done( (data) => {
	            	
	                if(! data.result){
	            		UIkit.modal.alert("払出取消に失敗しました").then(function(){
							canAjax = true; // 再びAjaxできるようにする
						});
	            		return false;
	                }
	                UIkit.modal.alert("払出取消が完了しました").then(function(){
						location.href ="<?php echo $link ?>&table_cache=true";
					});
					
	            })
	            // Ajaxリクエストが失敗した時発動
	            .fail( (data) => {
	            	
	        		UIkit.modal.alert("払出取消に失敗しました").then(function(){
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
		
		function createLabel(){
			UIkit.modal.confirm("ラベル発行を行います").then(function(){
				$('form[name=LabelCreate]').submit();
			});
			return true;
		}
	</script>
  </body>
</html>