<?php

namespace App\Lib;

class SpiralArea {

	protected $jsessionid = '';
	protected $myAreaTitle = '';
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

	public function getUrl(){
		$apiHeader = array("area","mypage");
		$parameters = array();
		$parameters['jsessionid'] = $this->jsessionid;
		$parameters['my_area_title'] = $this->myAreaTitle;
		$this->clearData();

		return $this->apiSpiral->requestAPI($apiHeader, $parameters);
	}

	private function clearData(){
		$this->jsessionid = '';
		$this->myAreaTitle = '';
	}

}