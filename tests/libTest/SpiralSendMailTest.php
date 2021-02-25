<?php

namespace phpUnit\Test;

use PHPUnit\Framework\TestCase;

require_once ('src/NewJoyPla/lib/ApiSpiral.php');
require_once ('src/NewJoyPla/lib/Define.php');
require_once ('mock/Spiral.php');
require_once ('mock/SpiralApiRequest.php');
require_once ('mock/PbSpiralApiCommunicator.php');
require_once ('src/NewJoyPla/lib/SpiralSendMail.php');


class SpiralSendMailTest extends TestCase
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

    public function testRegist()
    {
        $spiral = new \Spiral();
        $spiralApiCommunicator = $spiral->getSpiralApiCommunicator();
        
        //Success
        $spiralApiRequest = $this->createMock(\SpiralApiRequest::class);
        $spiralApiRequest->method('entrySet')->willReturn($this->returnSuccess);

        $SpiralSendMail = new \App\Lib\SpiralSendMail($spiral,$spiralApiCommunicator,$spiralApiRequest);

        $this->assertEquals($this->returnSuccess, $SpiralSendMail->regist());

        //Error
        $spiralApiRequest = $this->createMock(\SpiralApiRequest::class);
        $spiralApiRequest->method('entrySet')->willReturn($this->returnError);
        
        $SpiralSendMail = new \App\Lib\SpiralSendMail($spiral,$spiralApiCommunicator,$spiralApiRequest);

        $this->assertEquals($this->returnError, $SpiralSendMail->regist());

    }
    
    public function testList()
    {
        $spiral = new \Spiral();
        $spiralApiCommunicator = $spiral->getSpiralApiCommunicator();
        
        //Success
        $spiralApiRequest = $this->createMock(\SpiralApiRequest::class);
        $spiralApiRequest->method('entrySet')->willReturn($this->returnSuccess);

        $SpiralSendMail = new \App\Lib\SpiralSendMail($spiral,$spiralApiCommunicator,$spiralApiRequest);

        $this->assertEquals($this->returnSuccess, $SpiralSendMail->list());
        
        //Error
        $spiralApiRequest = $this->createMock(\SpiralApiRequest::class);
        $spiralApiRequest->method('entrySet')->willReturn($this->returnError);

        $SpiralSendMail = new \App\Lib\SpiralSendMail($spiral,$spiralApiCommunicator,$spiralApiRequest);

        $this->assertEquals($this->returnError, $SpiralSendMail->list());
    }
    public function testCancel()
    {
        $spiral = new \Spiral();
        $spiralApiCommunicator = $spiral->getSpiralApiCommunicator();
        
        //Success
        $spiralApiRequest = $this->createMock(\SpiralApiRequest::class);
        $spiralApiRequest->method('entrySet')->willReturn($this->returnSuccess);

        $SpiralSendMail = new \App\Lib\SpiralSendMail($spiral,$spiralApiCommunicator,$spiralApiRequest);

        $this->assertEquals($this->returnSuccess, $SpiralSendMail->cancel());
        
        //Error
        $spiralApiRequest = $this->createMock(\SpiralApiRequest::class);
        $spiralApiRequest->method('entrySet')->willReturn($this->returnError);

        $SpiralSendMail = new \App\Lib\SpiralSendMail($spiral,$spiralApiCommunicator,$spiralApiRequest);

        $this->assertEquals($this->returnError, $SpiralSendMail->cancel());
    }
    public function testThanks()
    {
        $spiral = new \Spiral();
        $spiralApiCommunicator = $spiral->getSpiralApiCommunicator();
        
        //Success
        $spiralApiRequest = $this->createMock(\SpiralApiRequest::class);
        $spiralApiRequest->method('entrySet')->willReturn($this->returnSuccess);

        $SpiralSendMail = new \App\Lib\SpiralSendMail($spiral,$spiralApiCommunicator,$spiralApiRequest);

        $this->assertEquals($this->returnSuccess, $SpiralSendMail->thanks('0001','1'));

        
        //Error
        $spiralApiRequest = $this->createMock(\SpiralApiRequest::class);
        $spiralApiRequest->method('entrySet')->willReturn($this->returnError);
        
        $SpiralSendMail = new \App\Lib\SpiralSendMail($spiral,$spiralApiCommunicator,$spiralApiRequest);

        $this->assertEquals($this->returnError, $SpiralSendMail->thanks('0001','1'));

    }
    
}