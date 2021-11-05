<?php

/*
namespace App\Api;

class GetHospitalUserCount{
    private $spiralDataBase;
    private $userInfo;
    
    private $database = 'NJ_HUserDB';

    public function __construct(\App\Lib\SpiralDataBase $spiralDataBase, \App\Lib\UserInfo $userInfo){
        $this->spiralDataBase = $spiralDataBase;
        $this->userInfo = $userInfo;
    }

    public function select(string $hospitalId = null){
        return $this->getHospitalUser($hospitalId);
    }

    private function getHospitalUser(string $hospitalId = null){
        $this->spiralDataBase->setDataBase($this->database);
        if($hospitalId == null){
            $hospitalId = $this->userInfo->getHospitalId();
        }
        $this->spiralDataBase->addSearchCondition('hospitalId',$hospitalId);
        $this->spiralDataBase->addSelectFields('id');
        return $this->spiralDataBase->doSelect();
    }

}