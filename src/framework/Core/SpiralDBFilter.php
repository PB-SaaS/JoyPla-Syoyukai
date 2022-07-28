<?php

namespace App\Lib;

class SpiralDBFilter{

    protected $database;
    protected $selectName = '';
	protected $fields = array();
	
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
	
	public function addSelectName(string $selectName){
        $this->selectName = $selectName;
	}
	
	public function addFields(array $fields){
        $this->fields[] = $fields;
	} 

	public function create(){
		$apiHeader = array("db_filter","create");
		$parameters = array();
		$parameters['db_title'] = $this->database;
		$parameters['select_name'] = $this->selectName;
		$parameters['fields'] = $this->fields;

		$listData = $this->list();
		if($listData['code'] != '0'){
			return $listData;
		}

		foreach($listData['data'] as $selectName){
			if($selectName['select_name'] == $this->selectName){
				return array('code'=> '0','select_name'=>$this->selectName,'message'=>'registered');
			}
		}
		$this->clearData();
		
		return $this->apiSpiral->requestAPI($apiHeader, $parameters);
	}

	public function list(){
		$apiHeader = array("db_filter","list");
		$parameters = array();
		$parameters['db_title'] = $this->database;
		$parameters['select_name'] = $this->selectName;
		return $this->apiSpiral->requestAPI($apiHeader, $parameters);
	}

	private function clearData(){
		$this->database = '';
		$this->selectName = '';
		$this->fields = '';
	}

}