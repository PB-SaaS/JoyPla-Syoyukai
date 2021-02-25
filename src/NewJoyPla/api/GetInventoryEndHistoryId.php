<?php

namespace App\Api;

class GetInventoryEndHistoryId{

    private $spiralDataBase;
	private $userInfo;

    private $divisionId;
	
    private $InventoryEId;
    private $InventoryHId;
    
    private $EndHistoryDatabase = 'NJ_InventoryEDB';

    public function __construct(\App\Lib\SpiralDataBase $spiralDataBase, \App\Lib\UserInfo $userInfo){
        $this->spiralDataBase = $spiralDataBase;
        $this->userInfo = $userInfo;
    }
    
    public function getInventoryEndHistoryId(){

		$result = $this->getInventoryEndHistory();
		if($result['code'] != 0){
			return '';
		}

		if($result['count'] == 0){
			$this->InventoryEId = $this->makeInventoryEId();
			$makeInventoryEData = $this->makeInventoryEData();
			$result = $this->regInventoryEDB($makeInventoryEData);
			if($result['code'] != "0"){
				return '';
			}
			return $this->InventoryEId;
		}

		$this->InventoryEId = $result['data'][0][1];
        
        return $this->InventoryEId;
    }
    
    private function getInventoryEndHistory(){
        $this->spiralDataBase->setDataBase($this->EndHistoryDatabase);
        $this->spiralDataBase->addSearchCondition('hospitalId',$this->userInfo->getHospitalId());
        $this->spiralDataBase->addSearchCondition('inventoryStatus','1');
        $this->spiralDataBase->addSelectFields('id','inventoryEndId');
        return $this->spiralDataBase->doSelect();
    }
	
	private function makeInventoryEId(){
	
        /**
         * ここに処理を書く
		 */
		$id = '09';
		
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
	
	private function makeInventoryEData(){
		$insertData = array(
			array(
				"name" => "registrationTime",
				"value" => "now"
			),
			array(
				"name" => "hospitalId",
				"value" => $this->userInfo->getHospitalId()
			),
			array(
				"name" => "inventoryEndId",
				"value" => $this->InventoryEId
			),
		);
		return $insertData;
    }
    
	private function regInventoryEDB(array $insertData){
        $this->spiralDataBase->setDataBase($this->EndHistoryDatabase);
        return $this->spiralDataBase->doInsert($insertData);
	}

}