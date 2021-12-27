<?php

/*
namespace App\Api;

class GetStock{

    private $spiralDataBase;
	private $userInfo;

    private $divisionId;
    
    private $database = 'stockManagement';

    public function __construct(\App\Lib\SpiralDataBase $spiralDataBase, \App\Lib\UserInfo $userInfo){
        $this->spiralDataBase = $spiralDataBase;
        $this->userInfo = $userInfo;
    }

    private function setDivisionId(string $divisionId){
        $this->divisionId = $divisionId;
	}

    public function getStockData(array $items,string $divisionId){
		$this->setDivisionId($divisionId);
		return $this->getStockDB($items);
	}
    
    private function getStockDB(array $items){
    	$this->spiralDataBase->setDataBase($this->database);
		$this->spiralDataBase->addSearchCondition('hospitalId',$this->userInfo->getHospitalId());
		$this->spiralDataBase->addSearchCondition('divisionId',$this->divisionId);
		foreach($items as $inHpitemId => $record){
			$this->spiralDataBase->addSearchCondition('inHospitalItemId',$inHpitemId,'=','or');
		}
        $this->spiralDataBase->addSelectFields('inHospitalItemId','stockQuantity','rackName','divisionName','constantByDiv','distributorName');
		return $this->spiralDataBase->doSelectLoop();
	}

}*/