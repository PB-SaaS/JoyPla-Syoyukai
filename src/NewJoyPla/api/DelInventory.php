<?php

namespace App\Api;
/**
 * DelInventory
 * 棚卸履歴削除
 *
 * @package App\Api
 * @since PHP7.2
 * @author ito.shun <ito.shun@pi-pe.co.jp>
 * @copyright  PipedBits All Rights Reserved
 */

class DelInventory{

    private $spiralDataBase;
    
    private $historyDatabase = 'NJ_InventoryEDB';
    private $childDatabase = 'NJ_InventoryHDB';

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
	 * 棚卸終了伝票削除関数
     * 
	 * @access public
	 * @param string $inventoryEndId 棚卸終了伝票番号
	 * @param string $authKey 変更削除キー
	 * @return boolean
	 */
    public function deleteEndHistory(string $inventoryEndId,string $authKey){
        $delete = $this->deleteInventoryEDB($inventoryEndId,$authKey);
        if($delete['code'] != 0){
            return false;
        }
        return true;
    }

    /**
	 * 棚卸終了伝票削除関数
     * 
	 * @access private
	 * @param string $inventoryEndId 棚卸終了伝票番号
	 * @param string $authKey 変更削除キー
	 * @return array
	 */
    private function deleteInventoryEDB(string $inventoryEndId,string $authKey){
        $this->spiralDataBase->setDataBase($this->historyDatabase);
		$this->spiralDataBase->addSearchCondition('inventoryEndId',$inventoryEndId);
		$this->spiralDataBase->addSearchCondition('invEndAuthKey',$authKey);
        $this->spiralDataBase->addSelectNameCondition('');
        return $this->spiralDataBase->doDelete();
    }

    /**
	 * 部署別棚卸伝票削除関数
     * 
	 * @access public
	 * @param string $inventoryHId 部署別棚卸伝票番号
	 * @param string $authKey 変更削除キー
	 * @return boolean
	 */
    public function deleteHistory(string $inventoryHId,string $authKey){
        $delete = $this->deleteInventoryHDB($inventoryHId,$authKey);
        if($delete['code'] != 0){
            return false;
        }
        return true;
    }

    /**
	 * 部署別棚卸伝票削除関数
     * 
	 * @access public
	 * @param string $inventoryHId 部署別棚卸伝票番号
	 * @param string $authKey 変更削除キー
	 * @return array
	 */
    private function deleteInventoryHDB(string $inventoryHId,string $authKey){
        $this->spiralDataBase->setDataBase($this->childDatabase);
		$this->spiralDataBase->addSearchCondition('inventoryHId',$inventoryHId);
		$this->spiralDataBase->addSearchCondition('invHAuthKey',$authKey);
        $this->spiralDataBase->addSelectNameCondition('');
        return $this->spiralDataBase->doDelete();
    }
}