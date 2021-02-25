<?php
//発注確定処理
include_once 'NewJoyPla/lib/ApiSpiral.php';
include_once "NewJoyPla/lib/Define.php";
include_once 'NewJoyPla/lib/SpiralDataBase.php';
include_once 'NewJoyPla/lib/UserInfo.php';
include_once 'NewJoyPla/lib/Func.php';
include_once 'NewJoyPla/api/RegOrder.php';
include_once 'NewJoyPla/api/RegInventoryTrdb.php';
include_once 'NewJoyPla/api/GetDivision.php';
//include_once 'NewJoyPla/api/GetHospitalData.php';

include_once "NewJoyPla/lib/SpiralDBFilter.php";
include_once "NewJoyPla/lib/SpiralSendMail.php";
include_once "NewJoyPla/api/GetCardInfo.php";

$userInfo = new App\Lib\UserInfo($SPIRAL);

$spiralApiCommunicator = $SPIRAL->getSpiralApiCommunicator();
$spiralApiRequest = new SpiralApiRequest();
$spiralDataBase = new App\Lib\SpiralDataBase($SPIRAL,$spiralApiCommunicator,$spiralApiRequest);

//発注確定処理
$regOrder = new App\Api\RegOrder($spiralDataBase);

//単票情報取得
$cardInfo = new App\Api\GetCardInfo($spiralDataBase);
$card = $cardInfo->select("orderdataDB",$SPIRAL->getCardId(),"orderNumber","receivingTarget","divisionId","hospitalName","postalCode","prefectures","address","divisionName","orderTime","orderNumber","itemsNumber","totalAmount","hospitalId","distributorId");
$card["data"] = $spiralDataBase->arrayToNameArray($card["data"],array("orderNumber","receivingTarget","divisionId","hospitalName","postalCode","prefectures","address","divisionName","orderTime","orderNumber","itemsNumber","totalAmount","hospitalId","distributorId"));

$orderNumber = $card['data'][0]['orderNumber'];

$crypt   = $SPIRAL->getSpiralCryptOpenSsl();
$orderAuthKey = $crypt->decrypt($_POST['orderAuthKey'], 'JoyPla');

$result = $regOrder->order($orderNumber,$orderAuthKey,$_POST['ordercomment']);

//部署情報取得
$getDivision = new App\Api\GetDivision($spiralDataBase,$userInfo);
$divisionData = $getDivision->select();
if($divisionData['code'] != '0'){
	echo json_encode(array('result'=>false));
	exit;
}

//在庫データの更新
$regInventoryTrdb = new App\Api\RegInventoryTrdb($spiralDataBase,$userInfo);

$orderWithinCount = array();
if(isset($_POST['order'])){
	$orderWithinCount = $_POST['order'];
}

foreach($orderWithinCount as $key => $record){
	$orderWithinCount[$key]['countNum'] = $record['quantity'] * $record['countNum'];
}

/*
$getHospitalData = new App\Api\GetHospitalData($spiralDataBase,$userInfo);
$hospitalData = $getHospitalData->select();
*/

if($card['data'][0]['receivingTarget'] == '1'){ //大倉庫
	$divisionId = $divisionData['store'][0][1];
}
if($card['data'][0]['receivingTarget'] == '2'){ //発注部署
	$divisionId = $card['data'][0]['divisionId'];
}

$result = $regInventoryTrdb->orderWithinCount($orderWithinCount,$divisionId,'1');


//卸業者へのメール送信
$subject = "[JoyPla] 発注が行われました";


$divisionId = $card['data'][0]['divisionId'];
$hospitalName = $card['data'][0]['hospitalName'];
$postalCode =  $card['data'][0]['postalCode'];
$prefectures = $card['data'][0]['prefectures'];
$address = $card['data'][0]['address'];
$divisionName = $card['data'][0]['divisionName'];

//$orderTime = $card['data'][0]['orderTime'];
$orderTime = date("Y年m月d日 H時i分s秒");
$orderNumber = $card['data'][0]['orderNumber'];
$itemsNumber = $card['data'][0]['itemsNumber'];

$totalAmount = number_format($card['data'][0]['totalAmount']);
$hospitalId =  $card['data'][0]['hospitalId'];
$distributorId =  $card['data'][0]['distributorId'];

$url = OROSHI_LOGIN_URL;
$bodyText = <<<EOM
%val:usr:name% 様

JoyPla からお知らせです。
発注書が送信されておりますので、下記の通りお知らせします。

[医療機関]
施設名 $hospitalName
〒$postalCode $prefectures $address
部署名 $divisionName

[発注内容]

発注日時 $orderTime

発注番号 $orderNumber

発注品目 $itemsNumber (品目)


合計金額 $totalAmount

下記URLよりログインしてご確認ください
$url

※このメールへの返信は受け付けていません。
EOM;
if(isset($distributorId)){
	$spiralApiCommunicator = $SPIRAL->getSpiralApiCommunicator();
	$spiralApiRequest = new SpiralApiRequest();
	$SpiralDBFilter = new App\Lib\SpiralDBFilter($SPIRAL,$spiralApiCommunicator,$spiralApiRequest);
	$database = 'NJ_OUserDB';
	$selectName = 'S_O_'.$distributorId;
	
	$SpiralDBFilter->setDataBase($database);

	$fields = array(
		array('name'=>'distributorId','label'=>'name_'.$distributorId,'value1'=>$distributorId,'condition'=>'matches'),
		//array('name'=>'deletionFlag','label'=>'oroshi_not_del','value1'=>'t','condition'=>'is_boolean','exclude'=>'t'),
		);
	
	$SpiralDBFilter->addSelectName($selectName);
	
	foreach($fields as $field){
		$SpiralDBFilter->addFields($field);
	}
	
	$test = $SpiralDBFilter->create();
	$spiralApiCommunicator = $SPIRAL->getSpiralApiCommunicator();
	$spiralApiRequest = new SpiralApiRequest();
	$SpiralSendMail = new App\Lib\SpiralSendMail($SPIRAL,$spiralApiCommunicator,$spiralApiRequest);
	
	$SpiralSendMail->setDataBase($database);
	$SpiralSendMail->addMailFieldTitle(HP_MAIL_FIELD_TITLE);
	$SpiralSendMail->addSubject($subject);
	$SpiralSendMail->addBodyText($bodyText);
	$SpiralSendMail->addFromAddress(FROM_ADDRESS);
	$SpiralSendMail->addFromName(FROM_NAME);
	$SpiralSendMail->addSelectName($selectName);
	$SpiralSendMail->regist();

}

//結果を返却
echo json_encode(array('result'=>$result));
