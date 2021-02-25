
<?php
$pattern = '';
if(isset($_POST['pattern']) && $_POST['pattern'] != ''){
	$pattern = $_POST['pattern'];
}

if($pattern === ''){

	include_once 'NewJoyPla/lib/Func.php';

	$itemsData = array();
	$distributorName = "";

	if(isset($_POST['itemsData']) && $_POST['itemsData'] != ""){
		$itemsData = \App\Lib\requestUrldecode($_POST['itemsData']);
	}
	if(isset($_POST['distributorName']) && $_POST['distributorName'] != ""){
		$distributorName = App\Lib\html($_POST['distributorName']);
	}
	$nowTime = date('Y年m月d日 H時i分s秒');

	?>

	<!DOCTYPE html>
	<html>
	<head>
		<title>JoyPla 入庫ラベル発行</title>
		<?php include_once 'NewJoyPla/src/Head.php'; ?>
		<style>
			.printarea{
				width:80mm;
				height:auto;
				font-size:12px;
				border: 1px solid gray;
				padding: 5px;
			}
			.font-size-16{
				font-size:16px;
			}
			
			@media print{
				.printarea{
					border: none;
					padding: 0px;
				}
			}
		</style>

	</head>
	<body>
		<?php include_once 'NewJoyPla/src/HeaderForMypage.php'; ?>
		<div class="animsition uk-margin-bottom" uk-height-viewport="expand: true">
			<div class="uk-section uk-section-default uk-preserve-color uk-padding-remove" id="page_top">
				<div class="uk-container uk-container-expand">
					<h1 class="no_print">入庫ラベル発行</h1>
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
						if($item['totalReturnCount'] == null){
							$item['totalReturnCount'] = 0;
						}
						$quantity = '';
						if((int)$item['quantity'] >= 10000 ){
							$quantity = '9999';
						} else if((int)$item['quantity'] < 1 ){
							$quantity = str_pad(1 , 4, "0", STR_PAD_LEFT);
						} else {
							$quantity = str_pad((int)$item['quantity'] , 4, "0", STR_PAD_LEFT);
						}
						
						for($rnum = 1 ; $rnum <= (int)$item['receivingCount'] - (int)$item['totalReturnCount']; $rnum++){
							//$barcodeId = "01".$inHPId.$item['quantity'];
							$barcodeId = "01".$item["labelId"].$quantity;
							echo "<div class='printarea'>";
							echo "<span>".$distributorName."</span><br>";
							echo "<span>メーカー名：".$item['makerName']."</span><br>";
							echo "<span>商品名：".$item['itemName']."</span><br>";
							echo "<span>規格：".$item['itemStandard']."</span><br>";
							echo "<span>商品コード：".$item['itemCode']."</span> ";
							echo "<span>入数：".$item['quantity'].$item['quantityUnit']."</span><br>";
							echo "<span>".$nowTime."</span><br>";
							echo "<div class='uk-text-center' id='barcode_".$num."'>".$barcodeId."</div>";
							echo "</div>";
							$num ++ ;
						}
					}
					?>
					</div>
				</div>
				
			</div>
		</div>
		<script>
			$(function(){
			let count = $('#createLabel').children().length;
			let num ;
			for(let i = 1 ; i <= count ; i++){
				num = $('#barcode_' + i).text();
				$('#barcode_' + i).html('<svg id="barcode_area_'+i+'"></svg>');
				generateBarcode('barcode_area_' + i,num);
				//$('td#barcode_' + i +' div').barcode(num, "ean13",{barWidth: 2 ,barHeight: 40 , output: 'css'});
			}
			});
		</script>
	</body>
	</html>
