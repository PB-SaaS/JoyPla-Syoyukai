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
require_once ('src/NewJoyPla/api/RegReturn.php');


class RegReturnTest extends TestCase
{
    private $Spiral;

    private $returnSuccess =  array(
        'data' => array(),
        'code' => '0',
        'message' => 'message',
        'count' => '1'
    );

    private $returnError =  array(
        'data' => array(),
        'code' => '1',
        'message' => 'message',
        'count' => '0'
        );
    
        
    public function testRegister()
    {
        $spiral = new \Spiral();
         
        $userInfo = new \App\Lib\UserInfo($spiral);
        $spiralApiCommunicator = $spiral->getSpiralApiCommunicator();
        
        //Success
        $spiralApiRequest = $this->createMock(\SpiralApiRequest::class);
        $spiralApiRequest->method('entrySet')->willReturn($this->returnSuccess);
        $spiralDataBase = new \App\Lib\SpiralDataBase($spiral,$spiralApiCommunicator,$spiralApiRequest);
        
        $regReturn = new \App\Api\RegReturn($spiralDataBase,$userInfo);

        $array = array(
            'inHpItemsId' => array(
                'receivingNumber' => 'test00001',
                'orderCNumber' => 'test00001',
                'price' => '10',
                'receivingCount' => '10',
                'returnCount' => '1',
                'totalReturnCount' => '0'
            )
        );
        $this->assertTrue($regReturn->register('receivingHistoryId','distributorId',$array));

        
        //Success
        $spiralApiRequest = $this->createMock(\SpiralApiRequest::class);
        $spiralApiRequest->method('entrySet')->willReturn($this->returnSuccess);
        $spiralDataBase = new \App\Lib\SpiralDataBase($spiral,$spiralApiCommunicator,$spiralApiRequest);
        
        $regReturn = new \App\Api\RegReturn($spiralDataBase,$userInfo);

        $array = array();
        $this->assertTrue($regReturn->register('receivingHistoryId','distributorId',$array));

        //Error
        $spiralApiRequest = $this->createMock(\SpiralApiRequest::class);
        $spiralApiRequest->method('entrySet')->willReturn($this->returnError);
        $spiralDataBase = new \App\Lib\SpiralDataBase($spiral,$spiralApiCommunicator,$spiralApiRequest);
        
        $regReturn = new \App\Api\RegReturn($spiralDataBase,$userInfo);

        $array = array(
            'inHpItemsId' => array(
                'receivingNumber' => 'test00001',
                'orderCNumber' => 'test00001',
                'price' => '10',
                'receivingCount' => '10',
                'returnCount' => '1',
                'totalReturnCount' => '0'
            )
        );
        $this->assertFalse($regReturn->register('receivingHistoryId','distributorId',$array));
        
    }
}