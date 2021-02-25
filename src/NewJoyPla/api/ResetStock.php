<?php

namespace App\Api;

class ResetStock{

    private $spiralDataBase;
	private $userInfo;

    private $divisionId;
    
    private $database = 'NJ_stockDB';
    private $mstDatabase = 'NJ_inHPItemDB';

    public function __construct(\App\Lib\SpiralDataBase $spiralDataBase, \App\Lib\UserInfo $userInfo){
        $this->spiralDataBase = $spiralDataBase;
        $this->userInfo = $userInfo;
    }

    private function setDivisionId(string $divisionId){
        $this->divisionId = $divisionId;
	}

    public function resetInHPItem(array $items){
		$bulkUpdateData = $this->makeBulkUpdateData($items);
		$result = $this->resetInHPItemDB($bulkUpdateData);
		if($result['code'] != "0"){
			var_dump($result);
			return false;
		}
		return true;
	} 
    
    public function resetStock(string $divisionId){
        $this->setDivisionId($divisionId);
		$result = $this->resetStockDB();
		if($result['code'] != "0"){
			var_dump($result);
			return false;
		}
		return true;
	}
	
	public function getStock(array $divisionIdArray){
		return $result = $this->getStockDB($divisionIdArray);
    }
    
    private function resetStockDB(){
    	$this->spiralDataBase->setDataBase($this->database);
        $this->spiralDataBase->addSearchCondition('hospitalId',$this->userInfo->getHospitalId());
        $this->spiralDataBase->addSearchCondition('divisionId',$this->divisionId);
        $this->spiralDataBase->addSelectNameCondition('');
		return $this->spiralDataBase->doUpdate(array(array('name'=> 'invFinishTime','value'=>'now'),array('name'=> 'stockQuantity','value'=>'0')));
	}
	
    private function getStockDB(array $divisionIdArray){
    	$this->spiralDataBase->setDataBase($this->database);
		$this->spiralDataBase->addSearchCondition('hospitalId',$this->userInfo->getHospitalId());
		foreach($divisionIdArray as $divisionId){
			$this->spiralDataBase->addSearchCondition('divisionId',$divisionId,'!=',"and");
		}
        $this->spiralDataBase->addSelectFields('inHospitalItemId','stockQuantity');
		return $this->spiralDataBase->doSelectLoop();
	}

	private function makeBulkUpdateData(array $items){
		$result = array();
		foreach($items as $inHpItemId => $array){
			$result[] = array($inHpItemId,$array['countNum']);
		}
		return $result;
	}

	private function resetInHPItemDB(array $blukUpdateData){
		$this->spiralDataBase->setDataBase($this->mstDatabase);
        $columns = array('inHospitalItemId','HPstock');
		$this->spiralDataBase->addSelectNameCondition('');
		$this->spiralDataBase->addSearchCondition('hospitalId',$this->userInfo->getHospitalId());
		return $this->spiralDataBase->doBulkUpdate('inHospitalItemId',$columns,$blukUpdateData);
	}

}