<?php

namespace App\Api;

class RegInventoryEndHistory{

    private $spiralDataBase;
	private $userInfo;

    private $divisionId;
	
	
	private $totalAmount;
	private $itemsNumber;
    
    private $historyDatabase = 'NJ_InventoryEDB';
    private $childDatabase = 'NJ_InventoryDB';

    public function __construct(\App\Lib\SpiralDataBase $spiralDataBase, \App\Lib\UserInfo $userInfo){
        $this->spiralDataBase = $spiralDataBase;
        $this->userInfo = $userInfo;
	}
	
    private function setDivisionId(string $divisionId){
        $this->divisionId = $divisionId;
	}

    public function updateHistory(string $InventoryEId){

		$inventoryDB = $this->getInventoryDB($InventoryEId);
		if($inventoryDB['code'] != "0"){
			var_dump($inventoryHDB);
			return false;
		}
		$this->totalAmount = $this->sumInventoryAmount($inventoryDB['data']);
		$this->itemsNumber = $this->sumItemsNumber($inventoryDB['data']);
		
		$result = $this->updateInventoryEndHistoryDB($InventoryEId);

		if($result['code'] != "0"){
			var_dump($result);
			return false;
		}
		return true;
	}

	private function sumInventoryAmount(array $array){
		$sum = 0;
		foreach($array as $record){
			$sum = $sum + $record[1];
		}

		return $sum;
	}

	private function sumItemsNumber(array $array){
		$sum = 0;
		$items = array();
		foreach($array as $record){
			if(in_array($record[0], $items)){
				continue;
			}
			$items[] = $record[0];
			$sum ++;
		}

		return $sum;
	}
/*
	private function getInventoryHDB(string $inventoryEId){
        $this->spiralDataBase->setDataBase($this->childDatabase);
        $this->spiralDataBase->addSearchCondition('inventoryEndId',$inventoryEId);
		$this->spiralDataBase->addSearchCondition('hospitalId',$this->userInfo->getHospitalId());
		$this->spiralDataBase->setGroupByFields('inHospitalItemId');
        $this->spiralDataBase->addSelectFields('hospitalId','itemsNumber','totalAmount','inventoryEndId','inHospitalItemId');
        return $this->spiralDataBase->doSelectLoop();
	}
*/
	private function getInventoryDB(string $inventoryEId){
		$this->spiralDataBase->setDataBase($this->childDatabase);
		$this->spiralDataBase->addSearchCondition('inventoryEndId',$inventoryEId);
		$this->spiralDataBase->addSearchCondition('hospitalId',$this->userInfo->getHospitalId());
		$this->spiralDataBase->addSelectFields('inHospitalItemId','inventryAmount');
		return $this->spiralDataBase->doSelectLoop();
	}

	private function updateInventoryEndHistoryDB(string $inventoryEId){
		$this->spiralDataBase->setDataBase($this->historyDatabase);
		$this->spiralDataBase->addSearchCondition('inventoryEndId',$inventoryEId);
        $this->spiralDataBase->addSearchCondition('hospitalId',$this->userInfo->getHospitalId());
        $this->spiralDataBase->addSelectNameCondition('');
        return $this->spiralDataBase->doUpdate(array(array('name'=> 'itemsNumber','value'=>$this->itemsNumber),array('name'=> 'totalAmount','value'=>$this->totalAmount)));
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