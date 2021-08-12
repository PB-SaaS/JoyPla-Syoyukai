<?php
include_once 'NewJoyPla/lib/ApiSpiral.php';
include_once "NewJoyPla/lib/Define.php";
include_once "NewJoyPla/lib/SpiralDataBase.php";
include_once "NewJoyPla/lib/UserInfo.php";
include_once "NewJoyPla/api/GetCardInfo.php";
include_once "NewJoyPla/api/GetOrderItems.php";
include_once "NewJoyPla/api/GetReceivingItems.php";
include_once "NewJoyPla/api/UpdateOrder.php";
include_once "NewJoyPla/lib/Func.php";

$userInfo = new App\Lib\UserInfo($SPIRAL);

$spiralApiCommunicator = $SPIRAL->getSpiralApiCommunicator();
$spiralApiRequest = new SpiralApiRequest();
$spiralDataBase = new App\Lib\SpiralDataBase($SPIRAL,$spiralApiCommunicator,$spiralApiRequest);

$cardInfo = new App\Api\GetCardInfo($spiralDataBase);
$card = $cardInfo->select("orderdataDB",$SPIRAL->getCardId(),"orderNumber","orderStatus","orderAuthKey","divisionId");

if($userInfo->getUserPermission() != "1" && $card["data"][0][3] != $userInfo->getDivisionId()){
	App\Lib\viewNotPossible();
	exit;
}
$getOrderItems = new App\Api\GetOrderItems($spiralDataBase);
$orderItems = $getOrderItems->select($card["data"][0][0],"orderNumber","makerName","itemName","itemCode","itemStandard","price","quantity","orderQuantity","orderPrice","itemJANCode","quantityUnit","itemUnit","orderCNumber","inHospitalItemId","dueDate","labelId","receivingNum");
$orderItems = $spiralDataBase->arrayToNameArray($orderItems["data"],array("orderNumber","makerName","itemName","itemCode","itemStandard","price","quantity","orderQuantity","orderPrice","itemJANCode","quantityUnit","itemUnit","orderCNumber","inHospitalItemId","dueDate","labelId","receivingNum"));

//,"makerName","itemName","itemCode","itemStandard","price","quantity","orderQuantity","orderPrice","itemJANCode"

$ItemsToJs = array();
$Items = array();

foreach($orderItems as $record){
	$Items[] = $record["orderCNumber"];
}

$getReceivingItems = new App\Api\GetReceivingItems($spiralDataBase,$userInfo);
$receivingData = $getReceivingItems->select($Items);
$receivingData = $spiralDataBase->arrayToNameArray($receivingData["data"],array("registrationTime","orderCNumber","receivingCount","receivingHId"));


foreach($orderItems as $key => $record){
	$orderItems[$key]["receivingNowCount"] = $record['receivingNum'];
	$orderItems[$key]["receivingFlag"] = false;
	$orderItems[$key]["remainingCount"] = 0;
	/*
	foreach($receivingData as $receivingRecord){
		if($record["orderCNumber"] == $receivingRecord["orderCNumber"]){
			//$orderItems[$key]["receivingNowCount"] += (int)$receivingRecord["receivingCount"];
		}
	}
	*/
}

