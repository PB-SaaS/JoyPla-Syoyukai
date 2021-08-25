<?php

namespace App\Api;

class UpdateOrder{

    private $spiralDataBase;
    private $price;
    
    private $historyDatabase = 'NJ_OrderHDB';
    private $childDatabase = 'NJ_OrderDB';

    public function __construct(\App\Lib\SpiralDataBase $spiralDataBase){
        $this->spiralDataBase = $spiralDataBase;
    }

    public function update(string $orderNum,string $orderAuthKey,array $array){
        $makeOrderData = array();
        $makeOrderData = $this->makeOrderData($array);

        //$result = $this->updateOrderDB($makeOrderData); //更新しない
        /*
        if($result['code'] != 0){
            var_dump($result);
            return false;
        }
        */
        
        $this->pattern = $this->checkPattern($array);
        
        $makeOrderHistoryData = $this->makeOrderHistoryData($array);
        $result = $this->updateOrderHistoryDB($orderNum,$orderAuthKey,$makeOrderHistoryData);

        if($result['code'] != 0){
            var_dump($result);
            return false;
        }

        return true;
    }

    public function updateWithDelAcceptance(string $orderNum,string $orderAuthKey,array $array){
        $makeOrderData = array();
        $makeOrderData = $this->makeOrderDataWithDelAcceptance($array);
        
        $result = $this->updateOrderDB($makeOrderData); 
        
        if($result['code'] != 0){
            var_dump($result);
            return false;
        }
        
        $this->pattern = '7';//納品取消
        
        $makeOrderHistoryData = $this->makeOrderHistoryData($makeOrderData);

        $result = $this->updateOrderHistoryDB($orderNum,$orderAuthKey,$makeOrderHistoryData);
        

        if($result['code'] != 0){
            var_dump($result);
            return false;
        }

        return true;
    }
    

    private function checkPattern(array $array){
    	$checkList1 = array();
    	$checkList2 = array();
		foreach($array as $record){
			if($record['receivingNowCount'] != 0){
				$checkList1[] = $record;
            }
            if($record['receivingFlag']){
				$checkList2[] = $record;
            }
		}
		
		if(count($array) == count($checkList2)){
			return 6;
		}
		if(count($checkList1) != 0 ){
			return 5;
		}
		return 0;
    }

    private function makeOrderData(array $array){
        $itemList = array();
		foreach($array as $inHPItemid => $data){
			if($data['orderQuantity'] - $data['receivingNowCount'] <= 0){
			$itemList[] = array(
                $data['orderCNumber'],
                'now',
				'1',
				);
			}
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
        
        $columns = array('orderCNumber','receivingTime','receivingFlag');

        $this->spiralDataBase->setDataBase($this->childDatabase);

        return $this->spiralDataBase->doBulkUpdate('orderCNumber',$columns ,$array);
    }

    private function makeOrderHistoryData(array $array){
        $receivingTime = null;
        if($this->pattern == '7'){
            $receivingTime = '';
        }
        if($this->pattern == '6'){
            $receivingTime = 'now';
        }
        
		$insertData = array(
            array(
                "name" => "receivingTime",
                "value" => $receivingTime,
            ),
            array(
                "name" => "itemsNumber",
                "value" => count($array),
            )
        );

        if($this->pattern == '7' || $this->pattern == '6' || $this->pattern == '5'){
            $insertData[] = array(
                "name" => "orderStatus",
                "value" => $this->pattern,
            );
        }
        return $insertData;
    }

    private function updateOrderHistoryDB(string $orderNum,string $orderAuthKey,array $array){
        
        $this->spiralDataBase->setDataBase($this->historyDatabase);
		$this->spiralDataBase->addSearchCondition('orderNumber',$orderNum);
		$this->spiralDataBase->addSearchCondition('orderAuthKey',$orderAuthKey);
        return $this->spiralDataBase->doUpdate($array);
    }
}