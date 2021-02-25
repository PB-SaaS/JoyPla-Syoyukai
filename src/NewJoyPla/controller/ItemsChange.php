<?php
//院内商品データの一括更新
include_once 'NewJoyPla/lib/ApiSpiral.php';
include_once "NewJoyPla/lib/Define.php";
include_once 'NewJoyPla/lib/SpiralDataBase.php';
include_once 'NewJoyPla/api/UpdateInHPItems.php';

$spiralApiCommunicator = $SPIRAL->getSpiralApiCommunicator();
$spiralApiRequest = new SpiralApiRequest();
$spiralDataBase = new App\Lib\SpiralDataBase($SPIRAL,$spiralApiCommunicator,$spiralApiRequest);


$updateInHPItems = new App\Api\UpdateInHPItems($spiralDataBase);

$itemId = $SPIRAL->getContextByFieldTitle("itemId");
$result = $updateInHPItems->updateInHPItems($itemId);
