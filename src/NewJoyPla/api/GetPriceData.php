<?php

namespace App\Api;

class GetPriceData{
    private $database = '310_ItemsPriceDb';
    private $column = array("priceId","distributorId","distributorName","quantity","price","hospitalId","requestFlg","quantityUnit","itemUnit","notice");
    /**
    * コンストラクタ
    * 
    * @access public
    * @param SpiralDataBase
    */
    public function __construct(\App\Lib\SpiralDataBase $spiralDataBase){
        $this->spiralDataBase = $spiralDataBase;
    }
    
    public function getPriceData(string $itemId , string $hospitalId ,string $priceId = null){
        $this->spiralDataBase->setDataBase($this->database);
        $this->spiralDataBase->addSortField('id' , 'asc' );
        $this->spiralDataBase->addSearchCondition('itemId',$itemId);
        $this->spiralDataBase->addSearchCondition('notUsedFlag','0');
        $this->spiralDataBase->addSearchCondition('requestFlg','1');
        $this->spiralDataBase->addSearchCondition('hospitalId',$hospitalId);
        if($priceId != null){
            $this->spiralDataBase->addSearchCondition('priceId',$priceId);
        }
        $this->spiralDataBase->addSelectFieldsToArray($this->column);
        $this->spiralDataBase->addSelectNameCondition('');
        $result = $this->spiralDataBase->doSelectLoop();
        return $this->spiralDataBase->arrayToNameArray($result['data'],$this->column);
    }

}