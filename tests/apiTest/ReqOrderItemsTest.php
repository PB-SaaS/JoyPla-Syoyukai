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
require_once ('src/NewJoyPla/api/ReqOrderItems.php');


class ReqOrderItemsTest extends TestCase
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
    
        
    public function testBulkUpdate()
    {
        $spiral = new \Spiral();
         
        $userInfo = new \App\Lib\UserInfo($spiral);
        $spiralApiCommunicator = $spiral->getSpiralApiCommunicator();
        
        //Success
        $spiralApiRequest = $this->createMock(\SpiralApiRequest::class);
        $spiralApiRequest->method('entrySet')->willReturn($this->returnSuccess);
        $spiralDataBase = new \App\Lib\SpiralDataBase($spiral,$spiralApiCommunicator,$spiralApiRequest);
        
        $reqOrderItems = new \App\Api\ReqOrderItems($spiralDataBase,$userInfo);

        $updateData = array(
            array('orderCNumber','orderQuantity'),
            array('orderCNumber','orderQuantity'),
            array('orderCNumber','orderQuantity'),
            array('orderCNumber','orderQuantity'),
        );
        
        $this->assertEquals($this->returnSuccess, $reqOrderItems->bulkUpdate($updateData));

        
        //Success
        $spiralApiRequest = $this->createMock(\SpiralApiRequest::class);
        $spiralApiRequest->method('entrySet')->willReturn($this->returnSuccess);
        $spiralDataBase = new \App\Lib\SpiralDataBase($spiral,$spiralApiCommunicator,$spiralApiRequest);
        
        $reqOrderItems = new \App\Api\ReqOrderItems($spiralDataBase,$userInfo);

        $updateData = array(
        );
        
        $this->assertEquals(array('code' => '1', 'message' => 'no data'), $reqOrderItems->bulkUpdate($updateData));

        
        //Error
        $spiralApiRequest = $this->createMock(\SpiralApiRequest::class);
        $spiralApiRequest->method('entrySet')->willReturn($this->returnError);
        $spiralDataBase = new \App\Lib\SpiralDataBase($spiral,$spiralApiCommunicator,$spiralApiRequest);
        
        $reqOrderItems = new \App\Api\ReqOrderItems($spiralDataBase,$userInfo);


        $updateData = array(
            array('orderCNumber','orderQuantity'),
            array('orderCNumber','orderQuantity'),
            array('orderCNumber','orderQuantity'),
            array('orderCNumber','orderQuantity'),
        );
        
        $this->assertEquals($this->returnError, $reqOrderItems->bulkUpdate($updateData));

    }

    
    public function testDelete()
    {
        $spiral = new \Spiral();
         
        $userInfo = new \App\Lib\UserInfo($spiral);
        $spiralApiCommunicator = $spiral->getSpiralApiCommunicator();
        
        //Success
        $spiralApiRequest = $this->createMock(\SpiralApiRequest::class);
        $spiralApiRequest->method('entrySet')->willReturn($this->returnSuccess);
        $spiralDataBase = new \App\Lib\SpiralDataBase($spiral,$spiralApiCommunicator,$spiralApiRequest);
        
        $reqOrderItems = new \App\Api\ReqOrderItems($spiralDataBase,$userInfo);

        $orderCNumber = array(
            'orderCNumber1',
            'orderCNumber2',
            'orderCNumber3',
            'orderCNumber4',
        );
        
        $this->assertEquals($this->returnSuccess, $reqOrderItems->delete($orderCNumber));

        
        //Success
        $spiralApiRequest = $this->createMock(\SpiralApiRequest::class);
        $spiralApiRequest->method('entrySet')->willReturn($this->returnSuccess);
        $spiralDataBase = new \App\Lib\SpiralDataBase($spiral,$spiralApiCommunicator,$spiralApiRequest);
        
        $reqOrderItems = new \App\Api\ReqOrderItems($spiralDataBase,$userInfo);

        $orderCNumber = array(
        );
        
        $this->assertEquals(array('code' => '1', 'message' => 'no data'), $reqOrderItems->delete($orderCNumber));

        
        //Error
        $spiralApiRequest = $this->createMock(\SpiralApiRequest::class);
        $spiralApiRequest->method('entrySet')->willReturn($this->returnError);
        $spiralDataBase = new \App\Lib\SpiralDataBase($spiral,$spiralApiCommunicator,$spiralApiRequest);
        
        $reqOrderItems = new \App\Api\ReqOrderItems($spiralDataBase,$userInfo);

        $orderCNumber = array(
            'orderCNumber1',
            'orderCNumber2',
            'orderCNumber3',
            'orderCNumber4',
        );
        
        $this->assertEquals($this->returnError, $reqOrderItems->delete($orderCNumber));

    }
}