<?php

namespace App\Lib;

class SpiralSendMail{

    protected $database;
    protected $mailFieldTitle = '';
    protected $reserveDate = 'now';
    protected $subject = '';
    protected $bodyText = '';
    protected $fromAddress = '';
    protected $fromName = '';
    protected $replyTo = '';
    protected $selectName = '';
    protected $searchCondition = array();
    protected $apiSpiral;

	public function __construct(\Spiral $SPIRAL,\PbSpiralApiCommunicator $PbSpiralApiCommunicator ,\SpiralApiRequest $SpiralApiRequest){
		$this->spiral = $SPIRAL;
		$this->setApiSpiral($PbSpiralApiCommunicator,$SpiralApiRequest);
	}
	
	private function setApiSpiral(\PbSpiralApiCommunicator $PbSpiralApiCommunicator ,\SpiralApiRequest $SpiralApiRequest){
		//$this->spiral->setApiTokenTitle(APITITLE); //APIタイトル
		$this->apiSpiral = new \App\Lib\ApiSpiral($this->spiral);
		$this->apiSpiral->setApiCommunicator($PbSpiralApiCommunicator);
		$this->apiSpiral->setSpiralApiRequest($SpiralApiRequest);
	}

	public function setDataBase(string $database){
		$this->database = $database;
	}
	
	public function addMailFieldTitle(string $field){
        $this->mailFieldTitle = $field;
	}
	
	public function addReserveDate(string $date){
        $this->reserveDate = $date;
	}
	
	public function addSubject(string $subject){
        $this->subject = $subject;
	}
	
	public function addBodyText(string $bodyText){
        $this->bodyText = $bodyText;
	}
	
	public function addFromAddress(string $fromAddress){
        $this->fromAddress = $fromAddress;
	}
	
	public function addFromName(string $fromName){
        $this->fromName = $fromName;
	}
	
	public function addReplyTo(string $replyTo){
        $this->replyTo = $replyTo;
	}
	
	public function addSelectName(string $selectName){
        $this->selectName = $selectName;
	}
	
	public function addSearchCondition(array $searchCondition){
        $this->searchCondition[] = $searchCondition;
	}

	public function regist(){
		$apiHeader = array("deliver_express2","regist");
		$parameters = array();
		$parameters['db_title'] = $this->database;
		$parameters['mail_field_title'] = $this->mailFieldTitle;
		$parameters['reserve_date'] = $this->reserveDate;
		$parameters['subject'] = $this->subject;
		$parameters['body_text'] = $this->bodyText;
		$parameters['from_address'] = $this->fromAddress;
		$parameters['from_name'] = $this->fromName;
		$parameters['reply_to'] = $this->replyTo;
		$parameters['select_name'] = $this->selectName;
		$this->clearData();
		return $this->apiSpiral->requestAPI($apiHeader, $parameters);
	}

	public function list(){
		$apiHeader = array("deliver_express2","list");
		$parameters = array();
		$parameters['db_title'] = $this->database;
		$parameters['search_condition'] = $this->searchCondition;
		$this->clearData();
		return $this->apiSpiral->requestAPI($apiHeader, $parameters);
	}

	public function cancel(){
		$apiHeader = array("deliver_express2","cancel");
		$parameters = array();
		$parameters['db_title'] = $this->database;
		$parameters['search_condition'] = $this->searchCondition;
		$this->clearData();
		return $this->apiSpiral->requestAPI($apiHeader, $parameters);
	}

	public function thanks($rule_id,$id){
		$apiHeader = array("deliver_thanks","send");
		$parameters = array();
		$parameters['rule_id'] = $rule_id;
		$parameters['id'] = $id;
		return $this->apiSpiral->requestAPI($apiHeader, $parameters);
	}

	private function clearData(){
		$this->database;
		$this->mailFieldTitle = '';
		$this->reserveDate = 'now';
		$this->subject = '';
		$this->bodyText = '';
		$this->fromAddress = '';
		$this->fromName = '';
		$this->replyTo = '';
		$this->selectName = '';
	}

}