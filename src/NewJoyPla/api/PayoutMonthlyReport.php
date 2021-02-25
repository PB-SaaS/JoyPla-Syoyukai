<?php

namespace App\Api;

class PayoutMonthlyReport{
    private $spiralDataBase;
    private $userInfo;
    
    private $PayoutDB = 'NJ_PayoutDB';
    private $inHPItemDB = 'itemInHospitalDB';

    private $PayoutData = array();
    private $inHPItemData = array();

    public function __construct(\App\Lib\SpiralDataBase $spiralDataBase, \App\Lib\UserInfo $userInfo){
        $this->spiralDataBase = $spiralDataBase;
        $this->userInfo = $userInfo;
    }

    public function dataSelect(string $startMonth = null,string $endMonth = null,string $divisionId = null,string $itemName = null,string $itemCode = null,string $itemStandard = null,int $page = null,int $maxCount = null){
        $this->setPayoutDBSearch($startMonth,$endMonth,$divisionId);
        $this->PayoutData = $this->getPayoutDB();
        if($this->PayoutData['code'] != '0'){
            //var_dump($this->PayoutData);
            return $this->PayoutData;
        }
        if($this->PayoutData['count'] == '0'){
            return array('data'=>array(),'count'=>'0','totalAmount' => '0');
        }
        $this->setInHPItemDBSearch($itemName,$itemCode,$itemStandard,$page,$maxCount);
        $this->inHPItemData = $this->getInHPItemDB();
        if($this->inHPItemData['code'] != '0'){
            //var_dump($this->inHPItemData);
            return $this->inHPItemData;
        }
        return $this->makePayoutMonthlyReport();
    }

    private function setStartMonth(string $startMonth){
        $this->spiralDataBase->addSearchCondition('registrationTime',$startMonth,'>=','and');
    }

    private function setEndMonth(string $endMonth){
        $this->spiralDataBase->addSearchCondition('registrationTime',$endMonth,'<=','and');
    }

    private function setDivisionId(string $divisionId){
        $this->spiralDataBase->addSearchCondition('targetDivisionId',$divisionId,'=','and');
    }

    private function setItemName(string $itemName){
        $this->spiralDataBase->addSearchCondition('itemName',"%$itemName%",'LIKE','and');
    }
    
    private function setItemCode(string $itemCode){
        $this->spiralDataBase->addSearchCondition('itemCode',"%$itemCode%",'LIKE','and');
    }

    private function setItemStandard(string $itemStandard){
        $this->spiralDataBase->addSearchCondition('itemStandard',"%$itemStandard%",'LIKE','and');
    }
    
    private function setPage(int $page){
        $this->spiralDataBase->setPage($page);
    }

    private function setMaxCount(int $maxCount){
        $this->spiralDataBase->setLinesPerPage($maxCount);
    }

    private function setPayoutDBSearch(string $startMonth = null,string $endMonth = null,string $divisionId = null){
        
        if($startMonth != null){
            $this->setStartMonth($startMonth);
        }
        if($endMonth != null){
            $this->setEndMonth(date("Y-m-d",strtotime($endMonth . "+1 day")));
        }
        if($divisionId != null){
            $this->setDivisionId($divisionId);
        }
    }

    private function setInHPItemDBSearch(string $itemName = null,string $itemCode = null,string $itemStandard = null,int $page = null,int $maxCount = null){
        if($itemName != null){
            $this->setItemName($itemName);
        }
        if($itemCode != null){
            $this->setItemCode($itemCode);
        }
        if($itemStandard != null){
            $this->setItemStandard($itemStandard);
        }
        if($page != null){
            $this->setPage($page);
        }
        if($maxCount != null){
            $this->setMaxCount($maxCount);
        }
    }

    private function makePayoutMonthlyReport(){
        $result = array();
        foreach($this->inHPItemData['data'] as $inHPitem){
            $payoutQuantity = $this->getPayoutQuantity($inHPitem[1]);
            $result['data'][] = array(
                'id' => $inHPitem[0],
                'inHospitalItemId' => $inHPitem[1],
                'makerName' => $inHPitem[2],
                'itemName' => $inHPitem[3],
                'itemCode' => $inHPitem[4],
                'itemStandard' => $inHPitem[5],
                'minPrice' => $inHPitem[6],
                'payoutQuantity' => $payoutQuantity,
                'totalAmount' => $inHPitem[6] * $payoutQuantity,
                'quantityUnit'=> $inHPitem[7]
            );
        }
        $result['count'] = $this->inHPItemData['count'];
        $result['totalAmount'] = $this->getTotalAmount();
        return $result;
    }

    private function getPayoutQuantity(string $inHospitalItemId){
        $payoutQuantity = 0;
        foreach($this->PayoutData['data'] as $payoutItem){
            if($inHospitalItemId == $payoutItem[0]){
                $payoutQuantity = $payoutQuantity + $payoutItem[1];
            }
        }
        return $payoutQuantity;
    }

    private function getPayoutDB(){
        $this->spiralDataBase->setDataBase($this->PayoutDB);
        $this->spiralDataBase->addSearchCondition('hospitalId',$this->userInfo->getHospitalId());
        $this->spiralDataBase->addSelectFields('inHospitalItemId','payoutQuantity','registrationTime','payoutAmount');
        return $this->spiralDataBase->doSelectLoop();
    }

    private function getInHPItemDB(){
        $this->spiralDataBase->setDataBase($this->inHPItemDB);
        $this->spiralDataBase->addSearchCondition('hospitalId',$this->userInfo->getHospitalId());
        foreach($this->PayoutData['data'] as $record){
            $this->spiralDataBase->addSearchCondition('inHospitalItemId',$record[0],'=','or');
        }
        $this->spiralDataBase->addSelectFields('id','inHospitalItemId','makerName','itemName','itemCode','itemStandard','minPrice','quantityUnit');
        return $this->spiralDataBase->doSelect();
    }

    private function getTotalAmount(){
        $payoutQuantity = 0;
        foreach($this->PayoutData['data'] as $payoutItem){
            $payoutQuantity += (float)$payoutItem[3];
        }
        return $payoutQuantity;
    }
}