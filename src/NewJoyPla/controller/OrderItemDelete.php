
<?php
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
	$request[] = $key;
}
if(count($request) == 0){
   echo json_encode(array('result'=>true));
   exit;
}

$result = $reqOrderItems->delete($request);
if($result['code'] != 0){
   echo json_encode(array('result'=>false));
   exit;
}
//結果を返却
echo json_encode(array('result'=>true));