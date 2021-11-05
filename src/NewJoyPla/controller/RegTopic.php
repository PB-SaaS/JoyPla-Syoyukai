<?php
//トピック登録後通知　サンクスページに挿入
/*
include_once 'NewJoyPla/lib/ApiSpiral.php';
include_once "NewJoyPla/lib/Define.php";
include_once "NewJoyPla/lib/SpiralDBFilter.php";
include_once "NewJoyPla/lib/SpiralSendMail.php";
include_once "NewJoyPla/lib/Func.php";
include_once "NewJoyPla/lib/Define.php";


/****
 * 病院ユーザーへの送信 
 */
/*
if($SPIRAL->getContextByFieldTitle("hospitalId") != ''){

	$spiralApiCommunicator = $SPIRAL->getSpiralApiCommunicator();
	$spiralApiRequest = new SpiralApiRequest();
	$SpiralDBFilter = new App\Lib\SpiralDBFilter($SPIRAL,$spiralApiCommunicator,$spiralApiRequest);
	$database = 'NJ_HUserDB';
	$selectName = 'S_H_'.$SPIRAL->getContextByFieldTitle("hospitalId");
	
	$SpiralDBFilter->setDataBase($database);
	
	$hospitalId = App\Lib\html($SPIRAL->getContextByFieldTitle("hospitalId"));

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
	
	$topicTitle = App\Lib\html($SPIRAL->getContextByFieldTitle("topicTitle"));
	$topicName = App\Lib\html($SPIRAL->getContextByFieldTitle("topicName"));
	
	$subject = "[JoyPla] ".$topicName."さんがトピック「".$topicTitle."」を作成しました";
	
	$url = LOGIN_URL;
	
	$bodyText = <<<EOM
%val:usr:name% 様

JoyPla からお知らせです。

新しいトピックが生成されました。

【タイトル】$topicTitle
【作成者】$topicName

下記URLよりログインしてご確認ください
$url

※このメールへの返信は受け付けていません。
EOM;
	
	
	$SpiralSendMail->setDataBase($database);
	$SpiralSendMail->addMailFieldTitle(HP_MAIL_FIELD_TITLE);
	$SpiralSendMail->addSubject($subject);
	$SpiralSendMail->addBodyText($bodyText);
	$SpiralSendMail->addFromAddress(FROM_ADDRESS);
	$SpiralSendMail->addFromName(FROM_NAME);
	$SpiralSendMail->addSelectName($selectName);
	$SpiralSendMail->regist();

}

/****
 * 卸ユーザーへの送信 
 */
/*
if($SPIRAL->getContextByFieldTitle("distributorId")  != '' ){
		
	$spiralApiCommunicator = $SPIRAL->getSpiralApiCommunicator();
	$spiralApiRequest = new SpiralApiRequest();
	$SpiralDBFilter = new App\Lib\SpiralDBFilter($SPIRAL,$spiralApiCommunicator,$spiralApiRequest);
	$database = 'NJ_OUserDB';
	$selectName = 'S_O_'.$SPIRAL->getContextByFieldTitle("distributorId");
	
	$SpiralDBFilter->setDataBase($database);
	
	$distributorId = App\Lib\html($SPIRAL->getContextByFieldTitle("distributorId"));

	$fields = array(
		array('name'=>'distributorId','label'=>'name_'.$distributorId ,'value1'=>$distributorId ,'condition'=>'matches'),
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
	
	$topicTitle = App\Lib\html($SPIRAL->getContextByFieldTitle("topicTitle"));
	$topicName = App\Lib\html($SPIRAL->getContextByFieldTitle("topicName"));
	
	$subject = "[JoyPla] ".$topicName."さんがトピック「".$topicTitle."」を作成しました";
	
	$url = LOGIN_URL;

	$bodyText = <<<EOM
%val:usr:name% 様

JoyPla からお知らせです。

新しいトピックが生成されました。

【タイトル】$topicTitle
【作成者】$topicName

下記URLよりログインしてご確認ください
$url

※このメールへの返信は受け付けていません。
EOM;

	
	$SpiralSendMail->setDataBase($database);
	$SpiralSendMail->addMailFieldTitle(HP_MAIL_FIELD_TITLE);
	$SpiralSendMail->addSubject($subject);
	$SpiralSendMail->addBodyText($bodyText);
	$SpiralSendMail->addFromAddress(FROM_ADDRESS);
	$SpiralSendMail->addFromName(FROM_NAME);
	$SpiralSendMail->addSelectName($selectName);
	$SpiralSendMail->regist();

}
*/