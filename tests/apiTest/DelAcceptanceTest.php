<?php

namespace phpUnit\Test;

use PHPUnit\Framework\TestCase;

require_once ('src/NewJoyPla/lib/ApiSpiral.php');
require_once ('src/NewJoyPla/lib/Define.php');
require_once ('src/NewJoyPla/lib/SpiralDataBase.php');
require_once ('mock/Spiral.php');
require_once ('mock/SpiralApiRequest.php');
require_once ('mock/PbSpiralApiCommunicator.php');
require_once ('src/NewJoyPla/api/DelAcceptance.php');


class DelAcceptanceTest extends TestCase
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

    public function testDelete()
    {
        $spiral = new \Spiral();
        $spiralApiCommunicator = $spiral->getSpiralApiCommunicator();
        
        //Success
        $spiralApiRequest = $this->createMock(\SpiralApiRequest::class);
        $spiralApiRequest->method('entrySet')->willReturn($this->returnSuccess);
        $spiralDataBase = new \App\Lib\SpiralDataBase($spiral,$spiralApiCommunicator,$spiralApiRequest);
        
        $delAcceptance = new \App\Api\DelAcceptance($spiralDataBase);

        $this->assertTrue($delAcceptance->delete('test','test'));

        //Error
        $spiralApiRequest = $this->createMock(\SpiralApiRequest::class);
        $spiralApiRequest->method('entrySet')->willReturn($this->returnError);
        $spiralDataBase = new \App\Lib\SpiralDataBase($spiral,$spiralApiCommunicator,$spiralApiRequest);
        
        $delAcceptance = new \App\Api\DelAcceptance($spiralDataBase);

        $this->assertFalse($delAcceptance->delete('test','test'));
    }
}