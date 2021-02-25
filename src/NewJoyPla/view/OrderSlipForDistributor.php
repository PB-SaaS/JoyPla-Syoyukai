
<?php
include_once 'NewJoyPla/lib/ApiSpiral.php';
include_once "NewJoyPla/lib/Define.php";
include_once 'NewJoyPla/lib/SpiralDataBase.php';
include_once 'NewJoyPla/lib/UserInfo.php';
include_once 'NewJoyPla/api/GetCardInfo.php';
include_once 'NewJoyPla/api/GetOrderItems.php';
include_once 'NewJoyPla/api/GetReceivingItems.php';
include_once 'NewJoyPla/api/UpdateOrder.php';
include_once 'NewJoyPla/lib/Func.php';

$userInfo = new App\Lib\UserInfo($SPIRAL);

$spiralApiCommunicator = $SPIRAL->getSpiralApiCommunicator();
$spiralApiRequest = new SpiralApiRequest();
$spiralDataBase = new App\Lib\SpiralDataBase($SPIRAL,$spiralApiCommunicator,$spiralApiRequest);

$cardInfo = new App\Api\GetCardInfo($spiralDataBase);
$card = $cardInfo->select('orderdataDB',$SPIRAL->getCardId(),'orderNumber','orderStatus','orderAuthKey');

$getOrderItems = new App\Api\GetOrderItems($spiralDataBase);
$orderItems = $getOrderItems->select($card["data"][0][0],'orderNumber','makerName','itemName','itemCode','itemStandard','price','quantity','orderQuantity','orderPrice','itemJANCode','quantityUnit','itemUnit','orderCNumber','inHospitalItemId','dueDate');
$orderItems = $spiralDataBase->arrayToNameArray($orderItems["data"],array('orderNumber','makerName','itemName','itemCode','itemStandard','price','quantity','orderQuantity','orderPrice','itemJANCode','quantityUnit','itemUnit','orderCNumber','inHospitalItemId','dueDate'));

//,'makerName','itemName','itemCode','itemStandard','price','quantity','orderQuantity','orderPrice','itemJANCode'

$ItemsToJs = array();
$Items = array();

foreach($orderItems as $record){
	$Items[] = $record['orderCNumber'];
}

$getReceivingItems = new App\Api\GetReceivingItems($spiralDataBase,$userInfo);
$receivingData = $getReceivingItems->select($Items);
$receivingData = $spiralDataBase->arrayToNameArray($receivingData["data"],array('registrationTime','orderCNumber','receivingCount','receivingHId'));


foreach($orderItems as $key => $record){
	$orderItems[$key]['receivingNowCount'] = 0;
	$orderItems[$key]['receivingFlag'] = false;
	$orderItems[$key]['remainingCount'] = 0;
	foreach($receivingData as $receivingRecord){
		if($record['orderCNumber'] == $receivingRecord['orderCNumber']){
			$orderItems[$key]['receivingNowCount'] += (int)$receivingRecord['receivingCount'];
		}
	}
}

$num = 1;
foreach($orderItems as $key => $record){
	$orderItems[$key]['remainingCount'] = $record['orderQuantity'] - $record['receivingNowCount'];
	if($record['orderQuantity'] <= $record['receivingNowCount']){
		$orderItems[$key]['receivingFlag'] = true;
	}
	$ItemsToJs[$record['inHospitalItemId']] = array(
		'num' => $num,
		'orderCNumber'=> $record['orderCNumber'],
		'orderQuantity'=> $record['orderQuantity'],
        'countNum'=> $record['orderQuantity'],
    	//'changeReceiving'=> '0',
        'receivingFlag'=> $orderItems[$key]['receivingFlag'],
        'receivingNowCount'=> $orderItems[$key]['receivingNowCount'],
        //'receivingBeforeCount'=> $orderItems[$key]['receivingNowCount'],
        'receivingCount' => 0,
        'quantity'=>$orderItems[$key]['quantity'],
        'makerName'=>$orderItems[$key]['makerName'],
        'itemName'=>$orderItems[$key]['itemName'],
        'itemStandard'=>$orderItems[$key]['itemStandard'],
        'itemCode'=>$orderItems[$key]['itemCode'],
        'quantityUnit'=>$orderItems[$key]['quantityUnit'],
        'itemJANCode'=>$orderItems[$key]['itemJANCode'],
        'receivingFlag'=>$orderItems[$key]['receivingFlag'],
        'lotNum' => $orderItems[$key]['receivingNowCount'],
        'price' => $orderItems[$key]['price'],
        'dueDate' => $orderItems[$key]['dueDate']
		);
	$num++;
}


