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
require_once ('src/NewJoyPla/api/SearchBarcode.php');


class SearchBarcodeTest extends TestCase
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

    private $returnSuccess2 =  array(
        'data' => array(
            array()
        ),
        'ulrs' => array('url'),
        'code' => '0',
        'message' => 'message',
        'count' => '0'
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
        
        //Success
        $spiralApiRequest = $this->createMock(\SpiralApiRequest::class);
        $spiralApiRequest->method('entrySet')->willReturn($this->returnSuccess);
        $spiralDataBase = new \App\Lib\SpiralDataBase($spiral,$spiralApiCommunicator,$spiralApiRequest);
        
        $spiralTable = new \App\Lib\SpiralTable($spiral,$spiralApiCommunicator,$spiralApiRequest);

        $searchBarcode = new \App\Api\SearchBarcode($spiralDataBase,$spiralTable,$userInfo);
        $result = array(
            'code' => '0',
            'message' => 'message',
            'data' => array(array('1','1')),
            'ulrs' => array('url'),
            'count' => '1');

        $this->assertEquals($result, $searchBarcode->search('021234567890121234','jsessonId','myAreaTitle',$this->cardTitles));
        $this->assertEquals($result, $searchBarcode->search('031234567890121234','jsessonId','myAreaTitle',$this->cardTitles));
        $this->assertEquals($result, $searchBarcode->search('041234567890121234','jsessonId','myAreaTitle',$this->cardTitles));
        $this->assertEquals($result, $searchBarcode->search('051234567890121234','jsessonId','myAreaTitle',$this->cardTitles));
        $this->assertEquals($result, $searchBarcode->search('061234567890121234','jsessonId','myAreaTitle',$this->cardTitles));

        
        //Success No Urls
        $spiralApiRequest = $this->createMock(\SpiralApiRequest::class);
        $spiralApiRequest->method('entrySet')->willReturn($this->returnSuccess2);
        $spiralDataBase = new \App\Lib\SpiralDataBase($spiral,$spiralApiCommunicator,$spiralApiRequest);
        
        $spiralTable = new \App\Lib\SpiralTable($spiral,$spiralApiCommunicator,$spiralApiRequest);

        $searchBarcode = new \App\Api\SearchBarcode($spiralDataBase,$spiralTable,$userInfo);
        $result = array(
            'code' => '1',
            'message' => 'no urls',
            'urls' => array());

        $this->assertEquals($result, $searchBarcode->search('021234567890121234','jsessonId','myAreaTitle',$this->cardTitles));
        $this->assertEquals($result, $searchBarcode->search('031234567890121234','jsessonId','myAreaTitle',$this->cardTitles));
        $this->assertEquals($result, $searchBarcode->search('041234567890121234','jsessonId','myAreaTitle',$this->cardTitles));
        $this->assertEquals($result, $searchBarcode->search('051234567890121234','jsessonId','myAreaTitle',$this->cardTitles));
        $this->assertEquals($result, $searchBarcode->search('061234567890121234','jsessonId','myAreaTitle',$this->cardTitles));

        
        //Error
        $spiralApiRequest = $this->createMock(\SpiralApiRequest::class);
        $spiralApiRequest->method('entrySet')->willReturn($this->returnError);
        $spiralDataBase = new \App\Lib\SpiralDataBase($spiral,$spiralApiCommunicator,$spiralApiRequest);
        
        $spiralTable = new \App\Lib\SpiralTable($spiral,$spiralApiCommunicator,$spiralApiRequest);

        $searchBarcode = new \App\Api\SearchBarcode($spiralDataBase,$spiralTable,$userInfo);
        $result = array(
            'code' => '1',
            'message' => 'error!',
            'urls' => array());

        $this->assertEquals($result, $searchBarcode->search('021234567890121234','jsessonId','myAreaTitle',$this->cardTitles));
        $this->assertEquals($result, $searchBarcode->search('031234567890121234','jsessonId','myAreaTitle',$this->cardTitles));
        $this->assertEquals($result, $searchBarcode->search('041234567890121234','jsessonId','myAreaTitle',$this->cardTitles));
        $this->assertEquals($result, $searchBarcode->search('051234567890121234','jsessonId','myAreaTitle',$this->cardTitles));
        $this->assertEquals($result, $searchBarcode->search('061234567890121234','jsessonId','myAreaTitle',$this->cardTitles));

        
        //Error No Id
        $spiralApiRequest = $this->createMock(\SpiralApiRequest::class);
        $spiralApiRequest->method('entrySet')->willReturn($this->returnSuccess2);
        $spiralDataBase = new \App\Lib\SpiralDataBase($spiral,$spiralApiCommunicator,$spiralApiRequest);
        
        $spiralTable = new \App\Lib\SpiralTable($spiral,$spiralApiCommunicator,$spiralApiRequest);

        $searchBarcode = new \App\Api\SearchBarcode($spiralDataBase,$spiralTable,$userInfo);
        $result = array(
            'code' => '99',
            'message' => 'no urls',
            'urls' => array());

        $this->assertEquals($result, $searchBarcode->search('','jsessonId','myAreaTitle',$this->cardTitles));
        $this->assertEquals($result, $searchBarcode->search('','jsessonId','myAreaTitle',$this->cardTitles));
        $this->assertEquals($result, $searchBarcode->search('','jsessonId','myAreaTitle',$this->cardTitles));
        $this->assertEquals($result, $searchBarcode->search('','jsessonId','myAreaTitle',$this->cardTitles));
        $this->assertEquals($result, $searchBarcode->search('','jsessonId','myAreaTitle',$this->cardTitles));


        

    }

}