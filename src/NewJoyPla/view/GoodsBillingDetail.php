<?php
include_once 'NewJoyPla/lib/ApiSpiral.php';
include_once "NewJoyPla/lib/Define.php";
include_once 'NewJoyPla/lib/SpiralDataBase.php';
include_once 'NewJoyPla/lib/UserInfo.php';
include_once 'NewJoyPla/lib/Func.php';
include_once 'NewJoyPla/api/UpdateUnordered.php';
include_once 'NewJoyPla/api/GetCardInfo.php';
include_once 'NewJoyPla/api/GetItemBilling.php';


$userInfo = new App\Lib\UserInfo($SPIRAL);

$spiralApiCommunicator = $SPIRAL->getSpiralApiCommunicator();
$spiralApiRequest = new SpiralApiRequest();
$spiralDataBase = new App\Lib\SpiralDataBase($SPIRAL,$spiralApiCommunicator,$spiralApiRequest);

$cardInfo = new App\Api\GetCardInfo($spiralDataBase);
$card = $cardInfo->select('NJ_BillingHDB',$SPIRAL->getCardId(),'billingNumber','billingAuthKey',"divisionId");

if($userInfo->getUserPermission() != "1" && $card["data"][0][2] != $userInfo->getDivisionId()){
	App\Lib\viewNotPossible();
	exit;
}

$getItemBilling = new App\Api\GetItemBilling($spiralDataBase,$userInfo);
$billingData = $getItemBilling->getItemBilling($card['data'][0][0]);

$billingData = $spiralDataBase->arrayToNameArray($billingData['data'],array('registrationTime','updateTime','inHospitalItemId','billingNumber','price','billingQuantity','billingAmount','hospitalId','divisionId','itemId','itemName','itemCode','itemStandard','itemJANCode','quantityUnit','makerName','itemUnit','quantity'));

foreach($billingData as $record){
	$ItemsToJs[$record['inHospitalItemId']] = array(
		'billingQuantity'=> $record['billingQuantity']
		);
}

$crypt   = $SPIRAL->getSpiralCryptOpenSsl();
$billingAuthKey = $crypt->encrypt($card['data'][0][1], 'JoyPla');


if($userInfo->getUserPermission() == "1"){
	$link = '%url/table:back%';
}else{
	$link = '%url/rel:mpgt:page_266881%';
} 
?>
<!DOCTYPE html>
<html>
  <head>
    <title>JoyPla 消費物品</title>
	<?php include_once 'NewJoyPla/src/Head.php'; ?>

  </head>
  <body>
    <?php include_once 'NewJoyPla/src/HeaderForMypage.php'; ?>
    <div class="animsition uk-margin-bottom" uk-height-viewport="expand: true">
	  	<div class="uk-section uk-section-default uk-preserve-color uk-padding-remove uk-margin-top" id="page_top">
		    <div class="uk-container uk-container-expand">
		    	<ul class="uk-breadcrumb no_print">
				    <li><a href="%url/rel:mpg:top%">TOP</a></li>
				    <li><a href="<?php echo $link ?>&table_cache=true"><span>消費一覧</span></a></li>
				    <li><span>消費物品</span></li>
				</ul>
				<div class="no_print uk-margin">
					<input class="print_hidden uk-button uk-button-default" type="submit" value="印刷プレビュー" onclick="window.print();return false;">
					<input class="print_hidden uk-button uk-button-danger" type="submit" value="消費取消" onclick="billingDelete();return false;">
				</div>
		    	<div class="uk-text-center uk-text-large">
		    		<p class="uk-text-bold" style="font-size: 32px">消　費　物　品</p>
		    	</div>
		    	<div uk-grid>
			    	<div class="uk-width-1-2">
		    			<table class="uk-table uk-width-1-1 uk-width-2-3@m uk-table-divider uk-text-nowrap">
		    				<tr>
		    					<td class="uk-text-bold">消費日時</td>
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
		    					<td class="uk-text-right">￥<script>price('%val:usr:totalAmount%')</script> - </td>
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
										<th>入数</th>
										<th>価格</th>
										<th>消費数</th>
										<th>金額</th>
				    				</tr>
				    			</thead>
				    			<tbody>
				    				<?php
				    					$num = 1;
				    					//'orderNumber','makerName','itemName','itemCode','itemStandard','price','quantity','orderQuantity','orderPrice','itemJANCode'
				    					foreach($billingData as $record){
				    						echo '<tr>';
				    						echo '<td>'.$num.'</td>';
				    						echo '<td>'.$record['makerName'].'</td>';
				    						echo '<td>'.$record['itemName'].'</td>';
				    						echo '<td>'.$record['itemCode'].'</td>';
				    						echo '<td>'.$record['itemStandard'].'</td>';
				    						echo '<td>'.$record['quantity'].$record['quantityUnit'].'</td>';
				    						echo '<td>￥<script>price(fixed("'.$record['price'].'"))</script> / '.$record['itemUnit'].'</td>';
				    						echo '<td>'.$record['billingQuantity'].$record['quantityUnit'].'</td>';
				    						echo '<td>￥<script>price(fixed("'.$record['billingAmount'].'"))</script></td>';
				    						echo '</tr>';
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
		function billingDelete(){
			if(!canAjax) { 
				console.log('通信中');
				return;
			}
			UIkit.modal.confirm('消費情報を取消します。<br>よろしいですか<br>注意:在庫数を戻します。').then(function () {
				
				loading();
				canAjax = false; // これからAjaxを使うので、新たなAjax処理が発生しないようにする
				$.ajax({
					async: false,
	                url:'%url/card:page_267234%',
	                type:'POST',
	                data:{
	                	divisionId:"%val:usr:divisionId%",
	                	billingData : JSON.stringify( itemsToJs ),
	                	billingAuthKey: "<?php echo $billingAuthKey ?>",
	                	billingNumber: "%val:usr:billingNumber%",
	                },
	                dataType: 'json'
	            })
	            // Ajaxリクエストが成功した時発動
	            .done( (data) => {
	                if(! data.result){
	            		UIkit.modal.alert("消費取消に失敗しました").then(function(){
							canAjax = true; // 再びAjaxできるようにする
						});
	            		return false;
	                }
	                UIkit.modal.alert("消費取消が完了しました").then(function () {
						canAjax = true; // 再びAjaxできるようにする
						location.href ="<?php echo $link ?>&table_cache=true";
					});
	            })
	            // Ajaxリクエストが失敗した時発動
	            .fail( (data) => {
	        		UIkit.modal.alert("消費取消に失敗しました").then(function(){
						canAjax = true; // 再びAjaxできるようにする
					});
	        		return false;
	            })
	            // Ajaxリクエストが成功・失敗どちらでも発動
	            .always( (data) => {
	            	loading_remove();
	            });
			}, function () {
				UIkit.modal.alert("中止しました");
			});
		}
		
	</script>
  </body>
</html>