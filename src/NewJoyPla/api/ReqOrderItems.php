<?php

namespace App\Api;

class ReqOrderItems{

    private $spiralDataBase;
    private $database = 'NJ_OrderDB';
    public function __construct(\App\Lib\SpiralDataBase $spiralDataBase){
        $this->spiralDataBase = $spiralDataBase;
    }

    public function bulkUpdate(array $updateData){
        if(count($updateData) == 0){
            return array('code' => '1', 'message' => 'no data');
        }
        $this->spiralDataBase->setDataBase($this->database);
        return $this->spiralDataBase->doBulkUpdate('orderCNumber',array('orderCNumber','orderQuantity') ,$updateData);
    } 

    public function delete(array $deleteData){
        if(count($deleteData) == 0){
            return array('code' => '1', 'message' => 'no data');
        }
        $this->spiralDataBase->setDataBase($this->database);
        foreach($deleteData as $num){
            $this->spiralDataBase->addSearchCondition('orderCNumber',$num,'=','or');
        }
        $this->spiralDataBase->addSelectNameCondition('');
        return $this->spiralDataBase->doDelete();
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