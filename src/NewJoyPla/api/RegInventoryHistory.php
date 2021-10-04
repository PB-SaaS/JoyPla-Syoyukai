<?php

namespace App\Api;

class RegInventoryHistory{

    private $spiralDataBase;
	private $userInfo;

    private $divisionId;
	
	
	private $totalAmount;
	private $itemsNumber;
    
    private $historyDatabase = 'NJ_InventoryHDB';
    private $childDatabase = 'NJ_InventoryDB';

    public function __construct(\App\Lib\SpiralDataBase $spiralDataBase, \App\Lib\UserInfo $userInfo){
        $this->spiralDataBase = $spiralDataBase;
        $this->userInfo = $userInfo;
	}
	
    private function setDivisionId(string $divisionId){
        $this->divisionId = $divisionId;
	}

    public function updateHistory(string $InventoryHId,string $itemsNumber = "0",string $totalAmount = "0"){

		$inventoryDB = $this->getInventoryDB($InventoryHId);

		$this->totalAmount = $this->sumInventryAmount($inventoryDB['data']);
		$this->itemsNumber = $this->sumItemsNumber($inventoryDB['data']);

		if($itemsNumber != $this->itemsNumber || $totalAmount != $this->totalAmount ){
		
			$result = $this->updateInventoryHistoryDB($InventoryHId);

			if($result['code'] != "0"){
				var_dump($result);
				return false;
			}
		}

		return true;
	}

	private function sumInventryAmount(array $array){
		$sum = 0;
		foreach($array as $record){
			$sum = $sum + (float)$record[1];
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

	private function getInventoryDB(string $inventoryHId){
        $this->spiralDataBase->setDataBase($this->childDatabase);
        $this->spiralDataBase->addSearchCondition('inventoryHId',$inventoryHId);
        $this->spiralDataBase->addSearchCondition('hospitalId',$this->userInfo->getHospitalId());
        $this->spiralDataBase->addSelectFields('inHospitalItemId','inventryAmount');
        return $this->spiralDataBase->doSelectLoop();
	}
	
	private function updateInventoryHistoryDB(string $inventoryHId){
        $this->spiralDataBase->setDataBase($this->historyDatabase);
		$this->spiralDataBase->addSearchCondition('inventoryHId',$inventoryHId);
        $this->spiralDataBase->addSelectNameCondition('');
        return $this->spiralDataBase->doUpdate(array(array('name'=> 'updateTime','value'=>"now"),array('name'=> 'itemsNumber','value'=>$this->itemsNumber),array('name'=> 'totalAmount','value'=>$this->totalAmount)));
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