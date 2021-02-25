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
require_once ('src/NewJoyPla/api/GoodsBillingMonthlyReport.php');


class GoodsBillingMonthlyReportTest extends TestCase
{
    private $Spiral;

    private $returnSuccess =  array(
        'data' => array(),
        'code' => '0',
        'message' => 'message',
        'count' => '1'
    );

    private $returnError1 =  array(
        'data' => array(),
        'code' => '0',
        'message' => 'message',
        'count' => '0'
        );
        
    private $returnError2 =  array(
        'code' => '1',
        'message' => 'error!'
        );
    
        
    public function testDataSelect()
    {
        $spiral = new \Spiral();
         
        $userInfo = new \App\Lib\UserInfo($spiral);
        $spiralApiCommunicator = $spiral->getSpiralApiCommunicator();
        
        //Success
        $spiralApiRequest = $this->createMock(\SpiralApiRequest::class);
        $spiralApiRequest->method('entrySet')->willReturn($this->returnSuccess);
        $spiralDataBase = new \App\Lib\SpiralDataBase($spiral,$spiralApiCommunicator,$spiralApiRequest);
        
        $goodsBillingMonthlyReport = new \App\Api\GoodsBillingMonthlyReport($spiralDataBase,$userInfo);
        $result = array(
            'count' => '1',
            'totalAmount' => 0
        );
        $this->assertEquals($result, $goodsBillingMonthlyReport->dataSelect('2020/01/01','2020/01/01','divisionId','itemName','itemCode','itemStandard','100','10'));

        //Error1
        $spiralApiRequest = $this->createMock(\SpiralApiRequest::class);
        $spiralApiRequest->method('entrySet')->willReturn($this->returnError1);
        $spiralDataBase = new \App\Lib\SpiralDataBase($spiral,$spiralApiCommunicator,$spiralApiRequest);
        
        $goodsBillingMonthlyReport = new \App\Api\GoodsBillingMonthlyReport($spiralDataBase,$userInfo);
        $result = array(
            'count' => '0',
            'totalAmount' => '0',
            'data' => Array ()
        );
        $this->assertEquals($result, $goodsBillingMonthlyReport->dataSelect('2020/01/01','2020/01/01','divisionId','itemName','itemCode','itemStandard','100','10'));


        //Error2
        $spiralApiRequest = $this->createMock(\SpiralApiRequest::class);
        $spiralApiRequest->method('entrySet')->willReturn($this->returnError2);
        $spiralDataBase = new \App\Lib\SpiralDataBase($spiral,$spiralApiCommunicator,$spiralApiRequest);
        
        $goodsBillingMonthlyReport = new \App\Api\GoodsBillingMonthlyReport($spiralDataBase,$userInfo);
        $result = array(
            'code' => '1',
            'message' => 'error!'
        );
        $this->assertEquals($result, $goodsBillingMonthlyReport->dataSelect('2020/01/01','2020/01/01','divisionId','itemName','itemCode','itemStandard','100','10'));
    }
}