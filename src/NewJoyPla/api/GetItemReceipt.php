<?php

namespace App\Api;

class GetItemReceipt{
    
    private $spiralDataBase;
    private $userInfo;

    private $receivingHId;

    private $database = 'receivingdatav2';

    private $column = array('id','makerName','itemName','itemCode','itemStandard','quantity','quantityUnit','itemUnit','itemJANCode','orderQuantity','receivingCount','orderCNumber','inHospitalItemId','totalReturnCount','receivingNumber','price','labelId','officialFlag','lotNumber','lotDate');
    
    public function __construct(\App\Lib\SpiralDataBase $spiralDataBase,\App\Lib\UserInfo $userInfo){
        $this->spiralDataBase = $spiralDataBase;
        $this->userInfo = $userInfo;
    }

    public function getItemReceipt(string $receivingHId){
        $this->receivingHId = $receivingHId;
        $result = $this->getReceivingDb();
        if($result['code'] != 0){
            return $result;
        }

        $result['data'] = $this->spiralDataBase->arrayToNameArray($result['data'],$this->column);
        return $result;
    }

    private function getReceivingDb(){
        $this->spiralDataBase->setDataBase($this->database);
        $this->spiralDataBase->addSelectFieldsToArray($this->column);
        $this->spiralDataBase->addSearchCondition('receivingHId',$this->receivingHId);
        $this->spiralDataBase->addSearchCondition('hospitalId',$this->userInfo->getHospitalId());
        return $this->spiralDataBase->doSelectLoop();
    }
}