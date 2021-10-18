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

foreach($orderItems as $key => $record){
	$orderItems[$key]["receivingNowCount"] = $record['receivingNum'];
	$orderItems[$key]["receivingFlag"] = false;
	$orderItems[$key]["remainingCount"] = 0;
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
		"labelId" => $orderItems[$key]["labelId"],
		"itemUnit" => $orderItems[$key]["itemUnit"],
		"remainingCount" => $orderItems[$key]["remainingCount"],//今回入庫可能数
		);
	$num++;
}

if($card["data"][0][1] != 8)
{
	$updateOrder = new App\Api\UpdateOrder($spiralDataBase);
	$result = $updateOrder->update($card["data"][0][0],$card["data"][0][2],$orderItems);
}



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
							<input class="print_hidden uk-button uk-button-default" type="button" value="印刷プレビュー" onclick="window.print();return false;">
							<input class="print_hidden uk-button uk-button-danger" type="button" value="発注書取消" onclick="order_slip_detail.orderedDelete();return false;">
							<input class="print_hidden uk-button uk-button-primary" type="button" value="納品照合" onclick="order_slip_detail.deliveryCheck();return false;">
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
											<th>JANコード</th>
											<th>価格</th>
											<th>入数</th>
											<th>発注数</th>
											<th>入庫数</th>
											<th>今回入庫数</th>
											<th>納期</th>
											<th>金額</th>
											<th>ロット番号</th>
											<th>使用期限</th>
											<th>入庫情報追加</th>
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
					    						echo "<td>".$record["itemJANCode"]."</td>";
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
					    							echo "<td><input type='number' class='uk-input receiving_".$record["inHospitalItemId"]."' name='count'";
													foreach($attr as $key => $val) echo " $key='$val' ";
													echo "style='width:82px' value='0' onchange='order_slip_detail.active(this);order_slip_detail.countChange(this,\"".$record["inHospitalItemId"]."\");'><span class='uk-text-small uk-text-middle'>".$record["itemUnit"]."</span></td>";
					    						}
					    						echo "<td>".$record["dueDate"]."</td>";
					    						echo "<td>￥<script>price('".$record["orderPrice"]."')</script></td>";
					    						//echo "<td id="barcode_".$num."" class="uk-text-center">".$barcodeId."</td>";

					    						if($record["receivingFlag"]){
					    							echo "<td class='uk-text-center'></td>";
					    							echo "<td class='uk-text-center'></td>";
					    						} else {
					    							echo "<td class='uk-text-small uk-text-break' style='white-space: break-spaces;'><input type='text' class='uk-input lot_{$record["inHospitalItemId"]}' style='width:184px;' onchange='order_slip_detail.active(this)' maxlength='20'></td>";
					    							echo "<td class='uk-text-small uk-text-break' style='white-space: break-spaces;'><input type='date' class='uk-input lotDate_{$record["inHospitalItemId"]}' style='width:184px;' onchange='order_slip_detail.active(this)'></td>";
					    						}

					    						if($record["receivingFlag"]){
					    							echo "<td class='uk-text-center'>入庫済み</td>";
					    						} else {
					    							echo "<td class='uk-text-center'><button type='button' class='uk-button uk-button-default uk-button-small' onclick='order_slip_detail.addLotInput(".$num.",\"".$record["inHospitalItemId"]."\",null,null)'>追加</button></td>";
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
	<form action="%url/rel:mpgt:ReceivingLabel%" target="_blank" method="post" class="print_hidden uk-inline" id="createLabelForm">
		<!-- <input type="hidden" value="" name="itemsData" id="itemsData"> -->
		<input type="hidden" id="receivingId" name="receivingId">
		<input type="hidden" value="%val:usr:distributorName%" name="distributorName">
	</form>
	
	<!-- This is a button toggling the modal with the default close button -->
		<!-- This is the modal with the default close button -->
		<div id="modal-gs1128" uk-modal>
		    <div class="uk-modal-dialog uk-modal-body">
		    	<form onsubmit="order_slip_detail.gs1_128($('#GS1-128').val());return false;" action="#">
			        <button class="uk-modal-close" type="button" uk-close></button>
			        <h2 class="uk-modal-title">GS1-128 読取</h2>
			        <input type="text" class="uk-input" placeholder="GS1-128" id="GS1-128" autofocus="true">
			        <div class="uk-margin-top select_items" style="display:none">
				        <p>商品特定</p>
				        <select name="not_items_info" id="not_items_info" class="uk-select">
				        	<option value=""> --- 選択してください --- </option>
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
			            <button class="uk-button uk-button-primary" type="button" onclick="order_slip_detail.gs1_128($('#GS1-128').val());">反映</button>
			        </p>
		        </form>
		    </div>
		</div>
		
		<script>
			upToData = <?php echo json_encode($ItemsToJs); ?>;

			
			class OrderSlipDetail
			{
				constructor(phpData)
				{
					this.phpData = phpData;
					this.back();

					this.canAjax = true;
					this.gs1128_object = {};
					this.order_num = $("#hacchu_num").text();
					$("#order_barcode").html("<svg id='barcode_hacchu'></svg>");
					this.notItemsInfo("");
					generateBarcode("barcode_hacchu",this.order_num);
				}

				back(){
					if(this.phpData.slipDetailPattern == "delete"){
						UIkit.modal.alert("発注商品が0件となりました。<br>発注書一覧へ戻ります。").then(function(){
							location.href =this.phpData.backToLink+"&table_cache=true";
						});
					}
				
					if(this.phpData.slipDetailPattern == "1"){
						UIkit.modal.alert("未発注書です。<br>発注書一覧へ戻ります。").then(function(){
							location.href =this.phpData.backToLink+"&table_cache=true";
						});
					}
						
				}

				notItemsInfo(jancode)
				{
					let select = document.querySelector("#not_items_info");
					while(select.firstChild)
					{
						select.removeChild(select.firstChild)
					}
					let option = "";
					option = document.createElement("option");
					option.value = "";
					option.text  = " ----- 選択してください -----";
					select.appendChild(option); 
					for ( var key in this.phpData['phpItemsData'] ) 
					{
						if(parseInt(this.phpData['phpItemsData'][key]["orderQuantity"]) > 0)
						{
							if( parseInt(this.phpData['phpItemsData'][key]["orderQuantity"]) <= parseInt(this.phpData['phpItemsData'][key]["receivingCount"]))
							{ 
								continue; 
							}
						} 
						else if(parseInt(this.phpData['phpItemsData'][key]["orderQuantity"]) < 0)
						{
							if( parseInt(this.phpData['phpItemsData'][key]["orderQuantity"]) >= parseInt(this.phpData['phpItemsData'][key]["receivingCount"]))
							{ 
								continue; 
							}
						}

						if( jancode !== "" && jancode !== this.phpData['phpItemsData'][key]["itemJANCode"])
						{
							continue;
						}
						option = document.createElement("option");
						option.value = key;
						option.text  = this.phpData['phpItemsData'][key]["itemName"];
						select.appendChild(option); 
					
					};
				}
				deliveryCheck(){
					if(!this.canAjax ) {
						console.log('通信中');
						return;
					}
					//$_POST["orderHistoryId"],$_POST["divisionId"],$_POST["receiving"]
					
					let flg = true;
					let tmp = this;
					let labelCreateFlg = false;
					UIkit.modal.confirm("入力された値で納品照合を行います。<br>よろしいですか").then(function () {
						let receivingData = {};
						let regLotData = tmp.makeRegData();

						if(Object.keys(regLotData).length == 0){
							UIkit.modal.alert("納品対象がありません。<br>入庫数を入力して下さい");
							return false;
						}

						Object.keys(regLotData).forEach(function (inHPItemKey) {
							receivingData[inHPItemKey] = tmp.phpData['phpItemsData'][inHPItemKey];
							/*
							Object.keys(regLotData[inHPItemKey]).forEach(function (lotKey) {
								receivingData[inHPItemKey]['receivingCount'] = (receivingData[inHPItemKey]['receivingCount'] + regLotData[inHPItemKey][lotKey]['receivingCount']);
							});
							*/
							//if(this.phpData['phpItemsData'][key].changeReceiving != "0"){
							if(tmp.phpData['phpItemsData'][inHPItemKey]['receivingCount'] > 0){
								labelCreateFlg = true;
							}
							
							if (Math.abs(parseInt(tmp.phpData['phpItemsData'][inHPItemKey]['remainingCount'])) < Math.abs(parseInt(receivingData[inHPItemKey]['receivingCount']))) {
								UIkit.modal.alert("入庫数が発注数を超えています<br>今回入庫数を確認して下さい");
								return false;
							}
						});


						if(Object.keys(regLotData).length == 0){
							UIkit.modal.alert("ロット情報の入力を確認してください");
							return false;
						}

						loading();
						tmp.canAjax= false; // これからAjaxを使うので、新たなAjax処理が発生しないようにする
						$.ajax({
							async: false,
							url:"%url/card:page_267241%",
							type:"POST",
							data:{
								orderHistoryId : "%val:usr:orderNumber%",
								divisionId : "%val:usr:divisionId%",
								distributorId : "%val:usr:distributorId%",
								receiving : JSON.stringify(receivingData),
								regData : JSON.stringify(regLotData),
								divisionId : tmp.phpData.divisionId
							},
							dataType: "json"
						})
						// Ajaxリクエストが成功した時発動
						.done( (data) => {
							if(! data.result){
								UIkit.modal.alert("納品照合に失敗しました").then(function(){
									tmp.canAjax = true; // 再びAjaxできるようにする
								});
								return false;
							}
							UIkit.modal.alert("納品照合が完了しました").then(function () {
								if(labelCreateFlg)
								{
									UIkit.modal.confirm("ラベル発行を行いますか").then(function () {
										tmp.createLabel(data.historyId);
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
								tmp.canAjax = true; // 再びAjaxできるようにする
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

				chkReceivingCount(inHospitalItemId) {
					let flg = true;
					if(parseInt(this.phpData['phpItemsData'][inHospitalItemId]["orderQuantity"]) > 0 )
					{
						if(parseInt(this.phpData['phpItemsData'][inHospitalItemId]["orderQuantity"]) <= parseInt(this.phpData['phpItemsData'][inHospitalItemId].lotNum))
						{
							flg = false;
						}
						if(parseInt(this.phpData['phpItemsData'][inHospitalItemId].remainingCount) <= parseInt(this.phpData['phpItemsData'][inHospitalItemId].receivingCount))
						{
							flg = false;
						}
						return flg;
					} 
					else if(parseInt(this.phpData['phpItemsData'][inHospitalItemId]["orderQuantity"]) < 0 )
					{
						if(parseInt(this.phpData['phpItemsData'][inHospitalItemId]["orderQuantity"]) >= parseInt(this.phpData['phpItemsData'][inHospitalItemId].lotNum))
						{
							flg = false;
						}
						if(parseInt(this.phpData['phpItemsData'][inHospitalItemId].remainingCount) >= parseInt(this.phpData['phpItemsData'][inHospitalItemId].receivingCount))
						{
							flg = false;
						}
						return flg;
					}
				}

				addLotInput(num,inHospitalItemId,lotval,lotDate,lotQuantity){
					let itemId = inHospitalItemId;
					if (this.chkReceivingCount(inHospitalItemId) == false) {
						UIkit.modal.alert("発注数より多くはできません。ご確認ください");
						return;
					}

					this.phpData['phpItemsData'][inHospitalItemId].receivingCount = parseInt(this.phpData['phpItemsData'][inHospitalItemId].receivingCount) + 1;

					let trElm = document.createElement("tr"); 
					let tdElm = document.createElement("td");
					tdElm.colSpan = "10";
					trElm.appendChild(tdElm); 

					tdElm = document.createElement("td");
					tdElm.className = "uk-text-small uk-text-break";
					tdElm.style = "white-space: break-spaces";

					tdElm.appendChild(document.createTextNode("今回入庫数"));
					tdElm.appendChild(document.createElement("br"));

					let numinput = document.createElement("input");
					numinput.className = "uk-input receiving_" +inHospitalItemId;
					numinput.name = "count";
					numinput.type = "number";
					if(parseInt(this.phpData['phpItemsData'][inHospitalItemId]["orderQuantity"]) > 0 )
					{
						numinput.value = 1;
						numinput.min = 0;
					}
					else 
					{
						numinput.value = -1;
						numinput.max = 0;
					}
					numinput.style.width = "82px";
					numinput.style.backgroundColor = "rgb(255, 204, 153)";

					numinput.onchange = function () 
					{
						order_slip_detail.countChange(this,inHospitalItemId);
					}

					let span = document.createElement("span");
					span.className = "uk-text-small uk-text-middle";

					let itemUnit = document.createTextNode(this.phpData['phpItemsData'][inHospitalItemId].itemUnit);
					span.appendChild(itemUnit);

					tdElm.appendChild(numinput); 
					tdElm.appendChild(span);
					trElm.appendChild(tdElm); 


					tdElm = document.createElement("td");
					tdElm.colSpan = "2";
					trElm.appendChild(tdElm); 


					tdElm = document.createElement("td");
					tdElm.className = "uk-text-small uk-text-break";
					tdElm.style = "white-space: break-spaces";

					tdElm.appendChild(document.createTextNode("ロット番号"));
					tdElm.appendChild(document.createElement("br"));
					tdElm.className = "uk-text-small";
					let lotinput = document.createElement("input");
					lotinput.className = "uk-input lot_" +inHospitalItemId;
					lotinput.type = "text";
					lotinput.maxLength = 20;
					if (lotval) {
						lotinput.value = lotval;
						lotinput.style.backgroundColor = "rgb(255, 204, 153)";
					}
					lotinput.onchange  = function () {  
							$(this).css({'background':'rgb(255, 204, 153)'});
						};

					tdElm.appendChild(lotinput); 
					trElm.appendChild(tdElm); 


					tdElm = document.createElement("td");
					tdElm.className = "uk-text-small uk-text-break";
					tdElm.style = "white-space: break-spaces";

					tdElm.appendChild(document.createTextNode("使用期限"));
					tdElm.appendChild( document.createElement("br"));
					let dateinput = document.createElement("input");

					dateinput.className = "uk-input lotDate_" +inHospitalItemId;
					dateinput.type = "date";
					if(lotDate){
						dateinput.value = lotDate;
						dateinput.style.backgroundColor = "rgb(255, 204, 153)";
					}
					dateinput.onchange  = function () {  
							$(this).css({'background':'rgb(255, 204, 153)'});
						};

					tdElm.appendChild(dateinput); 
					trElm.appendChild(tdElm); 


					tdElm = document.createElement("td");
					tdElm.className = "uk-text-center";
					let btninput = document.createElement("input");
					btninput.className = "uk-button uk-button-danger uk-button-small";
					btninput.type = "button";
					btninput.value = "削除";
					let tmp = this;
					btninput.onclick  = function () {
							var elm = $(this).parent().parent().find(".receiving_"+inHospitalItemId)[0];
							elm.min = 0 ;
							elm.value = 0 ;
							tmp.countChange(elm,inHospitalItemId);
							$(this).parent().parent().remove();
					};

					tdElm.appendChild(btninput);
					
					trElm.appendChild(tdElm); 

					$("#tr_"+num).after(trElm);

					this.countChange(numinput,inHospitalItemId);//初期化

				}

				orderedDelete(){
					if(!this.canAjax ) { 
						console.log('通信中');
						return;
					}
					if( "%val:usr:orderStatus:id%" == "5" || "%val:usr:orderStatus:id%" == "6" ){
						UIkit.modal.alert("すでに入庫済みの情報があります。削除はできません");
						return false;
					}
					let tmp = this;
					UIkit.modal.confirm("発注書を削除します。<br>よろしいですか").then(function () {
						loading();
						tmp.canAjax = false; // これからAjaxを使うので、新たなAjax処理が発生しないようにする
						$.ajax({
							async: false,
							url:"%url/card:page_267242%",
							type:"POST",
							data:{
								orderNum : "%val:usr:orderNumber%",
								orderData : JSON.stringify( tmp.phpData['phpItemsData'] ),
								orderAuthKey: tmp.phpData['authKeyCrypt'],
								countFlg : true,
								divisionId : tmp.phpData['divisionIdCrypt']
							},
							dataType: "json"
						})
						// Ajaxリクエストが成功した時発動
						.done( (data) => {
							
							if(! data.result){
								UIkit.modal.alert("発注書取消に失敗しました").then(function(){
									tmp.canAjax = true; // 再びAjaxできるようにする
								});
								return false;
							}
							UIkit.modal.alert("発注書取消が完了しました").then(function(){
								location.href =tmp.phpData.backToLink+"&table_cache=true";
							});
							
						})
						// Ajaxリクエストが失敗した時発動
						.fail( (data) => {
							
							UIkit.modal.alert("伝票削除に失敗しました").then(function(){
								tmp.canAjax = true; // 再びAjaxできるようにする
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

				active(elm) {
					if (elm.style) {
						elm.style.backgroundColor = "rgb(255, 204, 153)";
					}
				}

				countChange(elm,itemId){
					let target = parseInt($(elm).val());
					$(elm).css({'background':'rgb(255, 204, 153)'});
					//let itemId = $.trim(($(elm).attr("class")).replace("uk-input receiving_", ""));
					let total = 0;
					$(document).find('.receiving_' + itemId).each(function() {
						total = total + parseInt($(this).val());
					});
					this.phpData['phpItemsData'][itemId].receivingCount = total;
					let remaining = Math.abs(parseInt(this.phpData['phpItemsData'][itemId].remainingCount)) - Math.abs(total);
					if (remaining < 0) {
						$(elm).val(1);
						UIkit.modal.alert("発注数より多くはできません。ご確認ください");
						return;
					} else {
						if(this.phpData['phpItemsData'][itemId]['orderQuantity'] > 0) {
							this.setMaxNum(remaining, itemId);
						} else {
							this.setMinNum(-remaining, itemId);
						}
					}
				}
				createLabel(historyId){
					//$("#itemsData").val(JSON.stringify( this.phpData['phpItemsData'] ));
					$("#receivingId").val(historyId);
					
					$("#createLabelForm").submit();
					location.reload();
				}

				gs1_128(gs1128){
					this.gs1128_object = {};
					if(gs1128.indexOf("]C1") !== 0){
						//UIkit.modal.alert("GS1-128ではありません");
						//return ;
						return this.gs1_128("]C1"+gs1128);
					} else {
						let obj = check_gs1128(gs1128);
						let chkflg = false;
						let objkey = $(".select_items select").val();
						let tmp = this;
						
						if( objkey == "" && ! obj.hasOwnProperty("01")){
							UIkit.modal.alert("商品情報が含まれておりませんでした<br>選択肢からお選びいただき再度、反映をクリックしてください。").then(function(){
								$(".select_items").show();
								UIkit.modal($('#modal-gs1128')).show();
							});
							return;
						}
						
						if(objkey == "" ){
							let searchJan = addCheckDigit(obj["01"]);
							console.log(searchJan);
							Object.keys(this.phpData['phpItemsData']).forEach(function (key) {
							if(searchJan == tmp.phpData['phpItemsData'][key]["itemJANCode"]){
								chkflg = true;
								objkey = key;
								}
							});
						} else {
							chkflg = true;
						}
						
						if(!chkflg){
							UIkit.modal.alert("対象の発注商品が見つかりませんでした").then(function(){
								UIkit.modal($('#modal-gs1128')).show();
							});
							return false;
						}

						if (this.chkReceivingCount(objkey) == false) {
							UIkit.modal.alert("発注数より多くはできません。ご確認ください");
							return;
						}

						let existflg = false;
						let objLot = (obj["10"] === void 0) ? "" : obj["10"]; //lotNumber
						let objLotDate = (obj["17"] === void 0) ? "" : this.changeDate(obj["17"]); //lotDate 

						$(document).find(".lot_" + objkey).each(function() {
							let addRowLot = $(this).val();
							let addRowLotDate = $(this).parents("tr").find(".lotDate_" + objkey).val();
							let addRowNum = parseInt($(this).parents("tr").find(".receiving_" + objkey).val());
							let mathNum = 1;
							if(tmp.phpData['phpItemsData'][objkey]["orderQuantity"] <= 0){
								mathNum = -1;
							}

							if ((addRowLot == objLot) && (addRowLotDate == objLotDate)) {
								$(this).parents("tr").find(".receiving_" + objkey).val(addRowNum + mathNum).css({'background':'rgb(255, 204, 153)'});
								let elm = $(this).parents("tr").find(".receiving_" + objkey)[0];
								tmp.countChange(elm,objkey);
								existflg = true;
								return false;
							}

							if (addRowNum === 0 && !addRowLot && !addRowLotDate) {
								let trId = $(this).parents("tr").attr("id");
								if (trId) {
									$(this).val(objLot).css({'background':'rgb(255, 204, 153)'});
									$(this).parents("tr").find(".lotDate_" + objkey).val(objLotDate).css({'background':'rgb(255, 204, 153)'});
									$(this).parents("tr").find(".receiving_" + objkey).val(mathNum).css({'background':'rgb(255, 204, 153)'});
									existflg = true;
									return false;
								}
							}
						});

						if (existflg) {
							/*
							this.phpData['phpItemsData'][objkey].receivingCount = parseInt(this.phpData['phpItemsData'][objkey].receivingCount) + 1;
							let remaining = parseInt(this.phpData['phpItemsData'][objkey].remainingCount) - parseInt(this.phpData['phpItemsData'][objkey].receivingCount);
							if(remaining <= 0){
								setMinNum(remaining, objkey);
							}
							if(remaining >= 0){
								setMaxNum(remaining, objkey);
							}
							*/
						}
						if (!existflg) {
							this.addLotInput(this.phpData['phpItemsData'][objkey]["num"],objkey,objLot,objLotDate,obj["30"]);
						}

						$(".select_items").hide();
						$(".select_items select").val("");
						$("#GS1-128").val("");
						document.getElementById("GS1-128").focus();
					}
				}
				changeDate(text){
					if(text == null){
						return "";
					}
					if(text.length == "6"){
						text = 20 + text;
					}
					let date = text.slice(6, 8);
					if(parseInt(text.slice(6, 8)) == 0){
						date = '01';
					}
					return text.slice(0, 4) + "-" + text.slice(4, 6) + "-" + date;
				}

				setMaxNum(remaining, itemId) {
					$(document).find('.receiving_' + itemId).each(function() {
						let maxNum = parseInt(remaining) + parseInt($(this).val());
						$(this).attr("max", maxNum);
					});
				}
				setMinNum(remaining, itemId) {
					$(document).find('.receiving_' + itemId).each(function() {
						let minNum = parseInt(remaining) + parseInt($(this).val());
						$(this).attr("min", minNum);
					});
				}

				makeRegData() {
					let regObj = {};
					let flg = true;
					let regex = /^[0-9a-zA-Z]+$/;
					let tmp = this;
					Object.keys(this.phpData['phpItemsData']).forEach(function (inHPItemKey) {
						let i = 1;
						regObj[inHPItemKey] = {};
						$(document).find(".receiving_" + inHPItemKey).each(function() {
							let rowNum = parseInt($(this).val());
							if (rowNum != 0) {
								let rowLotNum = $(this).parents("tr").find(".lot_" + inHPItemKey).val();
								let rowLotDate = $(this).parents("tr").find(".lotDate_" + inHPItemKey).val();

								if (rowLotNum && rowLotDate) {
									let lotKey = rowLotNum + rowLotDate;
									let temp = Object.entries(regObj[inHPItemKey]);
									let chkLotDup = temp.findIndex(([id, data]) => data.lotNumber == rowLotNum && data.lotDate == rowLotDate);
									if (chkLotDup === -1) {
										regObj[inHPItemKey][lotKey] = {};
										regObj[inHPItemKey][lotKey]["inHPItemid"] = inHPItemKey;
										regObj[inHPItemKey][lotKey]["orderCNumber"] = tmp.phpData['phpItemsData'][inHPItemKey]["orderCNumber"];
										regObj[inHPItemKey][lotKey]["quantity"] = tmp.phpData['phpItemsData'][inHPItemKey]["quantity"];
										regObj[inHPItemKey][lotKey]["price"] = tmp.phpData['phpItemsData'][inHPItemKey]["price"];
										regObj[inHPItemKey][lotKey]["lotNumber"] = rowLotNum;
										regObj[inHPItemKey][lotKey]["lotDate"] = rowLotDate;
										regObj[inHPItemKey][lotKey]["receivingCount"] = rowNum;
										i++;
									} else {
										regObj[inHPItemKey][lotKey]["receivingCount"] = parseInt(regObj[inHPItemKey][lotKey]["receivingCount"]) + rowNum;
									}
								}
						
								if (!rowLotNum && !rowLotDate) {
									if (regObj[inHPItemKey][0] === void 0) {
										regObj[inHPItemKey][0] = {};
										regObj[inHPItemKey][0]["inHPItemid"] = inHPItemKey;
										regObj[inHPItemKey][0]["orderCNumber"] = tmp.phpData['phpItemsData'][inHPItemKey]["orderCNumber"];
										regObj[inHPItemKey][0]["quantity"] = tmp.phpData['phpItemsData'][inHPItemKey]["quantity"];
										regObj[inHPItemKey][0]["price"] = tmp.phpData['phpItemsData'][inHPItemKey]["price"];
										regObj[inHPItemKey][0]["receivingCount"] = rowNum;
									} else {
										regObj[inHPItemKey][0]["receivingCount"] = parseInt(regObj[inHPItemKey][0]["receivingCount"]) + rowNum;
									}
								}
						
								if ((!rowLotNum && rowLotDate) || (rowLotNum && !rowLotDate)) {
									flg = false;
								}
								if ((!regex.test(rowLotNum)) || (encodeURI(rowLotNum).replace(/%../g, "*").length > 20)) {
									flg = false;
								}
							}
						});
					});
					if (!flg) { regObj = {}; }
					return regObj;
				}
				

			}

			let phpData = {
				'phpItemsData' : JSON.parse('<?php echo json_encode($ItemsToJs); ?>'),
				'slipDetailPattern' : '<?php echo $result["pattern"] ?>',
				'backToLink': '<?php echo $link ?>',
				'divisionIdCrypt' : '<?php echo $divisionIdCrypt ?>',
				'authKeyCrypt' : '<?php echo $authKeyCrypt ?>'
			};
			let order_slip_detail = new OrderSlipDetail(phpData);
    	</script>
  </body>
</html>