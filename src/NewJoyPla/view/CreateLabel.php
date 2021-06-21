<?php

$pattern = '';
$inHpItemMaster = array();

if(isset($_POST['pattern']) && $_POST['pattern'] != ''){
	$pattern = $_POST['pattern'];
}

$nowTime = date('Y年m月d日 H時i分s秒');

$title = '入庫';

include_once 'NewJoyPla/api/GetInHPItems.php';
include_once 'NewJoyPla/lib/Func.php';
include_once 'NewJoyPla/lib/ApiSpiral.php';
include_once 'NewJoyPla/lib/SpiralDataBase.php';
include_once 'NewJoyPla/lib/UserInfo.php';
include_once 'NewJoyPla/api/GetHospitalData.php';
include_once "NewJoyPla/lib/Define.php";
include_once 'NewJoyPla/api/GetStock.php';

$userInfo = new App\Lib\UserInfo($SPIRAL);

$spiralApiCommunicator = $SPIRAL->getSpiralApiCommunicator();
$spiralApiRequest = new \SpiralApiRequest();
$spiralDataBase = new App\Lib\SpiralDataBase($SPIRAL,$spiralApiCommunicator,$spiralApiRequest);

$getHospitalData = new App\Api\GetHospitalData($spiralDataBase,$userInfo);
$hospitalData = $getHospitalData->select();


$itemsData = array();

if(isset($_POST['itemsData']) && $_POST['itemsData'] != ""){
	$itemsData = \App\Lib\requestUrldecode($_POST['itemsData']);
}

$getInHPItems = new App\Api\GetInHPItems($spiralDataBase,$userInfo);

foreach($itemsData as $inHPId => $item){
	$getInHPItems->setInHospitalItemId($inHPId);
}

$inHpItemMaster = $getInHPItems->getInHPItems();

if($pattern === 'payout'){
	$title = '払出';
	
	$sourceDivision = "";

	if(isset($_POST['sourceDivision']) && $_POST['sourceDivision'] != ""){
		$sourceDivision = \App\Lib\html($_POST['sourceDivision']);
	}

	$getStock = new App\Api\GetStock($spiralDataBase,$userInfo);
	$stockData = $getStock->getStockData($itemsData,$sourceDivision);
	foreach($stockData['data'] as $stockRecord){
		$itemsData[$stockRecord[0]]['sourceDivisionName'] = $stockRecord[3];
		$itemsData[$stockRecord[0]]['sourceRackName'] = $stockRecord[2];
	}

	$targetDivision = "";

	if(isset($_POST['targetDivision']) && $_POST['targetDivision'] != ""){
		$targetDivision = \App\Lib\html($_POST['targetDivision']);
	}
	if($targetDivision != ''){
		$getStock = new App\Api\GetStock($spiralDataBase,$userInfo);
		$stockData = $getStock->getStockData($itemsData,$targetDivision);
		foreach($stockData['data'] as $stockRecord){
			$itemsData[$stockRecord[0]]['divisionName'] = $stockRecord[3];
			$itemsData[$stockRecord[0]]['rackName'] = $stockRecord[2];
			$itemsData[$stockRecord[0]]['constantByDiv'] = $stockRecord[4];
			if($itemsData[$stockRecord[0]]['distributorName'] == ''){
				$itemsData[$stockRecord[0]]['distributorName'] = $stockRecord[5];
			}
			
		}
	}

$original_design = <<<EOM
	<div class='printarea uk-margin-remove'>
		<div>
			<b class='font-size-16'>%JoyPla:itemName%</b>
			<div class='uk-child-width-1-2' uk-grid>
				<div class=''>
					<span>%JoyPla:itemMaker%</span><br>
					<span>%JoyPla:catalogNo% %JoyPla:itemStandard%</span><br>
					<span>%JoyPla:inHPId%</span><br>
				</div>
				<div class='uk-text-right uk-padding-remove'>
					<b>%JoyPla:sourceDivisionName%</b> <span>元棚番:%JoyPla:sourceRackName%</span><br>
					<b>%JoyPla:divisionName%</b> <span>払出棚番:%JoyPla:rackName%</span><br>
					<span>定数:%JoyPla:constantByDiv%%JoyPla:quantityUnit%</span><br>
					<span class='uk-text-bold' style='font-size:1.25em'>入数:%JoyPla:quantity%%JoyPla:quantityUnit%</span><br>
				</div>
			</div>
			<div class='uk-text-center' id='barcode_%JoyPla:num%'>%JoyPla:barcodeId%</div>
			<div class='uk-text-right'>%JoyPla:distributorName%</div>
		</div>
	</div>
EOM;
	if($hospitalData['data'][0]['labelDesign2'] != ''){
		$original_design = htmlspecialchars_decode($hospitalData['data'][0]['labelDesign2']);
	}
	
} else {

	
	$distributorName = "";

	if(isset($_POST['distributorName']) && $_POST['distributorName'] != ""){
		$distributorName = \App\Lib\html($_POST['distributorName']);
	}
	
	//配列の修正
	foreach($itemsData as $key => $item){
		if($item['makerName'] != ''){
			$itemsData[$key]['itemMaker'] = $item['makerName'];
		}	
		if($distributorName != ''){
			$itemsData[$key]['distributorName'] = $distributorName;
		}	
	}
$original_design = <<<EOM
	<div class="printarea uk-margin-remove">
		<span>%JoyPla:distributorName%</span><br>
		<span>メーカー名：%JoyPla:itemMaker%</span><br>
		<span>商品名：%JoyPla:itemName%</span><br>
		<span>規格：%JoyPla:itemStandard%</span><br>
		<span>商品コード：%JoyPla:itemCode%</span>
		<span>入数：%JoyPla:quantity%%JoyPla:quantityUnit%</span><br>
		<span>%JoyPla:nowTime%</span><br>
		<div class="uk-text-center" id="barcode_%JoyPla:num%">%JoyPla:barcodeId%</div>
	</div>
EOM;
	if($hospitalData['data'][0]['labelDesign1'] != ''){
		$original_design = htmlspecialchars_decode($hospitalData['data'][0]['labelDesign1']);
	}
}


