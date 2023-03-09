<?php

namespace framework\SpiralConnecter;

use Exception;
use HttpRequest;
use HttpRequestParameter;
use Logger;
use Spiral;
use SpiralApiRequest;

class SpiralConnecter implements SpiralConnecterInterface
{
    private $apiCommunicator;

    public static ?Logger $logger = null;

    public function __construct(Spiral $spiral)
    {
        $this->apiCommunicator = $spiral->getSpiralApiCommunicator();
    }

    public function request(
        XSpiralApiHeaderObject $header,
        HttpRequestParameter $httpRequestParameter
    ) {
        $request = new SpiralApiRequest();
        foreach ($httpRequestParameter->toArray() as $key => $val) {
            $request->put($key, $val);
        }
        $response = $this->apiCommunicator->request(
            $header->func(),
            $header->method(),
            $request
        );

        if ($response->get('code') != 0) {
            throw new Exception(
                $response->get('message'),
                $response->get('code')
            );
        }

        $logs = [];
        $logs[] = $this->logObject($header, $httpRequestParameter, $response);
        $this->logging($logs);

        return $response->entrySet();
    }

    public function bulkRequest(
        XSpiralApiHeaderObject $header,
        array $httpRequestParameters
    ) {
        $result = [];
        $log = [];
        foreach ($httpRequestParameters as $key => $httpRequestParameter) {
            if ($httpRequestParameter instanceof HttpRequestParameter) {
                $res = $this->request($header, $httpRequestParameter);
                $logs[] = $this->logObject(
                    $header,
                    $httpRequestParameters[$key],
                    $res
                );

                array_merge($result, $res);
            }
        }

        $this->logging($logs);

        return $result;
    }

    private function logObject(
        $header,
        HttpRequestParameter $request,
        $response
    ) {
        global $SPIRAL;
        return [
            'execTime' => Logger::getTime(),
            'AccountId' => $SPIRAL->getAccountId(),
            'request' => $request->get('db_title'),
            'header' => $header->__toString(),
            'code' => $response->getResultCode(),
            'message' => $response->getMessage(),
        ];
    }

    public function logging($bodys)
    {
        if ($this::$logger) {
            $this::$logger->outBulk($bodys);
        }
    }
}
