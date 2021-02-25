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
require_once ('src/NewJoyPla/api/GetReceivingHistory.php');


class GetReceivingHistoryTest extends TestCase
{
    private $Spiral;

    private $returnSuccess =  array(
        'data' => array(
            array(
                'registrationTime',
                'distributorName',
                'distributorId',
                'orderHistoryId',
                'hospitalName',
                'postalCode',
                'prefectures',
                'address',
                'phoneNumber',
                'ordererUserName',
                'authKey',
                'orderAuthKey',
                'divisionId'
            ),
        ),
        'code' => '0',
        'message' => 'message',
        'count' => '1'
    );

    private $returnError =  array(
        'code' => '1',
        'message' => 'error!'
        );
        
    public function testSelect()
    {
        $spiral = new \Spiral();
         
        $userInfo = new \App\Lib\UserInfo($spiral);
        $spiralApiCommunicator = $spiral->getSpiralApiCommunicator();
        
        //Success
        $spiralApiRequest = $this->createMock(\SpiralApiRequest::class);
        $spiralApiRequest->method('entrySet')->willReturn($this->returnSuccess);
        $spiralDataBase = new \App\Lib\SpiralDataBase($spiral,$spiralApiCommunicator,$spiralApiRequest);
        
        $result = $this->returnSuccess;
        $result['data'] = array(
            array(
                'registrationTime'=>'registrationTime',
                'distributorName'=>'distributorName',
                'distributorId'=>'distributorId',
                'orderHistoryId'=>'orderHistoryId',
                'hospitalName'=>'hospitalName',
                'postalCode'=>'postalCode',
                'prefectures'=>'prefectures',
                'address'=>'address',
                'phoneNumber'=>'phoneNumber',
                'ordererUserName'=>'ordererUserName',
                'authKey'=>'authKey',
                'orderAuthKey'=>'orderAuthKey',
                'divisionId'=>'divisionId'
            ),
        );

        $getReceivingHistory = new \App\Api\GetReceivingHistory($spiralDataBase,$userInfo);

        $this->assertEquals($result, $getReceivingHistory->select('receivingHId'));

        //Error
        $spiralApiRequest = $this->createMock(\SpiralApiRequest::class);
        $spiralApiRequest->method('entrySet')->willReturn($this->returnError);
        $spiralDataBase = new \App\Lib\SpiralDataBase($spiral,$spiralApiCommunicator,$spiralApiRequest);
        

        $getReceivingHistory = new \App\Api\GetReceivingHistory($spiralDataBase,$userInfo);

        $this->assertEquals($this->returnError, $getReceivingHistory->select('receivingHId'));
    }
}