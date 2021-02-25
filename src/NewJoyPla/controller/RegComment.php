<?php
//トピックにコメントを投稿
include_once 'NewJoyPla/lib/ApiSpiral.php';
include_once "NewJoyPla/lib/Define.php";
include_once 'NewJoyPla/lib/SpiralDataBase.php';
include_once 'NewJoyPla/api/RegComment.php';
include_once "NewJoyPla/lib/SpiralDBFilter.php";
include_once "NewJoyPla/lib/SpiralSendMail.php";
include_once "NewJoyPla/lib/Func.php";
include_once "NewJoyPla/api/GetCardInfo.php";

$spiralApiCommunicator = $SPIRAL->getSpiralApiCommunicator();
$spiralApiRequest = new SpiralApiRequest();
$spiralDataBase = new App\Lib\SpiralDataBase($SPIRAL,$spiralApiCommunicator,$spiralApiRequest);

$regComment = new App\Api\RegComment($spiralDataBase);

$crypt   = $SPIRAL->getSpiralCryptOpenSsl();
$authKey = $crypt->decrypt($_POST['authKey'], 'JoyPla');

//コメントを登録
$result = $regComment->regComment($_POST['topicId'],$authKey,$_POST['commentData']);

if($result['code'] != "0"){
	var_dump($result);
	echo json_encode(array('result'=>false));
	exit;
}

$cardInfo = new App\Api\GetCardInfo($spiralDataBase);
$card = $cardInfo->select("topicsDB",$SPIRAL->getCardId(),"topicId","authKey","topicTitle","hospitalId","distributorId");

//通知メール送信
$hospitalId = $card['data'][0][3];
$distributorId = $card['data'][0][4];

$topicTitle = $card['data'][0][2];
$topicName = urldecode($_POST['commentData']['name']);
$url = LOGIN_URL;
$bodyText = <<<EOM
%val:usr:name% 様

JoyPla からお知らせです。

次のトピックに返信がありました。

【タイトル】$topicTitle
【作成者】$topicName

下記URLよりログインしてご確認ください
$url

※このメールへの返信は受け付けていません。
EOM;
/****
 * 病院ユーザーへの送信 
 */
if(isset($hospitalId)){

	$spiralApiCommunicator = $SPIRAL->getSpiralApiCommunicator();
	$spiralApiRequest = new SpiralApiRequest();
	$SpiralDBFilter = new App\Lib\SpiralDBFilter($SPIRAL,$spiralApiCommunicator,$spiralApiRequest);
	$database = 'NJ_HUserDB';
	$selectName = 'S_H_'.$hospitalId;
	
	$SpiralDBFilter->setDataBase($database);

	$fields = array(
		array('name'=>'hospitalId','label'=>'name_'.$hospitalId,'value1'=>$hospitalId,'condition'=>'matches'),
		//array('name'=>'deletionFlag','label'=>'hospital_not_del','value1'=>'t','condition'=>'is_boolean','exclude'=>'t'),
		);
	
	$SpiralDBFilter->addSelectName($selectName);
	
	foreach($fields as $field){
		$SpiralDBFilter->addFields($field);
	}
	
	$SpiralDBFilter->create();
	
	$spiralApiCommunicator = $SPIRAL->getSpiralApiCommunicator();
	$spiralApiRequest = new SpiralApiRequest();
	$SpiralSendMail = new App\Lib\SpiralSendMail($SPIRAL,$spiralApiCommunicator,$spiralApiRequest);
	
	$subject = "[JoyPla] ".$topicName."さんがトピック「".$topicTitle."」に返信しました";
	
	$SpiralSendMail->setDataBase($database);
	$SpiralSendMail->addMailFieldTitle(HP_MAIL_FIELD_TITLE);
	$SpiralSendMail->addSubject($subject);
	$SpiralSendMail->addBodyText($bodyText);
	$SpiralSendMail->addFromAddress(FROM_ADDRESS);
	$SpiralSendMail->addFromName(FROM_NAME);
	$SpiralSendMail->addSelectName($selectName);
	$SpiralSendMail->regist();

	
}

$url = OROSHI_LOGIN_URL;
$bodyText = <<<EOM
%val:usr:name% 様

JoyPla からお知らせです。

次のトピックに返信がありました。

【タイトル】$topicTitle
【作成者】$topicName

下記URLよりログインしてご確認ください
$url

※このメールへの返信は受け付けていません。
EOM;
/****
 * 卸ユーザーへの送信 
 */
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
	
	$SpiralDBFilter->create();
	
	$spiralApiCommunicator = $SPIRAL->getSpiralApiCommunicator();
	$spiralApiRequest = new SpiralApiRequest();
	$SpiralSendMail = new App\Lib\SpiralSendMail($SPIRAL,$spiralApiCommunicator,$spiralApiRequest);
	
	$subject = "[JoyPla] ".$topicName."さんがトピック「".$topicTitle."」に返信しました";
	
	$SpiralSendMail->setDataBase($database);
	$SpiralSendMail->addMailFieldTitle(OROSHI_MAIL_FIELD_TITLE);
	$SpiralSendMail->addSubject($subject);
	$SpiralSendMail->addBodyText($bodyText);
	$SpiralSendMail->addFromAddress(FROM_ADDRESS);
	$SpiralSendMail->addFromName(FROM_NAME);
	$SpiralSendMail->addSelectName($selectName);
	$SpiralSendMail->regist();

}
//結果を返却
echo json_encode(array('result'=>true));