$num = 1;
$minusOrder = false;
foreach($orderItems as $key => $record){
	$orderItems[$key]["remainingCount"] = $record["orderQuantity"] - $record["receivingNowCount"];

	if($record["orderQuantity"] < 0 )
	{
		$minusOrder = true;
	}
	
	if($record["orderQuantity"] > 0 && $record["orderQuantity"] <= $record["receivingNowCount"]){
		$orderItems[$key]["receivingFlag"] = true;
	} else if($record["orderQuantity"] < 0 && $record["orderQuantity"] >= $record["receivingNowCount"]){
		$orderItems[$key]["receivingFlag"] = true;
	}
	
	$ItemsToJs[$record["inHospitalItemId"]] = array(
		"num" => $num,
		"orderCNumber"=> $record["orderCNumber"],
		"orderQuantity"=> $record["orderQuantity"],
		"countNum"=> $record["orderQuantity"],
		//"changeReceiving"=> "0",
		"receivingFlag"=> $orderItems[$key]["receivingFlag"],
		"receivingNowCount"=> $orderItems[$key]["receivingNowCount"],//入庫完了数
		//"receivingBeforeCount"=> $orderItems[$key]["receivingNowCount"],//前回までの入庫数
		"receivingCount" => 0,//今回入庫
		"quantity"=>$orderItems[$key]["quantity"],
		"makerName"=>$orderItems[$key]["makerName"],
		"itemName"=>$orderItems[$key]["itemName"],
		"itemStandard"=>$orderItems[$key]["itemStandard"],
		"itemCode"=>$orderItems[$key]["itemCode"],
		"quantityUnit"=>$orderItems[$key]["quantityUnit"],
		"itemJANCode"=>$orderItems[$key]["itemJANCode"],
		"receivingFlag"=>$orderItems[$key]["receivingFlag"],
		"lotNum" => $orderItems[$key]["receivingNowCount"],
		"price" => $orderItems[$key]["price"],
		"itemUnit" => $orderItems[$key]["itemUnit"],
		"labelId" => $orderItems[$key]["labelId"],
		);
	$num++;
}


$updateOrder = new App\Api\UpdateOrder($spiralDataBase);
$result = $updateOrder->update($card["data"][0][0],$card["data"][0][2],$orderItems);


$crypt   = $SPIRAL->getSpiralCryptOpenSsl();
$authKeyCrypt = $crypt->encrypt($card["data"][0][2], "JoyPla");

//var_dump($ItemsToJs);
//var_dump($receivingData);

$crypt   = $SPIRAL->getSpiralCryptOpenSsl();
$divisionIdCrypt = $crypt->encrypt($card["data"][0][3], "JoyPla");

