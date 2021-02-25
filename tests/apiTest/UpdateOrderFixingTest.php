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
require_once ('src/NewJoyPla/api/UpdateOrderFixing.php');


class UpdateOrderFixingTest extends TestCase
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
    
        
    public function testUpdate()
    {
        $spiral = new \Spiral();
        
        $spiralApiCommunicator = $spiral->getSpiralApiCommunicator();
        
        //Success
        $spiralApiRequest = $this->createMock(\SpiralApiRequest::class);
        $spiralApiRequest->method('entrySet')->willReturn($this->returnSuccess);
        $spiralDataBase = new \App\Lib\SpiralDataBase($spiral,$spiralApiCommunicator,$spiralApiRequest);
        
        $updateOrderFixing = new \App\Api\UpdateOrderFixing($spiralDataBase);

        $array = array(
            'inHpItemsId' => array(
                'orderCNumber' => 'test00001',
                'price' => '10',
                'orderQuantity' => '1',//注文数
                'receivingFlag' => '1',//前回までの入庫数
                'receivingNowCount' => '1',//前回までの入庫数
                'receivingCount' => '10',
                'dueDate' => '2020年10月10日'
            )
        );
        $this->assertTrue($updateOrderFixing->update('orderNum','orderAuthKey',$array));

        
        //Error
        $spiralApiRequest = $this->createMock(\SpiralApiRequest::class);
        $spiralApiRequest->method('entrySet')->willReturn($this->returnError);
        $spiralDataBase = new \App\Lib\SpiralDataBase($spiral,$spiralApiCommunicator,$spiralApiRequest);
        
        $updateOrderFixing = new \App\Api\UpdateOrderFixing($spiralDataBase);

        $array = array(
            'inHpItemsId' => array(
                'orderCNumber' => 'test00001',
                'price' => '10',
                'orderQuantity' => '1',//注文数
                'receivingFlag' => '1',//前回までの入庫数
                'receivingNowCount' => '1',//前回までの入庫数
                'receivingCount' => '10',
                'dueDate' => '2020年10月10日'
            )
        );
        $this->assertFalse($updateOrderFixing->update('orderNum','orderAuthKey',$array));

    }

    
    public function testDelete()
    {
        $spiral = new \Spiral();
        
        $spiralApiCommunicator = $spiral->getSpiralApiCommunicator();
        
        //Success
        $spiralApiRequest = $this->createMock(\SpiralApiRequest::class);
        $spiralApiRequest->method('entrySet')->willReturn($this->returnSuccess);
        $spiralDataBase = new \App\Lib\SpiralDataBase($spiral,$spiralApiCommunicator,$spiralApiRequest);
        
        $updateOrderFixing = new \App\Api\UpdateOrderFixing($spiralDataBase);

        $array = array(
            'inHpItemsId' => array(
                'orderCNumber' => 'test00001',
                'price' => '10',
                'orderQuantity' => '1',//注文数
                'receivingFlag' => '1',//前回までの入庫数
                'receivingNowCount' => '1',//前回までの入庫数
                'receivingCount' => '10',
                'dueDate' => '2020年10月10日'
            )
        );
        $this->assertTrue($updateOrderFixing->delete('orderNum','orderAuthKey',$array));

        
        //Error
        $spiralApiRequest = $this->createMock(\SpiralApiRequest::class);
        $spiralApiRequest->method('entrySet')->willReturn($this->returnError);
        $spiralDataBase = new \App\Lib\SpiralDataBase($spiral,$spiralApiCommunicator,$spiralApiRequest);
        
        $updateOrderFixing = new \App\Api\UpdateOrderFixing($spiralDataBase);

        $array = array(
            'inHpItemsId' => array(
                'orderCNumber' => 'test00001',
                'price' => '10',
                'orderQuantity' => '1',//注文数
                'receivingFlag' => '1',//前回までの入庫数
                'receivingNowCount' => '1',//前回までの入庫数
                'receivingCount' => '10',
                'dueDate' => '2020年10月10日'
            )
        );
        $this->assertFalse($updateOrderFixing->delete('orderNum','orderAuthKey',$array));
    }
}