<?php

namespace phpUnit\Test;

use PHPUnit\Framework\TestCase; 

require_once ('src/NewJoyPla/lib/ApiSpiral.php');
require_once ('src/NewJoyPla/lib/Define.php');
require_once ('src/NewJoyPla/lib/UserInfo.php');
require_once ('src/NewJoyPla/lib/SpiralDataBase.php');
require_once ('src/NewJoyPla/lib/SpiralTable.php');
require_once ('mock/Spiral.php');
require_once ('mock/SpiralApiRequest.php');
require_once ('mock/PbSpiralApiCommunicator.php');
require_once ('src/NewJoyPla/api/OroshiSearchBarcode.php');


class OroshiSearchBarcodeTest extends TestCase
{
    private $Spiral;

    private $returnSuccess =  array(
        'data' => array(
            array('1','1')
        ),
        'ulrs' => array('url'),
        'code' => '0',
        'message' => 'message',
        'count' => '1'
    );

    private $returnError =  array(
        'code' => '1',
        'message' => 'error!'
        );
    
    private $cardTitles = array(
        '02' => '02',
        '03_unorder' => '03_unorder',
        '03_order' => '03_order',
        '04' => '04',
        '05' => '05',
        '06' => '06',
        '08' => '08'
    );
        
    public function testSearch()
    {
        $spiral = new \Spiral();
         
        $userInfo = new \App\Lib\UserInfo($spiral);
        $spiralApiCommunicator = $spiral->getSpiralApiCommunicator();
        
        //Success No urls
        $spiralApiRequest = $this->createMock(\SpiralApiRequest::class);
        $spiralApiRequest->method('entrySet')->willReturn($this->returnSuccess);
        $spiralDataBase = new \App\Lib\SpiralDataBase($spiral,$spiralApiCommunicator,$spiralApiRequest);
        
        $spiralTable = new \App\Lib\SpiralTable($spiral,$spiralApiCommunicator,$spiralApiRequest);

        $oroshiSearchBarcode = new \App\Api\OroshiSearchBarcode($spiralDataBase,$spiralTable,$userInfo);
        $result = array(
            'code' => '1',
            'urls' => array(),
            'message' => 'no urls');

        $this->assertEquals($result, $oroshiSearchBarcode->search('031234567890121234','jsessonId','myAreaTitle',$this->cardTitles));
        //$this->assertEquals($result, $oroshiSearchBarcode->search('041234567890121234','jsessonId','myAreaTitle',$this->cardTitles));


        //Success

        $result = $this->returnSuccess;
        $result['data'] = array(array('1','2'));

        $spiralApiRequest = $this->createMock(\SpiralApiRequest::class);
        $spiralApiRequest->method('entrySet')->willReturn($result);
        $spiralDataBase = new \App\Lib\SpiralDataBase($spiral,$spiralApiCommunicator,$spiralApiRequest);
        
        $spiralTable = new \App\Lib\SpiralTable($spiral,$spiralApiCommunicator,$spiralApiRequest);

        $oroshiSearchBarcode = new \App\Api\OroshiSearchBarcode($spiralDataBase,$spiralTable,$userInfo);
        $result = array(
            'code' => '0',
            'message' => 'message',
            'data' => array(array('1','2')),
            'ulrs' => array('url'),
            'count' => '1');

        $this->assertEquals($result, $oroshiSearchBarcode->search('031234567890121234','jsessonId','myAreaTitle',$this->cardTitles));
        $this->assertEquals($result, $oroshiSearchBarcode->search('041234567890121234','jsessonId','myAreaTitle',$this->cardTitles));


        
        //Error
        $spiralApiRequest = $this->createMock(\SpiralApiRequest::class);
        $spiralApiRequest->method('entrySet')->willReturn($this->returnError);
        $spiralDataBase = new \App\Lib\SpiralDataBase($spiral,$spiralApiCommunicator,$spiralApiRequest);
        
        $spiralTable = new \App\Lib\SpiralTable($spiral,$spiralApiCommunicator,$spiralApiRequest);

        $oroshiSearchBarcode = new \App\Api\OroshiSearchBarcode($spiralDataBase,$spiralTable,$userInfo);
        $result = array('code' => '1','urls'=>array(),'message'=>'error!');

        $this->assertEquals($result, $oroshiSearchBarcode->search('031234567890121234','jsessonId','myAreaTitle',$this->cardTitles));
        $this->assertEquals($result, $oroshiSearchBarcode->search('041234567890121234','jsessonId','myAreaTitle',$this->cardTitles));
    
    
        //Error
        $spiralApiRequest = $this->createMock(\SpiralApiRequest::class);
        $spiralApiRequest->method('entrySet')->willReturn($this->returnError);
        $spiralDataBase = new \App\Lib\SpiralDataBase($spiral,$spiralApiCommunicator,$spiralApiRequest);
        
        $spiralTable = new \App\Lib\SpiralTable($spiral,$spiralApiCommunicator,$spiralApiRequest);

        $oroshiSearchBarcode = new \App\Api\OroshiSearchBarcode($spiralDataBase,$spiralTable,$userInfo);
        $result = array('code' => '99','urls'=>array(),'message'=>'no urls');

        $this->assertEquals($result, $oroshiSearchBarcode->search('','jsessonId','myAreaTitle',$this->cardTitles));
    }
}