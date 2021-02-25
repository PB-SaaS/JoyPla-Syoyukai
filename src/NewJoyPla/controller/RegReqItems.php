<?php
//見積商品登録　サンクスページに挿入
include_once 'NewJoyPla/lib/ApiSpiral.php';
include_once "NewJoyPla/lib/Define.php";
include_once 'NewJoyPla/lib/SpiralDataBase.php';
include_once 'NewJoyPla/lib/UserInfo.php';
include_once 'NewJoyPla/api/UpdateRequestStatus.php';

include_once "NewJoyPla/lib/SpiralSendMail.php";

$userInfo = new App\Lib\UserInfo($SPIRAL);

$spiralApiCommunicator = $SPIRAL->getSpiralApiCommunicator();
$spiralApiRequest = new SpiralApiRequest();
$spiralDataBase = new App\Lib\SpiralDataBase($SPIRAL,$spiralApiCommunicator,$spiralApiRequest);

$updateRequestStatus = new App\Api\UpdateRequestStatus($spiralDataBase);
$updateRequestStatus->itemsReg($SPIRAL->getContextByFieldTitle("requestId"));

$requestData = $updateRequestStatus->selectQRequestDB($SPIRAL->getContextByFieldTitle("requestId"));

$spiralApiCommunicator = $SPIRAL->getSpiralApiCommunicator();
$spiralApiRequest = new SpiralApiRequest();
$SpiralSendMail = new App\Lib\SpiralSendMail($SPIRAL,$spiralApiCommunicator,$spiralApiRequest);
$result = $SpiralSendMail->thanks(MITSUMORI_ITEMS_REG,$requestData['data'][0][0]);

if($result['code'] != '0'){
    var_dump($result);
}