<?php

namespace App\Api;

class GetReceivingHistory{
    private $spiralDataBase;
    private $userInfo;
    
    private $database = 'receiptHDB';

    private $column = array('registrationTime','distributorName','distributorId','orderHistoryId','hospitalName','postalCode','prefectures','address','phoneNumber','ordererUserName','authKey','orderAuthKey','divisionId');

    public function __construct(\App\Lib\SpiralDataBase $spiralDataBase, \App\Lib\UserInfo $userInfo){
        $this->spiralDataBase = $spiralDataBase;
        $this->userInfo = $userInfo;
    }

    public function select(string $receivingHId){
        $result = $this->getReceivingHistory($receivingHId);
        if($result['code'] != 0){
            return $result;
        }
        $result['data'] = $this->spiralDataBase->arrayToNameArray($result['data'],$this->column);
        return $result;
    }

    private function getReceivingHistory(string $receivingHId){
        $this->spiralDataBase->setDataBase($this->database);
        $this->spiralDataBase->addSearchCondition('receivingHId',$receivingHId);
        $this->spiralDataBase->addSelectFieldsToArray($this->column);
        return $this->spiralDataBase->doSelect();
    }

}