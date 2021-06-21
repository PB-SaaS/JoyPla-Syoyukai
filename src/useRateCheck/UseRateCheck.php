<?php 

class UseRateCheck{

    private $spiral = '';
    private $_token = '00011Be8BfDFae1aa4ad1179a25545efa22b59275ba354899fbb';
    private $_secret = '8510b67a2ece280f1eb69f597995803cc104b104';

    private $trDdName = 'dataBaseUseRate';
    private $acName = 'joypla2_dev';//account毎に変更

    private $databases = array(
        "1" => "NJ_HUserDB",
        "2" => "NJ_HospitalDB",
        "3" => "NJ_divisionDB",
        "4" => "NJ_OUserDB",
        "5" => "NJ_distributorDB",
        "6" => "NJ_itemDB",
        "7" => "NJ_inHPItemDB",
        "8" => "NJ_BillingHDB",
        "9" => "NJ_BillingDB",
        "10" =>  "NJ_OrderHDB",
        "11" => "NJ_OrderDB",
        "12" => "NJ_stockDB",
        "13" => "NJ_ReceivingHDB",
        "14" => "NJ_ReceivingDB",
        "15" => "NJ_PayoutHDB",
        "16" => "NJ_PayoutDB",
        "17" => "NJ_InventoryHDB",
        "18" => "NJ_InventoryDB",
        "19" => "NJ_InventoryEDB",
        "20" => "NJ_TenantAdminDB",
        "21" => "NJ_LotDB",
        "22" => "NJ_ReturnDB",
        "23" => "NJ_ReturnHDB",
        "24" => "NJ_QRequestDB",
        "25" => "NJ_reqItemDB",
        "26" => "NJ_TopicDB",
        "27" => "NJ_NoticeDB",
        "28" => "NJ_contactUsDB"
    );

    public function __construct(\Spiral $spiral){
        $this->spiral = $spiral;
    }

    private function databaseGet(string $dbTitle){
        //$this->spiral->setApiTokenTitle(''); //Api
        $api_communicator = $this->spiral->getSpiralApiCommunicator();
    
        // リクエストを作成します。
        $request = new \SpiralApiRequest();
        $request->put("db_title"         , $dbTitle);
    
        // スパイラルAPIサーバへリクエストを送信します。
        $response = $api_communicator->request("database", "get", $request);

        $responseArray = array();
		foreach($response->entrySet() as $key => $val){
			$responseArray[$key] = $this->obj2arr($val);
		}
        return $responseArray;
    }

    
    private function databaseSelect(string $dbTitle){
        //$this->spiral->setApiTokenTitle(''); //Api
        $api_communicator = $this->spiral->getSpiralApiCommunicator();
    
        // リクエストを作成します。
        $request = new \SpiralApiRequest();
        $request->put("db_title"         , $dbTitle);
        $request->put("lines_per_page"         , "1");
        
        // スパイラルAPIサーバへリクエストを送信します。
        $response = $api_communicator->request("database", "select", $request);

        $responseArray = array();
		foreach($response->entrySet() as $key => $val){
			$responseArray[$key] = $this->obj2arr($val);
		}
        return $responseArray;
    }

	private function obj2arr($obj){
		
		if ( ! is_object($obj) && ! is_array($obj)  ) return htmlspecialchars($obj, ENT_QUOTES, "UTF-8");//PHPサーバーはUTF-8
	
		$arr = (array) $obj;
	
		foreach ( $arr as &$a )
		{
			$a = $this->obj2arr($a);
		}
		return $arr;
	}

    public function getUsingInfo(){
        $result = array();
        foreach($this->databases as $num => $databaseName){
            $tmp = $this->databaseGet($databaseName);
            $result['assigningCount'.$num] =  $tmp['schema']['record_limit'];
            $tmp = $this->databaseSelect($databaseName);
            $result['registCount'.$num] =  $tmp['count'];
        }
        $insertResult = $this->regMstAc($result);
        return $insertResult;
    }

    private function getInsertData($result){
        $remake = array(
            array('name'=>'date','value'=>'now'),
            array('name'=>'accountName','value'=>$this->acName)
        );
        foreach($result as $key => $val){
            $remake[] = array('name'=>$key,'value'=>$val);
        }
        return $remake;
    }

    private function getLocator(){
        // ロケータのURL (変更の必要はありません)
        $locator = "https://www.pi-pe.co.jp/api/locator";
        // スパイラルの操作画面で発行したトークンを設定します。
        $TOKEN = $this->_token;
        // API用のHTTPヘッダ
        $api_headers = array(
            "X-SPIRAL-API: locator/apiserver/request",
            "Content-Type: application/json; charset=UTF-8",
        );
        // 送信するJSONデータを作成
        $parameters = array();
        $parameters["spiral_api_token"] = $TOKEN; //トークン
        // 送信用のJSONデータを作成します。
        $json = json_encode($parameters);
        // curlライブラリを使って送信します。
        $curl = curl_init($locator);
        curl_setopt($curl, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($curl, CURLOPT_POST , true);
        curl_setopt($curl, CURLOPT_POSTFIELDS , $json);
        curl_setopt($curl, CURLOPT_HTTPHEADER , $api_headers);
        curl_exec($curl);
        // エラーがあればエラー内容を表示
        if (curl_errno($curl)) echo curl_error($curl);
        $response = curl_multi_getcontent($curl);
        curl_close($curl);
        $response_json = json_decode($response , true);
        // サービス用のURL
        return $response_json['location'];
    }

    private function regMstAc($result){

        $TOKEN = $this->_token;
        $SECRET = $this->_secret;

        // -----------------------------------------------------------------------------
        // select
        // -----------------------------------------------------------------------------
        // API用のHTTPヘッダ
        $api_headers = array(
        "X-SPIRAL-API: database/insert/request",
        "Content-Type: application/json; charset=UTF-8",
        );
        // 送信するJSONデータを作成
        $parameters = array();
        $parameters["spiral_api_token"] = $TOKEN; //トークン
        $parameters["db_title"] = $this->trDdName; //DBのタイトル
        $parameters["passkey"] = \time(); //エポック秒
        // 表示カラム名
        var_dump($this->getInsertData($result));
        
        $parameters["data"] = $this->getInsertData($result);

        // 署名を付けます
        $key = $parameters["spiral_api_token"] . "&" . $parameters["passkey"];
        $parameters["signature"] = \hash_hmac('sha1', $key, $SECRET, false);
        // 送信用のJSONデータを作成します。
        $json = \json_encode($parameters);
        echo "===> database/select\n";
        // curlライブラリを使って送信します。
        $curl = \curl_init($this->getLocator());
        \curl_setopt($curl, CURLOPT_RETURNTRANSFER, 1);
        \curl_setopt($curl, CURLOPT_POST , true);
        \curl_setopt($curl, CURLOPT_POSTFIELDS , $json);
        \curl_setopt($curl, CURLOPT_HTTPHEADER , $api_headers);
        \curl_exec($curl);
        // エラーがあればエラー内容を表示
        if (\curl_errno($curl)) echo \curl_error($curl);
        $response = \curl_multi_getcontent($curl);
        \curl_close($curl);
        // 画面に表示
        return \json_decode($response, true);
    }

}