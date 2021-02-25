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

$orderCount = $_POST['payoutData'];

foreach($orderCount as $key => $record){
	$orderCount[$key]['countNum'] = $record['payoutQuantity'];
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
//結果を返却
echo json_encode(array('result'=>$result));