<?php
//発注書のコメント更新
include_once 'NewJoyPla/lib/ApiSpiral.php';
include_once "NewJoyPla/lib/Define.php";
include_once 'NewJoyPla/lib/SpiralDataBase.php';
include_once 'NewJoyPla/lib/UserInfo.php';
include_once 'NewJoyPla/lib/Func.php';
include_once 'NewJoyPla/api/RegOrder.php';
include_once "NewJoyPla/api/GetCardInfo.php";

$userInfo = new App\Lib\UserInfo($SPIRAL);

$spiralApiCommunicator = $SPIRAL->getSpiralApiCommunicator();
$spiralApiRequest = new SpiralApiRequest();
$spiralDataBase = new App\Lib\SpiralDataBase($SPIRAL,$spiralApiCommunicator,$spiralApiRequest);

$regOrder = new App\Api\RegOrder($spiralDataBase);

$cardInfo = new App\Api\GetCardInfo($spiralDataBase);
$card = $cardInfo->select("orderdataDB",$SPIRAL->getCardId(),"orderNumber","receivingTarget","divisionId","hospitalName","postalCode","prefectures","address","divisionName","orderTime","orderNumber","itemsNumber","totalAmount","hospitalId","distributorId");
$card["data"] = $spiralDataBase->arrayToNameArray($card["data"],array("orderNumber","receivingTarget","divisionId","hospitalName","postalCode","prefectures","address","divisionName","orderTime","orderNumber","itemsNumber","totalAmount","hospitalId","distributorId"));

$orderNumber = $card['data'][0]['orderNumber'];

$crypt   = $SPIRAL->getSpiralCryptOpenSsl();
$orderAuthKey = $crypt->decrypt($_POST['orderAuthKey'], 'JoyPla');

//発注伝票コメントを更新
$result = $regOrder->updateOrderComment($orderNumber,$orderAuthKey,$_POST['ordercomment']);
//結果を返却
echo json_encode(array('result'=>$result));