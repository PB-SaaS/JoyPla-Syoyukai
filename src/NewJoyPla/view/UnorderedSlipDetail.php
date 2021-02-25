<?php
include_once 'NewJoyPla/lib/ApiSpiral.php';
include_once "NewJoyPla/lib/Define.php";
include_once "NewJoyPla/lib/Func.php";
include_once "NewJoyPla/lib/SpiralDataBase.php";
include_once "NewJoyPla/lib/UserInfo.php";
include_once "NewJoyPla/api/UpdateUnordered.php";
include_once "NewJoyPla/api/GetCardInfo.php";
include_once "NewJoyPla/api/GetOrderItems.php";

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
$orderItems = $getOrderItems->select($card["data"][0][0],"orderNumber","makerName","itemName","itemCode","itemStandard","price","quantity","orderQuantity","orderPrice","itemJANCode","quantityUnit","itemUnit","orderCNumber","inHospitalItemId");
$orderItems = $spiralDataBase->arrayToNameArray($orderItems["data"],array("orderNumber","makerName","itemName","itemCode","itemStandard","price","quantity","orderQuantity","orderPrice","itemJANCode","quantityUnit","itemUnit","orderCNumber","inHospitalItemId"));

//,"makerName","itemName","itemCode","itemStandard","price","quantity","orderQuantity","orderPrice","itemJANCode"

$updateUnordered = new App\Api\UpdateUnordered($spiralDataBase);
$result = $updateUnordered->update($card["data"][0][0]);

$ItemsToJs = array();
foreach($orderItems as $record){
	$ItemsToJs[$record["inHospitalItemId"]] = array(
		"countNum"=> $record["orderQuantity"],
		"quantity" => $record["quantity"]
		);
	
}

$crypt   = $SPIRAL->getSpiralCryptOpenSsl();
$authKeyCrypt = $crypt->encrypt($card["data"][0][2], "JoyPla");


