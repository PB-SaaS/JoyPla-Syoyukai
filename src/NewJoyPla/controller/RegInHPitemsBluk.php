<?php
//院内商品情報の一括登録
/*
include_once 'NewJoyPla/lib/ApiSpiral.php';
include_once "NewJoyPla/lib/Define.php";
include_once 'NewJoyPla/lib/SpiralDataBase.php';
include_once 'NewJoyPla/lib/UserInfo.php';
include_once 'NewJoyPla/api/RegInHPitemsBlukIns.php';

$spiralApiCommunicator = $SPIRAL->getSpiralApiCommunicator();
$spiralApiRequest = new SpiralApiRequest();
$spiralDataBase = new App\Lib\SpiralDataBase($SPIRAL,$spiralApiCommunicator,$spiralApiRequest);

$userInfo = new App\Lib\UserInfo($SPIRAL);

$blukinsert = new App\Api\RegInHPitemsBlukIns($spiralDataBase,$userInfo);
$blukinsertData = $blukinsert->blukinsert($_POST['insertData']);

$result = true;
if($blukinsertData['code'] != "0"){
	$result = false;
}
//結果を返却
echo json_encode(array('result'=>$result,'response'=>$blukinsertData));