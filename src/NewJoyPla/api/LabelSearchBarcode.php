<?php

namespace App\Api;

class LabelSearchBarcode{

    private $spiralDataBase;
    private $userInfo;
    
    private $InHPItemDB = 'itemInHospitalv2';

    private $fields = array("makerName","itemName","itemCode","itemStandard","quantity","price","itemJANCode","distributorName","inHospitalItemId","quantityUnit","itemUnit","distributorId","labelId","unitPrice");

    public function __construct(\App\Lib\SpiralDataBase $spiralDataBase, \App\Lib\UserInfo $userInfo){
        $this->spiralDataBase = $spiralDataBase;
        $this->userInfo = $userInfo;
    }
/**
 * $items = array(
 * "maker" => "小林製薬",
 * "shouhinName" => "オードムーゲローション",
 * "code" => "10180951",
 * "kikaku" => "規格",
 * "irisu" => "10",
 * "kakaku" => "35",
 * "jan" => "4987072035306",
 * "oroshi" => "株式会社パイプドビッツ",
 * "recordId" => "00000002",
 * "unit" => "個",
 * "itemUnit" => "箱",
 * "distributorId" => "pipedbits"
 * );
 * 
 */
    public function search(string $systemCheckId){
        if(preg_match('/^1/', $systemCheckId) && strlen($systemCheckId) == 14){
            //旧JoyPlaのラベル
            $labelId = substr($systemCheckId, 1 , 5);
            $labelId = str_pad($labelId, 8, 0, STR_PAD_LEFT);
            $customQuantity = substr($systemCheckId, 10 , 4);
            
            $result = $this->getInHPItemDBForOldLabel($labelId);

            if($result['code'] != '0'){
                return $result;
            }

            if($result['count'] == '0'){
                return array('code' => '1' , 'data' => array(),'message' => $result['message'] ,'count' => "0");
            }

            $data = $this->remakeData($result['data'][0],$customQuantity);
            
            return array('code' => $result['code'] , 'data' => $data ,'message' => $result['message'] ,'count' => $result['count']);
        }
        if(preg_match('/^01/', $systemCheckId) && strlen($systemCheckId) == 14){
            $inHospitalItemId = substr($systemCheckId, 2 , 8);
            $customQuantity = substr($systemCheckId, 10 , 4);

            $result = $this->getInHPItemDB($inHospitalItemId);
            
            if($result['code'] != '0'){
                return $result;
            }

            if($result['count'] == '0'){
                return array('code' => $result['code']  , 'data' => array(),'message' => $result['message'] ,'count' => "0");
            }

            $data = $this->remakeData($result['data'][0],$customQuantity);
            
            return array('code' => $result['code'] , 'data' => $data ,'message' => $result['message'] ,'count' => $result['count']);
        }
        return array('code' => '1' , 'data' => array(),'message' => "no data" ,'count' => "0");
    }

    private function getInHPItemDBForOldLabel(string $labelId){
        $this->spiralDataBase->setDataBase($this->InHPItemDB);
        $this->spiralDataBase->addSearchCondition('labelId',$labelId);
        $this->spiralDataBase->addSearchCondition('hospitalId',$this->userInfo->getHospitalId());
        $this->spiralDataBase->addSelectFieldsToArray($this->fields);
        return $this->spiralDataBase->doSelect();
    }

    private function getInHPItemDB(string $labelId){
        $this->spiralDataBase->setDataBase($this->InHPItemDB);
        //$this->spiralDataBase->addSearchCondition('inHospitalItemId',$inHospitalItemId);
        $this->spiralDataBase->addSearchCondition('labelId',$labelId);
        $this->spiralDataBase->addSearchCondition('hospitalId',$this->userInfo->getHospitalId());
        $this->spiralDataBase->addSelectFieldsToArray($this->fields);
        return $this->spiralDataBase->doSelect(); 
    }

    private function remakeData(array $resultData,String $customQuantity){
        $items = array(
            "maker" => $resultData[0],
            "shouhinName" => $resultData[1],
            "code" => $resultData[2],
            "kikaku" => $resultData[3],
            "irisu" => $resultData[4],
            "kakaku" => $resultData[5],
            "jan" => $resultData[6],
            "oroshi" => $resultData[7],
            "recordId" => $resultData[8],
            "unit" => $resultData[9],
            "itemUnit" => $resultData[10],
            "distributorId" => $resultData[11],
            "count" => (int)$customQuantity,//ラベルの入数
            "labelId" => $resultData[12],
            "unitPrice" => $resultData[13]
            );

        return $items;
    }

}