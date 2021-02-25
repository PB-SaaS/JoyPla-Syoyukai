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
require_once ('src/NewJoyPla/api/GetInventoryHistoryId.php');


class GetInventoryHistoryIdTest extends TestCase
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
        
    public function testGetInventoryHistoryId()
    {
        $spiral = new \Spiral();
        
        $userInfo = new \App\Lib\UserInfo($spiral);
        $spiralApiCommunicator = $spiral->getSpiralApiCommunicator();
        
        //Success No Insert
        $spiralApiRequest = $this->createMock(\SpiralApiRequest::class);
        
        $response = $this->returnSuccess;
        $response['count'] = '1';
        $response['code'] = '0';
        $response['data'] = array(array('1','Id00001'));

        $spiralApiRequest->method('entrySet')->willReturn($response);
        $spiralDataBase = new \App\Lib\SpiralDataBase($spiral,$spiralApiCommunicator,$spiralApiRequest);
        
        $getInventoryHistoryId = new \App\Api\GetInventoryHistoryId($spiralDataBase,$userInfo);

        $this->assertEquals('Id00001', $getInventoryHistoryId->getInventoryHistoryId('divisionId','InventoryEId'));

        //Error No Insert
        $spiralApiRequest = $this->createMock(\SpiralApiRequest::class);

        $response = $this->returnSuccess;
        $response['count'] = '1';
        $response['code'] = '99';
        $response['data'] = array(array('1','Id00001'));

        $spiralApiRequest->method('entrySet')->willReturn($response);
        $spiralDataBase = new \App\Lib\SpiralDataBase($spiral,$spiralApiCommunicator,$spiralApiRequest);
        
        $getInventoryHistoryId = new \App\Api\GetInventoryHistoryId($spiralDataBase,$userInfo);

        $this->assertEquals('', $getInventoryHistoryId->getInventoryHistoryId('divisionId','InventoryEId'));

        
        //Success Insert
        $response = $this->returnSuccess;
        $response['count'] = '0';
        $response['code'] = '0';
        $response['data'] = array(array('1','Id00001'));

        $spiralApiRequest = $this->createMock(\SpiralApiRequest::class);
        $spiralApiRequest->method('entrySet')->willReturn($response);
        $spiralDataBase = new \App\Lib\SpiralDataBase($spiral,$spiralApiCommunicator,$spiralApiRequest);
        
        $getInventoryHistoryId = new \App\Api\GetInventoryHistoryId($spiralDataBase,$userInfo);
        
        $result = $getInventoryHistoryId->getInventoryHistoryId('divisionId','InventoryEId');
        $this->assertIsString($result);
        $this->assertEquals(18, strlen($result));
    }
}