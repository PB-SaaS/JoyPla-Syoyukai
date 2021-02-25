<?php

namespace App\Api;

class GetReturnData{
    private $spiralDataBase;
    private $userInfo;
    
    private $database = 'returnData';
    private $column = array('id','makerName','itemName','itemCode','itemStandard','quantity','quantityUnit','itemUnit','itemJANCode','price','returnCount','returnPrice','receivingCount');
    
    public function __construct(\App\Lib\SpiralDataBase $spiralDataBase, \App\Lib\UserInfo $userInfo){
        $this->spiralDataBase = $spiralDataBase;
        $this->userInfo = $userInfo;
    }

    public function select(string $returnHistoryID){
        $result =  $this->getReturnItems($returnHistoryID);
        if($result['code'] != 0){
            return $result;
        }

        $result['data'] = $this->spiralDataBase->arrayToNameArray($result['data'],$this->column);
        return $result;
    }

    private function getReturnItems(string $returnHistoryID){
        $this->spiralDataBase->setDataBase($this->database);
        $this->spiralDataBase->addSearchCondition('returnHistoryID',$returnHistoryID);
        $this->spiralDataBase->addSelectFieldsToArray($this->column);
        return $this->spiralDataBase->doSelect();
    }

}