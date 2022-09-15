<?php 
namespace JoyPla\Application\LoggingObject;

use Exception;
use HttpRequest;
use HttpRequestParameter;
use LoggingObject;

class Spiralv2LogginObject extends HttpRequest implements LoggingObject
{
    private string $baseUrl = "https://api.spiral-platform.com/v1/";
    private string $appId = "";
    private string $dbId = "";

    public int $logLevel = 3;

    public function __construct($apiKey , $appId , $dbId)
    {
        $this->appId = $appId;
        $this->dbId = $dbId;
        $this->url = $this->baseUrl."apps/{$this->appId}/dbs/{$this->dbId}/records/";
        $this->httpHeader = [
            "Authorization:Bearer ".$apiKey,
            "Content-Type:application/json",
            "X-Spiral-App-Authority"."manage",
            "X-Spiral-Api-Version: 1.1"
        ];
    }

    public function insert(array $data)
    {
        $param = new HttpRequestParameter();

        foreach($data as $key => $v)
        {
            $param->set($key , $v);
        }

        $this->post($param);
    } 

    
    public function bulkInsert(array $data)
    {//https://api.spiral-platform.com/v1/apps/{app}/dbs/{db}/records/bulk
        $this->url = $this->baseUrl."apps/{$this->appId}/dbs/{$this->dbId}/records/bulk";
        $param = new HttpRequestParameter();
        $param->set('records',$data);
        $this->post($param);
    } 
}