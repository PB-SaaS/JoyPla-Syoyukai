<?php

namespace App\Api;

class OroshiSearchBarcode{

    private $spiralDataBase;
    private $userInfo;
    private $spiralTable;
    
    private $orderHDB = 'NJ_OrderHDB';

    private $receivingHDB = 'NJ_ReceivingHDB';
    
    public function __construct(\App\Lib\SpiralDataBase $spiralDataBase,\App\Lib\SpiralTable $spiralTable, \App\Lib\UserInfo $userInfo){
        $this->spiralDataBase = $spiralDataBase;
        $this->spiralTable = $spiralTable;
        $this->userInfo = $userInfo;
    }

    /**
     * $cardTitles = array(
     * '02' => url,
     * '03_unorder' => url,
     * '03_order' => url,
     * '04' => url,
     * '05' => url,
     * '06' => url,
     * '08' => url,
     * )
     */
    public function search(string $systemCheckId,string $jsessonId,string $myAreaTitle,array $cardTitles){
        
        if(preg_match('/^03/', $systemCheckId) && strlen($systemCheckId) == 18){
            //注文書
            $result = $this->getOrderHDB($systemCheckId);
            if($result['code'] != '0'){
                return array('code' => $result['code'],'urls'=>array(),'message'=>$result['message']);
            }
            if($result['count'] == '0'){
                return array('code' => '1','urls'=>array(),'message'=>'no urls');
            }
            
            $cardTitle = '';
            
            if(isset($cardTitles['03_order'])){
                $cardTitle = $cardTitles['03_order'];
            }

            if($result['data'][0][1] == '1' && isset($cardTitles['03_unorder'])){
                return array('code' => '1','urls'=>array(),'message'=>'no urls');
            }

            $result = $this->getUrl($result['data'][0][0], $jsessonId, $myAreaTitle, $cardTitle);

            if($result['code'] != '0'){
                return array('code' => $result['code'],'urls'=>array(),'message'=>$result['message']);
            }
            return $result;
        }
        if(preg_match('/^04/', $systemCheckId) && strlen($systemCheckId) == 18){
            //検収書
            $result = $this->getReceivingHDB($systemCheckId);
            if($result['code'] != '0'){
                return array('code' => $result['code'],'urls'=>array(),'message'=>$result['message']);
            }
            if($result['count'] == '0'){
                return array('code' => '1','urls'=>array(),'message'=>'no urls');
            }

            $cardTitle = '';
            if(isset($cardTitles['04'])){
                $cardTitle = $cardTitles['04'];
            }

            $result = $this->getUrl($result['data'][0][0], $jsessonId, $myAreaTitle, $cardTitle);

            if($result['code'] != '0'){
                return array('code' => $result['code'],'urls'=>array(),'message'=>$result['message']);
            }

            return $result;
        }
        return array('code' => '99','urls'=>array(),'message'=>'no urls');
    } 

    private function getOrderHDB(string $systemCheckId){
        $this->spiralDataBase->setDataBase($this->orderHDB);
        $this->spiralDataBase->addSearchCondition('orderNumber',$systemCheckId);
        $this->spiralDataBase->addSearchCondition('distributorId',$this->userInfo->getDistributorId());
        $this->spiralDataBase->addSelectFields('id','orderStatus');
        return $this->spiralDataBase->doSelect(); 
    }
    
    private function getReceivingHDB(string $systemCheckId){
        $this->spiralDataBase->setDataBase($this->receivingHDB);
        $this->spiralDataBase->addSearchCondition('receivingHId',$systemCheckId);
        $this->spiralDataBase->addSearchCondition('distributorId',$this->userInfo->getDistributorId());
        $this->spiralDataBase->addSelectFields('id');
        return $this->spiralDataBase->doSelect(); 
    }
    
    private function getUrl(string $id,string $jsessonId,string $myAreaTitle,string $cardTitle){
        $this->spiralTable->setJsessionid($jsessonId);
        $this->spiralTable->setMyAreaTitle($myAreaTitle);
        $this->spiralTable->setCardTitle($cardTitle);
        $this->spiralTable->addIds($id);
        return $this->spiralTable->getCardUrls();
    }


}