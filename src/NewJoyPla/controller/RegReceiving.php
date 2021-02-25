
<?php
//入庫データ登録
include_once 'NewJoyPla/lib/ApiSpiral.php';
include_once "NewJoyPla/lib/Define.php";
include_once 'NewJoyPla/lib/SpiralDataBase.php';
include_once 'NewJoyPla/lib/UserInfo.php';
include_once 'NewJoyPla/api/RegReceiving.php';
include_once 'NewJoyPla/api/RegInventoryTrdb.php';
include_once 'NewJoyPla/api/GetDivision.php';
include_once 'NewJoyPla/api/RegLot.php';
include_once 'NewJoyPla/api/GetHospitalData.php';

$userInfo = new App\Lib\UserInfo($SPIRAL);

$spiralApiCommunicator = $SPIRAL->getSpiralApiCommunicator();
$spiralApiRequest = new SpiralApiRequest();
$spiralDataBase = new App\Lib\SpiralDataBase($SPIRAL,$spiralApiCommunicator,$spiralApiRequest);

$regReceiving = new App\Api\RegReceiving($spiralDataBase,$userInfo);

$getHospitalData = new App\Api\GetHospitalData($spiralDataBase,$userInfo);
$hospitalData = $getHospitalData->select();

$getDivision = new App\Api\GetDivision($spiralDataBase,$userInfo);
$divisionData = $getDivision->select();
if($divisionData['code'] != '0'){
	echo json_encode(array('result'=>false));
	exit;
}

$divisionId = '';

if($hospitalData['data'][0]['receivingTarget'] == '1'){ //大倉庫へ納品の場合
	$divisionId = $divisionData['store'][0][1];
}
if($hospitalData['data'][0]['receivingTarget'] == '2'){ //発注部署へ納品の場合
	$crypt   = $SPIRAL->getSpiralCryptOpenSsl();
	$divisionId = $crypt->decrypt($_POST['divisionId'], 'JoyPla');
}

$result = $regReceiving->register($_POST['orderHistoryId'],$_POST['distributorId'],$divisionId,$_POST['receiving']);
if(! $result){
	echo json_encode(array('result'=>$result));
	exit;
}

//在庫情報更新
$regInventoryTrdb = new App\Api\RegInventoryTrdb($spiralDataBase,$userInfo);

$remakeData = array();

foreach($_POST['receiving'] as $itemId => $data){
	$remakeData[$itemId] = array();
	$remakeData[$itemId]['countNum'] = $data['quantity'] * $data['receivingCount'];
}

$result = $regInventoryTrdb->orderWithinCount($remakeData,$divisionId,'2');

if(! $result){
	echo json_encode(array('result'=>$result));
	exit;
}

$result = $regInventoryTrdb->orderCount($remakeData,$divisionId,'1');

if(! $result){
	echo json_encode(array('result'=>$result));
	exit;
}

$regLot = new App\Api\RegLot($spiralDataBase,$userInfo);


$result = $regLot->regLot($_POST['lotData'],$divisionId,$regReceiving->ReceivingHistoryId,null);
if($result['code'] != "0"){
	var_dump($result);
	echo json_encode(array('result'=>false));
	exit;
}

//結果を返却
echo json_encode(array('result'=>$result));


