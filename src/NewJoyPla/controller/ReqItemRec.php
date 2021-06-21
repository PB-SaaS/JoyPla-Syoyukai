<?php
include_once 'NewJoyPla/lib/ApiSpiral.php';
include_once 'NewJoyPla/lib/Define.php';
include_once 'NewJoyPla/lib/SpiralDataBase.php';
include_once 'NewJoyPla/lib/UserInfo.php';
include_once 'NewJoyPla/api/UpdateRequestStatus.php';
include_once "NewJoyPla/api/GetCardInfo.php";

$UserInfo = new \App\Lib\UserInfo($SPIRAL);

$spiralApiCommunicator = $SPIRAL->getSpiralApiCommunicator();
$spiralApiRequest = new SpiralApiRequest();
$spiralDataBase = new App\Lib\SpiralDataBase($SPIRAL,$spiralApiCommunicator,$spiralApiRequest);

$cardInfo = new App\Api\GetCardInfo($spiralDataBase);
$card = $cardInfo->select("NJ_QRequestDB",$SPIRAL->getCardId(),"requestId","requestStatus");

$updateRequestStatus = new \App\Api\UpdateRequestStatus($spiralDataBase);
$updateRequestStatus->hospitalCheck($card["data"][0][0]);
