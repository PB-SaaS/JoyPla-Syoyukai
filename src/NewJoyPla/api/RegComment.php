<?php

namespace App\Api;

class RegComment{

    private $spiralDataBase;
	private $topicId;
	private $authKey;
	
    private $database = 'NJ_comment';

    public function __construct(\App\Lib\SpiralDataBase $spiralDataBase){
        $this->spiralDataBase = $spiralDataBase;
	}
	
	public function regComment(String $topicId,String $authKey,array $commentData){
		$commentData = $this->requestUrldecode($commentData);

		$this->topicId = $topicId;
		$this->authKey = $authKey;

		return $this->insertComment($commentData);
	}

    private function insertComment(array $commentData){
		$insertData = array(
			array(
				"name" => "registrationTime",
				"value" => "now"
			),
			array(
				"name" => "topicId",
				"value" => $this->topicId
			),
			array(
				"name" => "name",
				"value" => $commentData['name']
			),
			array(
				"name" => "comment",
				"value" => $commentData['comment']
			),
			array(
				"name" => "authKey",
				"value" => $this->authKey
			)
		);
        /**
         * ここに処理を書く
         */
        $this->spiralDataBase->setDataBase($this->database);
        return $this->spiralDataBase->doInsert($insertData);
        //return $this->spiralDataBase->doInsert($insertData);
        //throw new Exception("エラーハンドリング");
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