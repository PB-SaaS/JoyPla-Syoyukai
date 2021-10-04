<?php

namespace App\Api;

class GetItemInventory{
    
    private $spiralDataBase;
    private $userInfo;

    private $payoutNumber;

    private $database = 'inventoryDatav2';
    
    public function __construct(\App\Lib\SpiralDataBase $spiralDataBase, \App\Lib\UserInfo $userInfo){
        $this->spiralDataBase = $spiralDataBase;
        $this->userInfo = $userInfo;
    }

    public function getItemInventory(string $inventoryHId){
        $this->inventoryHId = $inventoryHId;
        return $this->getInventoryDb();
    }

    private function getInventoryDb(){
        $this->spiralDataBase->setDataBase($this->database);
        $this->spiralDataBase->addSelectFields('registrationTime','inHospitalItemId','distributorName','makerName','itemName','itemCode','itemStandard','quantityUnit','price','inventryNum','inventryAmount','unitPrice');
        $this->spiralDataBase->addSearchCondition('inventoryHId',$this->inventoryHId);
        $this->spiralDataBase->addSearchCondition('hospitalId',$this->userInfo->getHospitalId());
        return $this->spiralDataBase->doSelectLoop();
    }
}