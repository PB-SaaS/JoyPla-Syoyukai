<?php

namespace App\Api;

/**
 * GetDivision
 * 部署情報取得
 *
 * @package App\Api
 * @since PHP7.2
 * @author ito.shun <ito.shun@pi-pe.co.jp>
 * @copyright  PipedBits All Rights Reserved
 */
/*
class GetDivision{

    private $spiralDataBase;
    private $userInfo;

    private $database = 'NJ_divisionDB';

    public function __construct(\App\Lib\SpiralDataBase $spiralDataBase, \App\Lib\UserInfo $userInfo){
        $this->spiralDataBase = $spiralDataBase;
        $this->userInfo = $userInfo;
    }

    public function select(string $divisionId = null){
        $result = $this->selectDivision($divisionId);

        if($result['code'] != 0){
            return $result;
        }

        $result2 = $this->selectStore();

        if($result2['code'] != 0){
            return $result2;
        }

        return array('store' => $result2['data'] , 'division'=> $result['data'] ,'code'=>$result2['code']);
    }

    private function selectDivision(string $divisionId = null){
        $this->spiralDataBase->setDataBase($this->database);
        $this->spiralDataBase->addSelectFields('registrationTime','divisionId','hospitalId','divisionName','divisionType','deleteFlag');
        $this->spiralDataBase->addSearchCondition('hospitalId',$this->userInfo->getHospitalId());
        $this->spiralDataBase->addSearchCondition('divisionType','2');
        if($divisionId != null){
            $this->spiralDataBase->addSearchCondition('divisionId',$divisionId);
        }
        return $this->spiralDataBase->doSelectLoop();
    }
    private function selectStore(){
        $this->spiralDataBase->setDataBase($this->database);
        $this->spiralDataBase->addSelectFields('registrationTime','divisionId','hospitalId','divisionName','divisionType','deleteFlag');
        $this->spiralDataBase->addSearchCondition('hospitalId',$this->userInfo->getHospitalId());
        $this->spiralDataBase->addSearchCondition('divisionType','1');
        return $this->spiralDataBase->doSelectLoop();
    }
}