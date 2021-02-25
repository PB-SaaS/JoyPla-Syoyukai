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
require_once ('src/NewJoyPla/api/LabelSearchBarcode.php');


class LabelSearchBarcodeTest extends TestCase
{
    private $Spiral;

    private $returnSuccess =  array(
        'data' => array(
            array(
                "maker",
                "shouhinName",
                "code",
                "kikaku",
                "irisu",
                "kakaku",
                "jan",
                "oroshi",
                "recordId",
                "unit",
                "itemUnit",
                "distributorId",
                "1",//ラベルの入数
            )
        ),
        'code' => '0',
        'message' => 'message',
        'count' => '1'
    );

    private $returnError =  array(
        'code' => '1',
        'message' => 'error!'
        );
    
        
    public function testSearch()
    {
        $spiral = new \Spiral();
         
        $userInfo = new \App\Lib\UserInfo($spiral);
        $spiralApiCommunicator = $spiral->getSpiralApiCommunicator();
        
        //Success
        $spiralApiRequest = $this->createMock(\SpiralApiRequest::class);
        $spiralApiRequest->method('entrySet')->willReturn($this->returnSuccess);
        $spiralDataBase = new \App\Lib\SpiralDataBase($spiral,$spiralApiCommunicator,$spiralApiRequest);
        
        $labelSearchBarcode = new \App\Api\LabelSearchBarcode($spiralDataBase,$userInfo);
        $result = $this->returnSuccess;
        $result['data'] = 
            array(
                "maker" => 'maker',
                "shouhinName" => 'shouhinName',
                "code" => 'code',
                "kikaku" => 'kikaku',
                "irisu" => 'irisu',
                "kakaku" => 'kakaku',
                "jan" => 'jan',
                "oroshi" => 'oroshi',
                "recordId" => 'recordId',
                "unit" => 'unit',
                "itemUnit" => 'itemUnit',
                "distributorId" => 'distributorId',
                "count" => 123,//ラベルの入数 0123
        );

        $this->assertEquals($result, $labelSearchBarcode->search('11234567890123'));
        $this->assertEquals($result, $labelSearchBarcode->search('01123456780123'));

        
        //Error
        $spiralApiRequest = $this->createMock(\SpiralApiRequest::class);
        $spiralApiRequest->method('entrySet')->willReturn($this->returnError);
        $spiralDataBase = new \App\Lib\SpiralDataBase($spiral,$spiralApiCommunicator,$spiralApiRequest);
        
        $labelSearchBarcode = new \App\Api\LabelSearchBarcode($spiralDataBase,$userInfo);
        $result = $this->returnError;

        $this->assertEquals($result, $labelSearchBarcode->search('11234567890123'));
        $this->assertEquals($result, $labelSearchBarcode->search('01123456780123'));

        
        //Success Count 0
        $spiralApiRequest = $this->createMock(\SpiralApiRequest::class);
        $result = $this->returnSuccess;
        $result['count'] = '0';
        $result['code'] = '1';

        $spiralApiRequest->method('entrySet')->willReturn($result);
        $spiralDataBase = new \App\Lib\SpiralDataBase($spiral,$spiralApiCommunicator,$spiralApiRequest);
        
        $labelSearchBarcode = new \App\Api\LabelSearchBarcode($spiralDataBase,$userInfo);

        $this->assertEquals($result, $labelSearchBarcode->search('11234567890123'));
        $this->assertEquals($result, $labelSearchBarcode->search('01123456780123'));

        //Success No Data
        $this->assertEquals(array('code' => '1' , 'data' => array(),'message' => "no data" ,'count' => "0"), $labelSearchBarcode->search(''));
    }
}