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
require_once ('src/NewJoyPla/api/RegUnordered.php');


class RegUnorderedTest extends TestCase
{
    private $Spiral;

    private $returnSuccess =  array(
        'data' => array(
            array('distributorName','distributorId'),
            array('distributorName2','distributorId2'),
            array('distributorName3','distributorId3'),
            array('distributorName4','distributorId4'),
        ),
        'code' => '0',
        'message' => 'message',
        'count' => '1'
    );

    
    private $returnSuccess2 =  array(
        'data' => array(
        //    array('distributorName','distributorId'),
        //    array('distributorName2','distributorId2'),
        //    array('distributorName3','distributorId3'),
        //    array('distributorName4','distributorId4'),
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
        
        //Success Request Data array()
        $spiralApiRequest = $this->createMock(\SpiralApiRequest::class);
        $spiralApiRequest->method('entrySet')->willReturn($this->returnSuccess);
        $spiralDataBase = new \App\Lib\SpiralDataBase($spiral,$spiralApiCommunicator,$spiralApiRequest);
        
        $regUnordered = new \App\Api\RegUnordered($spiralDataBase,$userInfo);

        $this->assertTrue($regUnordered->register(array(),'divisionId'));

        
        //Success No distributor Data
        $spiralApiRequest = $this->createMock(\SpiralApiRequest::class);
        $spiralApiRequest->method('entrySet')->willReturn($this->returnSuccess2);
        $spiralDataBase = new \App\Lib\SpiralDataBase($spiral,$spiralApiCommunicator,$spiralApiRequest);
        
        $regUnordered = new \App\Api\RegUnordered($spiralDataBase,$userInfo);

        $array = array(
            'inHpItemsId' => array(
                "maker" => 'maker',
                "shouhinName" => 'shouhinName',
                "code" => 'code',
                "kikaku" => 'kikaku',
                "irisu" => '10',
                "kakaku" => '100',
                "jan" => 'jan',
                "oroshi" => 'oroshi',
                "recordId" => 'recordId',
                "unit" => 'unit',
                "itemUnit" => 'itemUnit',
                "countNum" => '100',
                "distributorId" => 'distributorId',
                "count" => 123,//ラベルの入数 0123
            )
        );
        $this->assertTrue($regUnordered->register($array,'divisionId'));


        
        //Success
        $spiralApiRequest = $this->createMock(\SpiralApiRequest::class);
        $spiralApiRequest->method('entrySet')->willReturn($this->returnSuccess);
        $spiralDataBase = new \App\Lib\SpiralDataBase($spiral,$spiralApiCommunicator,$spiralApiRequest);
        
        $regUnordered = new \App\Api\RegUnordered($spiralDataBase,$userInfo);

        $array = array(
            'inHpItemsId' => array(
                "maker" => 'maker',
                "shouhinName" => 'shouhinName',
                "code" => 'code',
                "kikaku" => 'kikaku',
                "irisu" => '10',
                "kakaku" => '100',
                "jan" => 'jan',
                "oroshi" => 'oroshi',
                "recordId" => 'recordId',
                "unit" => 'unit',
                "itemUnit" => 'itemUnit',
                "countNum" => '100',
                "distributorId" => 'distributorId',
                "count" => 123,//ラベルの入数 0123
            )
        );
        $this->assertTrue($regUnordered->register($array,'divisionId'));

        
        //Error 
        $spiralApiRequest = $this->createMock(\SpiralApiRequest::class);
        $spiralApiRequest->method('entrySet')->willReturn($this->returnError);
        $spiralDataBase = new \App\Lib\SpiralDataBase($spiral,$spiralApiCommunicator,$spiralApiRequest);
        
        $regUnordered = new \App\Api\RegUnordered($spiralDataBase,$userInfo);

        $array = array(
            'inHpItemsId' => array(
                "maker" => 'maker',
                "shouhinName" => 'shouhinName',
                "code" => 'code',
                "kikaku" => 'kikaku',
                "irisu" => '10',
                "kakaku" => '100',
                "jan" => 'jan',
                "oroshi" => 'oroshi',
                "recordId" => 'recordId',
                "unit" => 'unit',
                "itemUnit" => 'itemUnit',
                "countNum" => '100',
                "distributorId" => 'distributorId',
                "count" => 123,//ラベルの入数 0123
            )
        );
        $this->assertFalse($regUnordered->register($array,'divisionId'));
    }
}