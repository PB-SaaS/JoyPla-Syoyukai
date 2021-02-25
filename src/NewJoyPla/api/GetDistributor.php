<?php

namespace App\Api;
/**
 * GetDistributor
 * 卸業者情報取得
 *
 * @package App\Api
 * @since PHP7.2
 * @author ito.shun <ito.shun@pi-pe.co.jp>
 * @copyright  PipedBits All Rights Reserved
 */

class GetDistributor{

  private $spiralDataBase;
  private $userInfo;
  
  private $distributorDB = 'NJ_distributorDB';

  /**
   * コンストラクタ
   * 
   * @access public
   * @param SpiralDataBase
   * @param UserInfo
   */
  public function __construct(\App\Lib\SpiralDataBase $spiralDataBase, \App\Lib\UserInfo $userInfo){
    $this->spiralDataBase = $spiralDataBase;
    $this->userInfo = $userInfo;
  }

  /**
	 * 卸業者情報取得
   * 
	 * @access public
	 * @return array
	 */
  public function getDistributor(){
    $result = $this->getDistributorDB();
    return $result;
  }

  /**
	 * 卸業者情報取得
   * 
	 * @access private
	 * @return array
	 */
  private function getDistributorDB(){
    $this->spiralDataBase->setDataBase($this->distributorDB);
    $this->spiralDataBase->addSelectFields('distributorName','distributorId');
    $this->spiralDataBase->addSearchCondition('hospitalId',$this->userInfo->getHospitalId());
    
    return $this->spiralDataBase->doSelect();
  }

  /**
	 * 卸業者情報取得
   * 
	 * @access public
　 * @param string $distributorId 卸業者ID
	 * @return array
	 */
  public function getMyDistributor(string $distributorId){
    $result = $this->getMyDistributorDB($distributorId);
    return $result;
  }

  /**
	 * 卸業者情報取得
   * 
	 * @access private
　 * @param string $distributorId 卸業者ID
	 * @return array
	 */
  private function getMyDistributorDB(string $distributorId){
    $this->spiralDataBase->setDataBase($this->distributorDB);
    $this->spiralDataBase->addSelectFields('distributorName','distributorId');
    $this->spiralDataBase->addSearchCondition('distributorId',$distributorId);
    return $this->spiralDataBase->doSelect();
  }
}