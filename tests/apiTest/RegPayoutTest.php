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
require_once ('src/NewJoyPla/api/RegPayout.php');


class RegPayoutTest extends TestCase
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
        
        $regPayout = new \App\Api\RegPayout($spiralDataBase,$userInfo);

        $this->assertTrue($regPayout->register(
            array(
            'test01' => array(
                'countNum'=> 10,
                'kakaku' => 100,
                'irisu' => 10,
                'unit' => 'A',
                'itemUnit' => 'B',
                'payoutCount'=>'1',
                'countLabelNum'=>'3',
            )),  'sourceDivisionId','sourceDivisionName', 'targetDivisionId', 'targetDivisionName'));

            
        //Error count(array())== 0
        $spiralApiRequest = $this->createMock(\SpiralApiRequest::class);
        $spiralApiRequest->method('entrySet')->willReturn($this->returnSuccess);
        $spiralDataBase = new \App\Lib\SpiralDataBase($spiral,$spiralApiCommunicator,$spiralApiRequest);
        
        $regPayout = new \App\Api\RegPayout($spiralDataBase,$userInfo);

        $this->assertFalse($regPayout->register(array(),  'sourceDivisionId','sourceDivisionName', 'targetDivisionId', 'targetDivisionName'));
    
            
        //Error
        $spiralApiRequest = $this->createMock(\SpiralApiRequest::class);
        $spiralApiRequest->method('entrySet')->willReturn($this->returnError);
        $spiralDataBase = new \App\Lib\SpiralDataBase($spiral,$spiralApiCommunicator,$spiralApiRequest);
        
        $regPayout = new \App\Api\RegPayout($spiralDataBase,$userInfo);

        $this->assertFalse($regPayout->register(
            array(
            'test01' => array(
                'countNum'=> 10,
                'kakaku' => 100,
                'irisu' => 10,
                'unit' => 'A',
                'itemUnit' => 'B',
                'payoutCount'=>'1',
                'countLabelNum'=>'3',
            )),  'sourceDivisionId','sourceDivisionName', 'targetDivisionId', 'targetDivisionName'));
    }
}