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
require_once ('src/NewJoyPla/api/RegInHPitemsBlukIns.php');


class RegInHPitemsBlukInsTest extends TestCase
{
    private $Spiral;

    private $returnSuccess =  array(
        'data' => array(
            array(
                "itemId",
            ),
            array(
                "distributorId",
            )
        ),
        'code' => '0',
        'message' => 'message',
        'count' => '1'
    );


    private $returnError1 =  array(
        'data' => array(
            array(
                "distributorId",
            )
        ),
        'code' => '0',
        'message' => 'message',
        'count' => '1'
    );

    
    private $returnError2 =  array(
        'data' => array(
            array(
                "itemId",
            )
        ),
        'code' => '0',
        'message' => 'message',
        'count' => '1'
    );

    private $returnError3 =  array(
        'data' => array(),
        'code' => '1',
        'message' => 'message',
        'count' => '0'
        );
    
        
    public function testBlukinsert()
    {
        $spiral = new \Spiral();
         
        $userInfo = new \App\Lib\UserInfo($spiral);
        $spiralApiCommunicator = $spiral->getSpiralApiCommunicator();
        
        //Success
        $spiralApiRequest = $this->createMock(\SpiralApiRequest::class);
        $spiralApiRequest->method('entrySet')->willReturn($this->returnSuccess);
        $spiralDataBase = new \App\Lib\SpiralDataBase($spiral,$spiralApiCommunicator,$spiralApiRequest);
        
        $regInHPitemsBlukIns = new \App\Api\RegInHPitemsBlukIns($spiralDataBase,$userInfo);
        $this->assertEquals($this->returnSuccess, $regInHPitemsBlukIns->blukinsert(
            array(
                array(
                    "itemId",
                    "distributorId",
                    "catalogNo",
                    "serialNo",
                    "quantity",
                    "itemUnit",
                    "medicineCategory",
                    "homeCategory",
                    "notUsedFlag",
                    "notice",
                    "labelId",
                    "hospitalId"
                )
            )
        ));

        
        //Error1 No Items Id
        $spiralApiRequest = $this->createMock(\SpiralApiRequest::class);
        $spiralApiRequest->method('entrySet')->willReturn($this->returnError1);
        $spiralDataBase = new \App\Lib\SpiralDataBase($spiral,$spiralApiCommunicator,$spiralApiRequest);
        
        $regInHPitemsBlukIns = new \App\Api\RegInHPitemsBlukIns($spiralDataBase,$userInfo);

        $result = array(
            'code' => '99',
            'message' => 'not regist itemsId: itemId');

        $this->assertEquals($result, $regInHPitemsBlukIns->blukinsert(
            array(
                array(
                    "itemId",
                    "distributorId",
                    "catalogNo",
                    "serialNo",
                    "quantity",
                    "itemUnit",
                    "medicineCategory",
                    "homeCategory",
                    "notUsedFlag",
                    "notice",
                    "labelId",
                    "hospitalId"
                )
            )
        ));

        
        //Error2 No distributorId
        $spiralApiRequest = $this->createMock(\SpiralApiRequest::class);
        $spiralApiRequest->method('entrySet')->willReturn($this->returnError2);
        $spiralDataBase = new \App\Lib\SpiralDataBase($spiral,$spiralApiCommunicator,$spiralApiRequest);
        
        $regInHPitemsBlukIns = new \App\Api\RegInHPitemsBlukIns($spiralDataBase,$userInfo);

        $result = array(
            'code' => '99',
            'message' => 'not regist distributorId: distributorId');

        $this->assertEquals($result, $regInHPitemsBlukIns->blukinsert(
            array(
                array(
                    "itemId",
                    "distributorId",
                    "catalogNo",
                    "serialNo",
                    "quantity",
                    "itemUnit",
                    "medicineCategory",
                    "homeCategory",
                    "notUsedFlag",
                    "notice",
                    "labelId",
                    "hospitalId"
                )
            )
        ));

        //Error3 No distributorId
        $spiralApiRequest = $this->createMock(\SpiralApiRequest::class);
        $spiralApiRequest->method('entrySet')->willReturn($this->returnError3);
        $spiralDataBase = new \App\Lib\SpiralDataBase($spiral,$spiralApiCommunicator,$spiralApiRequest);
        
        $regInHPitemsBlukIns = new \App\Api\RegInHPitemsBlukIns($spiralDataBase,$userInfo);

        $this->assertEquals($this->returnError3, $regInHPitemsBlukIns->blukinsert(
            array(
                array(
                    "itemId",
                    "distributorId",
                    "catalogNo",
                    "serialNo",
                    "quantity",
                    "itemUnit",
                    "medicineCategory",
                    "homeCategory",
                    "notUsedFlag",
                    "notice",
                    "labelId",
                    "hospitalId"
                )
            )
        ));

    }
}