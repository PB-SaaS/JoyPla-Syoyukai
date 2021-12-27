<?php

/*
namespace App\Api;

class ReceivingMonthlyReport{
    private $spiralDataBase;
    private $userInfo;
    
    private $distributorDB = 'NJ_distributorDB';
    private $ReceivingDB = '310_receItems';
    private $inHPItemDB = 'itemInHospitalDB';

    private $ReceivingData = array();
    private $inHPItemData = array();

    public function __construct(\App\Lib\SpiralDataBase $spiralDataBase, \App\Lib\UserInfo $userInfo){
        $this->spiralDataBase = $spiralDataBase;
        $this->userInfo = $userInfo;
    }

    public function dataSelect(string $startMonth = null,string $endMonth = null,string $distributorId = null,string $divisionId = null,string $itemName = null,string $itemCode = null,string $itemStandard = null,int $page = null,int $maxCount = null){
        $this->setReceivingDBSearch($startMonth,$endMonth,$divisionId);
        $this->ReceivingData = $this->getReceivingDB();

        if($this->ReceivingData['code'] != '0'){
            //var_dump($this->ReceivingData); 
            return $this->ReceivingData;
        }

        if($this->ReceivingData['count'] == '0'){
            return array('data'=>array(),'count'=>'0','totalAmount' => '0');
        }
        
        $this->setInHPItemDBSearch($itemName,$itemCode,$itemStandard,$page,$maxCount,$distributorId);
        $this->inHPItemData = $this->getInHPItemDB();
        
        if($this->inHPItemData['code'] != '0'){
            //var_dump($this->inHPItemData);
            return $this->inHPItemData;
        }
        return $this->makeReceivingMonthlyReport();
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


    private function setReceivingDBSearch(string $startMonth = null,string $endMonth = null,string $divisionId = null){
        
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

    private function makeReceivingMonthlyReport(){
        $result = array();
        foreach($this->inHPItemData['data'] as $inHPitem){
            //$receivingCount = $this->getReceivingQuantity($inHPitem[1]);
            //$returnCount = $this->getReturnCount($inHPitem[1]);
            $getInformationByPrice = $this->getInformationByPrice($inHPitem[1]);
            $result['data'][] = array(		
                'id' => $inHPitem[0],
                'inHospitalItemId' => $inHPitem[1],
                'makerName' => $inHPitem[2],
                'itemName' => $inHPitem[3],
                'itemCode' => $inHPitem[4],
                'itemStandard' => $inHPitem[5],
                'distributorName'=>  $getInformationByPrice['distributorName'],
                'quantity' => $getInformationByPrice['quantity'],
                'price' => $getInformationByPrice['price'],
                'receivingCount' => $getInformationByPrice['receivingCount'],
                'totalAmount' => $getInformationByPrice['totalAmount'],
                'itemUnit'=> $getInformationByPrice['itemUnit'],
                'totalReturnCount'=> $getInformationByPrice['returnCount'],
                'adjAmount' => $getInformationByPrice["adjAmount"],
                'priceAfterAdj' => $getInformationByPrice["priceAfterAdj"],
            );
        }
        $result['count'] = $this->inHPItemData['count'];
        $result['totalAmount'] = $this->getTotalAmount();
        return $result;
    }

/*
    private function getReceivingQuantity(string $inHospitalItemId){
        $ReceivingQuantity = 0;
        foreach($this->ReceivingData['data'] as $ReceivingItem){
            if($inHospitalItemId == $ReceivingItem[0]){
                $ReceivingQuantity = $ReceivingQuantity + (int)$ReceivingItem[1];
            }
        }
        return $ReceivingQuantity;
    }
*/
/*

    private function getReturnCount(string $inHospitalItemId){
        $ReturnCount = 0;
        foreach($this->ReceivingData['data'] as $ReceivingItem){
            if($inHospitalItemId == $ReceivingItem[0]){
                $ReturnCount = $ReturnCount + (int)$ReceivingItem[4];
            }
        }
        return $ReturnCount;
    }
    private function getInformationByPrice(string $inHospitalItemId){
        $disAndPriceArray = array();
        $receivingDataByPrice = array("price" => array(), "quantity" => array(), "receivingCount" => array(), "returnCount" => array(), "totalAmount" => array(), "adjAmount" => array(), "itemUnit" => array(), "distributorName" => array());
        foreach($this->ReceivingData['data'] as $receiving){
            if($inHospitalItemId == $receiving[0]){
                $key = array_search($receiving[10] ."_". $receiving[5], $disAndPriceArray);
                if($key === false){
                    $disAndPriceArray[] = $receiving[10] ."_". $receiving[5];
                    $key = array_search($receiving[10] ."_". $receiving[5], $disAndPriceArray);

                    $receivingDataByPrice["price"][$key] = $receiving[5];
                    $receivingDataByPrice["quantity"][$key] = 0;
                    $receivingDataByPrice["receivingCount"][$key] = 0;
                    $receivingDataByPrice["adjAmount"][$key] = 0;
                    $receivingDataByPrice["returnCount"][$key] = 0;
                }
                $receivingDataByPrice["distributorName"][$key] = $receiving[11];
                $receivingDataByPrice["quantity"][$key] = $receiving[6];
                $receivingDataByPrice["itemUnit"][$key] = $receiving[9];
                $receivingDataByPrice["receivingCount"][$key] = $receivingDataByPrice["receivingCount"][$key] + $receiving[1];
                $receivingDataByPrice["adjAmount"][$key] = $receivingDataByPrice["adjAmount"][$key] + $receiving[7];
                $receivingDataByPrice["returnCount"][$key] = $receivingDataByPrice["returnCount"][$key] + $receiving[4];
            }
        }
        
        foreach($receivingDataByPrice["price"] as $key => $byPriceData){
            $receivingDataByPrice["totalAmount"][$key] = $byPriceData * $receivingDataByPrice["receivingCount"][$key] ;
            $receivingDataByPrice["priceAfterAdj"][$key] = $receivingDataByPrice["totalAmount"][$key] + $receivingDataByPrice["adjAmount"][$key];
        }
        return $receivingDataByPrice;
    }

    private function getTotalAmount(){
        $ReceivingQuantity = 0;
        foreach($this->ReceivingData['data'] as $ReceivingItem){
            $ReceivingQuantity += $ReceivingItem[8];
        }
        return $ReceivingQuantity;
    }

    private function getReceivingDB(){
        $this->spiralDataBase->setDataBase($this->ReceivingDB);
        $this->spiralDataBase->addSearchCondition('hospitalId',$this->userInfo->getHospitalId());
        $this->spiralDataBase->addSelectFields('inHospitalItemId','receivingCount','registrationTime','receivingPrice','totalReturnCount','price','quantity','adjAmount','priceAfterAdj','itemUnit','distributorId','distributorName');
        return $this->spiralDataBase->doSelectLoop(); 
    }

    private function getInHPItemDB(){
        $this->spiralDataBase->setDataBase($this->inHPItemDB);
        $this->spiralDataBase->addSearchCondition('hospitalId',$this->userInfo->getHospitalId());
        foreach($this->ReceivingData['data'] as $record){
            $this->spiralDataBase->addSearchCondition('inHospitalItemId',$record[0],'=','or');
        }
        $this->spiralDataBase->addSortField('id', 'asc' );
        $this->spiralDataBase->addSelectFields('id','inHospitalItemId','makerName','itemName','itemCode','itemStandard','price','itemUnit','distributorName');
        return $this->spiralDataBase->doSelect();
    }
}
*/