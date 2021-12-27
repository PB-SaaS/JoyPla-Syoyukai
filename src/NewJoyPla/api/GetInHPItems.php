<?php

/*
namespace App\Api;

class GetInHPItems{
    
    private $spiralDataBase;
    private $userInfo;

    private $receivingHId;

    private $database = 'NJ_inHPItemDB';

    private $column = array(
        "id",
        "registrationTime",
        "updateTime",
        "inHospitalItemId",
        "authKey",
        "hospitalId",
        "distributorId",
        "catalogNo",
        "serialNo",
        "quantity",
        "quantityUnit",
        "itemUnit",
        "medicineCategory",
        "homeCategory",
        "notUsedFlag",
        "itemId",
        "itemName",
        "itemCode",
        "itemStandard",
        "itemJANCode",
        "notice",
        "HPstock",
        "makerName",
        "price",
        "oldPrice",
        "labelId",
        "minPrice",
        "officialFlag"
    );
    
    public function __construct(\App\Lib\SpiralDataBase $spiralDataBase,\App\Lib\UserInfo $userInfo){
        $this->spiralDataBase = $spiralDataBase;
        $this->userInfo = $userInfo;
    }

    public function setInHospitalItemId(string $inHospitalId){
        $this->spiralDataBase->addSearchCondition('inHospitalItemId',$inHospitalId,"=","or");
    }

    public function getInHPItems(){
        $result = $this->getInHPItemsDb();
        if($result['code'] != 0){
            return $result;
        }

        $result['data'] = $this->spiralDataBase->arrayToNameArray($result['data'],$this->column);
        return $this->makeArrayKeyToInItemId($result['data']);
    }

    private function getInHPItemsDb(){
        $this->spiralDataBase->setDataBase($this->database);
        $this->spiralDataBase->addSelectFieldsToArray($this->column);
        $this->spiralDataBase->addSearchCondition('hospitalId',$this->userInfo->getHospitalId());
        return $this->spiralDataBase->doSelectLoop();
    }

    private function makeArrayKeyToInItemId(array $data){
        $result = array();
        foreach($data as $record){
            $result[$record["inHospitalItemId"]] = $record;
        }
        return $result;
    }
}*/