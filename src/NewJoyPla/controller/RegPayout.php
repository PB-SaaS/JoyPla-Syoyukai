<?php
//払出情報登録
include_once 'NewJoyPla/lib/ApiSpiral.php';
include_once "NewJoyPla/lib/Define.php";
include_once 'NewJoyPla/lib/SpiralDataBase.php';
include_once 'NewJoyPla/lib/UserInfo.php';
include_once 'NewJoyPla/api/RegPayout.php';
include_once 'NewJoyPla/api/RegInventoryTrdb.php';

$userInfo = new App\Lib\UserInfo($SPIRAL);

$spiralApiCommunicator = $SPIRAL->getSpiralApiCommunicator();
$spiralApiRequest = new SpiralApiRequest();
$spiralDataBase = new App\Lib\SpiralDataBase($SPIRAL,$spiralApiCommunicator,$spiralApiRequest);

$regPayout= new App\Api\RegPayout($spiralDataBase,$userInfo);

$payoutData = $_POST['payout'];
foreach($payoutData as $payoutKey => $payoutRecord){
	$payoutData[$payoutKey]['countNum'] = (int)$payoutRecord['countNum'] * (int)$payoutRecord['countLabelNum'] ;
	$payoutData[$payoutKey]['payoutCount'] = $payoutRecord['countNum'];
}
//払出登録
$result = $regPayout->register($payoutData,$_POST['sourceDivisionId'],$_POST['sourceDivisionName'],$_POST['targetDivisionId'],$_POST['targetDivisionName']);

if($result != true){
	var_dump("test");
	echo json_encode(array('result'=>$result));
	exit;
}

//在庫情報更新
$regInventoryTrdb = new App\Api\RegInventoryTrdb($spiralDataBase,$userInfo);

$result = $regInventoryTrdb->orderCount($payoutData,$_POST['sourceDivisionId'],'2');

if($result != true){
	echo json_encode(array('result'=>$result));
	exit;
}

$result = $regInventoryTrdb->orderCount($payoutData,$_POST['targetDivisionId'],'1');
if($result != true){
	echo json_encode(array('result'=>$result));
	exit;
}//結果を返却
echo json_encode(array('result'=>$result));

