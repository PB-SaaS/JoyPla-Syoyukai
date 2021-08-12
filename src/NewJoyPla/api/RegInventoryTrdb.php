<?php

namespace App\Api;

class RegInventoryTrdb{
    
    private $spiralDataBase;
    private $divisionId;
    private $billingId;
    private $userInfo;
    
    private $TransactionDatabase = 'NJ_inventoryTRDB';

    
    public function __construct(\App\Lib\SpiralDataBase $spiralDataBase, \App\Lib\UserInfo $userInfo){
        $this->spiralDataBase = $spiralDataBase;
        $this->userInfo = $userInfo;
    }

    public function orderCount(array $array, string $divisionId, string $pattern){
    	  $array = $this->requestUrldecode($array);

        $regData = $this->makeRegData($array,$divisionId,$pattern);//入庫
        $result = $this->regInventoryTrdb($regData);
        if($result['code'] != 0){
    		  return false;
        }
        return true;
    }

    public function orderWithinCount(array $array, string $divisionId, string $pattern){
    	$array = $this->requestUrldecode($array);

        $regData = $this->makeOrderedData($array,$divisionId,$pattern);//発注中
        $result = $this->regInventoryTrdb($regData);
        if($result['code'] != 0){
    		  return false;
        }
        return true;
    }

    /**
     * $pattern = 1; // 加算
     * $pattern = 2; // 減算
     */
    private function makeOrderedData(array $array,string $divisionId,string $pattern){
      $itemList = array();
      $count = 0;
      foreach($array as $inHPItemid => $data){
        $count = (int)$data['countNum'];
        if($count <= 0)
        {
          continue; //マイナス発注は発注中個数の計算をしない。
        }
        if($pattern == '1'){
          $count = $count;
        }else if($pattern == '2'){
          $count = -$count;
        }
        if($count != 0){
            $itemList[] = array(
                      'now',
                      $divisionId,
                      $inHPItemid,
                      0,
                      $this->userInfo->getHospitalId(),
                      $count,
          );
        }
      }

      return $itemList;
    }

    /**
     * $pattern = 1; // 加算
     * $pattern = 2; // 減算
     */
    private function makeRegData(array $array,string $divisionId,string $pattern){
      $itemList = array();
      $count = 0;
      foreach($array as $inHPItemid => $data){
        $count = (int)$data['countNum'];
        if($pattern == '1'){
          $count = $count;
        }else if($pattern == '2'){
          $count = -$count;
        }
        if($count != 0){
            $itemList[] = array(
                      'now',
                      $divisionId,
                      $inHPItemid,
                      $count,
                      $this->userInfo->getHospitalId(),
                      0,
          );
        }
      }

		  return $itemList;
    }

    private function regInventoryTrdb(array $itemList){

        /**
         * ここに処理を書く
         */
        $columns = array('registrationTime','divisionId','inHospitalItemId','count','hospitalId','orderWithinCount');

        $this->spiralDataBase->setDataBase($this->TransactionDatabase);

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