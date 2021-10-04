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
include_once 'NewJoyPla/api/GetHospitalData.php';

$userInfo = new App\Lib\UserInfo($SPIRAL);

$spiralApiCommunicator = $SPIRAL->getSpiralApiCommunicator();
$spiralApiRequest = new SpiralApiRequest();
$spiralDataBase = new App\Lib\SpiralDataBase($SPIRAL,$spiralApiCommunicator,$spiralApiRequest);

$getInventoryEndHistory = new App\Api\GetInventoryEndHistoryId($spiralDataBase,$userInfo);
$inventoryEndHistoryId = $getInventoryEndHistory->getInventoryEndHistoryId();


$getInventoryHistory = new App\Api\GetInventoryHistoryId($spiralDataBase,$userInfo);
$InventoryHistoryId = $getInventoryHistory->getInventoryHistoryId($_POST['divisionId'],$inventoryEndHistoryId);

//棚卸情報の登録
$inventoryData = $_POST['inventory'];
foreach ($inventoryData as $array) {
	foreach ($array as $data) {
		if ($data['lotNumber']) {
			if ((!ctype_alnum($data['lotNumber'])) || (strlen($data['lotNumber']) > 20)) {
				echo json_encode(array('result'=>'invalid lotNumber'));
				exit;
			}
		}
	}
}

$getHospitalData = new App\Api\GetHospitalData($spiralDataBase,$userInfo);
$hospitalData = $getHospitalData->select();
$useUnitPrice = $hospitalData['data'][0]['invUnitPrice'];


$regInventory = new App\Api\RegInventory($spiralDataBase,$userInfo);
$result = $regInventory->register($inventoryData, $_POST['divisionId'], $inventoryEndHistoryId, $InventoryHistoryId, $useUnitPrice);
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

