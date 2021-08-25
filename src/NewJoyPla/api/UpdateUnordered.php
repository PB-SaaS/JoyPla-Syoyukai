<?php

namespace App\Api;

class UpdateUnordered{

    private $spiralDatabase;
    private $price;
    
    private $itemsNumber = 0;
    
    private $historyDatabase = 'NJ_OrderHDB';
    private $childDatabase = 'NJ_OrderDB';

    public function __construct(\App\Lib\SpiralDatabase $spiralDatabase){
        $this->spiralDatabase = $spiralDatabase;
    }

    public function update(string $orderNum){
        $result = $this->selectOrderInfo($orderNum);
        if($result['code'] != 0){
            return array("code"=>$result['code'],"pattern"=>"update");
        }

        if($result['count'] == 0){
            $delete = $this->deleteOrderHistoryDB($orderNum);
            if($delete['code'] != 0){
                return array("code"=>$delete['code'],"pattern"=>"delete");
            }
            return array("code"=>$delete['code'],"pattern"=>"delete");
        }

        $this->price = $this->orderPriceCalculation($result['data']);
        $this->itemsNumber = \count($result['data']);

        $result = $this->updateOrderHistoryDB($orderNum);
        
        if($result['code'] != 0){
            return array("code"=>$result['code'],"pattern"=>"update");
        }

        return array("code"=>$result['code'],"pattern"=>"update");
    }

    private function orderPriceCalculation($data){ 
        //('orderNumber','orderPrice');
        $price = 0;
        foreach($data as $d){
            $price += $d[1];
        }
        return $price;
    }
    
    private function selectOrderInfo(string $orderNum){

        $this->spiralDatabase->setDataBase($this->childDatabase);
		$this->spiralDatabase->addSearchCondition('orderNumber',$orderNum);
        $this->spiralDatabase->addSelectFields('orderNumber','orderPrice');
        return $this->spiralDatabase->doSelectLoop();
    }

    private function updateOrderHistoryDB(string $orderNum){
        
        $this->spiralDatabase->setDataBase($this->historyDatabase);
		$this->spiralDatabase->addSearchCondition('orderNumber',$orderNum);
        return $this->spiralDatabase->doUpdate(array(array('name'=> 'totalAmount','value'=>$this->price),array('name'=> 'itemsNumber','value'=>$this->itemsNumber)));
    }

    private function deleteOrderHistoryDB(string $orderNum){
        
        $this->spiralDatabase->setDataBase($this->historyDatabase);
		$this->spiralDatabase->addSearchCondition('orderNumber',$orderNum);
        return $this->spiralDatabase->doDelete();
    }
}