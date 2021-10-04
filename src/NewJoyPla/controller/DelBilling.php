<?php
//物品請求データの削除
include_once 'NewJoyPla/lib/ApiSpiral.php';
include_once "NewJoyPla/lib/Define.php";
include_once 'NewJoyPla/lib/SpiralDataBase.php';
include_once 'NewJoyPla/lib/UserInfo.php';
include_once 'NewJoyPla/api/RegInventoryTrdb.php';
include_once 'NewJoyPla/api/DelBilling.php';

$spiralApiCommunicator = $SPIRAL->getSpiralApiCommunicator();
$spiralApiRequest = new SpiralApiRequest();
$spiralDataBase = new App\Lib\SpiralDataBase($SPIRAL,$spiralApiCommunicator,$spiralApiRequest);

$userInfo = new App\Lib\UserInfo($SPIRAL);

//復号処理
$crypt   = $SPIRAL->getSpiralCryptOpenSsl();
$billingAuthKey = $crypt->decrypt($_POST['billingAuthKey'], 'JoyPla');

$delBilling = new App\Api\delBilling($spiralDataBase);
$result = $delBilling->delete($_POST['billingNumber'],$billingAuthKey);

if(! $result){
	echo json_encode(array('result'=>$result));
	exit;
}

//在庫を戻す処理
$billingData = $_POST['billingData'];
$orderCount = [];

foreach ($billingData as $val) {
	if (array_key_exists($val['inHospitalItemId'], $orderCount)) {
		$orderCount[$val['inHospitalItemId']]['countNum'] += (int)$val['billingQuantity'];
	} else {
		$orderCount[$val['inHospitalItemId']] = [];
		$orderCount[$val['inHospitalItemId']] = ['countNum' => (int)$val['billingQuantity']];
	}
}

$regInventoryTrdb = new App\Api\RegInventoryTrdb($spiralDataBase,$userInfo);
$result = $regInventoryTrdb->orderCount($orderCount,$_POST['divisionId'],'1');

if(! $result){
	echo json_encode(array('result'=>$result));
	exit;
}

//ロット管理情報
$lotData = [];

foreach ($billingData as $rows) {
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
			'stockQuantity' => (int)$rows['billingQuantity']
		];
	}
}

if($lotData) { 
	$result = $regInventoryTrdb->lotData($lotData,$_POST['divisionId'],'1'); // 在庫数加算

	if(! $result) {
		echo json_encode(array('result'=>$result));
		exit;
	}
}

//結果を返却
echo json_encode(array('result'=>$result));