<?php

namespace App\Api;

class RegRequestItems{

    private $database = "NJ_PriceDB";
    private $requestId = "";
    private $distributorId = "";
    
    public function __construct(\App\Lib\SpiralDataBase $spiralDataBase, \App\Lib\UserInfo $userInfo){
        $this->spiralDataBase = $spiralDataBase;
        $this->userInfo = $userInfo;
	}

    public function register(string $requestId,string $distributorId,array $itemsArray ){
        $itemsArray = $this->requestUrldecode($itemsArray);
        $this->requestId = $requestId;
        $this->distributorId = $distributorId;
        $insertData = $this->makeRegisterData($itemsArray);
        return $this->regDatabase($insertData);
    }
	
	private function makeRegisterData(array $itemsArray){
        $insertData = array();
        foreach($itemsArray as $items){
            if($items['quantity'] == "0"){
                continue;
            }
            $insertData[] = array(
                '3',
                $this->requestId,
                $this->distributorId,
                $items['itemId'],
                $items['quantity'],
                $this->userInfo->getHospitalId(),
                $items['quantityUnit'],
                $items['itemUnit'],
                $items['notice']
            );
        }

		return $insertData;
     
        //throw new Exception("エラーハンドリング");
    }

    private function regDatabase(array $insertData){

        /**
         * ここに処理を書く
         */
        $columns = array('requestFlg','requestId','distributorId','itemId','quantity','hospitalId','quantityUnit','itemUnit','notice');

        $this->spiralDataBase->setDataBase($this->database);

        return $this->spiralDataBase->doBulkInsert($columns ,$insertData);

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