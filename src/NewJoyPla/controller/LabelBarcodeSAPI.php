<?php
//商品ラベルのバーコード検索
include_once 'NewJoyPla/lib/ApiSpiral.php';
include_once "NewJoyPla/lib/Define.php";
include_once 'NewJoyPla/lib/SpiralDataBase.php';
include_once 'NewJoyPla/lib/UserInfo.php';

include_once "NewJoyPla/api/LabelSearchBarcode.php";


$userInfo = new App\Lib\UserInfo($SPIRAL);

$spiralApiCommunicator = $SPIRAL->getSpiralApiCommunicator();
$spiralApiRequest = new SpiralApiRequest();
$spiralDataBase = new App\Lib\SpiralDataBase($SPIRAL,$spiralApiCommunicator,$spiralApiRequest);


$LabelSearchBarcode = new App\Api\LabelSearchBarcode($spiralDataBase, $userInfo);

$barcode = '';
if(isset($_POST['barcode']) && $_POST['barcode'] != ''){
	$barcode = $_POST['barcode'];
}
$result = $LabelSearchBarcode->search($barcode);
//結果を返却
echo json_encode($result);
