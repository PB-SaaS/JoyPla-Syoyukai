<?php

namespace App\Api;

class GetInventoryHistoryId{

    private $spiralDataBase;
	private $userInfo;

    private $divisionId;
	
    private $InventoryHId;
    
    private $historyDatabase = 'NJ_InventoryHDB';

    public function __construct(\App\Lib\SpiralDataBase $spiralDataBase, \App\Lib\UserInfo $userInfo){
        $this->spiralDataBase = $spiralDataBase;
        $this->userInfo = $userInfo;
    }

    private function setDivisionId(string $divisionId){
        $this->divisionId = $divisionId;
	}
    
    public function getInventoryHistoryId(string $divisionId , string $InventoryEId){
        $this->setDivisionId($divisionId);
        
		$result = $this->getInventoryHistory($InventoryEId);
		if($result['code'] != '0'){
			return '';
		}

		if($result['count'] == 0){
			$this->InventoryHId = $this->makeInventoryHId();
			$makeInventoryHData = $this->makeInventoryHData($InventoryEId);
            $result = $this->regInventoryHDB($makeInventoryHData);
			if($result['code'] != '0'){
				return '';
			}
            
		} else {
			$this->InventoryHId = $result['data'][0][1];
		}

        return $this->InventoryHId;
    }
    
    private function getInventoryHistory(string $InventoryEId){
        $this->spiralDataBase->setDataBase($this->historyDatabase);
        $this->spiralDataBase->addSearchCondition('hospitalId',$this->userInfo->getHospitalId());
        $this->spiralDataBase->addSearchCondition('divisionId',$this->divisionId);
        $this->spiralDataBase->addSearchCondition('inventoryEndId',$InventoryEId);
        $this->spiralDataBase->addSelectFields('id','inventoryHId');
        return $this->spiralDataBase->doSelect();
    }

	private function makeInventoryHId(){
	
        /**
         * ここに処理を書く
		 */
		$id = '08';
		
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

    private function makeInventoryHData(string $InventoryEId){

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
					"name" => "inventoryHId",
					"value" => $this->InventoryHId
				),
				array(
					"name" => "inventoryEndId",
					"value" => $InventoryEId
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
					"value" => '0'
				),
				array(
					"name" => "totalAmount",
					"value" => '0'
				),
			);
		return $insertData;
     
        //throw new Exception("エラーハンドリング");
    }
    
    private function regInventoryHDB(array $insertData){

        /**
         * ここに処理を書く
         */
        $this->spiralDataBase->setDataBase($this->historyDatabase);
        return $this->spiralDataBase->doInsert($insertData);
        //return $this->spiralDataBase->doInsert($insertData);
        //throw new Exception("エラーハンドリング");
    }
    
    private function makeInventory(array $array){

        /**
         * ここに処理を書く
         */
        //$columns = array('registrationTime','updateTime','inventoryHId','inHospitalItemId','hospitalId','price','calculatingStock','inventryNum','inventryAmount');

		$itemList = array();
		foreach($array as $inHPid => $data){
			if( (int)$data['countNum']  > 0 ){
			$itemList[] = array(
				'now',
				'',
				$this->InventoryHId,
				$inHPid,
				$this->userInfo->getHospitalId(),
				str_replace(',', '', $data['kakaku']),
				'',
				(int)$data['countNum'],
				(string)(number_format(str_replace(',', '', $data['kakaku']) / $data['irisu'], 2) * (int)$data['countNum']),
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
        $columns = array('registrationTime','updateTime','inventoryHId','inHospitalItemId','hospitalId','price','calculatingStock','inventryNum','inventryAmount');

        $this->spiralDataBase->setDataBase($this->childDatabase);

        return $this->spiralDataBase->doBulkInsert($columns ,$itemList);

        //throw new Exception('エラーハンドリング');
    }
    
    private function totalAmount(array $array){
    	$num = 0;
    	foreach($array as $data){
    		$num += $data[8];
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