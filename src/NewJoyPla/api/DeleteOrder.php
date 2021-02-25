<?php

namespace App\Api;
/**
 * DeleteOrder
 * 注文書削除
 *
 * @package App\Api
 * @since PHP7.2
 * @author ito.shun <ito.shun@pi-pe.co.jp>
 * @copyright  PipedBits All Rights Reserved
 */

class DeleteOrder{

    private $spiralDataBase;
    
    private $historyDatabase = 'NJ_OrderHDB';

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
	 * @param string $orderNum 発注書番号
	 * @param string $orderAuthKey 変更削除キー
	 * @return boolean
	 */
    public function delete(string $orderNum,string $orderAuthKey){

        $delete = $this->deleteOrderHistoryDB($orderNum,$orderAuthKey);
        if($delete['code'] != 0){
            return false;
        }
        return true;
    }
    
    /**
	 * 削除関数
     * 
	 * @access private
	 * @param string $orderNum 発注書番号
	 * @param string $orderAuthKey 変更削除キー
	 * @return array
	 */
    private function deleteOrderHistoryDB(string $orderNum,string $orderAuthKey){
        $this->spiralDataBase->setDataBase($this->historyDatabase);
		$this->spiralDataBase->addSearchCondition('orderNumber',$orderNum);
		$this->spiralDataBase->addSearchCondition('orderAuthKey',$orderAuthKey);
        $this->spiralDataBase->addSelectNameCondition('');
        return $this->spiralDataBase->doDelete();
    }
}