$updateOrder = new App\Api\UpdateOrder($spiralDataBase);
$result = $updateOrder->update($card["data"][0][0],$card['data'][0][2],$orderItems);


$crypt   = $SPIRAL->getSpiralCryptOpenSsl();
$authKeyCrypt = $crypt->encrypt($card['data'][0][2], 'JoyPla');

//var_dump($ItemsToJs);
//var_dump($receivingData);
$orderFixingButton = "";
if($card["data"][0][1] > 4){
	$orderFixingButton = "disabled";
}

$orderFixingDel = "";
if(!($card["data"][0][1] == 3 || $card["data"][0][1] == 4)){
	$orderFixingDel = "disabled";
}
?>
<!DOCTYPE html>
<html>
  <head>
    <title>JoyPla 発注書</title>
	<?php include_once 'NewJoyPla/src/Head.php'; ?>

        <script>
		let canAjax = true;
		function back(){
			if('<?php echo $result["pattern"] ?>' == "delete"){
				UIkit.modal.alert("発注商品が0件となりました。<br>発注書一覧へ戻ります。").then(function(){
					location.href ="%url/table:back%&table_cache=true";
				});
			}
			
			if('<?php echo $card["data"][0][1] ?>' == "1"){
				UIkit.modal.alert("未発注書です。<br>発注書一覧へ戻ります。").then(function(){
					location.href ="%url/table:back%&table_cache=true";
				});
			}
		}
		back();
		
		</script>
  </head>
  <body>
    <?php include_once 'NewJoyPla/src/HeaderForMypage.php'; ?>
    <div class="animsition uk-margin-bottom" uk-height-viewport="expand: true">
	  	<div class="uk-section uk-section-default uk-preserve-color uk-padding-remove uk-margin-top" id="page_top">
		    <div class="uk-container uk-container-expand">
		    	<ul class="uk-breadcrumb no_print">
				    <li><a href="%url/rel:mpg:top%">TOP</a></li>
				    <li><a href="%url/table:back%&table_cache=true"><span>発注書一覧</span></a></li>
				    <li><span>発注書</span></li>
				</ul>
				<form action="#" method="post">
					<div class="uk-child-width-1-2@m no_print uk-margin" uk-grid>
						<div class="uk-text-left">
							<input class="print_hidden uk-button uk-button-default" type="submit" value="印刷プレビュー" onclick="window.print();return false;">
							<input class="print_hidden uk-button uk-button-danger" type="submit" value="受注取消" onclick="orderFixingDel();return false;" <?php echo $orderFixingDel ?>>
							<input class="print_hidden uk-button uk-button-primary" type="submit" value="受注確定" onclick="orderFixing();return false;" <?php echo $orderFixingButton ?>>
						</div>
					</div>
			    	<div class="uk-text-center uk-text-large">
			    		<p class="uk-text-bold" style="font-size: 32px">発　　注　　書</p>
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
			    					<td>合計金額</td>
			    					<td class="uk-text-right">￥<script>price('%val:usr:totalAmount%')</script> - </td>
			    				</tr>
			    			</table>
				    	</div>
				    	<div class="uk-width-1-2@m">
				    		<div class="uk-float-right uk-width-2-3@m">
				    			<table class="uk-table uk-width-1-1 uk-table-divider">
				    				<tr>
				    					<td>発注日時</td>
				    					<td>%val:usr:orderTime%</td>
				    				</tr>
				    				<tr>
				    					<td>発注番号</td>
				    					<td>%val:usr:orderNumber%</td>
				    				</tr>
				    			</table>
			    				<div id="order_barcode" class="uk-text-center">
		    						<span id="hacchu_num">%val:usr:orderNumber%</span>
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
			    	
			    	<div class="" id="tablearea">
				    		<div class="uk-overflow-auto">
					    		<table class="uk-table uk-table-hover uk-table-middle uk-table-divider uk-text-nowrap" id="tbl-Items">
					    			<thead>
					    				<tr>
											<th>NO</th>
											<th style="min-width:60px">メーカー</th>
											<th style="min-width:150px">商品名</th>
											<th>製品コード</th>
											<th>規格</th>
											<th>価格</th>
											<th>入数</th>
											<th>発注数</th>
											<th>入庫数</th>
											<th>金額</th>
											<th>納期</th>
											<th>ステータス</th>
					    				</tr>
					    			</thead>
					    			<tbody>
					    				<?php
					    					$num = 1;
											
					    					foreach($orderItems as $record){
					    						$barcodeId = "01".$record['inHospitalItemId']."X".$record['quantity'];
				    						
					    						echo "<tr id='tr_".$num."'>";
					    						echo "<td>$num</td>";
					    						echo "<td>".$record['makerName']."</td>";
					    						echo "<td>".$record['itemName']."</td>";
					    						echo "<td>".$record['itemCode']."</td>";
					    						echo "<td>".$record['itemStandard']."</td>";
					    						echo "<td>￥".$record['price']."<span class='uk-text-small'></span> / <span class='uk-text-small'>1".$record['itemUnit']."</span></td>";
					    						echo "<td>".$record['quantity']."<span class='uk-text-small'>".$record['quantityUnit']."</span></td>";
					    						echo "<td>".$record['orderQuantity']."<span class='uk-text-small'>".$record['itemUnit']."</span></td>";
					    					
					    						echo "<td>".$record['receivingNowCount']."<span class='uk-text-remove'>".$record['itemUnit']."</span></td>";
					    						
					    						echo "<td>￥<script>price('".$record['orderPrice']."')</script></td>";
					    						echo "<td><input type='date' class='uk-input' style='width: 165px' value='".\App\Lib\changeDateFormat('Y年m月d日' , $record['dueDate'] , 'Y-m-d')."' min='".date("Y-m-d")."' onchange='dueDateChange(\"".$record['inHospitalItemId']."\",this)'></td>";
					    						if($record['receivingFlag']){
					    						echo "<td>入庫済み</td>";
					    						} else if( $record['receivingNowCount'] > 0) {
					    							echo "<td>一部入庫(".$record['receivingNowCount']."/".$record['orderQuantity'].")</td>";
					    						} else {
					    							echo "<td>未入庫</td>";
					    						}
					    						echo "</tr>";
					    						$num++;
					    					}
					    				?>
					    			</tbody>
					    			
					    		</table>
					    	</div>
			    	</div>
			    	<div class="uk-width-3-4 uk-margin">
			    		<table class='uk-table uk-table-middle uk-table-divider'>
			    			<thead>
			    				<tr>
			    					<td colspan="2">備考</td>
			    				</tr>
			    			</thead>
			    			<tbody>
			    				<tr>
			    					<td class="uk-width-1-5">
			    					    %val:usr:divisionName%	
			    					</td>
			    					<td class="uk-width-4-5">
			    						%val:usr:ordercomment:br%
			    					</td>
			    				</tr>
			    			</tbody>
			    		</table>
			    	</div>
			   	</form>
		    </div>
		</div>
	</div>
	<form action="%url/rel:@mpgt:createLabel%" target="_blank" method="post" class="print_hidden uk-inline" id="createLabelForm">
		<input type="hidden" value='' name="itemsData" id="itemsData">
		<input type="hidden" value='%val:usr:distributorName%' name="distributorName">
	</form>
	
    <script>
    	let upToData = <?php echo json_encode($ItemsToJs); ?>;
    	let status = "%val:usr:orderStatus:id%";
    	$(function(){
    		
			/*
			 let count = $('#tbl-Items tbody').children().length;
			 let num ;
			 for(let i = 1 ; i <= count ; i++){
			 	num = $('#barcode_' + i).text();
				$('#barcode_' + i).html('<svg id="barcode_area_'+i+'"></svg>');
				generateBarcode('barcode_area_' + i,num);
			 	//$('td#barcode_' + i +' div').barcode(num, "ean13",{barWidth: 2 ,barHeight: 40 , output: 'css'});
			 }
			 */
			 let order_num = $('#hacchu_num').text();
			 //$('#hacchu_num').remove();
			 $('#order_barcode').html('<svg id="barcode_hacchu"></svg>');
			 generateBarcode('barcode_hacchu',order_num);
			 //$('td#order_barcode div').barcode({code:order_num, crc:false }, 'int25',{barWidth: 3 ,barHeight: 40 , output: 'css'});
			
    	});
		let gs1128_object = {};
		function dueDateChange(inHPitemid,elm){
			upToData[inHPitemid].dueDate = elm.value;
			elm.style="width: 165px;background-color: rgb(255, 204, 153)";
		}
		
		function orderFixing(){
			if(!canAjax) { 
				console.log('通信中');
				return;
			}
			//$_POST['orderHistoryId'],$_POST['divisionId'],$_POST['receiving']
			if(status > 3){
				UIkit.modal.alert("すでに受注済みです");
				return false;
			}

			UIkit.modal.confirm("受注を確定します。<br>よろしいですか").then(function () {
				loading();
				canAjax = false; // これからAjaxを使うので、新たなAjax処理が発生しないようにする
				$.ajax({
					async: false,
					url:'%url/card:page_267209%',
					type:'POST',
					data:{
						orderItems : JSON.stringify(upToData),
					},
					dataType: 'json'
				})
				// Ajaxリクエストが成功した時発動
				.done( (data) => {
					
					if(! data.result){
						UIkit.modal.alert("受注確定に失敗しました").then(function(){
							canAjax = true; // 再びAjaxできるようにする
						});
						return false;
					}
					UIkit.modal.alert("受注確定しました").then(function () {
						location.reload();
					});
				})
				// Ajaxリクエストが失敗した時発動
				.fail( (data) => {
					
					UIkit.modal.alert("受注確定に失敗しました").then(function(){
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
		
		function orderFixingDel(){
			if(!canAjax) { 
				console.log('通信中');
				return;
			}
			//$_POST['orderHistoryId'],$_POST['divisionId'],$_POST['receiving']
			UIkit.modal.confirm("受注を取消します。<br>よろしいですか").then(function () {
				loading();
				canAjax = false; // これからAjaxを使うので、新たなAjax処理が発生しないようにする
				$.ajax({
					async: false,
					url:'%url/card:page_162119%',
					type:'POST',
					data:{
						orderHistoryId : "%val:usr:orderNumber%",
						orderAuthKey : "<?php echo $authKeyCrypt ?>",
						orderItems : JSON.stringify(upToData),
					},
					dataType: 'json'
				})
				// Ajaxリクエストが成功した時発動
				.done( (data) => {
					
					if(! data.result){
						UIkit.modal.alert("受注取消に失敗しました").then(function(){
							canAjax = true; // 再びAjaxできるようにする
						});
						return false;
					}
					UIkit.modal.alert("受注取消しました").then(function(){
						location.reload();
					});
				})
				// Ajaxリクエストが失敗した時発動
				.fail( (data) => {
					UIkit.modal.alert("受注取消に失敗しました").then(function(){
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
    </script>
  </body>
</html>