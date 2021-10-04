<?php
//棚卸伝票の削除
include_once 'NewJoyPla/lib/ApiSpiral.php';
include_once "NewJoyPla/lib/Define.php";
include_once 'NewJoyPla/lib/SpiralDataBase.php';
include_once 'NewJoyPla/lib/UserInfo.php';
include_once 'NewJoyPla/api/RegInventoryTrdb.php';
include_once 'NewJoyPla/api/DelPayout.php';

$spiralApiCommunicator = $SPIRAL->getSpiralApiCommunicator();
$spiralApiRequest = new SpiralApiRequest();
$spiralDataBase = new App\Lib\SpiralDataBase($SPIRAL,$spiralApiCommunicator,$spiralApiRequest);

$userInfo = new App\Lib\UserInfo($SPIRAL);

$crypt   = $SPIRAL->getSpiralCryptOpenSsl();
$payoutAuthKey = $crypt->decrypt($_POST['payoutAuthKey'], 'JoyPla');

$delPayout = new App\Api\delPayout($spiralDataBase);
//削除実行
$result = $delPayout->delete($_POST['payoutHistoryId'],$payoutAuthKey);

if(! $result){
	echo json_encode(array('result'=>$result));
	exit;
}

$payoutData = $_POST['payoutData'];
$orderCount = [];

foreach($payoutData as $val){
	if (array_key_exists($val['inHospitalItemId'], $orderCount)) {
		$orderCount[$val['inHospitalItemId']]['countNum'] += (int)$val['payoutQuantity'];
	} else {
		$orderCount[$val['inHospitalItemId']] = [];
		$orderCount[$val['inHospitalItemId']] = ['countNum' => (int)$val['payoutQuantity']];
	}
}

//在庫を戻す
$regInventoryTrdb = new App\Api\RegInventoryTrdb($spiralDataBase,$userInfo);
$result = $regInventoryTrdb->orderCount($orderCount,$_POST['sourceDivisionId'],'1');

if(! $result){
	echo json_encode(array('result'=>$result));
	exit;
}

//在庫を戻す
$result = $regInventoryTrdb->orderCount($orderCount,$_POST['targetDivisionId'],'2');

if(! $result){
	echo json_encode(array('result'=>$result));
	exit;
}

//ロット管理情報
$lotData = [];

foreach ($payoutData as $rows) {
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
			'stockQuantity' => (int)$rows['payoutQuantity']
		];
	}
}

if($lotData) { 
	$result = $regInventoryTrdb->lotData($lotData,$_POST['sourceDivisionId'],'1'); // 在庫数加算
	if(!$result) {
		echo json_encode(array('result'=>$result));
		exit;
	}

	$result = $regInventoryTrdb->lotData($lotData,$_POST['targetDivisionId'],'2'); // 在庫数減算
	if(!$result) {
		echo json_encode(array('result'=>$result));
		exit;
	}
}

//結果を返却
echo json_encode(array('result'=>$result));