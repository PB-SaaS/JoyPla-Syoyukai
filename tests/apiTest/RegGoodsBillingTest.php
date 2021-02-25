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
require_once ('src/NewJoyPla/api/RegGoodsBilling.php');


class RegGoodsBillingTest extends TestCase
{
    private $Spiral;

    private $returnSuccess =  array(
        'data' => array(
        ),
        'code' => '0',
        'message' => 'message',
        'count' => '1'
    );

    private $returnError =  array(
        'data' => array(),
        'code' => '0',
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
        
        $regGoodsBilling = new \App\Api\RegGoodsBilling($spiralDataBase,$userInfo);
        $this->assertTrue($regGoodsBilling->register(
            array(
            'test01' => array(
                'countNum'=> 10,
                'kakaku' => 100,
                'irisu' => 10,
                'unit' => 'A',
                'itemUnit' => 'B',
            )),'divisionId'));

        //Error
        $spiralApiRequest = $this->createMock(\SpiralApiRequest::class);
        $spiralApiRequest->method('entrySet')->willReturn($this->returnError);
        $spiralDataBase = new \App\Lib\SpiralDataBase($spiral,$spiralApiCommunicator,$spiralApiRequest);
        
        $regGoodsBilling = new \App\Api\RegGoodsBilling($spiralDataBase,$userInfo);
        $this->assertFalse($regGoodsBilling->register(array(),'divisionId'));
    }
}