<?php
//見積商品採用　サンクスページに挿入
include_once 'NewJoyPla/lib/ApiSpiral.php';
include_once 'NewJoyPla/lib/Define.php';
include_once 'NewJoyPla/lib/SpiralDataBase.php';
include_once 'NewJoyPla/lib/UserInfo.php';
include_once 'NewJoyPla/api/UpdateRequestStatus.php';

$UserInfo = new \App\Lib\UserInfo($SPIRAL);

$spiralApiCommunicator = $SPIRAL->getSpiralApiCommunicator();
$spiralApiRequest = new SpiralApiRequest();
$spiralDataBase = new App\Lib\SpiralDataBase($SPIRAL,$spiralApiCommunicator,$spiralApiRequest);

$updateRequestStatus = new \App\Api\UpdateRequestStatus($spiralDataBase);
$updateRequestStatus->hospitalCheck($SPIRAL->getContextByFieldTitle("requestId"));
