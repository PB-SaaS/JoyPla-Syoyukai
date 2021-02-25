<?php

namespace App\Api;

class UpdateOrderFixing{

    private $spiralDataBase;
    private $price;
    
    private $historyDatabase = 'NJ_OrderHDB';
    private $childDatabase = 'NJ_OrderDB';

    private $hachuRarrival = '';
    private $pattern = '';

    public function __construct(\App\Lib\SpiralDataBase $spiralDataBase){
        $this->spiralDataBase = $spiralDataBase;
    }

    public function update(string $orderNum,string $orderAuthKey,array $array){
        $makeOrderData = array();

        $this->pattern =  '3';

        $makeOrderData = $this->makeOrderData($array);
        
        $result = $this->updateOrderDB($makeOrderData);

        if($result['code'] != 0){
            var_dump($result);
            return false;
        }

        $makeOrderHistoryData = $this->makeOrderHistoryData();
        $result = $this->updateOrderHistoryDB($orderNum,$orderAuthKey,$makeOrderHistoryData);

        if($result['code'] != 0){
            var_dump($result);
            return false;
        }

        return true;
    }

    public function delete(string $orderNum,string $orderAuthKey,array $array){
        $makeOrderData = array();

        $this->pattern =  '2';

        $makeOrderHistoryData = $this->makeOrderHistoryData();
        $result = $this->updateOrderHistoryDB($orderNum,$orderAuthKey,$makeOrderHistoryData);

        if($result['code'] != 0){
            var_dump($result);
            return false;
        }

        return true;
    }


    private function makeOrderData(array $array){
        $itemList = array();
		foreach($array as $inHPItemid => $data){
            if($data['dueDate'] != ''){
                $this->pattern =  '4';
            }
			$itemList[] = array(
                $data['orderCNumber'],
                $data['dueDate'],
				);
		}

		return $itemList;
    }

    private function makeOrderDataWithDelAcceptance(array $array){
        $itemList = array();
		foreach($array as $inHPItemid => $data){
			$itemList[] = array(
                $data['orderCNumber'],
                '',
				'0',
				);
		}

		return $itemList;
    }

    private function updateOrderDB(array $array){
        
        $columns = array('orderCNumber','dueDate');

        $this->spiralDataBase->setDataBase($this->childDatabase);
        $this->spiralDataBase->addSelectNameCondition('');
        return $this->spiralDataBase->doBulkUpdate('orderCNumber',$columns ,$array);
    }

    private function makeOrderHistoryData(){
		$insertData = array(
            array(
                "name" => "orderStatus",
                "value" => $this->pattern,
            ),
            array(
                "name" => "hachuRarrival",
                "value" => $this->hachuRarrival,
            )
        );

        return $insertData;
    }

    private function updateOrderHistoryDB(string $orderNum,string $orderAuthKey,array $array){
        
        $this->spiralDataBase->setDataBase($this->historyDatabase);
		$this->spiralDataBase->addSearchCondition('orderNumber',$orderNum);
		$this->spiralDataBase->addSearchCondition('orderAuthKey',$orderAuthKey);
        $this->spiralDataBase->addSelectNameCondition('');
        return $this->spiralDataBase->doUpdate($array);
    }
}