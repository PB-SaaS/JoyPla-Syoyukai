<?php

/*
namespace App\Api;

class RegItemsBlukIns{

    private $spiralDataBase;
    private $userInfo;

    private $database = "NJ_itemDB";

    public function __construct(\App\Lib\SpiralDataBase $spiralDataBase, \App\Lib\UserInfo $userInfo){
        $this->spiralDataBase = $spiralDataBase;
        $this->userInfo = $userInfo;
    }

    public function blukinsert(array $itemList){
        $itemList = $this->requestUrldecode($itemList);
        $itemList = $this->remake($itemList);
        return $this->regItems($itemList);
    }

    private function remake(array $itemList){
        foreach($itemList as &$record){
            $record[] = $this->userInfo->getTenantId();
        }
        return $itemList;
    }

    private function regItems(array $itemList){

        /**
         * ここに処理を書く
         */
        $columns = array("itemName","itemCode","itemStandard","itemJANCode","makerName","officialFlag","officialpriceOld","officialprice","quantity","quantityUnit","itemUnit","minPrice","tenantId");
        
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
 
