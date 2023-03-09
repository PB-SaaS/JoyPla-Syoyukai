<?php

namespace NewJoyPla\lib;

use HttpRequest;
use HttpRequestParameter;
use LoggingObject;

class Spiralv2LogginObject extends HttpRequest implements LoggingObject
{
    private string $baseUrl = 'https://api.spiral-platform.com/v1/';

    public int $logLevel = 0;

    public function __construct($apiKey, $appId, $dbId, $logLevel)
    {
        $this->url = $this->baseUrl . "apps/{$appId}/dbs/{$dbId}/records/";
        $this->httpHeader = [
            'Authorization:Bearer ' . $apiKey,
            'Content-Type:application/json',
            'X-Spiral-App-Authority' . 'manage',
            'X-Spiral-Api-Version: 1.1',
        ];
        $this->logLevel = $logLevel;
    }

    public function insert(array $data)
    {
        $param = new HttpRequestParameter();

        foreach ($data as $key => $v) {
            $param->set($key, $v);
        }

        $this->post($param);
    }

    public function bulkInsert(array $data)
    {
        $param = new HttpRequestParameter();

        foreach ($data as $key => $v) {
            $param->set($key, $v);
        }

        $this->post($param);
    }
}
