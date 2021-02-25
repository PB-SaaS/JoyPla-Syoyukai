
<?php
//伝票番号検索
include_once 'NewJoyPla/lib/ApiSpiral.php';
include_once "NewJoyPla/lib/Define.php";
include_once 'NewJoyPla/lib/SpiralDataBase.php';
include_once 'NewJoyPla/lib/UserInfo.php';

include_once "NewJoyPla/lib/SpiralTable.php";
include_once "NewJoyPla/api/SearchBarcode.php";

$spiralApiCommunicator = $SPIRAL->getSpiralApiCommunicator();
$spiralApiRequest = new SpiralApiRequest();

$userInfo = new App\Lib\UserInfo($SPIRAL);

$spiralDataBase = new App\Lib\SpiralDataBase($SPIRAL,$spiralApiCommunicator,$spiralApiRequest);

$spiralTable = new App\Lib\SpiralTable($SPIRAL,$spiralApiCommunicator,$spiralApiRequest);

$searchBarcode = new App\Api\SearchBarcode($spiralDataBase, $spiralTable, $userInfo);

//jsessonId を取得
$jsessonId = '';
if(isset($_COOKIE['JSESSIONID'])){
	$jsessonId = $_COOKIE['JSESSIONID'];
}
//検索キーワード を取得
$searchValue = '';
if(isset($_POST['searchValue'])){
	$searchValue = $_POST['searchValue'];
}

//定数から単票のタイトル を設定
$cardTitles =array(
      '02' => HP_BILLING_PAGE,
      '03_unorder' => HP_UNORDER_PAGE,
      '03_order' => HP_ORDER_PAGE,
      '04' => HP_RECEIVING_PAGE,
      '06' => HP_RETERN_PAGE,
      '05' => HP_PAYOUT_PAGE,
      );

$result = $searchBarcode->search($searchValue,$jsessonId,MY_AREA_TITLE,$cardTitles);

//結果を返却
echo json_encode($result);
