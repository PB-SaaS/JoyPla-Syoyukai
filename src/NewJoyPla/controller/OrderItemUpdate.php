<?php
//発注商品更新
include_once 'NewJoyPla/lib/ApiSpiral.php';
include_once "NewJoyPla/lib/Define.php";
include_once 'NewJoyPla/lib/SpiralDataBase.php';
include_once 'NewJoyPla/api/ReqOrderItems.php';

$spiralApiCommunicator = $SPIRAL->getSpiralApiCommunicator();
$spiralApiRequest = new SpiralApiRequest();
$spiralDataBase = new App\Lib\SpiralDataBase($SPIRAL,$spiralApiCommunicator,$spiralApiRequest);

$reqOrderItems = new App\Api\ReqOrderItems($spiralDataBase);

$request = array();
foreach($_POST['data'] as $key => $data){
	$request[] = array($key,$data['num']);
}

$result = $reqOrderItems->bulkUpdate($request);
if($result['code'] != 0){
   echo json_encode(array('result'=>false));
   exit;
}

$deleteRequest = array();
foreach($_POST['data'] as $key => $data){
   if($data['num'] != '0'){
      continue;
   }
	$deleteRequest[] = $key;
}

if(count($deleteRequest) != 0){
   //発注数0のものは削除
   $result = $reqOrderItems->delete($deleteRequest);
   if($result['code'] != 0){
      echo json_encode(array('result'=>false));
      exit;
   }
}
//結果を返却
echo json_encode(array('result'=>true));