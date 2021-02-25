<?php
//棚卸確定処理
include_once 'NewJoyPla/lib/ApiSpiral.php';
include_once "NewJoyPla/lib/Define.php";
include_once 'NewJoyPla/lib/SpiralDataBase.php';
include_once 'NewJoyPla/lib/UserInfo.php';
include_once 'NewJoyPla/api/RegInventoryFinish.php';
include_once 'NewJoyPla/api/GetInventory.php';
include_once 'NewJoyPla/api/ResetStock.php';
include_once 'NewJoyPla/api/RegInventoryTrdb.php';

$userInfo = new App\Lib\UserInfo($SPIRAL);

$spiralApiCommunicator = $SPIRAL->getSpiralApiCommunicator();
$spiralApiRequest = new SpiralApiRequest();
$spiralDataBase = new App\Lib\SpiralDataBase($SPIRAL,$spiralApiCommunicator,$spiralApiRequest);

$resetStock = new App\Api\ResetStock($spiralDataBase,$userInfo);
$regInventoryTrdb = new App\Api\RegInventoryTrdb($spiralDataBase,$userInfo);

$crypt   = $SPIRAL->getSpiralCryptOpenSsl();
$inventoryEAuthkey = $crypt->decrypt($_POST['inventoryEAuthkey'], 'JoyPla');

//棚卸情報取得
$getInventory = new App\Api\GetInventory($spiralDataBase,$userInfo);
$result = $getInventory->getInventory($_POST['inventoryEndHistoryId']);

if($result['code'] != '0'){
	var_dump($result);
	echo json_encode(array('result'=>false));
	exit;
}

$inventoryData = $spiralDataBase->arrayToNameArray($result["data"],array('id','divisionId','inventryNum','inHospitalItemId'));

//棚卸情報加工
$items = array();
foreach($inventoryData as $record){
	if(!array_key_exists($record['divisionId'], $items)){
		$items[$record['divisionId']] = array();
	}
	$items[$record['divisionId']][$record['inHospitalItemId']] = array('countNum'=>$record['inventryNum']);
}

//棚卸結果でデータ更新
foreach($items as $divisionId => $item){

	$result = $resetStock->resetStock($divisionId);
	if(!$result){
		var_dump($result);
		echo json_encode(array('result'=>false));
		exit;
	}
	
	$itemsforBluk = array_chunk($item, 999,true);
	foreach($itemsforBluk as $items_1000){
		
		$result = $regInventoryTrdb->orderCount($items_1000,$divisionId,'1');
		if(!$result){
			var_dump($result);
			echo json_encode(array('result'=>false));
			exit;
		}
	}
}

if(!$result){
	var_dump($result);
	echo json_encode(array('result'=>false));
	exit;
}

//在庫初期化

$divisionIdArray = array();

//foreach($items as $divisionId => $item){
	//$divisionIdArray[] = $divisionId;
//}

//現在在庫数取得
$result = $resetStock->getStock($divisionIdArray);
if($result['code'] != "0"){
	var_dump($result);
	echo json_encode(array('result'=>false));
	exit;
}

$request = array();

foreach($result['data'] as $record){
	$count = 0 ;
	if(array_key_exists($record[0], $request)){
		$count = (int)$request[$record[0]]['countNum'];
	}
	$request[$record[0]] = array('countNum' => (int)$record[1] + $count);
}

/**
 * 院内商品総数で更新 
 */
$requestforBluk = array_chunk($request, 999,true);

foreach($requestforBluk as $items_1000){
	$result = $resetStock->resetInHPItem($items_1000);
	if(!$result){
		var_dump($result);
		echo json_encode(array('result'=>false));
		exit; 
	}
}


//ステータス更新
$regInventoryFinish = new App\Api\RegInventoryFinish($spiralDataBase,$userInfo);
$result = $regInventoryFinish->updateHistory($_POST['inventoryEndHistoryId'],$inventoryEAuthkey);

if(!$result){
	echo json_encode(array('result'=>$result));
	exit;
}

//結果を返却
echo json_encode(array('result'=>true));

