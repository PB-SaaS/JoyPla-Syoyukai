<?php
//部署別棚卸データ伝票の削除
/*
include_once 'NewJoyPla/lib/ApiSpiral.php';
include_once "NewJoyPla/lib/Define.php";
include_once 'NewJoyPla/lib/SpiralDataBase.php';
include_once 'NewJoyPla/api/DelInventory.php';

$spiralApiCommunicator = $SPIRAL->getSpiralApiCommunicator();
$spiralApiRequest = new SpiralApiRequest();
$spiralDataBase = new App\Lib\SpiralDataBase($SPIRAL,$spiralApiCommunicator,$spiralApiRequest);

$delInventory= new App\Api\DelInventory($spiralDataBase);

$crypt   = $SPIRAL->getSpiralCryptOpenSsl();
$authKey= $crypt->decrypt($_POST['authKey'], 'JoyPla');

$result = $delInventory->deleteHistory($_POST['inventoryHId'],$authKey);
//結果を返却
echo json_encode(array('result'=>$result));
*/