if($userInfo->getUserPermission() == "1"){
	$link = '%url/table:back%';
}else{
	$link = '%url/rel:mpgt:page_266883%';
} 
?>
<!DOCTYPE html>
<html>
  <head>
    <title>JoyPla 発注書</title>
	<?php include_once "NewJoyPla/src/Head.php"; ?>

        <script>
		let canAjax = true;
		function back(){
			if("<?php echo $result["pattern"] ?>" == "delete"){
				UIkit.modal.alert("発注商品が0件となりました。<br>発注書一覧へ戻ります。").then(function(){
					location.href ="<?php echo $link ?>&table_cache=true";
				});
				
			}
			
			if("<?php echo $card["data"][0][1] ?>" == "1"){
				UIkit.modal.alert("未発注書です。<br>発注書一覧へ戻ります。").then(function(){
					location.href ="<?php echo $link ?>&table_cache=true";
				});
				
			}
		}
		back();
		
		</script>
  </head>
  <body>
    <?php include_once "NewJoyPla/src/HeaderForMypage.php"; ?>
    <div class="animsition uk-margin-bottom" uk-height-viewport="expand: true">
	  	<div class="uk-section uk-section-default uk-preserve-color uk-padding-remove uk-margin-top" id="page_top">
		    <div class="uk-container uk-container-expand">
		    	<ul class="uk-breadcrumb no_print">
				    <li><a href="%url/rel:mpg:top%">TOP</a></li>
				    <li><a href="<?php echo $link ?>&table_cache=true"><span>発注書一覧</span></a></li>
				    <li><span>発注書</span></li>
				</ul>
				<form action="#" method="post">
					<div class="uk-child-width-1-2@m no_print uk-margin" uk-grid>
						<div class="uk-text-left">
							<input class="print_hidden uk-button uk-button-default" type="submit" value="印刷プレビュー" onclick="window.print();return false;">
							<input class="print_hidden uk-button uk-button-danger" type="submit" value="発注書取消" onclick="orderedDelete();return false;">
							<input class="print_hidden uk-button uk-button-primary" type="submit" value="納品照合" onclick="deliveryCheck();return false;">
						</div>
						<div class="uk-text-right">
							<button type="button" class="uk-button uk-button-primary"  uk-toggle="target: #modal-gs1128">GS1-128で照合</button>
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
			    					<td class="uk-text-right">￥<script>price("%val:usr:totalAmount%")</script> - </td>
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
											<th>今回入庫数</th>
											<th>納期</th>
											<th>金額</th>
		    								<th>入庫情報記入</th>
					    				</tr>
					    			</thead>
					    			<tbody>
					    				<?php
					    					$num = 1;
											
					    					foreach($orderItems as $record){
					    						$barcodeId = "01".$record["inHospitalItemId"]."X".$record["quantity"];
				    						
					    						echo "<tr id='tr_".$num."'>";
					    						echo "<td>".$num."</td>";
					    						echo "<td>".$record["makerName"]."</td>";
					    						echo "<td>".$record["itemName"]."</td>";
					    						echo "<td>".$record["itemCode"]."</td>";
					    						echo "<td>".$record["itemStandard"]."</td>";
					    						echo "<td>￥<script>price('".$record["price"]."')</script><span class='uk-text-small'></span> / <span class='uk-text-small'>".$record["itemUnit"]."</span></td>";
					    						echo "<td>".$record["quantity"]."<span class='uk-text-small'>".$record["quantityUnit"]."</span></td>";
					    						echo "<td>".$record["orderQuantity"]."<span class='uk-text-small'>".$record["itemUnit"]."</span></td>";
					    					
					    						echo "<td>".$record["receivingNowCount"]."<span class='uk-text-remove uk-text-small'>".$record["itemUnit"]."</span></td>";
					    						if($record["receivingFlag"]){
					    							echo "<td>入庫完了</td>";
					    						} else {
													if( $record["orderQuantity"] > 0 )
													{
														$attr['min'] = 0;
														$attr['max'] = $record["remainingCount"];
													}
													else if( $record["orderQuantity"] < 0 )
													{
														$attr['min'] = $record["remainingCount"];
														$attr['max'] = 0;
													} 
													else 
													{
														$attr['min'] = 0;
														$attr['max'] = 0;
													}
					    							echo "<td><input type='number' class='uk-input receiving_".$record["inHospitalItemId"]."'";
													foreach($attr as $key => $val) echo " $key='$val' ";
													echo "style='width:82px' value='0' onchange='active(this,\"".$record["inHospitalItemId"]."\")'><span class='uk-text-small uk-text-middle'>".$record["itemUnit"]."</span></td>";
					    						}
					    						echo "<td>".$record["dueDate"]."</td>";
					    						echo "<td>￥<script>price('".$record["orderPrice"]."')</script></td>";
					    						//echo "<td id="barcode_".$num."" class="uk-text-center">".$barcodeId."</td>";
					    						if($record["receivingFlag"]){
					    							echo "<td class='uk-text-center'>入庫済み</td>";
					    						} else {
					    							echo "<td class='uk-text-center'><button type='button' class='uk-button uk-button-default uk-button-small' onclick='addLotInput(".$num.",\"".$record["inHospitalItemId"]."\",null,null)'>入庫情報記入</button></td>";
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
			    		<table class="uk-table uk-table-middle uk-table-divider">
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
		<input type="hidden" value="" name="itemsData" id="itemsData">
		<input type="hidden" value="%val:usr:distributorName%" name="distributorName">
	</form>
	
	<!-- This is a button toggling the modal with the default close button -->
		<!-- This is the modal with the default close button -->
		<div id="modal-gs1128" uk-modal>
		    <div class="uk-modal-dialog uk-modal-body">
		    	<form onsubmit="gs1_128($('#GS1-128').val());return false;" action="#">
			        <button class="uk-modal-close" type="button" uk-close></button>
			        <h2 class="uk-modal-title">GS1-128 読取</h2>
			        <input type="text" class="uk-input" placeholder="GS1-128" id="GS1-128" autofocus="true">
			        <div class="uk-margin-top select_items" style="display:none">
				        <p>商品特定</p>
				        <select name="not_items_info" id="not_items_info" class="uk-select">
							<?php
							/*
							$stringDom = '';
				        	foreach($ItemsToJs as $key => $val){
				        		$stringDom .= "<option value='".$key."'>".$val["itemName"]."</option>";
							}
							echo $stringDom;
				        	*/
							?>
				        </select>
			        </div>
					<p class="uk-text-right">
			            <button class="uk-button uk-button-primary" type="button" onclick="gs1_128($('#GS1-128').val());">反映</button>
			        </p>
		        </form>
		    </div>
		</div>
		
	
    <script>
    	let upToData = <?php echo json_encode($ItemsToJs); ?>;
    	
		
		let gs1128_object = {};
		$(function(){
		/*
		 let count = $("#tbl-Items tbody").children().length;
		 let num ;
		 for(let i = 1 ; i <= count ; i++){
		 	num = $("#barcode_" + i).text();
			$("#barcode_" + i).html("<svg id="barcode_area_"+i+""></svg>");
			generateBarcode("barcode_area_" + i,num);
		 	//$("td#barcode_" + i +" div").barcode(num, "ean13",{barWidth: 2 ,barHeight: 40 , output: "css"});
		 }
		 */
		notItemsInfo("");
		 let order_num = $("#hacchu_num").text();
		 //$("#hacchu_num").remove();
		 $("#order_barcode").html("<svg id='barcode_hacchu'></svg>");
		 generateBarcode("barcode_hacchu",order_num);
		 //$("td#order_barcode div").barcode({code:order_num, crc:false }, "int25",{barWidth: 3 ,barHeight: 40 , output: "css"});
		});

		function notItemsInfo(jancode)
		{
			let select = document.querySelector("#not_items_info");
			while(select.firstChild){
				select.removeChild(select.firstChild)
			}
			let option = "";
			option = document.createElement("option");
			option.value = "";
			option.text  = " ----- 選択してください -----";
			select.appendChild(option); 
			for ( var key in upToData ) {
				if(parseInt(upToData[key]["orderQuantity"]) > 0)
				{
					if( parseInt(upToData[key]["orderQuantity"]) <= parseInt(upToData[key]["receivingCount"]))
					{ 
						continue; 
					}
				} 
				else if(parseInt(upToData[key]["orderQuantity"]) < 0)
				{
					if( parseInt(upToData[key]["orderQuantity"]) >= parseInt(upToData[key]["receivingCount"]))
					{ 
						continue; 
					}
				}

				if( jancode !== "" && jancode !== upToData[key]["itemJANCode"]){
					continue;
				}
				option = document.createElement("option");
				option.value = key;
				option.text  = upToData[key]["itemName"];
				select.appendChild(option); 
			
			};
		}
		
		function deliveryCheck(){
			if(!canAjax) { 
				console.log('通信中');
				return;
			}
			//$_POST["orderHistoryId"],$_POST["divisionId"],$_POST["receiving"]
			
			let flg = true;
			let labelCreateFlg = false;
			
			UIkit.modal.confirm("入力された値で納品照合を行います。<br>よろしいですか").then(function () {
				let receivingData = {};
				Object.keys(upToData).forEach(function (key) {
					//if(upToData[key].changeReceiving != "0"){
					if(upToData[key].receivingCount != "0"){
						receivingData[key] = upToData[key];
						if(upToData[key]["receivingCount"] > 0)
						{
							labelCreateFlg = true;
						}
					}
				});

				if(!chkLotObj()){
					UIkit.modal.alert("ロット情報を入力してください");
					return false;
				}

				if(Object.keys(receivingData).length == 0){
					UIkit.modal.alert("納品対象がありません。<br>入庫数を入力して下さい");
					return false;
				}

				loading();
				canAjax = false; // これからAjaxを使うので、新たなAjax処理が発生しないようにする
				$.ajax({
					async: false,
					url:"%url/card:page_267241%",
					type:"POST",
					data:{
						orderHistoryId : "%val:usr:orderNumber%",
						divisionId : "%val:usr:divisionId%",
						distributorId : "%val:usr:distributorId%",
						receiving : JSON.stringify(receivingData),
						lotData : JSON.stringify(makeLotObj()),
						divisionId : "<?php echo $divisionIdCrypt ?>"
					},
					dataType: "json"
				})
				// Ajaxリクエストが成功した時発動
				.done( (data) => {
					
					if(! data.result){
						UIkit.modal.alert("納品照合に失敗しました").then(function(){
							canAjax = true; // 再びAjaxできるようにする
						});
						return false;
					}
					UIkit.modal.alert("納品照合が完了しました").then(function () {
						if(labelCreateFlg)
						{
							UIkit.modal.confirm("ラベル発行を行いますか").then(function () {
								createLabel();
							}, function () {
								location.reload();
							});
						} 
						else 
						{
							location.reload();
						}
					});
				})
				// Ajaxリクエストが失敗した時発動
				.fail( (data) => {
					UIkit.modal.alert("納品照合に失敗しました").then(function(){
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
		
		function addLotInput(num,inHospitalItemId,lotval,lotDate,lotQuantity){
			num_property = ["max" , "min"];
			if(parseInt(upToData[inHospitalItemId]["orderQuantity"]) > 0 )
			{
				num_property = ["max" , "min"];
				n_num = 1;
				if(parseInt(upToData[inHospitalItemId]["orderQuantity"]) <= parseInt(upToData[inHospitalItemId].lotNum)){
					console.log("testB");
					UIkit.modal.alert("発注数より多くはできません。ご確認ください");
					return ;
				}
				
				if($("input.receiving_"+inHospitalItemId)[0][num_property[0]] < (parseInt($("input.receiving_"+inHospitalItemId).val()) + n_num)){
					UIkit.modal.alert("発注数より多くはできません。ご確認ください");
					return ;
				}

			} else
			if(parseInt(upToData[inHospitalItemId]["orderQuantity"]) < 0 )
			{
				num_property = ["min" , "max"];
				n_num = -1;
				
				if(parseInt(upToData[inHospitalItemId]["orderQuantity"]) >= parseInt(upToData[inHospitalItemId].lotNum)){
					console.log("test");
					UIkit.modal.alert("発注数より多くはできません。ご確認ください");
					return ;
				}

				if($("input.receiving_"+inHospitalItemId)[0][num_property[0]] > (parseInt($("input.receiving_"+inHospitalItemId).val()) + n_num)){
					UIkit.modal.alert("発注数より多くはできません。ご確認ください");
					return ;
				}
			}

			
			upToData[inHospitalItemId].lotNum = parseInt(upToData[inHospitalItemId].lotNum) + n_num;
			
			let trElm = document.createElement("tr"); 
			let tdElm = document.createElement("td");
			tdElm.colSpan = "4";
			trElm.appendChild(tdElm); 
			
			
			tdElm = document.createElement("td");
			tdElm.className = "uk-text-small uk-text-break";
			tdElm.style = "white-space: break-spaces";
			html = document.createTextNode("ロット番号");
			tdElm.appendChild(html);
			brelm = document.createElement("br");
			tdElm.appendChild(brelm);
			tdElm.colSpan = "4";
			tdElm.className = "uk-text-small";
			input = document.createElement("input");
			input.className = "uk-input lot_" +inHospitalItemId;
			input.type = "text";
			input.value = lotval;
			if(lotval){
    			input.style.backgroundColor = "rgb(255, 204, 153)";
			}
			input.onchange  = function () {  
			    	$(this).css("background-color","rgb(255, 204, 153)");
			    };
			tdElm.appendChild(input); 
			trElm.appendChild(tdElm); 
			
			
			tdElm = document.createElement("td");
			tdElm.className = "uk-text-small uk-text-break";
			tdElm.style = "white-space: break-spaces";
			html = document.createTextNode("使用期限");
			tdElm.appendChild(html);
			brelm = document.createElement("br");
			tdElm.appendChild(brelm);
			tdElm.colSpan = "4";
			input = document.createElement("input");
			input.className = "uk-input lotDate_" +inHospitalItemId;
			input.type = "date";
			input.value = lotDate;
			if(lotDate){
    			input.style.backgroundColor = "rgb(255, 204, 153)";
			}
			input.onchange  = function () {  
			    	$(this).css("background-color","rgb(255, 204, 153)");
			    };
			
			tdElm.appendChild(input); 
			trElm.appendChild(tdElm); 
			/*
			tdElm = document.createElement("td");
			tdElm.className = "uk-text-small uk-text-break";
			tdElm.style = "white-space: break-spaces";
			html = document.createTextNode("入数");
			tdElm.appendChild(html);
			brelm = document.createElement("br");
			tdElm.appendChild(brelm);
			tdElm.colSpan = "1";
			input = document.createElement("input");
			input.className = "uk-input uk-width-small lotQuantity_" +inHospitalItemId;
			input.type = "number";
			input.value = lotQuantity;
			if(lotQuantity){
    			input.style.backgroundColor = "rgb(255, 204, 153)";
			}
			input.onchange  = function () {  
			    	$(this).css("background-color","rgb(255, 204, 153)");
			    };
			
			tdElm.appendChild(input); 
			trElm.appendChild(tdElm); 
			*/
			tdElm = document.createElement("td");
			tdElm.className = "uk-text-center";
			input = document.createElement("input");
			input.className = "uk-button uk-button-danger uk-button-small";
			input.type = "button";
			input.value = "削除";
			
			input.onclick  = function () {  
			    	$(this).parent().parent().remove();
					val = $("input.receiving_"+inHospitalItemId).val();
					min = $("input.receiving_"+inHospitalItemId)[0][num_property[1]];
					$("input.receiving_"+inHospitalItemId)[0][num_property[1]] = parseInt(min) - n_num;
					$("input.receiving_"+inHospitalItemId).val(parseInt(val) - n_num);
					upToData[inHospitalItemId].lotNum = parseInt(upToData[inHospitalItemId].lotNum) - n_num;
					active($("input.receiving_"+inHospitalItemId),inHospitalItemId);
			    };
			
			tdElm.appendChild(input); 
			trElm.appendChild(tdElm); 
			
			$("#tr_"+num).after(trElm);
			min = $("input.receiving_"+inHospitalItemId)[0][num_property[1]];
			$("input.receiving_"+inHospitalItemId)[0][num_property[1]] = parseInt(min) + n_num;
			val = $("input.receiving_"+inHospitalItemId).val();
			$("input.receiving_"+inHospitalItemId).val(parseInt(val) + n_num);
			$("input.receiving_"+inHospitalItemId).css("background-color","rgb(255, 204, 153)");
			active($("input.receiving_"+inHospitalItemId)[0],inHospitalItemId);
			
			
		}
		
		function orderedDelete(){
			if(!canAjax) { 
				console.log('通信中');
				return;
			}
			if( "%val:usr:orderStatus:id%" == "5" || "%val:usr:orderStatus:id%" == "6" ){
				UIkit.modal.alert("すでに入庫済みの情報があります。削除はできません");
				return false;
			}
			
			UIkit.modal.confirm("発注書を削除します。<br>よろしいですか").then(function () {
				loading();
				canAjax = false; // これからAjaxを使うので、新たなAjax処理が発生しないようにする
				$.ajax({
					async: false,
					url:"%url/card:page_267242%",
					type:"POST",
					data:{
						orderNum : "%val:usr:orderNumber%",
						orderData : JSON.stringify( upToData ),
						orderAuthKey: "<?php echo $authKeyCrypt ?>" ,
						countFlg : true,
						divisionId : "<?php echo $divisionIdCrypt ?>"
					},
					dataType: "json"
				})
				// Ajaxリクエストが成功した時発動
				.done( (data) => {
					
					if(! data.result){
						UIkit.modal.alert("発注書取消に失敗しました").then(function(){
							canAjax = true; // 再びAjaxできるようにする
						});
						return false;
					}
					UIkit.modal.alert("発注書取消が完了しました").then(function(){
						location.href ="<?php echo $link ?>&table_cache=true";
					});
					
				})
				// Ajaxリクエストが失敗した時発動
				.fail( (data) => {
					
					UIkit.modal.alert("伝票削除に失敗しました").then(function(){
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
    	
		function active(elm,inHospitalItemId){
			if(elm.style){
    			elm.style.backgroundColor = "rgb(255, 204, 153)";
			}
    		//upToData[inHospitalItemId].changeReceiving = elm.value;
    		//upToData[inHospitalItemId].receivingNowCount = elm.value;
    		upToData[inHospitalItemId].receivingCount = elm.value;
    
    	}
    	function createLabel(){
    		$("#itemsData").val(JSON.stringify( upToData ));
    		$("#createLabelForm").submit();
			location.reload();
    	}
    	

    	function gs1_128(gs1128){
    		gs1128_object = {};
			if(gs1128.indexOf("]C1") !== 0){
				return gs1_128("]C1"+gs1128);
			} else {
				gs1128 = gs1128.slice( 3 );
				let obj = check_gs1128(gs1128);
				let chkflg = false;
				let objkey = $(".select_items select").val();
				
				if( objkey == "" && ! obj.hasOwnProperty("01") )
				{
					UIkit.modal.alert("商品情報が含まれておりませんでした<br>選択肢からお選びいただき再度、反映をクリックしてください。").then(function(){
						$(".select_items").show();
						notItemsInfo("");
						UIkit.modal($('#modal-gs1128')).show();
					});
					return;
				}

				objectJan = [];
				
				if(objkey == "" ){
					searchJan = addCheckDigit(obj["01"]);
					for ( var key in upToData ) {
						if(parseInt(upToData[key]["orderQuantity"]) > 0)
						{
							if( parseInt(upToData[key]["orderQuantity"]) <= parseInt(upToData[key]["receivingCount"]))
							{ 
								continue; 
							}
						} 
						else if(parseInt(upToData[key]["orderQuantity"]) < 0)
						{
							if( parseInt(upToData[key]["orderQuantity"]) >= parseInt(upToData[key]["receivingCount"]))
							{ 
								continue; 
							}
						}

						if(searchJan == upToData[key]["itemJANCode"]){
							chkflg = true;
							objkey = key;
							objectJan.push(upToData[key]["itemJANCode"]);
						}
					};
					
					if(objectJan.length > 1)
					{
						UIkit.modal.alert("商品情報が複数見つかりました<br>選択肢からお選びいただき再度、反映をクリックしてください。").then(function(){
							$(".select_items").show();
							UIkit.modal($('#modal-gs1128')).show();
							notItemsInfo("");
						});
						return ;
					}
				} else {
					chkflg = true;
				}
				
				if(!chkflg){
					UIkit.modal.alert("対象の発注商品が見つかりませんでした").then(function(){
						UIkit.modal($('#modal-gs1128')).show();
					});
					return false;
				}

				addLotInput(upToData[objkey]["num"],objkey,obj["10"],changeDate(obj["17"]),obj["30"]);
				$(".select_items").hide();
				$(".select_items select").val("");
				$("#GS1-128").val("");
				document.getElementById("GS1-128").focus()
			}
		}
		
		function changeDate(text){
			if(text == null){
				return "";
			}
			if(text.length == "6"){
				text = 20 + text;
			}
			date = text.slice(6, 8);
			if(parseInt(text.slice(6, 8)) == 0){
				date = '01';
			}
			return text.slice(0, 4) + "-" + text.slice(4, 6) + "-" + date;
		}
		
		
				
		function makeLotObj(){
			let lotObj = {};
			Object.keys(upToData).forEach(function (inHPItemKey) {
					lotObj[inHPItemKey] = {};
					regType = "insert";
					$(".lot_"+inHPItemKey).each(function(index,elm){
						lotObj[inHPItemKey][index] = {};
						if(parseInt(upToData[inHPItemKey]["orderQuantity"]) < 0) {
							regType = "delete";
						}
						lotObj[inHPItemKey][index]["registType"] = regType;
						lotObj[inHPItemKey][index]["lotNumber"]= elm.value;
						lotObj[inHPItemKey][index]["inHospitalItemId"] = inHPItemKey;
					});
					$(".lotDate_"+inHPItemKey).each(function(index,elm){
						lotObj[inHPItemKey][index]["lotDate"] = elm.value;
					});
				
			});
			return lotObj;
		}

		function chkLotObj(){
			let lotObj = makeLotObj();
			let flg = true;
			Object.keys(lotObj).forEach(function (inHPItemKey) {
				Object.keys(lotObj[inHPItemKey]).forEach(function (index) {
					if(lotObj[inHPItemKey][index]["lotDate"] == '' || lotObj[inHPItemKey][index]["lotNumber"] == '' ){
						flg = false;
					}
				});
			});
			return flg;
		}
    </script>
  </body>
</html>