<?php

namespace phpUnit\Test;

use PHPUnit\Framework\TestCase;

require_once ('src/NewJoyPla/lib/ApiSpiral.php');
require_once ('src/NewJoyPla/lib/Define.php');
require_once ('mock/Spiral.php');
require_once ('mock/SpiralApiRequest.php');
require_once ('mock/PbSpiralApiCommunicator.php');
require_once ('src/NewJoyPla/lib/SpiralTable.php');

class SpiralTableTest extends TestCase
{
    private $Spiral;

    private $returnSuccess =  array(
        'data' => array(),
        'code' => '0',
        'message' => 'message',
        'count' => '1000'
    );

    private $returnError =  array(
        'code' => '1',
        'message' => 'error!'
        );

    public function testGetCardUrls()
    {
        $spiral = new \Spiral();
		$spiralApiCommunicator = $spiral->getSpiralApiCommunicator();
        //Success
        $spiralApiRequest = $this->createMock(\SpiralApiRequest::class);
        $spiralApiRequest->method('entrySet')->willReturn($this->returnSuccess);

        $spiralTable = new \App\Lib\SpiralTable($spiral,$spiralApiCommunicator,$spiralApiRequest);

        $this->assertEquals($this->returnSuccess, $spiralTable->getCardUrls());

        //Error
        $spiralApiRequest = $this->createMock(\SpiralApiRequest::class);
        $spiralApiRequest->method('entrySet')->willReturn($this->returnError);
        
        $spiralTable = new \App\Lib\SpiralTable($spiral,$spiralApiCommunicator,$spiralApiRequest);

        $this->assertEquals($this->returnError, $spiralTable->getCardUrls());
    }
}