<?php
//払出情報登録
include_once 'NewJoyPla/lib/ApiSpiral.php';
include_once "NewJoyPla/lib/Define.php";
include_once 'NewJoyPla/lib/SpiralDataBase.php';
include_once 'NewJoyPla/lib/UserInfo.php';
include_once 'NewJoyPla/api/RegPayout.php';
include_once 'NewJoyPla/api/RegInventoryTrdb.php';
include_once 'NewJoyPla/api/GetHospitalData.php';

$userInfo = new App\Lib\UserInfo($SPIRAL);

$spiralApiCommunicator = $SPIRAL->getSpiralApiCommunicator();
$spiralApiRequest = new SpiralApiRequest();
$spiralDataBase = new App\Lib\SpiralDataBase($SPIRAL,$spiralApiCommunicator,$spiralApiRequest);

$regPayout= new App\Api\RegPayout($spiralDataBase,$userInfo);

$payoutData = $_POST['payout'];
foreach($payoutData as $key => $array){
	foreach($array as $payoutKey => $payoutRecord){
		if ($payoutRecord['lotNumber']) {
			if ((!ctype_alnum($payoutRecord['lotNumber'])) || (strlen($payoutRecord['lotNumber']) > 20)) {
				echo json_encode(array('result'=>'invalid lotNumber'));
				exit;
			}
		}
		$payoutData[$key][$payoutKey]['countNum'] = (int)$payoutRecord['countNum'] * (int)$payoutRecord['countLabelNum'] ;
		$payoutData[$key][$payoutKey]['payoutCount'] = $payoutRecord['countNum'];
	}
}

$getHospitalData = new App\Api\GetHospitalData($spiralDataBase,$userInfo);
$hospitalData = $getHospitalData->select();
$useUnitPrice = $hospitalData['data'][0]['payoutUnitPrice'];


//払出登録
$result = $regPayout->register($payoutData,$_POST['sourceDivisionId'],$_POST['sourceDivisionName'],$_POST['targetDivisionId'],$_POST['targetDivisionName'],$useUnitPrice);

if($result != true){
	var_dump("test");
	echo json_encode(array('result'=>$result));
	exit;
}

//在庫情報更新
$payoutItemData = [];
foreach ($payoutData as $posts) {
	foreach ($posts as $val) {
		if (array_key_exists($val['recordId'], $payoutItemData)) {
			$payoutItemData[$val['recordId']]['countNum'] += (int)$val['countNum'];
		} else {
			$payoutItemData[$val['recordId']] = [];
			$payoutItemData[$val['recordId']] = ['countNum' => (int)$val['countNum']];
		}
	}
}

$regInventoryTrdb = new App\Api\RegInventoryTrdb($spiralDataBase,$userInfo);

$result = $regInventoryTrdb->orderCount($payoutItemData,$_POST['sourceDivisionId'],'2');

if($result != true){
	echo json_encode(array('result'=>$result));
	exit;
}

$result = $regInventoryTrdb->orderCount($payoutItemData,$_POST['targetDivisionId'],'1');
if($result != true){
	echo json_encode(array('result'=>$result));
	exit;
}

//ロット管理情報
$lotData = [];

foreach ($payoutData as $rows) {
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
	$result = $regInventoryTrdb->lotData($lotData,$_POST['targetDivisionId'],'1'); // 在庫数加算
	if(!$result) {
		echo json_encode(array('result'=>$result));
		exit;
	}

	$result = $regInventoryTrdb->lotData($lotData,$_POST['sourceDivisionId'],'2'); // 在庫数減算
	if(!$result) {
		echo json_encode(array('result'=>$result));
		exit;
	}
}

//結果を返却
echo json_encode(array('result'=>$result));

