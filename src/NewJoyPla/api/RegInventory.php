<?php

namespace App\Api;

class RegInventory{

    private $spiralDataBase;
	private $userInfo;

    private $divisionId;
	
    private $InventoryEId;
    private $InventoryHId;
    
    private $database = 'NJ_stocktakingTR';

    public function __construct(\App\Lib\SpiralDataBase $spiralDataBase, \App\Lib\UserInfo $userInfo){
        $this->spiralDataBase = $spiralDataBase;
        $this->userInfo = $userInfo;
	}
	
    private function setDivisionId(string $divisionId){
        $this->divisionId = $divisionId;
	}

    public function register(array $array, string $divisionId, string $InventoryEId , string $InventoryHId){
		$array = $this->requestUrldecode($array);
		$this->setDivisionId($divisionId);
		$makeInventory = $this->makeInventory($array,$InventoryEId,$InventoryHId);

		$result = $this->regInventory($makeInventory);
		if($result['code'] != "0"){
			var_dump($result);
			return false;
		}
		return true;
	}
	
    private function makeInventory(array $array, string $InventoryEId , string $InventoryHId){

        /**
         * ここに処理を書く
         */
        //$columns = array('registrationTime','updateTime','inventoryHId','inHospitalItemId','hospitalId','price','calculatingStock','inventryNum','inventryAmount');

		$itemList = array();
		foreach($array as $inHPid => $data){
			if( (int)$data['countNum']  > 0 ){
			$itemList[] = array(
				'now',
				$InventoryEId,
				$InventoryHId,
				$inHPid,
				$this->userInfo->getHospitalId(),
				$this->divisionId,
				str_replace(',', '', $data['kakaku']),
				'',
				(int)$data['countNum'],
				(string)(str_replace(',', '', $data['kakaku']) / $data['irisu'] * (int)$data['countNum']),
				$data['irisu'],
				$data['unit'],
				$data['itemUnit'],
				);
			}
		}

		return $itemList;
    }
    
    private function regInventory(array $itemList){

        /**
         * ここに処理を書く
         */
        $columns = array('registrationTime','inventoryEndId','inventoryHId','inHospitalItemId','hospitalId','divisionId','price','calculatingStock','inventryNum','inventryAmount','quantity','quantityUnit','itemUnit');

        $this->spiralDataBase->setDataBase($this->database);

        return $this->spiralDataBase->doBulkInsert($columns ,$itemList);

        //throw new Exception('エラーハンドリング');
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