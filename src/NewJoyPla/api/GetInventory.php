<?php

namespace App\Api;

class GetInventory{

    private $spiralDataBase;
	private $userInfo;

    private $divisionId;
	
    private $InventoryHId;
    
    private $database = 'NJ_InventoryDB';

    public function __construct(\App\Lib\SpiralDataBase $spiralDataBase, \App\Lib\UserInfo $userInfo){
        $this->spiralDataBase = $spiralDataBase;
        $this->userInfo = $userInfo;
    }

    private function setDivisionId(string $divisionId){
        $this->divisionId = $divisionId;
	}
    
    public function getInventory(string $InventoryEId){
        
        return $this->getInventoryDB($InventoryEId);
    }
    
    private function getInventoryDB(string $InventoryEId){
        $this->spiralDataBase->setDataBase($this->database);
        $this->spiralDataBase->addSearchCondition('hospitalId',$this->userInfo->getHospitalId());
        $this->spiralDataBase->addSearchCondition('inventoryEndId',$InventoryEId);
        $this->spiralDataBase->addSelectFields('id','divisionId','inventryNum','inHospitalItemId');
        return $this->spiralDataBase->doSelectLoop();
    }

}