$in_hospital_item<?php
//受注完了処理
include_once 'NewJoyPla/lib/ApiSpiral.php';
include_once "NewJoyPla/lib/Define.php";
include_once 'NewJoyPla/lib/SpiralDataBase.php';
include_once 'NewJoyPla/lib/UserInfo.php';
include_once 'NewJoyPla/api/UpdateOrderFixing.php';
include_once "NewJoyPla/api/GetCardInfo.php";
include_once "NewJoyPla/lib/SpiralDBFilter.php";
include_once "NewJoyPla/lib/SpiralSendMail.php";

$userInfo = new App\Lib\UserInfo($SPIRAL);

$spiralApiCommunicator = $SPIRAL->getSpiralApiCommunicator();
$spiralApiRequest = new SpiralApiRequest();
$spiralDataBase = new App\Lib\SpiralDataBase($SPIRAL,$spiralApiCommunicator,$spiralApiRequest);

$updateOrderFixing = new App\Api\UpdateOrderFixing($spiralDataBase);

$cardInfo = new App\Api\GetCardInfo($spiralDataBase);
$card = $cardInfo->select("orderdataDB",$SPIRAL->getCardId(),"orderNumber","receivingTarget","divisionId","hospitalName","postalCode","prefectures","address","divisionName","orderTime","orderNumber","itemsNumber","totalAmount","hospitalId","distributorId","orderAuthKey","distributorName");
$card["data"] = $spiralDataBase->arrayToNameArray($card["data"],array("orderNumber","receivingTarget","divisionId","hospitalName","postalCode","prefectures","address","divisionName","orderTime","orderNumber","itemsNumber","totalAmount","hospitalId","distributorId","orderAuthKey","distributorName"));

$orderItems = array();
if(isset($_POST['orderItems'])){
	$orderItems = $_POST['orderItems'];
}

$result = $updateOrderFixing->update($card["data"][0]["orderNumber"],$card["data"][0]["orderAuthKey"],$orderItems);

if(! $result){
	echo json_encode(array('result'=>$result));
	exit;
}

//病院へメールを送信
$subject = "[JoyPla] 受注されました";
$text = "受注";

foreach($orderItems as $record){
	if($record["dueDate"] != ""){
		$subject = "[JoyPla] 受注（納期報告）されました";
		$text = "受注（納期報告）";
		break;
	}
}

$divisionId = $card['data'][0]['divisionId'];
$hospitalName = $card['data'][0]['hospitalName'];
$postalCode =  $card['data'][0]['postalCode'];
$prefectures = $card['data'][0]['prefectures'];
$address = $card['data'][0]['address'];
$divisionName = $card['data'][0]['divisionName'];

$orderTime = $card['data'][0]['orderTime'];
$orderNumber = $card['data'][0]['orderNumber'];
$itemsNumber = $card['data'][0]['itemsNumber'];

$totalAmount = number_format($card['data'][0]['totalAmount']);
$hospitalId =  $card['data'][0]['hospitalId'];
$distributorId =  $card['data'][0]['distributorId'];
$distributorName = $card['data'][0]['distributorName'];

$staffName = $userInfo->getName();

$url = LOGIN_URL;
$bodyText = <<<EOM
%val:usr:name% 様

JoyPla からお知らせです。
$text がされましたので、下記の通りお知らせします。

[卸業者]
業者名 $distributorName
担当者 $staffName

[発注内容]

発注日時 $orderTime

発注番号 $orderNumber

発注品目 $itemsNumber (品目)


合計金額 $totalAmount

下記URLよりログインしてご確認ください
$url

※このメールへの返信は受け付けていません。
EOM;


//部署担当者用
if(isset($hospitalId)){
	
	$spiralApiCommunicator = $SPIRAL->getSpiralApiCommunicator();
	$spiralApiRequest = new SpiralApiRequest();
	$SpiralDBFilter = new App\Lib\SpiralDBFilter($SPIRAL,$spiralApiCommunicator,$spiralApiRequest);
	$database = 'NJ_HUserDB';
	$selectName = 'S_H_'.$divisionId.'_'.$hospitalId;
	
	$SpiralDBFilter->setDataBase($database);
	
	$fields = array(
		array('name'=>'hospitalId','label'=>'name_'.$hospitalId,'value1'=>$hospitalId,'condition'=>'matches'),
		//array('name'=>'deletionFlag','label'=>'hospital_not_del','value1'=>'t','condition'=>'is_boolean','exclude'=>'t'),
		array('name'=>'userPermission','label'=>'up_tantou','value1'=>'2','condition'=>'contains'),
		array('name'=>'divisionId','label'=>$divisionId,'value1'=>$divisionId,'condition'=>'matches'),
		);
	
	$SpiralDBFilter->addSelectName($selectName);
	
	foreach($fields as $field){
		$SpiralDBFilter->addFields($field);
	}
	
	$SpiralDBFilter->create();

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

//管理者用
if(isset($hospitalId)){
	
	$spiralApiCommunicator = $SPIRAL->getSpiralApiCommunicator();
	$spiralApiRequest = new SpiralApiRequest();
	$SpiralDBFilter = new App\Lib\SpiralDBFilter($SPIRAL,$spiralApiCommunicator,$spiralApiRequest);
	$database = 'NJ_HUserDB';
	$selectName = 'S_H_Admin_'.$hospitalId;
	
	$SpiralDBFilter->setDataBase($database);
	

	$fields = array(
		array('name'=>'hospitalId','label'=>'name_'.$hospitalId,'value1'=>$hospitalId,'condition'=>'matches'),
		//array('name'=>'deletionFlag','label'=>'hospital_not_del','value1'=>'t','condition'=>'is_boolean','exclude'=>'t'),
		array('name'=>'userPermission','label'=>'up_kanri','value1'=>'1','condition'=>'contains'),
		);
	
	$SpiralDBFilter->addSelectName($selectName);
	
	foreach($fields as $field){
		$SpiralDBFilter->addFields($field);
	}
	
	$SpiralDBFilter->create();
	
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