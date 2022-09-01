<?php 

namespace App\Lib ;

use LogConfig;
use Logger;

/**
 * SPIRAL API をラッピングしたアクセスクラスです。
 *
 * @access public
 * @author ito.shun <ito.shun@pi-pe.co.jp>
 * @copyright  PipedBits All Rights Reserved
 * @category Message
 * @package ApiSpiral
 */
 class ApiSpiral{

	protected $spiral;
	protected $request;
	protected $apiCommunicator;

	public static ?Logger $logger = null ;

	/**
	 *  Public requestAPI 
	 * 
	 *  SPIRAL API を利用するための機能
	 *
	 * @access public
	 * @param Array $apiHeader　SpiralApiRiquestHeader
	 * @param Array $parameters ApiRiquestParam
	 * @return Array $responseArray SpiralApiReturnData(htmlspecialchars)
	 * @see $SPIRAL SpiralApiRequest()
	 * @throws なし　返却値をもとにハンドリング
	 * @todo 未対応（改善）事項等
	 */
	public function __construct(\Spiral $SPIRAL){
	}

	public function setApiCommunicator($apiCommunicator){
		$this->apiCommunicator = $apiCommunicator;
	}

	public function setSpiralApiRequest(\SpiralApiRequest $spiralApiRequest){
		$this->request =  $spiralApiRequest;
	}

	public function requestAPI($apiHeader, $parameters){

		//$this->spiral->setApiTokenTitle(APITITLE); //APIタイトル
		//$apiCommunicator = $this->spiral->getSpiralApiCommunicator();

		foreach($parameters as $param_name => $param_value){
			$this->request->put($param_name, $param_value);
		}
		$response = $this->apiCommunicator->request($apiHeader[0], $apiHeader[1], $this->request);
		
		$this->logging($apiHeader[0] ."/". $apiHeader[1], $this->request , $response);
		
		$responseArray = array();
		foreach($response->entrySet() as $key => $val){
			$responseArray[$key] = $this->obj2arr($val,$apiHeader);
		}
		
		return $responseArray;
	}

	public function logging($header , $request ,$response)
	{
		global $SPIRAL;
		/*
        if(! LogConfig::EXPORT_TO_SPIRALV2){ return ""; }
        $spiralv2 = new LoggingSpiralv2(LogConfig::SPIRALV2_API_KEY , 'https://api.spiral-platform.com/v1/');
        $spiralv2->setAppId(LogConfig::LOGGING_APP_TITLE);
        $spiralv2->setDbId(LogConfig::SPIRAL_API_LOGGING_DB_TITLE);

        $logger = new Logger($spiralv2);
		*/
		if( $this::$logger )
		{
			$body = [
				'execTime' => Logger::getTime(),
				'AccountId' => $SPIRAL->getAccountId(),
				'request' => $request->get('db_title'),
				'header' => $header,
				'code' => $response->getResultCode(),
				'message' =>  $response->getMessage(),
			];

			$this::$logger->out($body);
		}
	}
	
	/**
	 *  Public Object->Array 
	 *
	 *  SPIRAL API の返却値をArrayに変換
	 *
	 * @access private
	 * @param Object or Array or String $obj 
	 * @param Array  apiHeader
	 * @return Array $arrayData SpiralApiReturnData(htmlspecialchars)
	 * @throws なし
	 * @todo 未対応（改善）事項等
	 */
	private function obj2arr($obj,$apiHeader){
		if ( ! is_object($obj) && ! is_array($obj) ){
			if($apiHeader[0] == "database" && $apiHeader[1] == "select" ){
				return htmlspecialchars($obj, ENT_QUOTES, "UTF-8");//PHPサーバーはUTF-8
			} else {
				return $obj;
			}
		}
	
		$arr = (array) $obj;
	
		foreach ( $arr as &$a ){
			$a = $this->obj2arr($a,$apiHeader);
		}
		return $arr;
	}
}