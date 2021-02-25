<?php

namespace App\Api;
/**
 * DelBilling
 * 物品請求履歴削除
 *
 * @package App\Api
 * @since PHP7.2
 * @author ito.shun <ito.shun@pi-pe.co.jp>
 * @copyright  PipedBits All Rights Reserved
 */
class DelBilling{

    private $spiralDataBase;
    
    private $historyDatabase = 'NJ_BillingHDB';

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
	 * @param string $billingNumber 物品請求履歴番号
	 * @param string $authKey 変更削除キー
	 * @return boolean True or False
	 * @todo 
	 */
    public function delete(string $billingNumber,string $authKey){
        $delete = $this->deleteBillingHistoryDB($billingNumber,$authKey);
        if($delete['code'] != 0){
            return false;
        }
        return true;
    }

	/**
	 * DBにリクエストを送る関数
     * 
	 * @access public
	 * @param string $billingNumber 物品請求履歴番号
	 * @param string $authKey 変更削除キー
	 * @return array 
	 */
    private function deleteBillingHistoryDB(string $billingNumber,string $authKey){
        $this->spiralDataBase->setDataBase($this->historyDatabase);
		$this->spiralDataBase->addSearchCondition('billingNumber',$billingNumber);
		$this->spiralDataBase->addSearchCondition('billingAuthKey',$authKey);
        $this->spiralDataBase->addSelectNameCondition('');
        return $this->spiralDataBase->doDelete();
    }
}