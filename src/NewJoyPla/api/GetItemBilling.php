<?php

namespace App\Api;

class GetItemBilling{
    
    private $spiralDataBase;
    private $userInfo;

    private $billingNumber;

    private $database = 'billingDetail';
    
    public function __construct(\App\Lib\SpiralDataBase $spiralDataBase, \App\Lib\UserInfo $userInfo){
        $this->spiralDataBase = $spiralDataBase;
        $this->userInfo = $userInfo;
    }

    public function getItemBilling(string $billingNumber){
        $this->billingNumber = $billingNumber;
        return $this->getBillingDb();
    }

    private function getBillingDb(){ 
        $this->spiralDataBase->setDataBase($this->database);
        $this->spiralDataBase->addSelectFields('registrationTime','updateTime','inHospitalItemId','billingNumber','price','billingQuantity','billingAmount','hospitalId','divisionId','itemId','itemName','itemCode','itemStandard','itemJANCode','quantityUnit','makerName','itemUnit','quantity');
        $this->spiralDataBase->addSearchCondition('billingNumber',$this->billingNumber);
        $this->spiralDataBase->addSearchCondition('hospitalId',$this->userInfo->getHospitalId());
        return $this->spiralDataBase->doSelectLoop();
    }
}