?>
<!DOCTYPE html>
	<html>
	<head>
		<title>JoyPla<?= $title  ?>ラベル発行</title>
		<?php include_once 'NewJoyPla/src/Head.php'; ?>
		<style>
		@media print{
			body{
				zoom: 1 !important; /* Equal to scaleX(0.7) scaleY(0.7) */
			}
		}
		html {
			color: #000;
		}
		#createLabel{
			font-size:0;
		}
		.a4area{
			break-after: always;
		}
		.printarea{
			vertical-align: top;
		}
		.printarea{
			font-size:12px;
			border: 1px solid gray;
			padding: 5px;
		}
		.font-size-16{
			font-size:16px;
		}
		</style>
	</head>
	<body>
		<?php include_once 'NewJoyPla/src/HeaderForMypage.php'; ?>
		<div class="animsition uk-margin-bottom" uk-height-viewport="expand: true">
			<div class="uk-section uk-section-default uk-preserve-color uk-padding-remove" id="page_top">
				<div class="uk-container uk-container-expand">
					<h1 class="no_print">
						<?= $title  ?>ラベル発行
					</h1>
					<div class="no_print">
					<form class="uk-form-stacked uk-child-width-1-2 uk-width-1-2@m" uk-grid>
						<div>
							<div class="uk-margin">
								<label class="uk-form-label">印刷タイプ</label>
								<div class="uk-form-controls">
									<select name="printType" class="uk-select">
										<option value="1">ラベルプリンター</option>
										<option value="2">A4印刷</option>
									</select>
								</div>
							</div>
							<div class="uk-margin">
								<label class="uk-form-label">バーコードの高さ</label>
								<div class="uk-form-controls">
									<input type="number" name="barcodeHeight" value="" class="uk-input uk-width-2-3"><span class="uk-text-bottom">px</span>
								</div>
							</div>
						</div>
						<div>
							<div class="uk-margin">
								<label class="uk-form-label">ラベルの幅</label>
								<div class="uk-form-controls">
									<input type="number" name="labelwidth" value="" class="uk-input uk-width-2-3"><span class="uk-text-bottom">mm</span>
								</div>
							</div>
							<div class="uk-margin">
								<label class="uk-form-label">ラベルの最小高さ</label>
								<div class="uk-form-controls">
									<input type="number" name="labelmheight" value="" class="uk-input uk-width-2-3"><span class="uk-text-bottom">mm</span>
								</div>
							</div>
						</div>
						<div class="uk-width-1-1 uk-text-center">
							<button onclick="label_setting()" type="button" class="uk-button uk-button-small ">反映</button>
						</div>

					</form>
					</div>
					<div class="uk-margin-bottom no_print" uk-grid>
						<div class="uk-width-1-2@m">
							<button class="uk-button uk-button-default" type="submit" onclick="window.print();return false;">印刷プレビュー</button>
						</div>
					</div>
					<div id="createLabel">
					<?php 
					$num = 1 ;
					foreach($itemsData as $inHPId => $item){
						$barcodeId = '';
						$quantity = '';
						if($item['totalReturnCount'] == null){
							$item['totalReturnCount'] = 0;
						}
						if((int)$inHpItemMaster[$inHPId]['quantity'] >= 10000 ){
							$quantity = '9999';
						} else if((int)$inHpItemMaster[$inHPId]['quantity'] < 1 ){
							$quantity = str_pad(1 , 4, "0", STR_PAD_LEFT);
						} else {
							$quantity = str_pad((int)$inHpItemMaster[$inHPId]['quantity'] , 4, "0", STR_PAD_LEFT);
						}
						$max = 0;
						if($pattern == 'payout'){
							$max = (int)$item['printCount'];
						} else {
							$max = (int)$item['receivingCount'] - (int)$item['totalReturnCount'];
						}
						for($rnum = 1 ; $rnum <= $max; $rnum++){
							$barcodeId = "01".$item["labelId"].$quantity;
							
							if(($num - 1 )% 2 == 0){
								echo "<div>";
							}
							$officialFlag = '';
							if($inHpItemMaster[$inHPId]['officialFlag'] == '1'){
								$officialFlag = '償還';
							}

							$design = $original_design;
							$design = str_replace('%JoyPla:nowTime%',			$nowTime, 									$design);//バーコードの値
							$design = str_replace('%JoyPla:barcodeId%',			$barcodeId, 								$design);//バーコードの値
							$design = str_replace('%JoyPla:num%',				$num, 										$design);//枚目
							$design = str_replace('%JoyPla:inHPId%',			$inHPId, 									$design);//院内商品ID
							$design = str_replace('%JoyPla:itemName%',			$inHpItemMaster[$inHPId]['itemName'],		$design);//商品名
							$design = str_replace('%JoyPla:itemCode%',			$inHpItemMaster[$inHPId]['itemCode'], 		$design);//製品コードb
							$design = str_replace('%JoyPla:itemStandard%',		$inHpItemMaster[$inHPId]['itemStandard'],	$design);//商品規格
							$design = str_replace('%JoyPla:itemJANCode%',		$inHpItemMaster[$inHPId]['itemJANCode'], 	$design);//JANコードb
							$design = str_replace('%JoyPla:itemUnit%',			$item['itemUnit'], 		$design);//個数単位
							$design = str_replace('%JoyPla:quantity%',			$item['quantity'], 		$design);//入り数
							$design = str_replace('%JoyPla:catalogNo%',			$inHpItemMaster[$inHPId]['catalogNo'], 		$design);//カタログ名
							$design = str_replace('%JoyPla:labelId%',			$inHpItemMaster[$inHPId]['labelId'], 		$design);//ラベルID
							$design = str_replace('%JoyPla:printCount%',		$item['printCount'],						$design);//印刷数
							$design = str_replace('%JoyPla:distributorName%',	$item['distributorName'],					$design);//卸業者名
							$design = str_replace('%JoyPla:itemMaker%',			$inHpItemMaster[$inHPId]['makerName'], 		$design);//メーカー名
							$design = str_replace('%JoyPla:quantityUnit%',		$item['quantityUnit'],	$design);//入数単位
							$design = str_replace('%JoyPla:sourceDivisionName%',$item['sourceDivisionName'],				$design);//払い出し元部署
							$design = str_replace('%JoyPla:sourceRackName%',	$item['sourceRackName'], 					$design);//払い出し元部署棚
							$design = str_replace('%JoyPla:divisionName%',		$item['divisionName'],						$design);//払い出し先部署 
							$design = str_replace('%JoyPla:rackName%',			$item['rackName'], 							$design);//払い出し先部署棚
							$design = str_replace('%JoyPla:constantByDiv%',		$item['constantByDiv'], 					$design);//払い出し先部署定数
							$design = str_replace('%JoyPla:officialFlag%',		$officialFlag,								$design);//償還フラグ

							echo $design;

							if(($num)% 2 == 0){
								echo "</div>";
							}
							$num ++ ;
						}
					}
					?>
					</div>
				</div>
			</div>
		</div>
		<script>
		function label_setting(){
			let label_setting = {
				"barcodeHeight":document.getElementsByName('barcodeHeight')[0].value,
				"labelwidth":document.getElementsByName('labelwidth')[0].value,
				"labelmheight":document.getElementsByName('labelmheight')[0].value,
				"printType":document.getElementsByName('printType')[0].value,
				};
			localStorage.setItem("joypla_LabelCreate", JSON.stringify(label_setting));
			location.reload();
		}
		$(function(){
			let label_setting = JSON.parse(localStorage.getItem("joypla_LabelCreate"));
			if(!label_setting){
				label_setting = {
				"barcodeHeight": 50,
				"labelwidth": 85,
				"labelmheight": 50,
				"printType": 1,
				};
			}

			document.getElementsByName('barcodeHeight')[0].value = label_setting["barcodeHeight"];
			document.getElementsByName('labelwidth')[0].value = label_setting["labelwidth"];
			document.getElementsByName('labelmheight')[0].value = label_setting["labelmheight"];
			document.getElementsByName('printType')[0].value = label_setting["printType"];

			const style = document.createElement('style');
			style.innerHTML = `
			.printarea {
				width:`+label_setting["labelwidth"]+`mm;
				min-height:`+label_setting["labelmheight"]+`mm;
			}
			`;

			if(label_setting["printType"] == '2'){
				style.innerHTML = `
				.printarea {
					width:`+label_setting["labelwidth"]+`mm;
					min-height:`+label_setting["labelmheight"]+`mm;
					display: inline-block;
					break-after: none !important;
				}
				`;
			}
			document.body.appendChild(style);
		});
		$(function(){
			//let count = $('#createLabel').children().length;
			let count = "<?= $num ?>";
			let num ;
			for(let i = 1 ; i <= count ; i++){
				num = $('#barcode_' + i).text();
				$('#barcode_' + i).html('<svg id="barcode_area_'+i+'"></svg>');
				generateBarcodeForLabel('barcode_area_' + i,num);
				//$('td#barcode_' + i +' div').barcode(num, "ean13",{barWidth: 2 ,barHeight: 40 , output: 'css'});
			}
			
			if(document.getElementsByName('printType')[0].value == 2){
				var pointHeight = 0;
				$("#createLabel > div ").each(function(){
					$(".printarea",this).each(function(){
						if(pointHeight < $(this).height() ){
							pointHeight = $(this).height();
						}
					});
					$(".printarea",this).css({ height:pointHeight+"px" });
					pointHeight = 0;
				});
			}
		});
		function generateBarcodeForLabel(idname,value){
			let height = document.getElementsByName('barcodeHeight')[0].value;
			JsBarcode("#"+idname,value,{format: "ITF", width: 1.8, height: height,fontSize: 14});
			//JsBarcode("#"+idname,value,{format: "CODE128", width: 1.8, height: 50,fontSize: 14});
			//JsBarcode("#"+idname,value,{ width: 1.8, height: 50,fontSize: 14});
			//$(elm).barcode(value.replace(/\r?\n/g,"").trim(), btype, settings);
		}
	</script>
	</body>
</html>