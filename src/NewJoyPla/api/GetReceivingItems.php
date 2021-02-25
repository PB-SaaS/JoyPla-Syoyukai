<?php

namespace App\Api;

class GetReceivingItems{
    private $spiralDataBase;
    private $userInfo;
    
    private $database = 'NJ_ReceivingDB';

    public function __construct(\App\Lib\SpiralDataBase $spiralDataBase, \App\Lib\UserInfo $userInfo){
        $this->spiralDataBase = $spiralDataBase;
        $this->userInfo = $userInfo;
    }

    public function select(array $itemids){
        return $this->getReceivingItems($itemids);
    }

    private function getReceivingItems(array $itemids){
        $this->spiralDataBase->setDataBase($this->database);
        foreach($itemids as $itemid){
            $this->spiralDataBase->addSearchCondition('orderCNumber',$itemid,'=','or');
        }
        $this->spiralDataBase->addSelectFields('registrationTime','orderCNumber','receivingCount','receivingHId');
        return $this->spiralDataBase->doSelect();
    }

}