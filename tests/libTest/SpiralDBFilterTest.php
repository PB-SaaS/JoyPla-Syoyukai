<?php

namespace phpUnit\Test;

use PHPUnit\Framework\TestCase;

require_once ('src/NewJoyPla/lib/ApiSpiral.php');
require_once ('src/NewJoyPla/lib/Define.php');
require_once ('mock/Spiral.php');
require_once ('mock/SpiralApiRequest.php');
require_once ('mock/PbSpiralApiCommunicator.php');
require_once ('src/NewJoyPla/lib/SpiralDBFilter.php');


class SpiralDBFilterTest extends TestCase
{
    private $spiral;

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

    public function testCreate()
    {
        $spiral = new \Spiral();
		$spiralApiCommunicator = $spiral->getSpiralApiCommunicator();
        //Success
        $spiralApiRequest = $this->createMock(\SpiralApiRequest::class);
        $spiralApiRequest->method('entrySet')->willReturn($this->returnSuccess);
        
        $SpiralDBFilter = new \App\Lib\SpiralDBFilter($spiral,$spiralApiCommunicator,$spiralApiRequest);
        $this->assertEquals($this->returnSuccess, $SpiralDBFilter->create());

        //Error
        $spiralApiRequest = $this->createMock(\SpiralApiRequest::class);
        $spiralApiRequest->method('entrySet')->willReturn($this->returnError);
        
        $SpiralDBFilter = new \App\Lib\SpiralDBFilter($spiral,$spiralApiCommunicator,$spiralApiRequest);

        $this->assertEquals($this->returnError, $SpiralDBFilter->create());
    }
    public function testList()
    {
        $spiral = new \Spiral();
		$spiralApiCommunicator = $spiral->getSpiralApiCommunicator();
        //Success
        $spiralApiRequest = $this->createMock(\SpiralApiRequest::class);
        $spiralApiRequest->method('entrySet')->willReturn($this->returnSuccess);
        $SpiralDBFilter = new \App\Lib\SpiralDBFilter($spiral,$spiralApiCommunicator,$spiralApiRequest);

        $this->assertEquals($this->returnSuccess, $SpiralDBFilter->list());

        //Error
        $spiralApiRequest = $this->createMock(\SpiralApiRequest::class);
        $spiralApiRequest->method('entrySet')->willReturn($this->returnError);
        
        $SpiralDBFilter = new \App\Lib\SpiralDBFilter($spiral,$spiralApiCommunicator,$spiralApiRequest);

        $this->assertEquals($this->returnError, $SpiralDBFilter->list());
    }
}