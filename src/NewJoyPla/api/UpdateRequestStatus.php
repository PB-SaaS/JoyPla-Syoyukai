<?php

namespace App\Api;

class UpdateRequestStatus{

    private $spiralDatabase;

    private $status = "";
    
    private $historyDatabase = 'NJ_QRequestDB';
    private $childDatabase = 'NJ_reqItemDB';

    public function __construct(\App\Lib\SpiralDatabase $spiralDatabase){
        $this->spiralDatabase = $spiralDatabase;
    }

    public function open(string $requestId){
        $this->status = "2";
        $updateQRequestDB = $this->updateQRequestDB($requestId);
        if($updateQRequestDB['code'] != "0"){
            var_dump($updateQRequestDB);
            return array();
        }
        return $updateQRequestDB;
    }

    public function itemsReg(string $requestId){
        $this->status = "3";
        $updateQRequestDB = $this->updateQRequestDB($requestId);
        if($updateQRequestDB['code'] != "0"){
            var_dump($updateQRequestDB);
            return array();
        }
        return $updateQRequestDB;
    }

    public function hospitalCheck(string $requestId){
        $this->status = "";
        $getReqItemDB = $this->getReqItemDB($requestId);
        
        if($getReqItemDB['code'] != "0"){
            var_dump($getReqItemDB);
            return array();
        }
        $this->status = $this->checkStaus($getReqItemDB['data']);
        $updateQRequestDB = $this->updateQRequestDB($requestId);
        if($updateQRequestDB['code'] != "0"){
            var_dump($updateQRequestDB);
            return array();
        }
        return $updateQRequestDB;
    }

    private function checkStaus(array $reqItemData){
        
        /**
         *  1	未開封
         *  2	開封
         *  3	商品記載有
         *  4	一部却下
         *  5	一部採用
         *  6	却下
         *  7	採用
         */
        $recordCount = count($reqItemData);
        $rec = 0;
        $not = 0;
        foreach($reqItemData as $record){
           if($record['1'] == '1'){
            $rec++;
           }
           
           if($record['1'] == '2'){
            $not++;
           }
        }
        if($recordCount == $rec){
            return 7;
        }
        if($recordCount == $not){
            return 6;
        }
        if($rec > 0){
            return 5;
        }
        if($not > 0){
            return 4;
        }
    }

    private function getReqItemDB(string $requestId){
        /** requestFlg
         * 1	採用
         * 2	不採用
         */
        $this->spiralDatabase->setDataBase($this->childDatabase);
        $this->spiralDatabase->addSelectFields('id','requestFlg');
        $this->spiralDatabase->addSearchCondition('requestId',$requestId);
        return $this->spiralDatabase->doSelectLoop();
    }

    private function updateQRequestDB(string $requestId){
        $updateData = array(
            array(
                "name" => "requestStatus",
                "value" => $this->status,
            )
        );
        $this->spiralDatabase->setDataBase($this->historyDatabase);
		$this->spiralDatabase->addSearchCondition('requestId',$requestId);
        $this->spiralDatabase->addSelectNameCondition('');
        return $this->spiralDatabase->doUpdate($updateData);
    }

    public function selectQRequestDB(string $requestId){
        $this->spiralDatabase->setDataBase($this->historyDatabase);
		$this->spiralDatabase->addSearchCondition('requestId',$requestId);
        $this->spiralDatabase->addSelectFields('id','requestTitle','distributorUName');
        $this->spiralDatabase->addSelectNameCondition('');
        return $this->spiralDatabase->doSelect();
    }
}