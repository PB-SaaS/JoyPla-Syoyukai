<?php
//棚卸終了伝票の削除
include_once 'NewJoyPla/lib/ApiSpiral.php';
include_once "NewJoyPla/lib/Define.php";
include_once 'NewJoyPla/lib/SpiralDataBase.php';
include_once 'NewJoyPla/api/DelInventory.php';

$spiralApiCommunicator = $SPIRAL->getSpiralApiCommunicator();
$spiralApiRequest = new SpiralApiRequest();
$spiralDataBase = new App\Lib\SpiralDataBase($SPIRAL,$spiralApiCommunicator,$spiralApiRequest);

$delInventory= new App\Api\delInventory($spiralDataBase,$userInfo);


$crypt   = $SPIRAL->getSpiralCryptOpenSsl();
$authKey= $crypt->decrypt($_POST['authKey'], 'JoyPla');

$result = $delInventory->deleteEndHistory($_POST['inventoryEndId'],$authKey);
//結果を返却
echo json_encode(array('result'=>$result));