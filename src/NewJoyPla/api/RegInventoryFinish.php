<?php

namespace App\Api;

class RegInventoryFinish{

    private $spiralDataBase;
	private $userInfo;

    private $divisionId;
	
	private $totalAmount;
	private $itemsNumber;
    
    private $historyDatabase = 'NJ_InventoryEDB';

    public function __construct(\App\Lib\SpiralDataBase $spiralDataBase, \App\Lib\UserInfo $userInfo){
        $this->spiralDataBase = $spiralDataBase;
        $this->userInfo = $userInfo;
	}

    public function updateHistory(string $InventoryEId,string $authKey){

		$result = $this->updateInventoryEndHistoryDB($InventoryEId,$authKey);

		if($result['code'] != "0"){
			var_dump($result);
			return false;
		}
		return true;
	}
    
	private function updateInventoryEndHistoryDB(string $inventoryEId,string $authKey){
		$this->spiralDataBase->setDataBase($this->historyDatabase);
		$this->spiralDataBase->addSearchCondition('inventoryEndId',$inventoryEId);
		$this->spiralDataBase->addSearchCondition('invEndAuthKey',$authKey);
        $this->spiralDataBase->addSearchCondition('hospitalId',$this->userInfo->getHospitalId());
        $this->spiralDataBase->addSelectNameCondition('');
        return $this->spiralDataBase->doUpdate(array(array('name'=> 'inventoryStatus','value'=>'2'),array('name'=> 'inventoryTime','value'=>'now')));
    }

    private function requestUrldecode(array $array){
		$result = array();
		foreach($array as $key => $value){
			if(is_array($value)){
				$result[$key] = $this->requestUrldecode($value);
			} else {
				$result[$key] = urldecode($value);
			}
		}
		return $result;
	}
}