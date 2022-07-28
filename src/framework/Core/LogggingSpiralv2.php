<?php 
namespace App\Lib;

use Exception;
use LoggingObject;

class LogggingSpiralv2 implements LoggingObject
{
    private string $baseUrl;
    private string $apiKey;
    private string $dbId;
    private string $appId;

    public function __construct($apiKey , $baseUrl) 
    {
        $this->apiKey = $apiKey;
        $this->baseUrl = $baseUrl;
    }

    public function setDbId($dbId)
    {
        $this->dbId = $dbId;
    }

    public function setAppId($appId)
    {
        $this->appId = $appId;
    }

    public function setBaseUrl($baseUrl)
    {
        $this->baseUrl = $baseUrl;
    }

    public function getDbId()
    {
        if($this->dbId == '' || $this->dbId == null)
        {
            throw new Exception('dbId is Null.' , 422);
        }

        return $this->dbId;
    }

    public function getAppId()
    {
        if($this->appId == '' || $this->appId == null)
        {
            throw new Exception('appId is Null.' , 422);
        }

        return $this->appId;
        
    }
    public function insert($data)
    {
        $url = $this->baseUrl."apps/{$this->getAppId()}/dbs/{$this->getDbId()}/records/";
        $method = 'POST';
        ($this->request($data , $url , $method));
    }

    public function request(array $body , string $apiUrl , $method)
    {
        $json_body = json_encode($body);
        $curl = curl_init();
        curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($curl, CURLOPT_URL , $apiUrl);
        curl_setopt($curl, CURLOPT_HTTPHEADER , [
                    "Authorization:Bearer ".$this->apiKey,
                    "Content-Type:application/json",
                    "X-Spiral-App-Authority"."manage",
                    "X-Spiral-Api-Version: 1.1"
        ]);
        curl_setopt($curl, CURLOPT_SSL_VERIFYPEER , false); 
        curl_setopt($curl, CURLOPT_POSTFIELDS , $json_body);
        curl_setopt($curl, CURLOPT_CUSTOMREQUEST , $method);
        $response = curl_exec($curl);
    }

}