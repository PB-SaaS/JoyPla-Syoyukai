
<?php
include_once 'NewJoyPla/lib/ApiSpiral.php';
include_once "NewJoyPla/lib/Define.php";
include_once 'NewJoyPla/lib/SpiralDataBase.php';
include_once 'NewJoyPla/lib/UserInfo.php';
include_once 'NewJoyPla/api/RegGoodsBilling.php';
include_once 'NewJoyPla/api/RegInventoryTrdb.php';
include_once 'NewJoyPla/api/GetStock.php';

$userInfo = new App\Lib\UserInfo($SPIRAL);

$spiralApiCommunicator = $SPIRAL->getSpiralApiCommunicator();
$spiralApiRequest = new SpiralApiRequest();
$spiralDataBase = new App\Lib\SpiralDataBase($SPIRAL,$spiralApiCommunicator,$spiralApiRequest);

$regGoodsBilling = new App\Api\RegGoodsBilling($spiralDataBase,$userInfo);

$billingData = array();
if(isset($_POST['billing'])){
	$billingData = $_POST['billing'];
}
$divisionId = '';
if(isset($_POST['divisionId'])){
	$divisionId = $_POST['divisionId'];
}

$result = $regGoodsBilling->register($billingData,$divisionId);

if($result != true){
	echo json_encode(array('result'=>$result));
	exit;
}

$getStock = new App\Api\GetStock($spiralDataBase,$userInfo);
$stockData = $getStock->getStockData($billingData,$divisionId);

if($stockData['code'] != '0'){
	echo json_encode(array('result'=>$result));
	exit;
}

foreach($billingData as $inHpitemId => $item){
	$checkFlg = false;
	foreach($stockData['data'] as $stock){
		if($inHpitemId == $stock[0]){
			$num = (int)$stock[1] - (int)$billingData[$inHpitemId]['countNum'];
			if($num > 0){
				$num = 0;
			}
			$billingData[$inHpitemId]['countNum'] = (int)$billingData[$inHpitemId]['countNum'] + $num;
			$checkFlg = true;
		}
	}
	if($checkFlg == false){
		$billingData[$inHpitemId]['countNum'] = 0;
	}
}

$regInventoryTrdb = new App\Api\RegInventoryTrdb($spiralDataBase,$userInfo);
$result = $regInventoryTrdb->orderCount($billingData,$divisionId,'2');
//結果を返却
echo json_encode(array('result'=>$result));

