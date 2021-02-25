<?php

namespace App\Api;

class RegOrder{

    private $spiralDataBase;
    
    private $historyDatabase = 'NJ_OrderHDB';

    public function __construct(\App\Lib\SpiralDataBase $spiralDataBase){
        $this->spiralDataBase = $spiralDataBase;
    }

    public function order(string $orderNum,string $orderAuthKey,string $orderComment){
        return $this->updateOrderHistoryDB($orderNum,$orderAuthKey,$orderComment);
    }

    public function updateOrderComment(string $orderNum,string $orderAuthKey,string $orderComment){
        return $this->updateOrderHistoryDBForComment($orderNum,$orderAuthKey,$orderComment);
    }

    private function updateOrderHistoryDBForComment(string $orderNum,string $orderAuthKey,string $orderComment){
        $this->spiralDataBase->setDataBase($this->historyDatabase);
		$this->spiralDataBase->addSearchCondition('orderNumber',$orderNum);
		$this->spiralDataBase->addSearchCondition('orderAuthKey',$orderAuthKey);
        $this->spiralDataBase->addSelectNameCondition('');
        return $this->spiralDataBase->doUpdate(array(array('name'=> 'ordercomment','value'=>urldecode($orderComment))));
    }

    private function updateOrderHistoryDB(string $orderNum,string $orderAuthKey,string $orderComment){
        $this->spiralDataBase->setDataBase($this->historyDatabase);
		$this->spiralDataBase->addSearchCondition('orderNumber',$orderNum);
		$this->spiralDataBase->addSearchCondition('orderAuthKey',$orderAuthKey);
        $this->spiralDataBase->addSelectNameCondition('');
        return $this->spiralDataBase->doUpdate(array(array('name'=> 'orderTime','value'=>'now'),array('name'=> 'orderStatus','value'=>2),array('name'=> 'ordercomment','value'=>urldecode($orderComment))));
    }

}