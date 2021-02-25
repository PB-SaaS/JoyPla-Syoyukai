<?php

namespace App\Api;

class GetTenantData{

    private $spiralDataBase;
    private $userInfo;

    private $database = 'NJ_TenantAdminDB';

    public function __construct(\App\Lib\SpiralDataBase $spiralDataBase, \App\Lib\UserInfo $userInfo){
        $this->spiralDataBase = $spiralDataBase;
        $this->userInfo = $userInfo;
    }

    public function select(string $tenantId = null){
        $result = $this->selectTenantAdminDB($tenantId);

        if($result['code'] != 0){
            return $result;
        }

        $result['data'] = $this->spiralDataBase->arrayToNameArray($result['data'],array('tenantId','tenantKind'));
        return $result;
    }

    private function selectTenantAdminDB(string $tenantId = null){
        $this->spiralDataBase->setDataBase($this->database);
        $this->spiralDataBase->addSelectFields('tenantId','tenantKind');
        if($tenantId == null){
            $tenantId = $this->userInfo->getTenantId();
        }
        $this->spiralDataBase->addSearchCondition('tenantId',$tenantId);
        return $this->spiralDataBase->doSelectLoop();
    }
}