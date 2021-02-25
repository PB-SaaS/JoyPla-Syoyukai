<?php

namespace App\Api;

class GetUnordered{
    private $spiralDataBase;
    private $userInfo;
    
    private $database = 'NJ_OrderHDB';

    public function __construct(\App\Lib\SpiralDataBase $spiralDataBase, \App\Lib\UserInfo $userInfo){
        $this->spiralDataBase = $spiralDataBase;
        $this->userInfo = $userInfo;
    }

    public function select(){
        return $this->getUnordered();
    }

    private function getUnordered(){
        $this->spiralDataBase->setDataBase($this->database);
        $this->spiralDataBase->addSearchCondition('hospitalId',$this->userInfo->getHospitalId());
        $this->spiralDataBase->addSearchCondition('orderStatus',1);
        $this->spiralDataBase->addSelectFields('id');
        return $this->spiralDataBase->doSelect();
    }

}