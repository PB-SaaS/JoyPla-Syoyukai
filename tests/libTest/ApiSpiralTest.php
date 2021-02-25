<?php

namespace phpUnit\Test;

use PHPUnit\Framework\TestCase;

require_once ('src/NewJoyPla/lib/ApiSpiral.php');
require_once ('src/NewJoyPla/lib/Define.php');
require_once ('mock/Spiral.php');
require_once ('mock/SpiralApiRequest.php');
require_once ('mock/PbSpiralApiCommunicator.php');


class ApiSpiralTest extends TestCase
{
    private $SPIRAL;

    private $returnSuccess =  array(
        'data' => array(array("<>\"'")),
        'code' => '0',
        'message' => 'message',
        'count' => '1'
    );

    public function testRequestAPI()
    {
        $SPIRAL = new \Spiral();
		//$spiralApiCommunicator = $spiral->getSpiralApiCommunicator();
		//$spiralApiRequest = new \SpiralApiRequest();
        //$stub = $this->createMock(SomeClass::class);
		$SPIRAL->setApiTokenTitle(APITITLE); //APIタイトル
        $this->apiSpiral = new \App\Lib\ApiSpiral($SPIRAL);
		$this->apiSpiral->setApiCommunicator($SPIRAL->getSpiralApiCommunicator());
        $spiralApiRequest = $this->createMock(\SpiralApiRequest::class);
        $spiralApiRequest->method('entrySet')->willReturn($this->returnSuccess);
        $this->apiSpiral->setSpiralApiRequest($spiralApiRequest);
        
        var_dump($this->apiSpiral->requestAPI(array("database","select"),array()));
        $this->assertArrayHasKey('code', $this->apiSpiral->requestAPI(array("database","select"),array()));
        $this->assertArrayHasKey('message', $this->apiSpiral->requestAPI(array("database","select"),array()));
    }
}