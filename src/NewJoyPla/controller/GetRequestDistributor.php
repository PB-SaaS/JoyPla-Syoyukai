<?php
//卸業者データの取得
/*
include_once 'NewJoyPla/lib/ApiSpiral.php';
include_once "NewJoyPla/lib/Define.php";
include_once 'NewJoyPla/lib/SpiralDataBase.php';
include_once 'NewJoyPla/lib/UserInfo.php';
include_once 'NewJoyPla/api/GetDistributor.php';

$spiralApiCommunicator = $SPIRAL->getSpiralApiCommunicator();
$spiralApiRequest = new SpiralApiRequest();
$spiralDataBase = new App\Lib\SpiralDataBase($SPIRAL,$spiralApiCommunicator,$spiralApiRequest);

$userInfo = new App\Lib\UserInfo($SPIRAL);

$getDistributor = new App\Api\GetDistributor($spiralDataBase,$userInfo);
$result = $getDistributor->getRequestDistributor();

if($result['code'] != '0'){
	echo json_encode(array('result'=>false));
	exit;
}
//結果を返却
echo json_encode(array('result'=>true,'data'=>$result['data']));