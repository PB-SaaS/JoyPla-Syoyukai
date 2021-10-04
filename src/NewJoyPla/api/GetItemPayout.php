<?php

namespace App\Api;

class GetItemPayout{
    
    private $spiralDataBase;
    private $userInfo;

    private $payoutNumber;

    private $database = 'payoutDatav2';
    
    public function __construct(\App\Lib\SpiralDataBase $spiralDataBase, \App\Lib\UserInfo $userInfo){
        $this->spiralDataBase = $spiralDataBase;
        $this->userInfo = $userInfo;
    }

    public function getItemPayout(string $payoutNumber){
        $this->payoutNumber = $payoutNumber;
        return $this->getPayoutDb();
    }

    private function getPayoutDb(){
        $this->spiralDataBase->setDataBase($this->database);
        $this->spiralDataBase->addSelectFields('registrationTime','payoutHistoryId','price','itemId','itemName','itemCode','itemStandard','itemJANCode','quantityUnit','payoutQuantity','payoutAmount','itemUnit','quantity','makerName','inHospitalItemId','payoutCount','payoutLabelCount','distributorId','catalogNo','labelId','lotNumber','lotDate','unitPrice');
        $this->spiralDataBase->addSearchCondition('payoutHistoryId',$this->payoutNumber);
        $this->spiralDataBase->addSearchCondition('hospitalId',$this->userInfo->getHospitalId());
        return $this->spiralDataBase->doSelectLoop();
    }
}