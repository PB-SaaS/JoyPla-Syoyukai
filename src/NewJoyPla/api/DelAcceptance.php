<?php

namespace App\Api;

/**
 * DelAcceptance
 * 検収書削除
 *
 * @package App\Api
 * @since PHP7.2
 * @author ito.shun <ito.shun@pi-pe.co.jp>
 * @copyright  PipedBits All Rights Reserved
 * 
 */
class DelAcceptance{
    
    private $spiralDataBase;
    
    private $historyDatabase = 'NJ_ReceivingHDB';
    //private $childDatabase = 'NJ_ReceivingDB';

    /**
	 * コンストラクタ
     * 
	 * @access public
	 * @param SpiralDataBase
	 */
    public function __construct(\App\Lib\SpiralDataBase $spiralDataBase){
        $this->spiralDataBase = $spiralDataBase;
    }

	/**
	 * 削除関数
     * 
	 * @access public
	 * @param string $receivingHId 検収書履歴番号
	 * @param string $authKey 変更削除キー
	 * @return boolean True or False
	 * @todo 
	 */
    public function delete(string $receivingHId,string $authKey){
    	if($receivingHId == ''){
    		return false;
    	}
        $delete = $this->deleteReceivingHistoryDB($receivingHId,$authKey);
        if($delete['code'] != 0){
            return false;
        }
        return true;
    }

	/**
	 * DBにリクエストを送る関数
     * 
	 * @access public
	 * @param string $receivingHId 検収書履歴番号
	 * @param string $authKey 変更削除キー
	 * @return array 
	 */
    private function deleteReceivingHistoryDB(string $receivingHId,string $authKey){
        $this->spiralDataBase->setDataBase($this->historyDatabase);
		$this->spiralDataBase->addSearchCondition('receivingHId',$receivingHId);
		$this->spiralDataBase->addSearchCondition('authKey',$authKey);
        $this->spiralDataBase->addSelectNameCondition('');
        return $this->spiralDataBase->doDelete();
    }
}