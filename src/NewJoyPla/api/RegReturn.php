<?php

namespace App\Api;

class RegReturn{

    private $spiralDataBase;
	private $divisionId;
	
	private $userInfo;

	public $returnHistoryId;
	private $receivingHistoryId;
	
	private $itemsNumber;
	private $returnTotalPrice = 0;
	
    private $historyDatabase = 'NJ_ReturnHDB';
	private $childDatabase = 'NJ_ReturnDB';
	
	
    private $receivingDatabase = 'NJ_ReceivingDB';

    public function __construct(\App\Lib\SpiralDataBase $spiralDataBase, \App\Lib\UserInfo $userInfo){
        $this->spiralDataBase = $spiralDataBase;
        $this->userInfo = $userInfo;
	}
	
    public function register(string $receivingHistoryId,string $distributorId,array $array){
		$array = $this->requestUrldecode($array);

		$this->distributorId = $distributorId;
		$this->receivingHistoryId = $receivingHistoryId;
		$this->returnHistoryId = $this->makeReturnHId();
		
		$makeReturn = $this->makeReturn($array);
		if(count($makeReturn) == "0"){
			return true;
		}
		
		$this->itemsNumber = count($makeReturn);
		$this->returnTotalPrice = $this->returnTotalPrice($makeReturn);
		$makeReturnHistory = $this->makeReturnHistory();

		$result = $this->regReturnHistory($makeReturnHistory);

		if($result['code'] != '0'){
			var_dump($result);
			return false;
		}


		$result = $this->regReturn($makeReturn);

		if($result['code'] != '0'){
			var_dump($result);
			return false;
		}

		$makeReceivingData = $this->makeReceivingData($array);
        $result = $this->updateReceivingDB($makeReceivingData);
		if($result['code'] != '0'){
			var_dump($result);
			return false;
		}

    	return true;
    }
    
	private function regReturnHistory(array $insertData){
        $this->spiralDataBase->setDataBase($this->historyDatabase);
        return $this->spiralDataBase->doInsert($insertData);
	}
	
	private function makeReturnHistory(){

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
					"name" => "receivingHId",
					"value" => $this->receivingHistoryId
				),
				array(
					"name" => "distributorId",
					"value" => $this->distributorId
				),
				array(
					"name" => "returnHistoryID",
					"value" => $this->returnHistoryId
				),
				array(
					"name" => "hospitalId",
					"value" => $this->userInfo->getHospitalId()
				),
				array(
					"name" => "itemsNumber",
					"value" => $this->itemsNumber
				),
				array(
					"name" => "returnTotalPrice",
					"value" => $this->returnTotalPrice
				)
			);
		return $insertData;
     
        //throw new Exception("エラーハンドリング");
    }
    private function regReturn(array $itemList){

        /**
         * ここに処理を書く
         */
        $columns = array('registrationTime','receivingNumber','price','returnCount','returnPrice','hospitalId','returnHistoryID','orderCNumber','receivingHId','inHospitalItemId','lotNumber','lotDate');

        $this->spiralDataBase->setDataBase($this->childDatabase);

        return $this->spiralDataBase->doBulkInsert($columns ,$itemList);

        //throw new Exception('エラーハンドリング');
	}
	
	private function returnTotalPrice(array $array){
		$count = 0;
		foreach($array as $data){
			$count += $data[4];
		}
		return $count;
	}

	private function makeReturn(array $array){

        /**
         * ここに処理を書く
         */
        //$columns = array('registrationTime','receivingNumber','price','returnCount','returnPrice','hospitalId','returnHistoryID');

		$itemList = array();
		foreach($array as $data){
			if($data['returnCount'] > 0){
			$itemList[] = array(
				'now',
				$data['receivingNumber'],
				$data['price'],
				$data['returnCount'],
				$data['price'] * $data['returnCount'],
				$this->userInfo->getHospitalId(),
				$this->returnHistoryId,
				$data['orderCNumber'],
				$this->receivingHistoryId,
				$data['inHospitalItemId'],
				$data['lotNumber'],
				$data['lotDate']
				);
			}
		}

		return $itemList;
    }

	private function makeReturnHId(){
	
        /**
         * ここに処理を書く
		 */
		$id = '06';
		
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

	private function makeReceivingData(array $array){
        $itemList = array();
		foreach($array as $inHPItemid => $data){
			$itemList[] = array(
				$data['receivingNumber'],
				(int)$data['totalReturnCount'] + (int)$data['returnCount']
				);
			
		}
		return $itemList;
	}
	
	private function updateReceivingDB(array $array){
        
        $columns = array('receivingNumber','totalReturnCount');

        $this->spiralDataBase->setDataBase($this->receivingDatabase);

        return $this->spiralDataBase->doBulkUpdate('receivingNumber',$columns ,$array);
    }
}