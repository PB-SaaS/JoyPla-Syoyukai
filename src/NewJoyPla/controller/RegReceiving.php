<?php
//入庫データ登録
include_once 'NewJoyPla/lib/ApiSpiral.php';
include_once "NewJoyPla/lib/Define.php";
include_once 'NewJoyPla/lib/SpiralDataBase.php';
include_once 'NewJoyPla/lib/UserInfo.php';
include_once 'NewJoyPla/api/RegReceiving.php';
include_once 'NewJoyPla/api/RegInventoryTrdb.php';
include_once 'NewJoyPla/api/GetDivision.php';
include_once 'NewJoyPla/api/GetHospitalData.php';

$userInfo = new App\Lib\UserInfo($SPIRAL);

$spiralApiCommunicator = $SPIRAL->getSpiralApiCommunicator();
$spiralApiRequest = new SpiralApiRequest();
$spiralDataBase = new App\Lib\SpiralDataBase($SPIRAL,$spiralApiCommunicator,$spiralApiRequest);

$regReceiving = new App\Api\RegReceiving($spiralDataBase,$userInfo);

$getHospitalData = new App\Api\GetHospitalData($spiralDataBase,$userInfo);
$hospitalData = $getHospitalData->select();

$getDivision = new App\Api\GetDivision($spiralDataBase,$userInfo);
$divisionData = $getDivision->select();
if($divisionData['code'] != '0'){
	echo json_encode(array('result'=>false));
	exit;
}

$regData = $_POST['regData'];
foreach ($regData as $array) {
	foreach ($array as $lotItem) {
		if ($lotItem['lotNumber']) {
			if ((!ctype_alnum($lotItem['lotNumber'])) || (strlen($lotItem['lotNumber']) > 20)) {
				echo json_encode(array('result'=>'invalid lotNumber'));
				exit;
			}
		}
	}
}

$divisionId = '';

if($hospitalData['data'][0]['receivingTarget'] == '1'){ //大倉庫へ納品の場合
	$divisionId = $divisionData['store'][0][1];
}
if($hospitalData['data'][0]['receivingTarget'] == '2'){ //発注部署へ納品の場合
	$crypt   = $SPIRAL->getSpiralCryptOpenSsl();
	$divisionId = $crypt->decrypt($_POST['divisionId'], 'JoyPla');
}

$result = $regReceiving->register($_POST['orderHistoryId'],$_POST['distributorId'],$divisionId,$_POST['receiving'],$regData);
if(! $result){
	echo json_encode(array('result'=>$result));
	exit;
}

//在庫情報更新
$regInventoryTrdb = new App\Api\RegInventoryTrdb($spiralDataBase,$userInfo);

$remakeData = array();

foreach($_POST['receiving'] as $itemId => $data){
	$remakeData[$itemId] = array();
	$remakeData[$itemId]['countNum'] = $data['quantity'] * $data['receivingCount'];
}

$result = $regInventoryTrdb->orderWithinCount($remakeData,$divisionId,'2');

if(! $result){
	echo json_encode(array('result'=>$result));
	exit;
}

$result = $regInventoryTrdb->orderCount($remakeData,$divisionId,'1');

if(! $result){
	echo json_encode(array('result'=>$result));
	exit;
}

//ロット管理情報
$lotData = [];

foreach ($regData as $rows) {
	foreach ($rows as $key => $val) {
		if ($val['lotNumber'] && $val['lotDate']) {
			$lotData[$val['inHPItemid'].$key] = [];
			$lotData[$val['inHPItemid'].$key] = [
				'inHPItemid' => $val['inHPItemid'],
				'lotNumber' => $val['lotNumber'],
				'lotDate' => $val['lotDate'],
				'stockQuantity' => (int)$val['quantity'] * (int)$val['receivingCount']
			];
		}
	}
}

if($lotData) { 
	$result = $regInventoryTrdb->lotData($lotData,$divisionId,'1'); //在庫数加算

	if(! $result) {
		echo json_encode(array('result'=>$result));
		exit;
	}
}


//結果を返却
echo json_encode(array('result'=>$result));
