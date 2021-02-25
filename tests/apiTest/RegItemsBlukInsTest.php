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
require_once ('src/NewJoyPla/api/RegItemsBlukIns.php');


class RegItemsBlukInsTest extends TestCase
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
    
        
    public function testBlukinsert()
    {
        $spiral = new \Spiral();
         
        $userInfo = new \App\Lib\UserInfo($spiral);
        $spiralApiCommunicator = $spiral->getSpiralApiCommunicator();
        
        //Success
        $spiralApiRequest = $this->createMock(\SpiralApiRequest::class);
        $spiralApiRequest->method('entrySet')->willReturn($this->returnSuccess);
        $spiralDataBase = new \App\Lib\SpiralDataBase($spiral,$spiralApiCommunicator,$spiralApiRequest);
        
        $regItemsBlukIns = new \App\Api\RegItemsBlukIns($spiralDataBase,$userInfo);
        $this->assertEquals($this->returnSuccess, $regItemsBlukIns->blukinsert(
            array(
                array(
                    "itemName",
                    "itemCode",
                    "itemStandard",
                    "itemJANCode",
                    "makerName",
                    "officialFlag",
                    "officialpriceOld",
                    "officialprice",
                    "quantity",
                    "quantityUnit",
                    "itemUnit",
                    "minPrice",
                    "tenantId"
                )
            )
        ));

        //Error
        $spiralApiRequest = $this->createMock(\SpiralApiRequest::class);
        $spiralApiRequest->method('entrySet')->willReturn($this->returnError);
        $spiralDataBase = new \App\Lib\SpiralDataBase($spiral,$spiralApiCommunicator,$spiralApiRequest);
        
        $regItemsBlukIns = new \App\Api\RegItemsBlukIns($spiralDataBase,$userInfo);

        $this->assertEquals($this->returnError, $regItemsBlukIns->blukinsert(
            array(
                array(
                    "itemName",
                    "itemCode",
                    "itemStandard",
                    "itemJANCode",
                    "makerName",
                    "officialFlag",
                    "officialpriceOld",
                    "officialprice",
                    "quantity",
                    "quantityUnit",
                    "itemUnit",
                    "minPrice",
                    "tenantId"
                )
            )
        ));

    }
}