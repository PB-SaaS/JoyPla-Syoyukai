<?php

/*
namespace App\Api;

class OrderMonthlyReport{
    private $spiralDataBase;
    private $userInfo;
    
    private $OrderDB = 'hacchuShouhin';
    private $inHPItemDB = 'itemInHospitalDB';
 
    private $OrderData = array();
    private $inHPItemData = array();

    public function __construct(\App\Lib\SpiralDataBase $spiralDataBase, \App\Lib\UserInfo $userInfo){
        $this->spiralDataBase = $spiralDataBase;
        $this->userInfo = $userInfo;
    }

    public function dataSelect(string $startMonth = null,string $endMonth = null,string $distributorId = null,string $divisionId = null,string $itemName = null,string $itemCode = null,string $itemStandard = null,int $page = null,int $maxCount = null){
        $this->setOrderDBSearch($startMonth,$endMonth,$divisionId);
        $this->OrderData = $this->getOrderDB();
        if($this->OrderData['code'] != '0'){
            //var_dump($this->OrderData);
            return $this->OrderData;
        }
        if($this->OrderData['count'] == '0'){
            return array('data'=>array(),'count'=>'0','totalAmount' => '0');
        }
        $this->setInHPItemDBSearch($itemName,$itemCode,$itemStandard,$page,$maxCount,$distributorId);
        $this->inHPItemData = $this->getInHPItemDB();
        if($this->inHPItemData['code'] != '0'){
            //var_dump($this->inHPItemData);
            return $this->inHPItemData;
        }
        return $this->makeOrderMonthlyReport();
    }

    private function setStartMonth(string $startMonth){
        $this->spiralDataBase->addSearchCondition('registrationTime',$startMonth,'>=','and');
    }

    private function setEndMonth(string $endMonth){
        $this->spiralDataBase->addSearchCondition('registrationTime',$endMonth,'<=','and');
    }

    private function setDistributorId(string $distributorId){
        $this->spiralDataBase->addSearchCondition('distributorId',$distributorId,'=','and');
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

    private function setDivisionId(string $divisionId){
        $this->spiralDataBase->addSearchCondition('divisionId',$divisionId,'=','and');
    }

    private function setOrderDBSearch(string $startMonth = null,string $endMonth = null,string $divisionId = null){
        
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

    private function setInHPItemDBSearch(string $itemName = null,string $itemCode = null,string $itemStandard = null,int $page = null,int $maxCount = null,string $distributorId = null){
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
        if($distributorId != null){
            $this->setDistributorId($distributorId);
        }
    }

    private function makeOrderMonthlyReport(){
        $result = array();
        foreach($this->inHPItemData['data'] as $inHPitem){
            $getInformationByPrice = $this->getInformationByPrice($inHPitem[1]);
            $result['data'][] = array(
                'id' => $inHPitem[0],
                'inHospitalItemId' => $inHPitem[1],
                'makerName' => $inHPitem[2],
                'itemName' => $inHPitem[3],
                'itemCode' => $inHPitem[4],
                'itemStandard' => $inHPitem[5],
                'price' => $getInformationByPrice['price'],
                'orderQuantity' => $getInformationByPrice['orderQuantity'],
                'totalAmount' => $getInformationByPrice['totalAmount'],
                'itemUnit'=> $getInformationByPrice['itemUnit'],
                'distributorName'=> $getInformationByPrice['distributorName'],
            );
        }
        $result['count'] = $this->inHPItemData['count'];
        $result['totalAmount'] = $this->getTotalAmount();
        return $result;
    }

    private function getInformationByPrice(string $inHospitalItemId){
        $disAndPriceArray = array();
        $orderDataByPrice = array("price" => array(), "quantity" => array(), "receivingCount" => array(), "returnCount" => array(), "totalAmount" => array(), "adjAmount" => array(), "itemUnit" => array(), "distributorName" => array());
        foreach($this->OrderData['data'] as $order){
            if($inHospitalItemId == $order[0]){
                $key = array_search($order[8] ."_". $order[7], $disAndPriceArray);
                if($key === false){
                    $disAndPriceArray[] = $order[8] ."_". $order[7];
                    $key = array_search($order[8] ."_". $order[7], $disAndPriceArray);

                    $orderDataByPrice["price"][$key] = $order[7];
                    $orderDataByPrice["quantity"][$key] = 0;
                    $orderDataByPrice["orderQuantity"][$key] = 0;
                }
                $orderDataByPrice["distributorName"][$key] = $order[9];
                $orderDataByPrice["quantity"][$key] = $order[4];
                $orderDataByPrice["itemUnit"][$key] = $order[6];
                $orderDataByPrice["orderQuantity"][$key] = $orderDataByPrice["orderQuantity"][$key] + $order[1];
            }
        }
        
        foreach($orderDataByPrice["price"] as $key => $byPriceData){
            $orderDataByPrice["totalAmount"][$key] = $byPriceData * $orderDataByPrice["orderQuantity"][$key] ;
        }
        return $orderDataByPrice;
    }
    
    private function getOrderQuantity(string $inHospitalItemId){
        $orderQuantity = 0;
        foreach($this->OrderData['data'] as $orderItem){
            if($inHospitalItemId == $orderItem[0]){
                $orderQuantity = $orderQuantity + $orderItem[1];
            }
        }
        return $orderQuantity;
    }

    private function getTotalAmount(){
        $orderQuantity = 0;
        foreach($this->OrderData['data'] as $orderItem){
            $orderQuantity += (float)$orderItem[3];
        }
        return $orderQuantity;
    }

    private function getOrderDB(){
        $this->spiralDataBase->setDataBase($this->OrderDB);
        $this->spiralDataBase->addSearchCondition('hospitalId',$this->userInfo->getHospitalId());
        $this->spiralDataBase->addSearchCondition('orderStatus','1','!=');
        $this->spiralDataBase->addSelectFields('inHospitalItemId','orderQuantity','registrationTime','orderPrice','quantity','quantityUnit','itemUnit','price','distributorId','distributorName');
        return $this->spiralDataBase->doSelectLoop();
    }

    private function getInHPItemDB(){
        $this->spiralDataBase->setDataBase($this->inHPItemDB);
        $this->spiralDataBase->addSearchCondition('hospitalId',$this->userInfo->getHospitalId());
        foreach($this->OrderData['data'] as $record){
            $this->spiralDataBase->addSearchCondition('inHospitalItemId',$record[0],'=','or');
        }
        $this->spiralDataBase->addSortField('id', 'asc' );
        $this->spiralDataBase->addSelectFields('id','inHospitalItemId','makerName','itemName','itemCode','itemStandard','price','itemUnit','distributorName');
        return $this->spiralDataBase->doSelect();
    }
}