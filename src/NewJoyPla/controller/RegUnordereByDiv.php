<?php
//発注調整用_未発注伝票作成
include_once 'NewJoyPla/lib/ApiSpiral.php';
include_once "NewJoyPla/lib/Define.php";
include_once 'NewJoyPla/lib/SpiralDataBase.php';
include_once 'NewJoyPla/lib/UserInfo.php';
include_once 'NewJoyPla/api/RegUnordered.php';

$userInfo = new App\Lib\UserInfo($SPIRAL);

$spiralApiCommunicator = $SPIRAL->getSpiralApiCommunicator();
$spiralApiRequest = new SpiralApiRequest();
$spiralDataBase = new App\Lib\SpiralDataBase($SPIRAL,$spiralApiCommunicator,$spiralApiRequest);

$regUnordered = new App\Api\RegUnordered($spiralDataBase,$userInfo);

foreach($_POST['ordered'] as $divisionId => $ordered){
  $result = $regUnordered->register($ordered,$divisionId );

  if(! $result){
     echo json_encode(array('result'=>$result));
  }
}

//結果を返却
echo json_encode(array('result'=>$result));

