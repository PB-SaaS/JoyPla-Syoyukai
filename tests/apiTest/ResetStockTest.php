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
require_once ('src/NewJoyPla/api/ResetStock.php');


class ResetStockTest extends TestCase
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
    
        
    public function testResetInHPItem()
    {
        $spiral = new \Spiral();
         
        $userInfo = new \App\Lib\UserInfo($spiral);
        $spiralApiCommunicator = $spiral->getSpiralApiCommunicator();
        
        //Success
        $spiralApiRequest = $this->createMock(\SpiralApiRequest::class);
        $spiralApiRequest->method('entrySet')->willReturn($this->returnSuccess);
        $spiralDataBase = new \App\Lib\SpiralDataBase($spiral,$spiralApiCommunicator,$spiralApiRequest);
        
        $resetStock = new \App\Api\ResetStock($spiralDataBase,$userInfo);

        $data = array(
            'inHpItemsId'=>array('countNum'=>'100'),
        );
        
        $this->assertTrue($resetStock->resetInHPItem($data));

        //Success
        $spiralApiRequest = $this->createMock(\SpiralApiRequest::class);
        $spiralApiRequest->method('entrySet')->willReturn($this->returnError);
        $spiralDataBase = new \App\Lib\SpiralDataBase($spiral,$spiralApiCommunicator,$spiralApiRequest);
        
        $resetStock = new \App\Api\ResetStock($spiralDataBase,$userInfo);

        $data = array(
            'inHpItemsId'=>array('countNum'=>'100'),
        );
        
        $this->assertFalse($resetStock->resetInHPItem($data));

    }

      
    public function testResetStock()
    {
        $spiral = new \Spiral();
         
        $userInfo = new \App\Lib\UserInfo($spiral);
        $spiralApiCommunicator = $spiral->getSpiralApiCommunicator();
        
        //Success
        $spiralApiRequest = $this->createMock(\SpiralApiRequest::class);
        $spiralApiRequest->method('entrySet')->willReturn($this->returnSuccess);
        $spiralDataBase = new \App\Lib\SpiralDataBase($spiral,$spiralApiCommunicator,$spiralApiRequest);
        
        $resetStock = new \App\Api\ResetStock($spiralDataBase,$userInfo);

        $this->assertTrue($resetStock->resetStock('divisionId'));

        //Success
        $spiralApiRequest = $this->createMock(\SpiralApiRequest::class);
        $spiralApiRequest->method('entrySet')->willReturn($this->returnError);
        $spiralDataBase = new \App\Lib\SpiralDataBase($spiral,$spiralApiCommunicator,$spiralApiRequest);
        
        $resetStock = new \App\Api\ResetStock($spiralDataBase,$userInfo);
        
        $this->assertFalse($resetStock->resetStock('divisionId'));

    }
    public function testGetStock()
    {
        $spiral = new \Spiral();
         
        $userInfo = new \App\Lib\UserInfo($spiral);
        $spiralApiCommunicator = $spiral->getSpiralApiCommunicator();
        
        //Success
        $spiralApiRequest = $this->createMock(\SpiralApiRequest::class);
        $spiralApiRequest->method('entrySet')->willReturn($this->returnSuccess);
        $spiralDataBase = new \App\Lib\SpiralDataBase($spiral,$spiralApiCommunicator,$spiralApiRequest);
        
        $resetStock = new \App\Api\ResetStock($spiralDataBase,$userInfo);

        $data = array(
            'divisionId1',
            'divisionId2',
            'divisionId3',
            'divisionId4',
            'divisionId5',
        );
        
        $this->assertEquals($this->returnSuccess,$resetStock->getStock($data));

        //Error
        $spiralApiRequest = $this->createMock(\SpiralApiRequest::class);
        $spiralApiRequest->method('entrySet')->willReturn($this->returnError);
        $spiralDataBase = new \App\Lib\SpiralDataBase($spiral,$spiralApiCommunicator,$spiralApiRequest);
        
        $resetStock = new \App\Api\ResetStock($spiralDataBase,$userInfo);
        
        $data = array(
            'divisionId1',
            'divisionId2',
            'divisionId3',
            'divisionId4',
            'divisionId5',
        );
        
        $this->assertEquals($this->returnError,$resetStock->getStock($data));

    }
}