<?php
//卸業者用伝票番号検索
include_once 'NewJoyPla/lib/ApiSpiral.php';
include_once "NewJoyPla/lib/Define.php";
include_once 'NewJoyPla/lib/SpiralDataBase.php';
include_once 'NewJoyPla/lib/UserInfo.php';

include_once "NewJoyPla/lib/SpiralTable.php";
include_once "NewJoyPla/api/OroshiSearchBarcode.php";

$userInfo = new App\Lib\UserInfo($SPIRAL);

$spiralApiCommunicator = $SPIRAL->getSpiralApiCommunicator();
$spiralApiRequest = new SpiralApiRequest();
$spiralDataBase = new App\Lib\SpiralDataBase($SPIRAL,$spiralApiCommunicator,$spiralApiRequest);

$spiralApiCommunicator = $SPIRAL->getSpiralApiCommunicator();
$spiralApiRequest = new SpiralApiRequest();
$spiralTable = new App\Lib\SpiralTable($SPIRAL,$spiralApiCommunicator,$spiralApiRequest);

$oroshiSearchBarcode = new App\Api\OroshiSearchBarcode($spiralDataBase, $spiralTable, $userInfo);

$jsessonId = '';
if(isset($_COOKIE['JSESSIONID'])){
	$jsessonId = $_COOKIE['JSESSIONID'];
}

$searchValue = '';
if(isset($_POST['searchValue'])){
	$searchValue = $_POST['searchValue'];
}

$cardTitles =array(
      '03_order' => OROSHI_ORDER_PAGE,
      '04' => OROSHI_RECEIVING_PAGE,
      );

$result = $oroshiSearchBarcode->search($searchValue,$jsessonId,OROSHI_MY_AREA_TITLE,$cardTitles);
//結果を返却
echo json_encode($result);
