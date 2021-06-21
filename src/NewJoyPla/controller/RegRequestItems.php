<?php
//一括見積商品登録　単票に設置
include_once 'NewJoyPla/lib/ApiSpiral.php';
include_once "NewJoyPla/lib/Define.php";
include_once 'NewJoyPla/lib/SpiralDataBase.php';
include_once 'NewJoyPla/lib/UserInfo.php';
include_once "NewJoyPla/api/GetCardInfo.php";
include_once "NewJoyPla/api/RegRequestItems.php";

$userInfo = new App\Lib\UserInfo($SPIRAL);

$spiralApiCommunicator = $SPIRAL->getSpiralApiCommunicator();
$spiralApiRequest = new SpiralApiRequest();
$spiralDataBase = new App\Lib\SpiralDataBase($SPIRAL,$spiralApiCommunicator,$spiralApiRequest);

$cardInfo = new App\Api\GetCardInfo($spiralDataBase);
$card = $cardInfo->select("requestData",$SPIRAL->getCardId(),"requestId","distributorId");
$card["data"] = $spiralDataBase->arrayToNameArray($card["data"],array("requestId","distributorId"));

$RegRequestItems = new App\Api\RegRequestItems($spiralDataBase,$userInfo);
$result = $RegRequestItems->register($card["data"][0]["requestId"],$card["data"][0]["distributorId"],$_POST["items"]);

$flg = false;
if($result['code'] == '0'){
    $flg = true;
}
echo json_encode(array('result'=>$flg));