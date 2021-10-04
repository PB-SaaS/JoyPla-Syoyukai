<?php

namespace App\Api;

class RegReceiving{

	private $spiralDataBase;
	private $divisionId;
	private $orderId;
	private $userInfo;
	public $ReceivingHistoryId;
	private $orderHistoryId;
	
	private $itemsNumber;
	
	private $historyDatabase = 'NJ_ReceivingHDB';
	private $childDatabase = 'NJ_ReceivingDB';
	
	
	private $orderDatabase = 'NJ_OrderDB';

    public function __construct(\App\Lib\SpiralDataBase $spiralDataBase, \App\Lib\UserInfo $userInfo){
        $this->spiralDataBase = $spiralDataBase;
        $this->userInfo = $userInfo;
	}
	
    public function register(string $orderHistoryId,string $distributorId,string $divisionId,array $array,array $regData){
		$array = $this->requestUrldecode($array);

		$this->distributorId = $distributorId;
		$this->orderHistoryId = $orderHistoryId;
		$this->ReceivingHistoryId = $this->makeReceivingHId();
		
		$makeReceiving = $this->makeReceiving($regData,$divisionId);

		if(count($makeReceiving) == "0"){
			return true;
		}
		
		$this->itemsNumber = count($makeReceiving);

		$makeReceivingHistory = $this->makeReceivingHistory();

		$result = $this->regReceivingHistory($makeReceivingHistory);

		if($result['code'] != '0'){
			return false;
		}
		
		$result = $this->regReceiving($makeReceiving);

		if($result['code'] != '0'){
			return false;
		}

		$makeOrderData = $this->makeOrderData($array);

        $result = $this->updateOrderDB($makeOrderData);
		if($result['code'] != '0'){
			return false;
		}

    	return true;
    }
    
	private function regReceivingHistory(array $insertData){
        $this->spiralDataBase->setDataBase($this->historyDatabase);
        return $this->spiralDataBase->doInsert($insertData);
	}
	
	private function makeReceivingHistory(){

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
					"value" => $this->ReceivingHistoryId,
				),
				array(
					"name" => "distributorId",
					"value" => $this->distributorId,
				),
				array(
					"name" => "orderHistoryId",
					"value" => $this->orderHistoryId,
				),
				array(
					"name" => "hospitalId",
					"value" => $this->userInfo->getHospitalId()
				),
				array(
					"name" => "itemsNumber",
					"value" => $this->itemsNumber
				)
			);
		return $insertData;
     
        //throw new Exception("エラーハンドリング");
    }
    private function regReceiving(array $itemList){

        /**
         * ここに処理を書く
         */
        $columns = array('registrationTime','orderCNumber','receivingCount','receivingHId','inHospitalItemId','price','receivingPrice','hospitalId','divisionId','lotNumber','lotDate');

        $this->spiralDataBase->setDataBase($this->childDatabase);

        return $this->spiralDataBase->doBulkInsert($columns ,$itemList);

        //throw new Exception('エラーハンドリング');
    }

	private function makeReceiving(array $array,string $divisionId){

        /**
         * ここに処理を書く
         */
        //$columns = array('registrationTime','updateTime','receivingTime','dueDate','orderCNumber','hospitalId','inHospitalItemId','orderNumber','price','orderQuantity','orderPrice','receivingFlag');

		$itemList = array();
		foreach ($array as $rows) {
			foreach ($rows as $data) {
				if($data['receivingCount'] != 0){
					$itemList[] = array(
						'now',
						$data['orderCNumber'],
						$data['receivingCount'],
						$this->ReceivingHistoryId,
						$data['inHPItemid'],
						$data['price'],
						$data['receivingCount'] * $data['price'],
						$this->userInfo->getHospitalId(),
						$divisionId,
						$data['lotNumber'],
						$data['lotDate']
					);
				}
			}
		}

		return $itemList;
    }

	private function makeReceivingHId(){
	
        /**
         * ここに処理を書く
		 */
		$id = '04';
		
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

	private function makeOrderData(array $array){
        $itemList = array();
		foreach($array as $inHPItemid => $data){
			//if($data['orderQuantity'] - ( (int)$data['receivingNowCount']  + (int)$data['receivingBeforeCount'] ) <= 0){
			if($data['orderQuantity'] - ( (int)$data['receivingNowCount']  + (int)$data['receivingCount'] ) <= 0){
			$itemList[] = array(
                $data['orderCNumber'],
                'now',
				'1',
				(int)$data['receivingNowCount']+(int)$data['receivingCount'],
				);
			} else {
			$itemList[] = array(
                $data['orderCNumber'],
                'now',
				'0',
				(int)$data['receivingNowCount']+(int)$data['receivingCount'],
				);
			}
		}
		return $itemList;
	}
	
	private function updateOrderDB(array $array){
        
        $columns = array('orderCNumber','receivingTime','receivingFlag','receivingNum');

        $this->spiralDataBase->setDataBase($this->orderDatabase);

        return $this->spiralDataBase->doBulkUpdate('orderCNumber',$columns ,$array);
    }
}