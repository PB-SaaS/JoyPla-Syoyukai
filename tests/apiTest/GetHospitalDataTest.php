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
require_once ('src/NewJoyPla/api/GetHospitalData.php');


class GetHospitalDataTest extends TestCase
{
    private $Spiral;

    private $returnSuccess =  array(
        'data' => array(
            array(
            '2021/01/01',
            '2021/01/02',
            'hospital_1',
            'hospitalName',
            '123-1234',
            '1',
            'test',
            '03-5575-6601',
            '03-5575-6601',
            'tenant_1',
            'name',
            'nameKana',
            'test@pi-pe.co.jp',
            'contactAddress',
            '1',
            '1',
            '1',
            '1',
            '1',
            '1',
            '1',
            '1',
            '1',
            '1',
            '1'),
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
        
        $getHospitalData = new \App\Api\GetHospitalData($spiralDataBase,$userInfo);

        $testResult = $this->returnSuccess;
        $testResult['data'] = array(
                    array (
                    'registrationTime' => '2021/01/01',
                    'updateTime' => '2021/01/02',
                    'hospitalId' => 'hospital_1',
                    'hospitalName' => 'hospitalName',
                    'postalCode' => '123-1234',
                    'prefectures' => '1',
                    'address' => 'test',
                    'phoneNumber' => '03-5575-6601',
                    'faxNumber' => '03-5575-6601',
                    'tenantId' => 'tenant_1',
                    'name' => 'name',
                    'nameKana' => 'nameKana',
                    'mailAddress' => 'test@pi-pe.co.jp',
                    'contactAddress' => 'contactAddress',
                    'plan' => '1',
                    'receivingTarget' => '1',
                    'function1' => '1',
                    'function2' => '1',
                    'function3' => '1',
                    'function4' => '1',
                    'function5' => '1',
                    'function6' => '1',
                    'function7' => '1',
                    'function8' => '1',
                    'registerableNum' => '1'
                    )
                    );

        $this->assertEquals($testResult, $getHospitalData->select('hospital_1'));

        //Error
        $spiralApiRequest = $this->createMock(\SpiralApiRequest::class);
        $spiralApiRequest->method('entrySet')->willReturn($this->returnError);
        $spiralDataBase = new \App\Lib\SpiralDataBase($spiral,$spiralApiCommunicator,$spiralApiRequest);
        
        $getHospitalData = new \App\Api\GetHospitalData($spiralDataBase,$userInfo);

        $this->assertEquals($this->returnError, $getHospitalData->select('hospital_1'));
    }
}