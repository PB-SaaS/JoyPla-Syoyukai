<?php
//返品データ登録
include_once 'NewJoyPla/lib/ApiSpiral.php';
include_once "NewJoyPla/lib/Define.php";
include_once 'NewJoyPla/lib/SpiralDataBase.php';
include_once 'NewJoyPla/lib/UserInfo.php';
include_once 'NewJoyPla/api/RegReturn.php';
include_once 'NewJoyPla/api/RegInventoryTrdb.php';
include_once 'NewJoyPla/api/GetDivision.php';
include_once 'NewJoyPla/api/GetHospitalData.php';


$userInfo = new App\Lib\UserInfo($SPIRAL);

$spiralApiCommunicator = $SPIRAL->getSpiralApiCommunicator();
$spiralApiRequest = new SpiralApiRequest();
$spiralDataBase = new App\Lib\SpiralDataBase($SPIRAL,$spiralApiCommunicator,$spiralApiRequest);

$regReturn = new App\Api\RegReturn($spiralDataBase,$userInfo);
//返品情報登録
$result = $regReturn->register($_POST['receivingHistoryId'],$_POST['distributorId'],$_POST['returnData']);
if(! $result){
	echo json_encode(array('result'=>$result));
	exit;
}


$getDivision = new App\Api\GetDivision($spiralDataBase,$userInfo);
$divisionData = $getDivision->select();
if($divisionData['code'] != '0'){
	echo json_encode(array('result'=>false));
	exit;
}

//在庫情報更新
$regInventoryTrdb = new App\Api\RegInventoryTrdb($spiralDataBase,$userInfo);

$remakeData = array();

foreach($_POST['returnData'] as $data){
	if (array_key_exists($data['inHospitalItemId'], $remakeData)) {
		$remakeData[$data['inHospitalItemId']]['countNum'] += $data['quantity'] * $data['returnCount'];
	} else {
		$remakeData[$data['inHospitalItemId']] = array();
		$remakeData[$data['inHospitalItemId']]['countNum'] = $data['quantity'] * $data['returnCount'];
	}
}

//オプション情報取得
$getHospitalData = new App\Api\GetHospitalData($spiralDataBase,$userInfo);
$hospitalData = $getHospitalData->select();

$divisionId = "";
if($hospitalData['data'][0]['receivingTarget'] == '1'){ //大倉庫へ納品の場合
	$divisionId = $divisionData['store'][0][1];
}
if($hospitalData['data'][0]['receivingTarget'] == '2'){ //発注部署へ納品の場合
	$divisionId = $_POST['divisionId'];
}

//返品分を引く
$result = $regInventoryTrdb->orderCount($remakeData,$divisionId,'2');

if(! $result){
	echo json_encode(array('result'=>$result));
	exit;
}

//ロット管理情報更新
$lotData = [];

foreach ($_POST['returnData'] as $rows) {
	if ($rows['lotNumber'] && $rows['lotDate']) {
		$date = urldecode($rows['lotDate']);
		$timestamp = date_create_from_format('Y年m月d日', $date)->getTimestamp();
		$lotDate = date('Y-m-d', $timestamp);
		$lot = $rows['inHospitalItemId'].$rows['lotNumber'].$lotDate;
		$lotData[$lot] = [];
		$lotData[$lot] = [
			'inHPItemid' => $rows['inHospitalItemId'],
			'lotNumber' => $rows['lotNumber'],
			'lotDate' => $lotDate,
			'stockQuantity' => (int)$rows['quantity'] * (int)$rows['returnCount']
		];
	}
}

if($lotData) {
	$result = $regInventoryTrdb->lotData($lotData,$divisionId,'2'); // 在庫数減算

	if(! $result) {
		echo json_encode(array('result'=>$result));
		exit;
	}
}

//結果を返却
echo json_encode(array('result'=>$result));