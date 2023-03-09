<?php

namespace framework\SpiralConnecter;

use Exception;
use HttpRequest;
use HttpRequestParameter;
use Logger;

class SpiralApiConnecter implements SpiralConnecterInterface
{
    private string $location = '';
    private string $token = '';
    private string $secret = '';
    public static ?Logger $logger = null;

    public function __construct(string $token = '', string $secret = '')
    {
        $this->token = $token;
        $this->secret = $secret;

        $request = new HttpRequest();
        $request->setHeader([
            'X-SPIRAL-API: locator/apiserver/request',
            'Content-Type: application/json; charset=UTF-8',
        ]);

        $httpRequestParameter = new HttpRequestParameter();
        $httpRequestParameter->set('spiral_api_token', $token);

        $request->setUrl('https://www.pi-pe.co.jp/api/locator');
        $response = $request->post($httpRequestParameter);

        if ($response->code != 0) {
            throw new Exception($response->message, $response->code);
        }
        $this->location = $response->location;
    }

    public function request(
        XSpiralApiHeaderObject $header,
        HttpRequestParameter $httpRequestParameter
    ) {
        $request = new HttpRequest();

        $request->setHeader([
            "X-SPIRAL-API: $header",
            'Content-Type: application/json; charset=UTF-8',
        ]);

        $passkey = time();

        $httpRequestParameter->set('spiral_api_token', $this->token);
        $httpRequestParameter->set('passkey', $passkey);

        $key = $this->token . '&' . $passkey;
        $httpRequestParameter->set(
            'signature',
            hash_hmac('sha1', $key, $this->secret, false)
        );

        $request->setUrl($this->location);

        $response = $request->post($httpRequestParameter);

        $logs = [];
        $logs[] = $this->logObject($header, $httpRequestParameter, $response);
        $this->logging($logs);
        if ($response->code != 0) {
            throw new Exception($response->message, $response->code);
        }

        return json_decode(json_encode($response), true);
    }

    public function bulkRequest(
        XSpiralApiHeaderObject $header,
        array $httpRequestParameters
    ) {
        $request = new HttpRequest();

        $request->setHeader([
            "X-SPIRAL-API: $header",
            'Content-Type: application/json; charset=UTF-8',
        ]);

        $passkey = time();
        foreach ($httpRequestParameters as &$httpRequestParameter) {
            $httpRequestParameter->set('spiral_api_token', $this->token);
            $httpRequestParameter->set('passkey', $passkey);

            $key = $this->token . '&' . $passkey;
            $httpRequestParameter->set(
                'signature',
                hash_hmac('sha1', $key, $this->secret, false)
            );
        }

        $request->setUrl($this->location);

        $responses = $request->postBulk($httpRequestParameters);

        $result = [];
        $logs = [];

        foreach ($responses as $key => $response) {
            if ($response->code != 0) {
                throw new Exception($response->message, $response->code);
            }

            $logs[] = $this->logObject(
                $header,
                $httpRequestParameters[$key],
                $response
            );

            $result = array_merge($result, $response->data);
        }

        $this->logging($logs);

        return $result;
    }

    private function logObject(
        $header,
        HttpRequestParameter $request,
        $response
    ) {
        return [
            'execTime' => Logger::getTime(),
            'AccountId' => 'localhost',
            'request' => $request->get('db_title'),
            'header' => $header->__toString(),
            'code' => $response->code,
            'message' => $response->message,
        ];
    }

    public function logging($bodys)
    {
        if ($this::$logger) {
            $this::$logger->outBulk($bodys);
        }
    }
}
