<?php

namespace App\Api;
/**
 * GetCardInfo
 * 単票情報取得
 *
 * @package App\Api
 * @since PHP7.2
 * @author ito.shun <ito.shun@pi-pe.co.jp>
 * @copyright  PipedBits All Rights Reserved
 */

class GetCardInfo{

    private $spiralDataBase;

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
     * 取得関数
     * 
     * @access public
     * @param string $database データベースタイトル
     * @param string $cardID レコードID
     * @param string ...$fields(可変) フィールド名
     * @return array
     */
    public function select(string $database, string $cardID , string ...$fields){
        $this->spiralDataBase->setDataBase($database);
        $this->spiralDataBase->addSelectFields(...$fields);
        $this->spiralDataBase->addSearchCondition('id',$cardID);
        return $this->spiralDataBase->doSelectLoop();
    }
}