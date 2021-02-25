<?php

namespace App\Api;
/**
 * DelPayout
 * 払出伝票削除
 *
 * @package App\Api
 * @since PHP7.2
 * @author ito.shun <ito.shun@pi-pe.co.jp>
 * @copyright  PipedBits All Rights Reserved
 */
class DelPayout{

    private $spiralDataBase;
    
    private $historyDatabase = 'NJ_PayoutHDB';

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
	 * @param string $payoutHistoryId 払出履歴番号
	 * @param string $authKey 変更削除キー
	 * @return boolean
	 */
    public function delete(string $payoutHistoryId,string $authKey){
        $delete = $this->deletePayoutHistoryDB($payoutHistoryId,$authKey);
        if($delete['code'] != 0){
            return false;
        }
        return true;
    }

    /**
	 * 削除関数
     * 
	 * @access public
	 * @param string $payoutHistoryId 払出履歴番号
	 * @param string $authKey 変更削除キー
	 * @return array
	 */
    private function deletePayoutHistoryDB(string $payoutHistoryId,string $authKey){
        $this->spiralDataBase->setDataBase($this->historyDatabase);
		$this->spiralDataBase->addSearchCondition('payoutHistoryId',$payoutHistoryId);
		$this->spiralDataBase->addSearchCondition('payoutAuthKey',$authKey);
        $this->spiralDataBase->addSelectNameCondition('');
        return $this->spiralDataBase->doDelete();
    }
}