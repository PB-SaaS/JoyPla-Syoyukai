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
require_once ('src/NewJoyPla/api/UpdateOrder.php');


class UpdateOrderTest extends TestCase
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
        
        $updateOrder = new \App\Api\UpdateOrder($spiralDataBase);

        $array = array(
            'inHpItemsId' => array(
                'orderCNumber' => 'test00001',
                'price' => '10',
                'orderQuantity' => '1',//注文数
                'receivingFlag' => '1',//前回までの入庫数
                'receivingNowCount' => '1',//前回までの入庫数
                'receivingCount' => '10'
            )
        );
        $this->assertTrue($updateOrder->update('orderNum','orderAuthKey',$array));

        
        //Error
        $spiralApiRequest = $this->createMock(\SpiralApiRequest::class);
        $spiralApiRequest->method('entrySet')->willReturn($this->returnError);
        $spiralDataBase = new \App\Lib\SpiralDataBase($spiral,$spiralApiCommunicator,$spiralApiRequest);
        
        $updateOrder = new \App\Api\UpdateOrder($spiralDataBase);

        $array = array(
            'inHpItemsId' => array(
                'orderCNumber' => 'test00001',
                'price' => '10',
                'orderQuantity' => '1',//注文数
                'receivingFlag' => '1',//前回までの入庫数
                'receivingNowCount' => '1',//前回までの入庫数
                'receivingCount' => '10'
            )
        );
        $this->assertFalse($updateOrder->update('orderNum','orderAuthKey',$array));

    }

    
    public function testUpdateWithDelAcceptance()
    {
        $spiral = new \Spiral();
        
        $spiralApiCommunicator = $spiral->getSpiralApiCommunicator();
        
        //Success
        $spiralApiRequest = $this->createMock(\SpiralApiRequest::class);
        $spiralApiRequest->method('entrySet')->willReturn($this->returnSuccess);
        $spiralDataBase = new \App\Lib\SpiralDataBase($spiral,$spiralApiCommunicator,$spiralApiRequest);
        
        $updateOrder = new \App\Api\UpdateOrder($spiralDataBase);

        $array = array(
            'inHpItemsId' => array(
                'orderCNumber' => 'test00001',
                'price' => '10',
                'orderQuantity' => '1',//注文数
                'receivingFlag' => '1',//前回までの入庫数
                'receivingNowCount' => '1',//前回までの入庫数
                'receivingCount' => '10'
            )
        );
        $this->assertTrue($updateOrder->updateWithDelAcceptance('orderNum','orderAuthKey',$array));

        
        //Error
        $spiralApiRequest = $this->createMock(\SpiralApiRequest::class);
        $spiralApiRequest->method('entrySet')->willReturn($this->returnError);
        $spiralDataBase = new \App\Lib\SpiralDataBase($spiral,$spiralApiCommunicator,$spiralApiRequest);
        
        $updateOrder = new \App\Api\UpdateOrder($spiralDataBase);

        $array = array(
            'inHpItemsId' => array(
                'orderCNumber' => 'test00001',
                'price' => '10',
                'orderQuantity' => '1',//注文数
                'receivingFlag' => '1',//前回までの入庫数
                'receivingNowCount' => '1',//前回までの入庫数
                'receivingCount' => '10'
            )
        );
        $this->assertFalse($updateOrder->updateWithDelAcceptance('orderNum','orderAuthKey',$array));
    }
}