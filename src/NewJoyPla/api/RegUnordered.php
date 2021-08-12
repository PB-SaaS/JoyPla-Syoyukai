<?php

namespace App\Api;

class RegUnordered{

    private $spiralDataBase;
    private $divisionId;
    private $orderId;
    private $userInfo;
    private $distributorId;
    
    private $distributorDB = 'NJ_distributorDB';
    
    private $historyDatabase = 'NJ_OrderHDB';
    private $childDatabase = 'NJ_OrderDB';

    public function __construct(\App\Lib\SpiralDataBase $spiralDataBase, \App\Lib\UserInfo $userInfo){
        $this->spiralDataBase = $spiralDataBase;
        $this->userInfo = $userInfo;
    }

    private function setDivisionId(string $divisionId){
        $this->divisionId = $divisionId;
    }
    
    public function register(array $array, string $divisionId){
    	$array = $this->requestUrldecode($array);
    	
        $this->setDivisionId($divisionId);
        $distributorDB = $this->getDistributorDB();
        if($distributorDB['count'] == 0){
        	return false;
        }
        
        foreach($distributorDB['data'] as $data){
        	$result = $this->arrayEachDistributor($data[1],$array);
	    	
	    	$this->distributorId = $data[1];
	    	
	    	$this->orderId = $this->makeOrderNumber();
	    	$childData = $this->makeOrder($result);
			if(count($childData) == 0){
	    		continue;
	    	}
	  
	        $historyData = $this->makeOrderHistory($childData);
	        
	        $response = $this->regOrderHistory($historyData);
	        
	        if($response['code'] != 0){
	    		return false;
	        }
	
	        $response = $this->regOrder($childData);
	        
	        if($response['code'] != 0){
	    		return false;
	        }
        }
    	return true;
    }
    
    private function arrayEachDistributor(string $distributorId , array $array){
    	$result = array();
    	foreach($array as $key => $data){
    		if($data['distributorId'] == $distributorId){
    			$result[$key] = $data;
    		}
    	}
    	
    	return $result;
    }
    
    private function makeOrderHistory(array $array){

        /**
         * ここに処理を書く
         */
        //array('registrationTime','orderTime','receivingTime','orderNumber','hospitalId','divisionId','itemsNumber','totalAmount','orderStatus','hachuRarrival');

		$insertData = array(
				array(
					"name" => "registrationTime",
					"value" => "now"
				),
				array(
					"name" => "orderTime",
					"value" => ""
				),
				array(
					"name" => "receivingTime",
					"value" => ""
				),
				array(
					"name" => "orderNumber",
					"value" => $this->orderId
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
				array(
					"name" => "orderStatus",
					"value" => "1"
				),
				array(
					"name" => "hachuRarrival",
					"value" => "未入庫"
				),
				array(
					"name" => "distributorId",
					"value" => $this->distributorId
				),
				array(
					"name" => "ordererUserName",
					"value" => $this->userInfo->getName()
				)
			);
		return $insertData;
     
        //throw new Exception("エラーハンドリング");
    }
    
    private function regOrderHistory(array $insertData){

        /**
         * ここに処理を書く
         */
        $this->spiralDataBase->setDataBase($this->historyDatabase);
        return $this->spiralDataBase->doInsert($insertData);
        //throw new Exception("エラーハンドリング");
    }
    
    private function makeOrder(array $array){

        /**
         * ここに処理を書く
         */
        //$columns = array('registrationTime','updateTime','receivingTime','dueDate','orderCNumber','hospitalId','inHospitalItemId','orderNumber','price','orderQuantity','orderPrice','receivingFlag');

		$itemList = array();
		foreach($array as $inHPItemid => $data){
			if(floor((int)$data['countNum'] / (int)$data['irisu']) != 0){
			$itemList[] = array(
				'now',
				'',
				'',
				'',
                '',
                $this->userInfo->getHospitalId(),
				$inHPItemid,
				$this->orderId,
				str_replace(',', '', $data['kakaku']),
				floor((int)$data['countNum'] / (int)$data['irisu']),
				str_replace(',', '', $data['kakaku']) * floor((int)$data['countNum'] / (int)$data['irisu']),
				'0',
				$data['irisu'],
				$data['unit'],
				$data['itemUnit'],
				$this->divisionId,
				$this->distributorId
				);
			}
		}

		return $itemList;
    }
    
    private function regOrder(array $itemList){

        /**
         * ここに処理を書く
         */
        $columns = array('registrationTime','updateTime','receivingTime','dueDate','orderCNumber','hospitalId','inHospitalItemId','orderNumber','price','orderQuantity','orderPrice','receivingFlag','quantity','quantityUnit','itemUnit','divisionId','distributorId');

        $this->spiralDataBase->setDataBase($this->childDatabase);

        return $this->spiralDataBase->doBulkInsert($columns ,$itemList);

        //throw new Exception('エラーハンドリング');
    }
    
    private function makeOrderNumber(){
	
        /**
         * ここに処理を書く
		 */
		$id = '03';
		
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
    		$num += $data[10];
    	}
    	return $num;
    }
    
    
	private function getDistributorDB(){
		$this->spiralDataBase->setDataBase($this->distributorDB);
		$this->spiralDataBase->addSelectFields('distributorName','distributorId');
		$this->spiralDataBase->addSearchCondition('hospitalId',$this->userInfo->getHospitalId());
		
		return $this->spiralDataBase->doSelect();
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