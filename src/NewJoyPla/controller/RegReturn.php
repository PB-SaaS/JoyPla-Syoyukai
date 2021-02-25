<?php
//返品データ登録
include_once 'NewJoyPla/lib/ApiSpiral.php';
include_once "NewJoyPla/lib/Define.php";
include_once 'NewJoyPla/lib/SpiralDataBase.php';
include_once 'NewJoyPla/lib/UserInfo.php';
include_once 'NewJoyPla/api/RegReturn.php';
include_once 'NewJoyPla/api/RegInventoryTrdb.php';
include_once 'NewJoyPla/api/GetDivision.php';


$userInfo = new App\Lib\UserInfo($SPIRAL);

$spiralApiCommunicator = $SPIRAL->getSpiralApiCommunicator();
$spiralApiRequest = new SpiralApiRequest();
$spiralDataBase = new App\Lib\SpiralDataBase($SPIRAL,$spiralApiCommunicator,$spiralApiRequest);

$regReturn = new App\Api\RegReturn($spiralDataBase,$userInfo);
//返品情報登録
$result = $regReturn->register($_POST['receivingHistoryId'],$_POST['distributorId'],$_POST['returnData']);
if(! $result){
	echo json_encode(array('result'=>$result));
	exit;
}


$getDivision = new App\Api\GetDivision($spiralDataBase,$userInfo);
$divisionData = $getDivision->select();
if($divisionData['code'] != '0'){
	echo json_encode(array('result'=>false));
	exit;
}
//在庫情報更新
$regInventoryTrdb = new App\Api\RegInventoryTrdb($spiralDataBase,$userInfo);

$remakeData = array();

foreach($_POST['returnData'] as $itemId => $data){
	$remakeData[$itemId] = array();
	$remakeData[$itemId]['countNum'] = $data['quantity'] * $data['returnCount'];
}
//返品分を引く
$result = $regInventoryTrdb->orderCount($remakeData,$divisionData['store'][0][1],'2');

if(! $result){
	echo json_encode(array('result'=>$result));
	exit;
}
//結果を返却
echo json_encode(array('result'=>$result));