<?php
} else if($pattern === 'payout'){

	include_once 'NewJoyPla/lib/Func.php';
	include_once 'NewJoyPla/lib/ApiSpiral.php';
	include_once "NewJoyPla/lib/Define.php";
	include_once 'NewJoyPla/lib/SpiralDataBase.php';
	include_once 'NewJoyPla/lib/UserInfo.php';
	include_once 'NewJoyPla/api/GetStock.php';

	$userInfo = new App\Lib\UserInfo($SPIRAL);

	$spiralApiCommunicator = $SPIRAL->getSpiralApiCommunicator();
	$spiralApiRequest = new SpiralApiRequest();
	$spiralDataBase = new App\Lib\SpiralDataBase($SPIRAL,$spiralApiCommunicator,$spiralApiRequest);
	
	$itemsData = array();
	
	if(isset($_POST['itemsData']) && $_POST['itemsData'] != ""){
		$itemsData = \App\Lib\requestUrldecode($_POST['itemsData']);
	}

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


	?>

	<!DOCTYPE html>
	<html>
	<head>
		<title>JoyPla 払出ラベル発行</title>
		<?php include_once 'NewJoyPla/src/Head.php'; ?>
		<style>
			.printarea{
				width:80mm;
				height:auto;
				font-size:12px;
				border: 1px solid gray;
				padding: 5px;
			}
			.font-size-16{
				font-size:16px;
			}
			
			@media print{
				.printarea{
					border: none;
					padding: 0px;
				}
			}
		</style>
	</head>
	<body>
		<?php include_once 'NewJoyPla/src/HeaderForMypage.php'; ?>
		<div class="animsition uk-margin-bottom" uk-height-viewport="expand: true">
			<div class="uk-section uk-section-default uk-preserve-color uk-padding-remove" id="page_top">
				<div class="uk-container uk-container-expand">
					<h1 class="no_print">払出ラベル発行</h1>
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
						if((int)$item['quantity'] >= 10000 ){
							$quantity = '9999';
						} else if((int)$item['quantity'] < 1 ){
							$quantity = str_pad(1 , 4, "0", STR_PAD_LEFT);
						} else {
							$quantity = str_pad((int)$item['quantity'] , 4, "0", STR_PAD_LEFT);
						}
						
						for($rnum = 1 ; $rnum <= (int)$item['printCount']; $rnum++){
							//$barcodeId = "01".$inHPId.$item['quantity'];
							$barcodeId = "01".$item["labelId"].$quantity;
							echo "<div class='printarea'>";
							echo "<div>";
							echo "<b class='font-size-16'>".$item['itemName']."</b>";
							echo "<div class='uk-child-width-1-2' uk-grid>";
							echo "<div>";
							echo "<span>".$item['maker']."</span><br>";
							echo "<span>".$item['catalogNo']." ".$item['itemStandard']."</span><br>";
							echo "<span>".$inHPId."</span><br>";
							echo "</div>";
							echo "<div class='uk-text-right uk-padding-remove'>";
							if($item['sourceDivisionName'] != ''){	
								echo "<b>".$item['sourceDivisionName']."</b> <span>元棚番:".$item['sourceRackName']."</span><br>";
							}
							echo "<b>".$item['divisionName']."</b> <span>払出棚番:".$item['rackName']."</span><br>";
							echo "<span>定数:".$item['constantByDiv'].$item['unit']."</span><br>";
							echo "<span>入数:".$item['quantity'].$item['unit']."</span><br>";
							echo "</div>";
							echo "</div>";
							echo "<div class='uk-text-center' id='barcode_".$num."'>".$barcodeId."</div>";
							echo "<div class='uk-text-right'>".$item['distributorName']."</div>";
							echo "</div>";
							echo "</div>";
							$num ++ ;
						}
					}
					?>
					</div>
				</div>
			</div>
		</div>
		<script>
			$(function(){
			let count = $('#createLabel').children().length;
			let num ;
			for(let i = 1 ; i <= count ; i++){
				num = $('#barcode_' + i).text();
				$('#barcode_' + i).html('<svg id="barcode_area_'+i+'"></svg>');
				generateBarcode('barcode_area_' + i,num);
				//$('td#barcode_' + i +' div').barcode(num, "ean13",{barWidth: 2 ,barHeight: 40 , output: 'css'});
			}
			});
		</script>
	</body>
	</html>
	<?php
}




?>