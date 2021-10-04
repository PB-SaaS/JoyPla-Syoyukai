<?php
include_once 'NewJoyPla/lib/ApiSpiral.php';
include_once "NewJoyPla/lib/Define.php";
include_once 'NewJoyPla/lib/SpiralDataBase.php';
include_once 'NewJoyPla/lib/UserInfo.php';
include_once 'NewJoyPla/api/RegGoodsBilling.php';
include_once 'NewJoyPla/api/RegInventoryTrdb.php';
include_once 'NewJoyPla/api/GetStock.php';
include_once 'NewJoyPla/api/GetHospitalData.php';

$userInfo = new App\Lib\UserInfo($SPIRAL);

$spiralApiCommunicator = $SPIRAL->getSpiralApiCommunicator();
$spiralApiRequest = new SpiralApiRequest();
$spiralDataBase = new App\Lib\SpiralDataBase($SPIRAL,$spiralApiCommunicator,$spiralApiRequest);

$regGoodsBilling = new App\Api\RegGoodsBilling($spiralDataBase,$userInfo);

$billingData = array();
if(isset($_POST['billing'])){
	$billingData = $_POST['billing'];
}
foreach($billingData as $array) {
	foreach($array as $billingRecord) {
		if ($billingRecord['lotNumber']) {
			if ((!ctype_alnum($billingRecord['lotNumber'])) || (strlen($billingRecord['lotNumber']) > 20)) {
				echo json_encode(array('result'=>'invalid lotNumber'));
				exit;
			}
		}
	}
}

$getHospitalData = new App\Api\GetHospitalData($spiralDataBase,$userInfo);
$hospitalData = $getHospitalData->select();
$useUnitPrice = $hospitalData['data'][0]['billingUnitPrice'];

$divisionId = '';
if(isset($_POST['divisionId'])){
	$divisionId = $_POST['divisionId'];
}

$result = $regGoodsBilling->register($billingData,$divisionId,$useUnitPrice);

if($result != true){
	echo json_encode(array('result'=>$result));
	exit;
}

$billingItemData = array();
foreach ($billingData as $posts) {
	foreach ($posts as $val) {
		if (array_key_exists($val['recordId'], $billingItemData)) {
			$billingItemData[$val['recordId']]['countNum'] += (int)$val['countNum'];
		} else {
			$billingItemData[$val['recordId']] = [];
			$billingItemData[$val['recordId']] = ['countNum' => (int)$val['countNum']];
		}
	}
}

$getStock = new App\Api\GetStock($spiralDataBase,$userInfo);
$stockData = $getStock->getStockData($billingData,$divisionId);

if($stockData['code'] != '0'){
	echo json_encode(array('result'=>$result));
	exit;
}


foreach($billingItemData as $inHpitemId => $item){
	$checkFlg = false;
	foreach($stockData['data'] as $stock){
		if($inHpitemId == $stock[0]){
			$num = (int)$stock[1] - (int)$billingItemData[$inHpitemId]['countNum'];
			if($num > 0){
				$num = 0;
			}
			$billingItemData[$inHpitemId]['countNum'] = (int)$billingItemData[$inHpitemId]['countNum'] + $num;
			$checkFlg = true;
		}
	}
	if($checkFlg == false){
		$billingItemData[$inHpitemId]['countNum'] = 0;
	}
}

$regInventoryTrdb = new App\Api\RegInventoryTrdb($spiralDataBase,$userInfo);
$result = $regInventoryTrdb->orderCount($billingItemData,$divisionId,'2');
if(! $result){
	echo json_encode(array('result'=>$result));
	exit;
}

//ロット管理情報
$lotData = [];

foreach ($billingData as $rows) {
	foreach ($rows as $key => $data) {
		if ($data['lotNumber'] && $data['lotDate']) {
			$lotData[$data['recordId'].$key] = [];
			$lotData[$data['recordId'].$key] = [
				'inHPItemid' => $data['recordId'],
				'lotNumber' => $data['lotNumber'],
				'lotDate' => $data['lotDate'],
				'stockQuantity' => (int)$data['countNum']
			];
		}
	}
}

if($lotData) { 
	$result = $regInventoryTrdb->lotData($lotData,$divisionId,'2'); //在庫数減算

	if(! $result) {
		echo json_encode(array('result'=>$result));
		exit;
	}
}

//結果を返却
echo json_encode(array('result'=>$result));

