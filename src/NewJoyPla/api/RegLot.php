<?php

namespace App\Api;

class RegLot{

    private $spiralDataBase;
    private $receivingHId;
    private $payoutHistoryId;
    private $divisionId;
	private $userInfo;
	
	private $receivingItemIds = array();
    
    private $database = 'NJ_LotDB';
    private $ReceivingDB = 'NJ_ReceivingDB';

    public function __construct(\App\Lib\SpiralDataBase $spiralDataBase, \App\Lib\UserInfo $userInfo){
        $this->spiralDataBase = $spiralDataBase;
        $this->userInfo = $userInfo;
    }
/**
 * lotData
 * array(
 * 	array(
 * 		LotId
 *      LotNumber
 *      LotDate
 *      inHospitalItemId
 *      receivingNumber = ''
 *      
 *  	)
 * )
 */
	public function regLot(array $lotData,string $divisionId,string $receivingHId = null,string $payoutHistoryId = null){

		$this->receivingHId = $receivingHId;
		$this->payoutHistoryId = $payoutHistoryId;
		$this->divisionId = $divisionId;

		$lotData = $this->remakeLotData($lotData);

		if($this->receivingHId != null){
			$ReceivingItems = $this->getReceivingItems($lotData);
			if($ReceivingItems['code'] != '0'){
				return $ReceivingItems;
			}
			$this->receivingItemIds = $this->remakeReceivingItems($ReceivingItems['data']);
		}
		$makeLotUpsertData = $this->makeLotUpsertData($lotData);
		return $this->regLotUpSert($makeLotUpsertData);
	}

	private function remakeLotData(array $lotData){
		$result = array();
		foreach($lotData as $lotRecord){
			foreach($lotRecord as $lot){
				if($lot['lotNumber'] != '' || $lot['lotDate'] != ''){
					$result[] = $lot;
				}
			}
		}
		return $result;
	}

	private function makeLotUpsertData(array $lotData){
		$result = array();
		foreach($lotData as $lot){
			$lotId = '';
			if($lot['lotId'] == ""){
				//$lotId = $this->makeLotId();
				$lotId = '';
			} else {
				$lotId = $lot['lotId'];
			}
			$receivingItemId = '';
			if(array_key_exists($lot['inHospitalItemId'], $this->receivingItemIds)){
				$receivingItemId = $this->receivingItemIds[$lot['inHospitalItemId']];
			}
			$result[] = array(
				'now',
				$lotId,
				$receivingItemId,
				$lot['payoutId'],
				$this->receivingHId,
				$this->payoutHistoryId,
				$lot['lotNumber'],
				$lot['lotDate'],
				$this->divisionId,
				$lot['inHospitalItemId'],
				$this->userInfo->getHospitalId(),
			);
		}
		return $result;
	}

    private function getReceivingItems(array $lotData){
        $this->spiralDataBase->setDataBase($this->ReceivingDB);
		$this->spiralDataBase->addSearchCondition('receivingHId',$this->receivingHId);
        foreach($lotData as $lot){
            $this->spiralDataBase->addSearchCondition('inHospitalItemId',$lot['inHospitalItemId'],'=','or');
        }
        $this->spiralDataBase->addSelectFields('receivingNumber','inHospitalItemId');
        return $this->spiralDataBase->doSelectLoop();
	}
	
	private function regLotUpSert(array $upsertData){
		$this->spiralDataBase->setDataBase($this->database);
		$column = array('updateTime','lotId','receivingNumber','payoutId','receivingHId','payoutHistoryId','lotNumber','lotDate','divisionId','inHospitalItemId','hospitalId');
        return $this->spiralDataBase->doBulkUpsert("lotId",$column,$upsertData);
	}

	private function remakeReceivingItems(array $ReceivingItems){
		$result = array();
		foreach($ReceivingItems as $key => $data){
			$result[$data[1]] = $data[0];
		}
		return $result;
	}
	
	private function makeLotId(){
	
        /**
         * ここに処理を書く
		 */
		$id = '99';
		
		//$id .= str_pad($this->userInfo->getHospitalId(), 3, 0, STR_PAD_LEFT);;
		
		$id .= date("ymdHis");
		
		$msec = microtime(true); 
 
		//$msec = explode('.', $msec); 
		//if( !isset($msec[1]) ){
		//	$msec[1] = "0000";	// $msec[1]がセットされたかどうかをチェックし、なければ0をセット
		//}

		$rand = rand( 1, 9999);

		$id .= str_pad($rand, 4, "0"); 
		
		return $id;
		
		//throw new Exception('エラーハンドリング');
    }
    
}