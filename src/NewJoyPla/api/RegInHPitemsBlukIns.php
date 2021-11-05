<?php

/*
namespace App\Api;

class RegInHPitemsBlukIns{

    private $spiralDataBase;
    private $userInfo;

    private $database = "NJ_inHPItemDB";
    private $mstDatabase = "NJ_itemDB";
    private $distributorDB = 'NJ_distributorDB';

    public function __construct(\App\Lib\SpiralDataBase $spiralDataBase, \App\Lib\UserInfo $userInfo){
        $this->spiralDataBase = $spiralDataBase;
        $this->userInfo = $userInfo;
    }

    public function blukinsert(array $itemList){
        $itemList = $this->requestUrldecode($itemList);
        $mstItems = $this->getMstData($itemList);

        if($mstItems['code'] != "0"){
            return $mstItems;
        }

        $result = $this->checkItems($itemList,$mstItems['data']);
        if($result['result'] != true){
            return array('code' => '99', "message" => 'not regist itemsId: '.$result['id']);
        }

        $distributor = $this->getDistributorDB();
        if($distributor['code'] != "0"){
            return $distributor;
        }

        $result = $this->checkDistributor($itemList,$distributor['data']);
        if($result['result'] != true){
            return array('code' => '99', "message" => 'not regist distributorId: '.$result['id']);
        }

        $itemList = $this->remake($itemList);

        return $this->regItems($itemList);
    }

    private function checkItems(array $itemList,array $mstItems){
        $remakeMstItems = array();
        foreach( $mstItems as $record ){
            $remakeMstItems[] = $record[0];//itemId
        }

        foreach($itemList as $record){
            if(!in_array($record[0], $remakeMstItems)){
                //配列に存在しない商品IDは登録できない
                return array('result' => false , 'id' => $record[0]);
            }
        }
        return array('result' => true);
    }

    private function checkDistributor(array $itemList,array $distributor){
        $remakeDistributor = array();
        foreach( $distributor as $record ){
            $remakeDistributor[] = $record[0];
        }

        foreach($itemList as $record){
            if(!in_array($record[1], $remakeDistributor)){
                //配列に存在しない卸業者IDは登録できない
                return array('result' => false , 'id' => $record[1]);
            }
        }
        return array('result' => true);
    }

    private function remake(array $itemList){
        foreach($itemList as &$record){
            $record[] = $this->userInfo->getHospitalId();
        }
        return $itemList;
    }

    private function getMstData(array $itemList){
        $this->spiralDataBase->setDataBase($this->mstDatabase);
        $this->spiralDataBase->addSelectFields('itemId','tenantId');
        foreach($itemList as $item){
            $this->spiralDataBase->addSearchCondition('itemId',$item[0],'=','or');
        }
        $this->spiralDataBase->addSearchCondition('tenantId',$this->userInfo->getTenantId());
        return $this->spiralDataBase->doSelectLoop();
    }

    private function getDistributorDB(){
		$this->spiralDataBase->setDataBase($this->distributorDB);
		$this->spiralDataBase->addSelectFields('distributorId','distributorName');
		$this->spiralDataBase->addSearchCondition('hospitalId',$this->userInfo->getHospitalId());
		
		return $this->spiralDataBase->doSelect();
	}

    private function regItems(array $itemList){

        /**
         * ここに処理を書く
         */
        $columns = array("itemId","distributorId","catalogNo","serialNo","quantity","itemUnit","medicineCategory","homeCategory","notUsedFlag","notice","labelId","hospitalId");
        
        $this->spiralDataBase->setDataBase($this->database);
        
        return $this->spiralDataBase->doBulkInsert($columns ,$itemList);
        
        //throw new Exception('エラーハンドリング');
    }

    private function requestUrldecode(array $array){
		$result = array();
		foreach($array as $key => $value){
			if(is_array($value)){
				$result[$key] = $this->requestUrldecode($value);
			} else {
				$result[$key] = urldecode($value);
			}
		}
		return $result;
	}
}
 
