<?php

/*
namespace App\Api;

class UpdateInHPItems{

    private $database = "NJ_inHPItemDB";
    private $itemId = "";

    public function __construct(\App\Lib\SpiralDataBase $spiralDataBase){
        $this->spiralDataBase = $spiralDataBase;
    } 

    public function updateInHPItems(string $itemId){
        $this->itemId = $itemId;
        return $this->updateInHPItemDB();
    }

    private function updateInHPItemDB(){
        $this->spiralDataBase->setDataBase($this->database);
		$this->spiralDataBase->addSearchCondition('itemId',$this->itemId);
        $this->spiralDataBase->addSelectNameCondition('');
        return $this->spiralDataBase->doUpdate(array(array("name" => "updateTime","value" => "now")));
    }
}
*/