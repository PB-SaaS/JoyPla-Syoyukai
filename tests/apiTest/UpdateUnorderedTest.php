<?php 

namespace phpUnit\Test;

use PHPUnit\Framework\TestCase; 

require_once ('src/NewJoyPla/lib/ApiSpiral.php');
require_once ('src/NewJoyPla/lib/Define.php');
require_once ('src/NewJoyPla/lib/UserInfo.php');
require_once ('src/NewJoyPla/lib/SpiralDataBase.php');
require_once ('mock/Spiral.php');
require_once ('mock/MethodTester.php');
require_once ('mock/SpiralApiRequest.php');
require_once ('mock/PbSpiralApiCommunicator.php');
require_once ('src/NewJoyPla/api/UpdateUnordered.php');


class UpdateUnorderedTest extends TestCase
{
    private $Spiral;

    private $returnSuccess =  array(
        'data' => array(),
        'code' => '0',
        'message' => 'message',
        'count' => '1'
    );

    private $returnSuccess2 =  array(
        'data' => array(),
        'code' => '0',
        'message' => 'message',
        'count' => '0'
    );

    private $returnError =  array(
        'data' => array(),
        'code' => '1',
        'message' => 'message',
        'count' => '0'
        );
    
        
    public function testUpdate()
    {
        $spiral = new \Spiral();
        
        $spiralApiCommunicator = $spiral->getSpiralApiCommunicator();
        
        //Success
        $spiralApiRequest = $this->createMock(\SpiralApiRequest::class);
        $spiralApiRequest->method('entrySet')->willReturn($this->returnSuccess);
        $spiralDataBase = new \App\Lib\SpiralDataBase($spiral,$spiralApiCommunicator,$spiralApiRequest);
        
        $updateUnordered = new \App\Api\UpdateUnordered($spiralDataBase);

        $this->assertEquals(array("code"=>'0',"pattern"=>"update"),$updateUnordered->update('orderNum'));

        
        //Success
        $spiralApiRequest = $this->createMock(\SpiralApiRequest::class);
        $spiralApiRequest->method('entrySet')->willReturn($this->returnSuccess2);
        $spiralDataBase = new \App\Lib\SpiralDataBase($spiral,$spiralApiCommunicator,$spiralApiRequest);
        
        $updateUnordered = new \App\Api\UpdateUnordered($spiralDataBase);

        $this->assertEquals(array("code"=>'0',"pattern"=>"delete"),$updateUnordered->update('orderNum'));


        //Error
        $spiralApiRequest = $this->createMock(\SpiralApiRequest::class);
        $spiralApiRequest->method('entrySet')->willReturn($this->returnError);
        $spiralDataBase = new \App\Lib\SpiralDataBase($spiral,$spiralApiCommunicator,$spiralApiRequest);
        
        $updateUnordered = new \App\Api\UpdateUnordered($spiralDataBase);

        $this->assertEquals(array("code"=>'1',"pattern"=>"update"),$updateUnordered->update('orderNum'));

    }

    public function testOrderPriceCalculation(){
        $spiral = new \Spiral();
        
        $spiralApiCommunicator = $spiral->getSpiralApiCommunicator();
        
        //Success
        $spiralApiRequest = $this->createMock(\SpiralApiRequest::class);
        $spiralApiRequest->method('entrySet')->willReturn($this->returnSuccess);
        $spiralDataBase = new \App\Lib\SpiralDataBase($spiral,$spiralApiCommunicator,$spiralApiRequest);
        
        $updateUnordered = new \MethodTester(new \App\Api\UpdateUnordered($spiralDataBase));
        $argument = array(
            array(0,100),
            array(0,200),
            array(0,300),
            array(0,400),
        );
        $this->assertEquals(1000,$updateUnordered->orderPriceCalculation($argument));
    }

}