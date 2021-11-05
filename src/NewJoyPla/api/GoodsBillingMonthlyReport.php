<?php
/*

namespace App\Api;

class GoodsBillingMonthlyReport{
    private $spiralDataBase;
    private $userInfo;
    
    private $billingDB = 'NJ_BillingDB';
    private $inHPItemDB = 'NJ_inHPItemDB';

    private $billingData = array();
    private $inHPItemData = array();

    public function __construct(\App\Lib\SpiralDataBase $spiralDataBase, \App\Lib\UserInfo $userInfo){
        $this->spiralDataBase = $spiralDataBase;
        $this->userInfo = $userInfo;
    }

    public function dataSelect(string $startMonth = null,string $endMonth = null,string $divisionId = null,string $itemName = null,string $itemCode = null,string $itemStandard = null,int $page = null,int $maxCount = null,bool $useUnitPrice){
        $this->setBillingDBSearch($startMonth,$endMonth,$divisionId);
        $this->billingData = $this->getBillingDB();
        if($this->billingData['code'] != '0'){
            //var_dump($this->billingData);
            return $this->billingData;
        }
        if($this->billingData['count'] == '0'){
            return array('data'=>array(),'count'=>'0','totalAmount' => '0');
        }
        $this->setInHPItemDBSearch($itemName,$itemCode,$itemStandard,$page,$maxCount);
        $this->inHPItemData = $this->getInHPItemDB();
        if($this->inHPItemData['code'] != '0')
        {
            //var_dump($this->inHPItemData);
            return $this->inHPItemData;
        }
        return $this->makeGoodsBillingMonthlyReport($useUnitPrice);
    }

    private function setStartMonth(string $startMonth){
        $this->spiralDataBase->addSearchCondition('registrationTime',$startMonth,'>=','and');
    }

    private function setEndMonth(string $endMonth){
        $this->spiralDataBase->addSearchCondition('registrationTime',$endMonth,'<=','and');
    }

    private function setDivisionId(string $divisionId){
        $this->spiralDataBase->addSearchCondition('divisionId',$divisionId,'=','and');
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

    private function setBillingDBSearch(string $startMonth = null,string $endMonth = null,string $divisionId = null){
        
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

    private function makeGoodsBillingMonthlyReport(bool $useUnitPrice){
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
                'price' => $getInformationByPrice['price'],
                'unitPrice' => $getInformationByPrice['unitPrice'],
                'quantity' => $getInformationByPrice['quantity'],
                'quantityUnit' => $getInformationByPrice['quantityUnit'],
                'billingQuantity' => $getInformationByPrice['billingQuantity'],
                'totalAmount' => $getInformationByPrice['totalAmount'],
            );
        }
        $result['count'] = $this->inHPItemData['count'];
        $result['totalAmount'] = $this->getTotalAmount();
        return $result;
    }

    private function getInformationByPrice(string $inHospitalItemId,bool $useUnitPrice){
        $goodsDataByPrice = array("price" => array(), "quantity" => array(), "billingQuantity" => array(), "quantityUnit" => array(), "unitPrice" => array());
        foreach($this->billingData['data'] as $billingItem){
            if($inHospitalItemId == $billingItem[0]){
                $key = array_search($billingItem[4], $goodsDataByPrice["price"]);
                if($key === false){
                    $goodsDataByPrice["price"][] = $billingItem[4];
                    $key = array_search($billingItem[4], $goodsDataByPrice["price"]);
                    $goodsDataByPrice["quantity"][$key] = 0;
                    $goodsDataByPrice["billingQuantity"][$key] = 0;
                    $goodsDataByPrice["unitPrice"][$key] = 0;
                }

                $goodsDataByPrice["quantity"][$key] = $billingItem[5];
                $goodsDataByPrice["quantityUnit"][$key] = $billingItem[6];
                $goodsDataByPrice["unitPrice"][$key] = $billingItem[7];
                $goodsDataByPrice["billingQuantity"][$key] = $goodsDataByPrice["billingQuantity"][$key] + $billingItem[1];
            }
        }
        
        if (!$useUnitPrice) {
          foreach($goodsDataByPrice["price"] as $key => $byPriceData){
              $goodsDataByPrice["totalAmount"][$key] = ( $byPriceData / $goodsDataByPrice["quantity"][$key] ) * $goodsDataByPrice["billingQuantity"][$key] ;
          }
        }
        if ($useUnitPrice) {
          foreach($goodsDataByPrice["price"] as $key => $byPriceData){
              $goodsDataByPrice["totalAmount"][$key] = $goodsDataByPrice["unitPrice"][$key] * $goodsDataByPrice["billingQuantity"][$key] ;
          }
        }
        return $goodsDataByPrice;
    }

    private function getBillingDB(){
        $this->spiralDataBase->setDataBase($this->billingDB);
        $this->spiralDataBase->addSearchCondition('hospitalId',$this->userInfo->getHospitalId());
        $this->spiralDataBase->addSelectFields('inHospitalItemId','billingQuantity','registrationTime','billingAmount','price','quantity','quantityUnit','unitPrice');
        return $this->spiralDataBase->doSelectLoop();
    }

    private function getInHPItemDB(){
        $this->spiralDataBase->setDataBase($this->inHPItemDB);
        $this->spiralDataBase->addSearchCondition('hospitalId',$this->userInfo->getHospitalId());
        foreach($this->billingData['data'] as $record){
            $this->spiralDataBase->addSearchCondition('inHospitalItemId',$record[0],'=','or');
        }
        $this->spiralDataBase->addSortField('id', 'asc' );
        $this->spiralDataBase->addSelectFields('id','inHospitalItemId','makerName','itemName','itemCode','itemStandard','minPrice','quantityUnit','price','quantity');
        return $this->spiralDataBase->doSelect();
    }

    private function getTotalAmount(){
        $billingQuantity = 0;
        foreach($this->billingData['data'] as $billingItem){
            $billingQuantity += (float)$billingItem[3];
        }
        return $billingQuantity;
    }
}