<?php
//卸業者発注書取消
include_once 'NewJoyPla/lib/ApiSpiral.php';
include_once "NewJoyPla/lib/Define.php";
include_once 'NewJoyPla/lib/SpiralDataBase.php';
include_once 'NewJoyPla/lib/UserInfo.php';
include_once 'NewJoyPla/api/UpdateOrderFixing.php';


$userInfo = new App\Lib\UserInfo($SPIRAL);

$spiralApiCommunicator = $SPIRAL->getSpiralApiCommunicator();
$spiralApiRequest = new SpiralApiRequest();
$spiralDataBase = new App\Lib\SpiralDataBase($SPIRAL,$spiralApiCommunicator,$spiralApiRequest);

$updateOrderFixing = new App\Api\UpdateOrderFixing($spiralDataBase);


$crypt = $SPIRAL->getSpiralCryptOpenSsl();
$orderAuthKey = $crypt->decrypt($_POST['orderAuthKey'], 'JoyPla');

$result = $updateOrderFixing->delete($_POST['orderHistoryId'],$orderAuthKey,$_POST['orderItems']);

if(! $result){
	echo json_encode(array('result'=>$result));
	exit;
}
//結果を返却
echo json_encode(array('result'=>$result));