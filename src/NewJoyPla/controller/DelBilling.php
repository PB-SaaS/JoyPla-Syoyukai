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
$orderCount = $_POST['billingData'];

foreach($orderCount as $key => $record){
	$orderCount[$key]['countNum'] = $record['billingQuantity'];
}

$regInventoryTrdb = new App\Api\RegInventoryTrdb($spiralDataBase,$userInfo);
$result = $regInventoryTrdb->orderCount($orderCount,$_POST['divisionId'],'1');

if(! $result){
	echo json_encode(array('result'=>$result));
	exit;
}

//結果を返却
echo json_encode(array('result'=>$result));