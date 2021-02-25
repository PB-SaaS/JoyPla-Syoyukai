<?php

namespace phpUnit\Test;

use PHPUnit\Framework\TestCase;

require_once ('src/NewJoyPla/lib/ApiSpiral.php');
require_once ('src/NewJoyPla/lib/Define.php');
require_once ('src/NewJoyPla/lib/SpiralDataBase.php');
require_once ('mock/Spiral.php');
require_once ('mock/SpiralApiRequest.php');
require_once ('mock/PbSpiralApiCommunicator.php');
require_once ('src/NewJoyPla/api/DelInventory.php');


class DelInventoryTest extends TestCase
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
        
    public function testDeleteHistory()
    {
        $spiral = new \Spiral();
        $spiralApiCommunicator = $spiral->getSpiralApiCommunicator();
        
        //Success
        $spiralApiRequest = $this->createMock(\SpiralApiRequest::class);
        $spiralApiRequest->method('entrySet')->willReturn($this->returnSuccess);
        $spiralDataBase = new \App\Lib\SpiralDataBase($spiral,$spiralApiCommunicator,$spiralApiRequest);
        
        $delInventory = new \App\Api\DelInventory($spiralDataBase);

        $this->assertTrue($delInventory->deleteHistory('test','test'));

        //Error
        $spiralApiRequest = $this->createMock(\SpiralApiRequest::class);
        $spiralApiRequest->method('entrySet')->willReturn($this->returnError);
        $spiralDataBase = new \App\Lib\SpiralDataBase($spiral,$spiralApiCommunicator,$spiralApiRequest);
        
        $delInventory = new \App\Api\DelInventory($spiralDataBase);

        $this->assertFalse($delInventory->deleteHistory('test','test'));
    }
    public function testDeleteEndHistory()
    {
        $spiral = new \Spiral();
        $spiralApiCommunicator = $spiral->getSpiralApiCommunicator();
        
        //Success
        $spiralApiRequest = $this->createMock(\SpiralApiRequest::class);
        $spiralApiRequest->method('entrySet')->willReturn($this->returnSuccess);
        $spiralDataBase = new \App\Lib\SpiralDataBase($spiral,$spiralApiCommunicator,$spiralApiRequest);
        
        $delInventory = new \App\Api\DelInventory($spiralDataBase);

        $this->assertTrue($delInventory->deleteEndHistory('test','test'));

        //Error
        $spiralApiRequest = $this->createMock(\SpiralApiRequest::class);
        $spiralApiRequest->method('entrySet')->willReturn($this->returnError);
        $spiralDataBase = new \App\Lib\SpiralDataBase($spiral,$spiralApiCommunicator,$spiralApiRequest);
        
        $delInventory = new \App\Api\DelInventory($spiralDataBase);

        $this->assertFalse($delInventory->deleteEndHistory('test','test'));
    }
}