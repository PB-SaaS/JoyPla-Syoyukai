<?php

/*
namespace App\Api;

class GetHospitalData{

    private $spiralDataBase;
    private $userInfo;

    private $database = 'NJ_HospitalDB';
    //ラベルデザイン取得を追加
    private $column = array(
      'registrationTime','updateTime','hospitalId','hospitalName','postalCode','prefectures','address','phoneNumber','faxNumber','tenantId','name','nameKana','mailAddress','contactAddress',
      'plan','receivingTarget','function1','function2','function3','function4','function5','function6','function7','function8',
      'registerableNum','authKey','labelDesign1','labelDesign2','billingUnitPrice','payoutUnitPrice','invUnitPrice'
      );
    public function __construct(\App\Lib\SpiralDataBase $spiralDataBase, \App\Lib\UserInfo $userInfo){
        $this->spiralDataBase = $spiralDataBase;
        $this->userInfo = $userInfo;
    }

    public function select(string $hospitalId = null){
        $result = $this->selectHospitalDB($hospitalId);

        if($result['code'] != 0){
            return $result;
        }

        $result['data'] = $this->spiralDataBase->arrayToNameArray($result['data'],$this->column);
        return $result;
    }

    private function selectHospitalDB(string $hospitalId = null){
        $this->spiralDataBase->setDataBase($this->database);
        $this->spiralDataBase->addSelectFieldsToArray($this->column);
        if($hospitalId == null){
            $hospitalId = $this->userInfo->getHospitalId();
        }
        $this->spiralDataBase->addSearchCondition('hospitalId',$hospitalId);
        return $this->spiralDataBase->doSelectLoop();
    }
}