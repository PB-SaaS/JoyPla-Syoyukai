<?php

require_once ('../src/NewJoyPla/lib/ApiSpiral.php');
require_once ('../src/NewJoyPla/lib/Define.php');
require_once ('../mock/Spiral.php');
require_once ('../mock/SpiralApiRequest.php');
require_once ('../mock/PbSpiralApiCommunicator.php');

require_once "../src/framework/Core/Collection.php";
require_once "../src/framework/Core/SpiralORM.php";

$SPIRAL = new \Spiral();
$ModelName = "";
if(isset($argv[1]))
{
    $ModelName = $argv[1];
    require_once "../src/JoyPla/Enterprise/SpiralDb/".$argv[1].".php";
}
foreach(get_declared_classes() as $className)
{
    if (preg_match("/^App\\\SpiralDb/", $className)) {
        if($ModelName === "" || "App\SpiralDb\\".$ModelName === $className)
        {
            $debug = new Debug($className) ;
            $debug->checkField();
        }
    }
}


class Debug 
{
    private $token = "00011KB9HzJA6571fc2a62048af337abb32cbab1e0dfa3c8aadb";
    private $secret = "f2bf4a8e7b3567fdba896429ea1c136e89320175";
    
    public function __construct($className)
    {
        $this->model = $className;
        $this->apiUrl = "";
        $this->schema = "";
        $this->getLocator();
        $this->getSchema();
    }
    
    public function getLocator()
    {
        if($this->apiUrl != "")
        {
            return $this->apiUrl;
        }
        $locator = "https://www.pi-pe.co.jp/api/locator";
        $TOKEN = $this->token;
        $api_headers = array(
        "X-SPIRAL-API: locator/apiserver/request",
        "Content-Type: application/json; charset=UTF-8",
        );
        $parameters = array();
        $parameters["spiral_api_token"] = $TOKEN; //トークン
        $json = json_encode($parameters);
        $curl = curl_init($locator);
        curl_setopt($curl, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($curl, CURLOPT_POST , true);
        curl_setopt($curl, CURLOPT_POSTFIELDS , $json);
        curl_setopt($curl, CURLOPT_HTTPHEADER , $api_headers);
        curl_exec($curl);
        if (curl_errno($curl)) echo curl_error($curl);
        $response = curl_multi_getcontent($curl);
        curl_close($curl);
        $response_json = json_decode($response , true);
        
        return $this->apiUrl = $response_json['location'];
    }
    
    public function getSchema()
    {
        if($this->schema != "")
        {
            return $this->schema;
        }
        $param_parameters = array();
        $param_parameters["db_title"] = $this->model::$spiral_db_name;
        
        $api_headers = array(
            "X-SPIRAL-API: database/get/request",
            "Content-Type: application/json; charset=UTF-8"
        );
        $param_parameters["spiral_api_token"] = $this->token;
        $param_parameters["passkey"] = time();
        $key = $param_parameters["spiral_api_token"] . "&" . $param_parameters["passkey"];
        $param_parameters["signature"] = hash_hmac('sha1', $key, $this->secret, false);
        $json = json_encode($param_parameters);
        $curl = curl_init($this->apiUrl);
        curl_setopt($curl, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($curl, CURLOPT_POST, true);
        curl_setopt($curl, CURLOPT_POSTFIELDS, $json);
        curl_setopt($curl, CURLOPT_HTTPHEADER, $api_headers);
        curl_exec($curl);
        if (curl_errno($curl)) echo curl_error($curl);
        $returnval = curl_multi_getcontent($curl);
        curl_close($curl);
        return $this->schema = json_decode($returnval);
    }
    
    
    public function checkField()
    {
        echo "CHECK START：". $this->model . PHP_EOL;
        foreach ( $this->schema->schema->fieldList as $field_info)
        {
            if($field_info->type == "mm_lookup"){
                break;
            }
            $check = false;
            foreach($this->model::$fillable as $setting_field_title)
            {
                if($field_info->title === $setting_field_title)
                {
                    $check = true;
                    break;
                }
            }
            if(!$check)
            {
                echo "setting is needed field : " .$field_info->title . PHP_EOL;
            }
        }
        
        foreach ( $this->model::$fillable as $setting_field_title )
        {
            $check = false;
            foreach($this->schema->schema->fieldList as $field_info)
            {
                if($field_info->title === $setting_field_title)
                {
                    $check = true;
                    break;
                }
            }
            if(!$check)
            {
                echo "setting is unneeded field : " .$setting_field_title . PHP_EOL;
            }
        }
        echo "CHECK END".PHP_EOL;
    }
}