<?php

namespace phpUnit\Test;

use PHPUnit\Framework\TestCase;

require_once ('src/NewJoyPla/lib/ApiSpiral.php');
require_once ('src/NewJoyPla/lib/Define.php');
require_once ('src/NewJoyPla/lib/UserInfo.php');
require_once ('src/NewJoyPla/lib/SpiralDataBase.php');
require_once ('mock/Spiral.php');
require_once ('mock/SpiralApiRequest.php');
require_once ('mock/PbSpiralApiCommunicator.php');
require_once ('src/NewJoyPla/api/GetInventoryEndHistoryId.php');


class GetInventoryEndHistoryIdTest extends TestCase
{
    private $Spiral;

    private $returnSuccess =  array(
        'data' => array(),
        'code' => '0',
        'message' => 'message',
        'count' => '1'
    );

    private $returnError =  array(
        'code' => '1',
        'message' => 'error!'
        );
        
    public function testGetInventoryEndHistoryId()
    {
        $spiral = new \Spiral();
        
        $userInfo = new \App\Lib\UserInfo($spiral);
        $spiralApiCommunicator = $spiral->getSpiralApiCommunicator();
        
        //Success No Insert
        $response = $this->returnSuccess;
        $response['count'] = '1';
        $response['code'] = '0';
        $response['data'] = array(array('1','EndId00001'));

        $spiralApiRequest = $this->createMock(\SpiralApiRequest::class);
        $spiralApiRequest->method('entrySet')->willReturn($response);
        $spiralDataBase = new \App\Lib\SpiralDataBase($spiral,$spiralApiCommunicator,$spiralApiRequest);
        
        $getInventoryEndHistoryId = new \App\Api\GetInventoryEndHistoryId($spiralDataBase,$userInfo);


        $this->assertEquals('EndId00001', $getInventoryEndHistoryId->getInventoryEndHistoryId());

        //Error No Insert
        $response = $this->returnSuccess;
        $response['count'] = '1';
        $response['code'] = '99';
        $response['data'] = array(array('1','EndId00001'));

        $spiralApiRequest = $this->createMock(\SpiralApiRequest::class);
        $spiralApiRequest->method('entrySet')->willReturn($response);
        $spiralDataBase = new \App\Lib\SpiralDataBase($spiral,$spiralApiCommunicator,$spiralApiRequest);
        
        $getInventoryEndHistoryId = new \App\Api\GetInventoryEndHistoryId($spiralDataBase,$userInfo);

        $this->assertEquals('', $getInventoryEndHistoryId->getInventoryEndHistoryId());

        
        //Success Insert
        $response = $this->returnSuccess;
        $response['count'] = '0';
        $response['code'] = '0';
        $response['data'] = array(array('1','EndId00001'));

        $spiralApiRequest = $this->createMock(\SpiralApiRequest::class);
        $spiralApiRequest->method('entrySet')->willReturn($response);
        $spiralDataBase = new \App\Lib\SpiralDataBase($spiral,$spiralApiCommunicator,$spiralApiRequest);
        
        $getInventoryEndHistoryId = new \App\Api\GetInventoryEndHistoryId($spiralDataBase,$userInfo);
        
        $result = $getInventoryEndHistoryId->getInventoryEndHistoryId();
        $this->assertIsString($result);
        $this->assertEquals(18, strlen($result));
    }
}