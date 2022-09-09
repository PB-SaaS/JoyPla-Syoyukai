<?php

namespace framework\SpiralConnecter;

use HttpRequest;
use HttpRequestParameter;
use Spiral;
use SpiralApiRequest;

class SpiralConnecter implements SpiralConnecterInterface  {

    private $apiCommunicator;

    public function __construct(Spiral $spiral)
    {
        $this->apiCommunicator = $spiral->getSpiralApiCommunicator();
    }

    public function request(XSpiralApiHeaderObject $header, HttpRequestParameter $httpRequestParameter)
    {
        $request = new SpiralApiRequest();
        foreach( $httpRequestParameter->toArray() as $key => $val )
        {
            $request->put($key , $val);
        }
        $response = $this->apiCommunicator->request($header->func(), $header->method() , $request);
		return $response->entrySet();
    }
}