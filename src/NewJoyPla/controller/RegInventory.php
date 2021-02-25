<?php
//棚卸情報の登録
include_once 'NewJoyPla/lib/ApiSpiral.php';
include_once "NewJoyPla/lib/Define.php";
include_once 'NewJoyPla/lib/SpiralDataBase.php';
include_once 'NewJoyPla/lib/UserInfo.php';
include_once 'NewJoyPla/api/GetInventoryEndHistoryId.php';
include_once 'NewJoyPla/api/GetInventoryHistoryId.php';
include_once 'NewJoyPla/api/RegInventory.php';
include_once 'NewJoyPla/api/RegInventoryHistory.php';
include_once 'NewJoyPla/api/RegInventoryEndHistory.php';

$userInfo = new App\Lib\UserInfo($SPIRAL);

$spiralApiCommunicator = $SPIRAL->getSpiralApiCommunicator();
$spiralApiRequest = new SpiralApiRequest();
$spiralDataBase = new App\Lib\SpiralDataBase($SPIRAL,$spiralApiCommunicator,$spiralApiRequest);

$getInventoryEndHistory = new App\Api\GetInventoryEndHistoryId($spiralDataBase,$userInfo);
$inventoryEndHistoryId = $getInventoryEndHistory->getInventoryEndHistoryId();


$getInventoryHistory = new App\Api\GetInventoryHistoryId($spiralDataBase,$userInfo);
$InventoryHistoryId = $getInventoryHistory->getInventoryHistoryId($_POST['divisionId'],$inventoryEndHistoryId);

//棚卸情報の登録
$regInventory = new App\Api\RegInventory($spiralDataBase,$userInfo);
$result = $regInventory->register( $_POST['inventory'],  $_POST['divisionId'],  $inventoryEndHistoryId ,  $InventoryHistoryId);
if(!$result){
	echo json_encode(array('result'=>$result));
	exit;
}

//部署別棚卸履歴の更新
$regInventoryHistory = new App\Api\RegInventoryHistory($spiralDataBase,$userInfo);
$result = $regInventoryHistory->updateHistory($InventoryHistoryId);
if(!$result){
	echo json_encode(array('result'=>$result));
	exit;
}

//棚卸履歴の更新
$regInventoryEndHistory = new App\Api\RegInventoryEndHistory($spiralDataBase,$userInfo);

$result = $regInventoryEndHistory->updateHistory($inventoryEndHistoryId);

if(!$result){
	echo json_encode(array('result'=>$result));
	exit;
}

//$regInventoryTrdb = new App\Api\RegInventoryTrdb($spiralDataBase,$userInfo);
//$result = $regInventoryTrdb->orderCount($_POST['billing'],$_POST['divisionId'],'2');
//結果を返却
echo json_encode(array('result'=>$result));

