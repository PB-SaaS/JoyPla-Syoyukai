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

    public function dataSelect(string $startMonth = null,string $endMonth = null,string $divisionId = null,string $itemName = null,string $itemCode = null,string $itemStandard = null,int $page = null,int $maxCount = null,bool $useUnitPrice){
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
        return $this->makePayoutMonthlyReport($useUnitPrice);
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

    private function makePayoutMonthlyReport(bool $useUnitPrice){
        $result = array();
        foreach($this->inHPItemData['data'] as $inHPitem){
            $getInformationByPrice = $this->getInformationByPrice($inHPitem[1],$useUnitPrice);
            $result['data'][] = array(
                'id' => $inHPitem[0],
                'inHospitalItemId' => $inHPitem[1],
                'makerName' => $inHPitem[2],
                'itemName' => $inHPitem[3],
                'itemCode' => $inHPitem[4],
                'itemStandard' => $inHPitem[5],
                //'minPrice' => $inHPitem[6],
                'price' => $getInformationByPrice["price"],
                'unitPrice' => $getInformationByPrice["unitPrice"],
                'quantity' => $getInformationByPrice["quantity"],
                'payoutQuantity' => $getInformationByPrice["payoutQuantity"],
                'totalAmount' => $getInformationByPrice["totalAmount"],
                'adjAmount' => $getInformationByPrice["adjAmount"],
                'priceAfterAdj' => $getInformationByPrice["priceAfterAdj"],
                'quantityUnit'=> $getInformationByPrice["quantityUnit"],
            );
        }
        $result['count'] = $this->inHPItemData['count'];
        $result['totalAmount'] = $this->getTotalAmount();
        return $result;
    }

    private function getInformationByPrice(string $inHospitalItemId,bool $useUnitPrice){
        $payoutDataByPrice = array("price" => array(), "quantity" => array(), "payoutQuantity" => array(), "totalAmount" => array(), "adjAmount" => array(), "quantityUnit" => array(), "unitPrice" => array());
        foreach($this->PayoutData['data'] as $payoutItem){
            if($inHospitalItemId == $payoutItem[0]){
                $key = array_search($payoutItem[4], $payoutDataByPrice["price"]);
                if($key === false){
                    $payoutDataByPrice["price"][] = $payoutItem[4];
                    $key = array_search($payoutItem[4], $payoutDataByPrice["price"]);
                    $payoutDataByPrice["quantity"][$key] = 0;
                    $payoutDataByPrice["payoutQuantity"][$key] = 0;
                    $payoutDataByPrice["unitPrice"][$key] = 0;
                }

                $payoutDataByPrice["quantity"][$key] = $payoutItem[5];
                $payoutDataByPrice["quantityUnit"][$key] = $payoutItem[8];
                $payoutDataByPrice["payoutQuantity"][$key] = $payoutDataByPrice["payoutQuantity"][$key] + $payoutItem[1];
                $payoutDataByPrice["unitPrice"][$key] = $payoutItem[9];
                $payoutDataByPrice["adjAmount"][$key] = $payoutDataByPrice["adjAmount"][$key] + $payoutItem[6];
            }
        }
        
        if (!$useUnitPrice) {
          foreach($payoutDataByPrice["price"] as $key => $byPriceData){
              $payoutDataByPrice["totalAmount"][$key] = ( $byPriceData / $payoutDataByPrice["quantity"][$key] ) * $payoutDataByPrice["payoutQuantity"][$key] ;
              $payoutDataByPrice["priceAfterAdj"][$key] = $payoutDataByPrice["totalAmount"][$key] + $payoutDataByPrice["adjAmount"][$key];
          }
        }
        if ($useUnitPrice) {
          foreach($payoutDataByPrice["price"] as $key => $byPriceData){
              $payoutDataByPrice["totalAmount"][$key] = $payoutDataByPrice["unitPrice"][$key] * $payoutDataByPrice["payoutQuantity"][$key] ;
              $payoutDataByPrice["priceAfterAdj"][$key] = $payoutDataByPrice["totalAmount"][$key] + $payoutDataByPrice["adjAmount"][$key];
          }
        }
        return $payoutDataByPrice;
    }

    private function getPayoutDB(){
        $this->spiralDataBase->setDataBase($this->PayoutDB);
        $this->spiralDataBase->addSearchCondition('hospitalId',$this->userInfo->getHospitalId());
        $this->spiralDataBase->addSelectFields('inHospitalItemId','payoutQuantity','registrationTime','payoutAmount','price','quantity','adjAmount','priceAfterAdj','quantityUnit','unitPrice');
        return $this->spiralDataBase->doSelectLoop();
    }

    private function getInHPItemDB(){
        $this->spiralDataBase->setDataBase($this->inHPItemDB);
        $this->spiralDataBase->addSearchCondition('hospitalId',$this->userInfo->getHospitalId());
        foreach($this->PayoutData['data'] as $record){
            $this->spiralDataBase->addSearchCondition('inHospitalItemId',$record[0],'=','or');
        }
        $this->spiralDataBase->addSortField('id', 'asc' );
        $this->spiralDataBase->addSelectFields('id','inHospitalItemId','makerName','itemName','itemCode','itemStandard','price','quantityUnit');
        return $this->spiralDataBase->doSelect();
    }

    private function getTotalAmount(){
        $TotalAmount = 0;
        foreach($this->PayoutData['data'] as $payoutItem){
            $TotalAmount += $payoutItem[7];
        }
        return $TotalAmount;
    }
}