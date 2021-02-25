<?php
//使用していないが機能としては残す 2021/02/25 
//検収書削除
include_once 'NewJoyPla/lib/ApiSpiral.php';
include_once "NewJoyPla/lib/Define.php";
include_once 'NewJoyPla/lib/SpiralDataBase.php';
include_once 'NewJoyPla/lib/UserInfo.php';
include_once 'NewJoyPla/api/DelAcceptance.php';
include_once 'NewJoyPla/api/RegInventoryTrdb.php';
include_once 'NewJoyPla/api/GetDivision.php';
include_once 'NewJoyPla/api/UpdateOrder.php';

$spiralApiCommunicator = $SPIRAL->getSpiralApiCommunicator();
$spiralApiRequest = new SpiralApiRequest();
$spiralDataBase = new App\Lib\SpiralDataBase($SPIRAL,$spiralApiCommunicator,$spiralApiRequest);

$userInfo = new App\Lib\UserInfo($SPIRAL);
$delAcceptance = new App\Api\delAcceptance($spiralDataBase);

//復号処理
$crypt   = $SPIRAL->getSpiralCryptOpenSsl();

$receivingAuthKey = '';
if(isset($_POST['receivingAuthKey']) && $_POST['receivingAuthKey'] != ''){
	$receivingAuthKey = $crypt->decrypt($_POST['receivingAuthKey'], 'JoyPla');
}

$orderAuthKey = '';
if(isset($_POST['orderAuthKey']) && $_POST['orderAuthKey'] != ''){
	$orderAuthKey = $crypt->decrypt($_POST['orderAuthKey'], 'JoyPla');
}

//削除処理
$result = $delAcceptance->delete($_POST['receivingHId'],$receivingAuthKey);

if(! $result){
	echo json_encode(array('result'=>$result));
	exit;
}

//部署情報取得
$getDivision = new App\Api\GetDivision($spiralDataBase,$userInfo);

$divisionData = $getDivision->select();
if($divisionData['code'] != '0'){
	echo json_encode(array('result'=>false));
	exit;
}

//削除分の個数を在庫から引く
$orderWithinCount = $_POST['acceptanceData'];

foreach($orderWithinCount as $key => $record){
	$orderWithinCount[$key]['countNum'] = $record['quantity'] * $record['receivingCount'];
}

$regInventoryTrdb = new App\Api\RegInventoryTrdb($spiralDataBase,$userInfo);

$result = $regInventoryTrdb->orderWithinCount($orderWithinCount,$divisionData['store'][0][1],'1');
if(! $result){
	echo json_encode(array('result'=>$result));
	exit;
}

$result = $regInventoryTrdb->orderCount($orderWithinCount,$divisionData['store'][0][1],'2');

if(! $result){
	echo json_encode(array('result'=>$result));
	exit;
}

$updateOrder = new App\Api\UpdateOrder($spiralDataBase);

//オーダーデータを更新
$result = $updateOrder->updateWithDelAcceptance($_POST['orderNum'],$orderAuthKey,$_POST['acceptanceData']);

if(! $result){
	echo json_encode(array('result'=>$result));
	exit;
}

//結果を返却
echo json_encode(array('result'=>$result));