<?php

namespace App\Api;

class RegGoodsBilling{

    private $spiralDataBase;
    private $divisionId;
    private $billingId;
    private $userInfo;
    
    
    private $historyDatabase = 'NJ_BillingHDB';
    private $childDatabase = 'NJ_BillingDB';

    public function __construct(\App\Lib\SpiralDataBase $spiralDataBase, \App\Lib\UserInfo $userInfo){
        $this->spiralDataBase = $spiralDataBase;
        $this->userInfo = $userInfo;
    }

    private function setDivisionId(string $divisionId){
        $this->divisionId = $divisionId;
    }
    
    public function register(array $array, string $divisionId, bool $useUnitPrice){
		$array = $this->requestUrldecode($array);
        $this->setDivisionId($divisionId);
    	$this->billingId = $this->makeGoodsBillingId();
  
    	$childData = $this->makeGoodsBilling($array,$useUnitPrice);
    	
    	if(count($childData) === 0){
    		return false;
    	}
  
    	$historyData = $this->makeGoodsBillingHistory($childData);
    	
    	$response = $this->regGoodsBillingHistory($historyData);
    	if($response['code'] != 0){
    		return false;
        }
        
    	$responseChild = $this->regGoodsBilling($childData);
    	if($responseChild['code'] != 0){
    		return false;
        }
        
        return true;
    }
    
    private function makeGoodsBillingHistory(array $array){
		if(count($array) == 0){
			return array();
		}
        /**
         * ここに処理を書く
         */
		$insertData = array(
				array(
					"name" => "registrationTime",
					"value" => "now"
				),
				array(
					"name" => "updateTime",
					"value" => ""
				),
				array(
					"name" => "billingNumber",
					"value" => $this->billingId
				),
				array(
					"name" => "hospitalId",
					"value" => $this->userInfo->getHospitalId()
				),
				array(
					"name" => "divisionId",
					"value" => $this->divisionId
				),
				array(
					"name" => "itemsNumber",
					"value" => count($array)
				),
				array(
					"name" => "totalAmount",
					"value" => $this->totalAmount($array)
				),
			);
		return $insertData;
     
        //throw new Exception("エラーハンドリング");
    }
    
    private function regGoodsBillingHistory(array $insertData){

        /**
         * ここに処理を書く
         */
        $this->spiralDataBase->setDataBase($this->historyDatabase);
        return $this->spiralDataBase->doInsert($insertData);
        //return $this->spiralDataBase->doInsert($insertData);
        //throw new Exception("エラーハンドリング");
    }
    
    private function makeGoodsBilling(array $array,bool $useUnitPrice){

        /**
         * ここに処理を書く
         */
        //$columns = array('registrationTime','updateTime','inHospitalItemId','billingNumber','price','billingQuantity','billingAmount','hospitalId','divisionId');

		$itemList = array();
		foreach($array as $rows){
			foreach($rows as $data){
				if( (int)$data['countNum']  > 0 ){
					if ($useUnitPrice) { $unitPrice = str_replace(',', '', $data['unitPrice']); }
					if (!$useUnitPrice) { $unitPrice = str_replace(',', '', $data['kakaku']) / $data['irisu']; }
					$itemList[] = array(
					'now',
					'',
					$data['recordId'],
					$this->billingId,
					str_replace(',', '', $data['kakaku']),
					(int)$data['countNum'],
					(int)$unitPrice * (int)$data['countNum'],
					$this->userInfo->getHospitalId(),
					$this->divisionId,
					$data['irisu'],
					$data['unit'],
					$data['itemUnit'],
					$data['lotNumber'],
					$data['lotDate'],
					$unitPrice
					);
				}
			}
		}

		return $itemList;
    }
    
    private function regGoodsBilling(array $itemList){

        /**
         * ここに処理を書く
         */
        $columns = array('registrationTime','updateTime','inHospitalItemId','billingNumber','price','billingQuantity','billingAmount','hospitalId','divisionId','quantity','quantityUnit','itemUnit','lotNumber','lotDate','unitPrice');

        $this->spiralDataBase->setDataBase($this->childDatabase);

        return $this->spiralDataBase->doBulkInsert($columns ,$itemList);

        //throw new Exception('エラーハンドリング');
    }
    
    private function makeGoodsBillingId(){
	
        /**
         * ここに処理を書く
		 */
		$id = '02';
		
		//$id .= str_pad($this->userInfo->getHospitalId(), 3, 0, STR_PAD_LEFT);;
		
		$id .= date("ymdHis");
		
		$msec = microtime(true); 
 
		$msec = explode('.', $msec); 
		 
		if( !isset($msec[1]) ){
			$msec[1] = "0000";	// $msec[1]がセットされたかどうかをチェックし、なければ0をセット
		}

		$id .= str_pad(substr($msec[1],0,3) , 4, "0"); 
		
		return $id;
	
		//throw new Exception('エラーハンドリング');
    }
    
    private function totalAmount(array $array){
    	$num = 0;
    	foreach($array as $data){
    		$num += $data[6];
    	}
    	return $num;
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