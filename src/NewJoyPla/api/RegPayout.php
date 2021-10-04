<?php

namespace App\Api;

class RegPayout{

    private $spiralDataBase;
    private $sourceDivisionId;
    private $sourceDivisionName;
    private $targetDivisionId;
    private $targetDivisionName;
    public $payoutId;
    private $userInfo;
    
    
    private $historyDatabase = 'NJ_PayoutHDB';
    private $childDatabase = 'NJ_PayoutDB';

    public function __construct(\App\Lib\SpiralDataBase $spiralDataBase, \App\Lib\UserInfo $userInfo){
        $this->spiralDataBase = $spiralDataBase;
        $this->userInfo = $userInfo;
    }

    private function setSourceDivisionId(string $sourceDivisionId, string $sourceDivisionName){
        $this->sourceDivisionId = $sourceDivisionId;
        $this->sourceDivisionName = $sourceDivisionName;
    }
	
	private function setTargetDivisionId(string $targetDivisionId, string $targetDivisionName){
        $this->targetDivisionId = $targetDivisionId;
        $this->targetDivisionName = $targetDivisionName;
	}
	
    public function register(array $array, string $sourceDivisionId, string $sourceDivisionName, string $targetDivisionId, string $targetDivisionName, bool $useUnitPrice){
		$array = $this->requestUrldecode($array);
		$sourceDivisionName = urldecode($sourceDivisionName);
		$targetDivisionName = urldecode($targetDivisionName);

		$this->setSourceDivisionId($sourceDivisionId,$sourceDivisionName);
		$this->setTargetDivisionId($targetDivisionId,$targetDivisionName);
		
    	$this->payoutId = $this->makePayoutId();
  
    	$childData = $this->makePayout($array,$useUnitPrice);
    	
    	if(count($childData) === 0){
    		var_dump($childData);
    		return false;
    	}
  
    	$historyData = $this->makePayoutHistory($childData);
    	
    	$response = $this->regPayoutHistory($historyData);
    	if($response['code'] != 0){
    		var_dump($response);
    		return false;
        }
        
    	$responseChild = $this->regPayout($childData);
    	if($responseChild['code'] != 0){
    		var_dump($responseChild);
    		return false;
        }
        return true;
    }
    
    private function makePayoutHistory(array $array){

        /**
         * ここに処理を書く
         */
		$insertData = array(
				array(
					"name" => "registrationTime",
					"value" => "now"
				),
				array(
					"name" => "payoutHistoryId",
					"value" => $this->payoutId,
				),
				array(
					"name" => "hospitalId",
					"value" => $this->userInfo->getHospitalId()
				),
				array(
					"name" => "sourceDivisionId",
					"value" => $this->sourceDivisionId,
				),
				array(
					"name" => "sourceDivision",
					"value" => $this->sourceDivisionName,
				),
				array(
					"name" => "targetDivisionId",
					"value" => $this->targetDivisionId,
				),
				array(
					"name" => "targetDivision",
					"value" => $this->targetDivisionName,
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
    
    private function regPayoutHistory(array $insertData){

        /**
         * ここに処理を書く
         */
        $this->spiralDataBase->setDataBase($this->historyDatabase);
        return $this->spiralDataBase->doInsert($insertData);
        //return $this->spiralDataBase->doInsert($insertData);
        //throw new Exception("エラーハンドリング");
    }
    
    private function makePayout(array $array, bool $useUnitPrice){

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
						$this->payoutId,
						'',
						$data['recordId'],
						$this->userInfo->getHospitalId(),
						$this->sourceDivisionId,
						$this->targetDivisionId,
						$data['irisu'],
						$data['unit'],
						$data['itemUnit'],
						str_replace(',', '', $data['kakaku']),
						(int)$data['countNum'],
						(int)$unitPrice * (int)$data['countNum'],
						$data['payoutCount'],
						$data['countLabelNum'],
						$data['lotNumber'],
						$data['lotDate'],
						$unitPrice
					);
				}
			}
		}

		return $itemList;
    }
    
    private function regPayout(array $itemList){

        /**
         * ここに処理を書く
         */
        $columns = array('registrationTime','updateTime','payoutHistoryId','payoutId','inHospitalItemId','hospitalId','sourceDivisionId','targetDivisionId','quantity','quantityUnit','itemUnit','price','payoutQuantity','payoutAmount','payoutCount','payoutLabelCount','lotNumber','lotDate','unitPrice');

        $this->spiralDataBase->setDataBase($this->childDatabase);

        return $this->spiralDataBase->doBulkInsert($columns ,$itemList);

        //throw new Exception('エラーハンドリング');
    }
    
    private function makePayoutId(){
	
        /**
         * ここに処理を書く
		 */
		$id = '05';
		
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
    		$num += $data[13];
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