if($userInfo->getUserPermission() == "1"){
	$link = '%url/table:back%';
}else{
	$link = '%url/rel:mpgt:page_266849%';
} 
?> 
<!DOCTYPE html>
<html>
  <head>
    <title>JoyPla 未発注書</title>
	<?php include_once "NewJoyPla/src/Head.php"; ?>
  </head>

  <body>

	<?php if($result["pattern"] == "delete" || $card["data"][0][1] == "2"){ ?>
		<script>
			UIkit.modal.alert("発注商品が0件となりました。<br>未発注書一覧へ戻ります。").then(function(){
				location.href ="<?php echo $link ?>&table_cache=true";
			});
		</script>
	<?php exit; } ?>


    <?php include_once "NewJoyPla/src/HeaderForMypage.php"; ?>
    <div class="animsition uk-margin-bottom" uk-height-viewport="expand: true">
	  	<div class="uk-section uk-section-default uk-preserve-color uk-padding-remove uk-margin-top" id="page_top">
		    <div class="uk-container uk-container-expand">
		    	<ul class="uk-breadcrumb no_print">
					<li><a href="%url/rel:mpg:top%">TOP</a></li>
				    <li><a href="<?php echo $link ?>&table_cache=true"><span>未発注書一覧</span></a></li>
				    <li><span>未発注書</span></li>
				</ul>
				<div class="no_print uk-margin" uk-margin>
					<input class="print_hidden uk-button uk-button-default uk-margin-small-top" type="submit" value="印刷プレビュー" onclick="window.print();return false;">
					<input class="print_hidden uk-button uk-button-danger uk-margin-small-top" type="submit" value="発注伝票取消" onclick="orderedDelete();return false;">
					<?php if($userInfo->getUserPermission() == "1"): ?>
					<input class="print_hidden uk-button uk-button-primary uk-margin-small-top" type="submit" id="orderFixButton" value="発注確定" onclick="orderFix();return false;">
					<?php endif; ?>
				</div>
		    	<div class="uk-text-center uk-text-large">
		    		<p class="uk-text-bold" style="font-size: 32px">（ 未 ）発　　注　　書</p>
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
			    			<table class="uk-table uk-width-1-1 uk-table-responsive uk-table-divider">
			    				<tr>
			    					<td>発注日時</td>
			    					<td> --- </td>
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
		    	
		    	<div class="uk-margin" id="tablearea">
		    		<form>
			    		<div class="no_print">
			    			<input class="print_hidden uk-button uk-button-primary" type="button" value="更新" onclick="itemlistUpdate();return false;">
			    			<input class="print_hidden uk-button uk-button-default" type="reset" value="リセット">
			    			<input class="print_hidden uk-button uk-button-danger" type="button" value="削除" onclick="itemlistDelete();return false;">
			    		</div>
			    		<div class="uk-overflow-auto">
				    		<table class="uk-table uk-table-hover uk-table-middle uk-table-divider uk-text-nowrap" id="tbl-Items">
				    			<thead>
				    				<tr>
										<th>NO</th>
										<th class="no_print"><input type="checkbox" onclick="allItems(this.checked)" class="uk-checkbox"></th>
										<th style="min-width:150px">メーカー</a></th>
										<th style="min-width:150px">商品名</a></th>
										<th>製品コード</a></th>
										<th>規格</a></th>
										<th>価格</a></th>
										<th>入数</a></th>
										<th style="min-width:150px;max-width: 180px;">数量</a></th>
										<th>金額</a></th>
				    				</tr>
				    			</thead>
				    			<tbody>
				    				<?php
										$num = 1;
										foreach($orderItems as $record){
	  										$barcodeId = "01".$record["inHospitalItemId"]."X".$record["quantity"];
				    						echo "<tr>";
				    						echo "<td>".$num."</td>";
				    						echo "<td class='no_print'><input type='checkbox' class='uk-checkbox itemsCheckBox' name='check".$num."'></td>";
				    						echo "<td>".$record["makerName"]."</td>";
				    						echo "<td>".$record["itemName"]."</td>";
				    						echo "<td>".$record["itemCode"]."</td>";
				    						echo "<td>".$record["itemStandard"]."</td>";
				    						echo "<td>￥".$record["price"]."<span class='uk-text-small'> / 1".$record["itemUnit"]."</span></td>";
				    						echo "<td>".$record["quantity"]."<span class='uk-text-small'>".$record["quantityUnit"]."</span></td>";
				    						echo "<td><input type='number' step='1' class='uk-input' style='color:#444444;width:100px;' onchange='active(this,".$num.")' min='1' value='".$record["orderQuantity"]."'><span class='uk-text-small uk-text-middle'>".$record["itemUnit"]."</span></td>";
				    						echo "<td>￥<script>price('".$record["orderPrice"]."')</script></td>";
				    						echo "<td style='display:none'>".$record["orderCNumber"]."</td>";
				    						echo "</tr>";
				    						$num++;
				    					}
				    				?>
				    			</tbody>
				    			
				    		</table>
				    	</div>
			    	
		    		</form>
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
		    						<textarea class="uk-textarea uk-width-1-1" rows="5" name="ordercomment" placeholder="コメント...">%val:usr:ordercomment%</textarea>
									<input class="print_hidden uk-button uk-button-primary uk-align-center" type="button" value="コメントを更新" onclick="commentUpdate();return false;">
								</td>
							</tr>
		    			</tbody>
		    		</table>
		    	</div>
		    </div>
		</div>
	</div>
    <script>
		let canAjax = true;
    	let upToData = {};
		$(function(){
		 
		 let order_num = $("#hacchu_num").text();
		 //$("#hacchu_num").remove();
		 $("#order_barcode").html("<svg id='barcode_hacchu'></svg>");
		 generateBarcode("barcode_hacchu",order_num);
		 //$("td#order_barcode div").barcode({code:order_num, crc:false }, "int25",{barWidth: 3 ,barHeight: 40 , output: "css"});
		});
		
    	
    	function active(elm,num){
    		elm.style.backgroundColor = "rgb(255, 204, 153)";
    		$("#tbl-Items tbody input[type='checkbox'][name='check"+num+"']").prop("checked", true);
    		$("#orderFixButton").prop("disabled", true);
    	}
    	
    	function allItems(checked){
    		$("#tbl-Items tbody input[type='checkbox']").prop("checked", checked);
    	}
		
		function itemlistUpdate(){
			if(!canAjax) { 
				console.log('通信中');
				return;
			}
			$("#tbl-Items tbody tr").map(function () {
				let td = $(this).children();
				if($(td[1]).children("input")[0].checked ){
					upToData[$(td[10]).text()] = {
						num: $(td[8]).children("input").val(),
					};
				} else {
					delete upToData[$(td[11]).text()] ;
				}
			});
			if(Object.keys(upToData).length == 0){
				UIkit.modal.alert("更新対象がありません").then(function(){
					loading_remove();
				});
				return
			}
			UIkit.modal.confirm(Object.keys(upToData).length+"件更新します。<br>よろしいですか").then(function () {
                loading();
				canAjax = false; // これからAjaxを使うので、新たなAjax処理が発生しないようにする
				$.ajax({
					async: false,
	                url:"%url/card:page_267235%",
	                type:"POST",
	                data:{data:JSON.stringify(upToData)},
	                dataType: "json"
		        })
	            // Ajaxリクエストが成功した時発動
	            .done( (data) => {
					if(! data.result){
						UIkit.modal.alert("更新に失敗しました").then(function(){
							canAjax = true; // 再びAjaxできるようにする
						});
						return false;
					}
	            	
	        		UIkit.modal.alert("更新が完了しました").then(function(){
	        			location.reload();
					});
	            })
	            .fail( (data) => {
	        		UIkit.modal.alert("更新に失敗しました").then(function(){
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
		
		function itemlistDelete(){
			if(!canAjax) { 
				console.log('通信中');
				return;
			}
			$("#tbl-Items tbody tr").map(function () {
				let td = $(this).children();
				if($(td[1]).children("input")[0].checked){
					upToData[$(td[10]).text()] = {
						num: $(td[8]).children("input").val(),
					};
				} else {
					delete upToData[$(td[10]).text()] ;
				}
			});
			if(Object.keys(upToData).length == 0){
				UIkit.modal.alert("削除対象がありません");
				return
			}
			UIkit.modal.confirm(Object.keys(upToData).length+"件削除します。<br>よろしいですか").then(function () {
                loading();
				canAjax = false; // これからAjaxを使うので、新たなAjax処理が発生しないようにする
				$.ajax({
					async: false,
	                url:"%url/card:page_267236%",
	                type:"POST",
	                data:{data:JSON.stringify(upToData)},
	                dataType: "json"
		        })
	            // Ajaxリクエストが成功した時発動
	            .done( (data) => {
					if(! data.result){
						UIkit.modal.alert("削除に失敗しました").then(function(){
							canAjax = true; // 再びAjaxできるようにする
						});
						return false;
					}
	            	
	        		UIkit.modal.alert("削除が完了しました").then(function(){
						location.reload();
					});
	            })
	            .fail( (data) => {
	        		UIkit.modal.alert("削除に失敗しました").then(function(){
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

		function commentUpdate(){
			if(!canAjax) { 
				console.log('通信中');
				return;
			}
			UIkit.modal.confirm("コメントを更新します。<br>よろしいですか").then(function () {
            	loading();
				canAjax = false; // これからAjaxを使うので、新たなAjax処理が発生しないようにする
				$.ajax({
					async: false,
	                url:"%url/card:page_162483%",
	                type:"POST",
	                data:{
                		orderAuthKey: "<?php echo $authKeyCrypt ?>" ,
	                	ordercomment: encodeURI($("textarea[name='ordercomment']").val()),
	                },
	                dataType: "json"
		        })
	            // Ajaxリクエストが成功した時発動
	            .done( (data) => {
					if(! data.result){
						UIkit.modal.alert("コメントに失敗しました").then(function(){
							canAjax = true; // 再びAjaxできるようにする
						});
						return false;
					}
	        		UIkit.modal.alert("コメントを更新しました").then(function(){
						location.reload();
					});
	            })
	            .fail( (data) => {
	        		UIkit.modal.alert("コメントの更新に失敗しました").then(function(){
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
		<?php if($userInfo->getUserPermission() == "1"): ?>
		function orderFix(){
			if(!canAjax) { 
				console.log('通信中');
				return;
			}
			let itemsToJs = objectValueToURIencode( <?php echo json_encode($ItemsToJs); ?> );
			UIkit.modal.confirm("発注を確定します。<br>よろしいですか").then(function () {
            	loading();
				canAjax = false; // これからAjaxを使うので、新たなAjax処理が発生しないようにする
				$.ajax({
					async: false,
	                url:"%url/card:page_267202%",
	                type:"POST",
	                data:{
                		orderAuthKey: "<?php echo $authKeyCrypt ?>" ,
	                	order:JSON.stringify(itemsToJs),
	                	ordercomment: encodeURI($("textarea[name='ordercomment']").val()),
	                },
	                dataType: "json"
		        })
	            // Ajaxリクエストが成功した時発動
	            .done( (data) => {
					if(! data.result){
						UIkit.modal.alert("発注書確定に失敗しました").then(function(){
							canAjax = true; // 再びAjaxできるようにする
						});
						return false;
					}
	        		UIkit.modal.alert("発注を確定しました").then(function(){
						UIkit.modal.alert("一覧表へ戻ります").then(function(){
							location.href ="<?php echo $link ?>&table_cache=true";
						});
					});
	            })
	            .fail( (data) => {
	        		UIkit.modal.alert("発注に失敗しました").then(function(){
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
		<?php endif; ?>
		
		function orderedDelete(){
			if(!canAjax) { 
				console.log('通信中');
				return;
			}
			UIkit.modal.confirm("発注書を削除します。<br>よろしいですか").then(function () {
				let itemsToJs = objectValueToURIencode( <?php echo json_encode($ItemsToJs); ?> );
				loading();
				canAjax = false; // これからAjaxを使うので、新たなAjax処理が発生しないようにする
				$.ajax({
					async: false,
					url:"%url/card:page_267227%",
					type:"POST",
					data:{
						orderAuthKey: "<?php echo $authKeyCrypt ?>" ,
						orderData : JSON.stringify( objectValueToURIencode(itemsToJs) ),
						divisionId : "%val:usr:divisionId%",
						countFlg : null
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
			},function(){
				UIkit.modal.alert("中止します");
			});
		}
    </script>
  </body>
</html>