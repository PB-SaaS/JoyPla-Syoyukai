<?php

namespace App\Lib;

class SpiralTable {

	protected $jsessionid = '';
	protected $myAreaTitle = '';
	protected $cardTitle = '';
	protected $ids = array();
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

	public function setJsessionid(string $jsessionid){
		$this->jsessionid = $jsessionid;
	}
	
	public function setMyAreaTitle(string $myAreaTitle){
        $this->myAreaTitle = $myAreaTitle;
	}

	public function setCardTitle(string $cardTitle){
        $this->cardTitle = $cardTitle;
	}
	
	public function addIds(string $id){
        $this->ids[] = $id;
	}

	public function getCardUrls(){
		$apiHeader = array("table","card");
		$parameters = array();
		$parameters['jsessionid'] = $this->jsessionid;
		$parameters['my_area_title'] = $this->myAreaTitle;
		$parameters['card_title'] = $this->cardTitle;
		$parameters['ids'] = $this->ids;
		$parameters['url_type'] = '2';
		$this->clearData();

		return $this->apiSpiral->requestAPI($apiHeader, $parameters);
	}

	private function clearData(){
		$this->jsessionid = '';
		$this->myAreaTitle = '';
		$this->cardTitle = '';
		$this->ids = array();
	}

}