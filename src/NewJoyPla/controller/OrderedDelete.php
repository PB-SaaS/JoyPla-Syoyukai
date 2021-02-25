<?php
//発注取消
include_once 'NewJoyPla/lib/ApiSpiral.php';
include_once "NewJoyPla/lib/Define.php";
include_once 'NewJoyPla/lib/SpiralDataBase.php';
include_once 'NewJoyPla/lib/UserInfo.php';
include_once 'NewJoyPla/api/DeleteOrder.php';
include_once 'NewJoyPla/api/RegInventoryTrdb.php';
include_once 'NewJoyPla/api/GetDivision.php';
include_once 'NewJoyPla/api/GetHospitalData.php';
include_once "NewJoyPla/api/GetCardInfo.php";

$spiralApiCommunicator = $SPIRAL->getSpiralApiCommunicator();
$spiralApiRequest = new SpiralApiRequest();
$spiralDataBase = new App\Lib\SpiralDataBase($SPIRAL,$spiralApiCommunicator,$spiralApiRequest);

$userInfo = new App\Lib\UserInfo($SPIRAL);
$deleteOrder = new App\Api\deleteOrder($spiralDataBase);

$cardInfo = new App\Api\GetCardInfo($spiralDataBase);
$card = $cardInfo->select("orderdataDB",$SPIRAL->getCardId(),"orderNumber","receivingTarget","divisionId","hospitalName","postalCode","prefectures","address","divisionName","orderTime","orderNumber","itemsNumber","totalAmount","hospitalId","distributorId","orderAuthKey");
$card["data"] = $spiralDataBase->arrayToNameArray($card["data"],array("orderNumber","receivingTarget","divisionId","hospitalName","postalCode","prefectures","address","divisionName","orderTime","orderNumber","itemsNumber","totalAmount","hospitalId","distributorId","orderAuthKey"));

$orderNumber = $card['data'][0]['orderNumber'];

$crypt   = $SPIRAL->getSpiralCryptOpenSsl();
$orderAuthKey = $crypt->decrypt($_POST['orderAuthKey'], 'JoyPla');

$divisionId = $card['data'][0]['divisionId'];

$result = $deleteOrder->delete($orderNumber,$orderAuthKey);

//在庫計算を含むか
//在庫計算は発注完了後の取り消し時に必要
//未発注伝票の取り消しには行わない
if($_POST['countFlg'] == 'true'){
	
	$getHospitalData = new App\Api\GetHospitalData($spiralDataBase,$userInfo);
	$hospitalData = $getHospitalData->select();
	
	$getDivision = new App\Api\GetDivision($spiralDataBase,$userInfo);
	$divisionData = $getDivision->select();
	if($divisionData['code'] != '0'){
		echo json_encode(array('result'=>false));
		exit;
	}
	
	if($hospitalData['data'][0]['receivingTarget'] == '1'){ //大倉庫
		$divisionId = $divisionData['store'][0][1];
	}
	
	$orderWithinCount = $_POST['orderData'];
	foreach($orderWithinCount as $key => $record){
		$orderWithinCount[$key]['countNum'] = $record['quantity'] * $record['countNum'];
	}
	
	$regInventoryTrdb = new App\Api\RegInventoryTrdb($spiralDataBase,$userInfo);
	$result = $regInventoryTrdb->orderWithinCount($orderWithinCount,$divisionId,'2');
}

//結果を返却
echo json_encode(array('result'=>$result));
return true;