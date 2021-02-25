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
require_once ('src/NewJoyPla/api/RegLot.php');


class RegLotTest extends TestCase
{
    private $Spiral;

    private $returnSuccess =  array(
        'data' => array(
            array('receivingNumbertest','inHospitalItemIdtest'),
        ),
        'code' => '0',
        'message' => 'message',
        'count' => '1'
    );

    private $returnError =  array(
        'data' => array(),
        'code' => '0',
        'message' => 'message',
        'count' => '0'
        );
    
        
    public function testRegLot()
    {
        $spiral = new \Spiral();
         
        $userInfo = new \App\Lib\UserInfo($spiral);
        $spiralApiCommunicator = $spiral->getSpiralApiCommunicator();
        
        //Success
        $spiralApiRequest = $this->createMock(\SpiralApiRequest::class);
        $spiralApiRequest->method('entrySet')->willReturn($this->returnSuccess);
        $spiralDataBase = new \App\Lib\SpiralDataBase($spiral,$spiralApiCommunicator,$spiralApiRequest);
        
        $regLot = new \App\Api\RegLot($spiralDataBase,$userInfo);

        $lotData = array(
                    array(
                        array(
                            'lotId' => 'test',
                            'lotNumber' => 'LotNumbertest',
                            'lotDate' => 'LotDatetest',
                            'inHospitalItemId' => 'inHospitalItemIdtest',
                            'receivingNumber' => 'receivingNumbertest',
                            'payoutId' => 'payoutIdtest',
                        )
                    )
                );
        $this->assertEquals($this->returnSuccess,$regLot->regLot($lotData,'divisionId',null,'payoutHistoryId'));
        $this->assertEquals($this->returnSuccess,$regLot->regLot($lotData,'divisionId','receivingHId','payoutHistoryId'));


        
        //Error
        $spiralApiRequest = $this->createMock(\SpiralApiRequest::class);
        $spiralApiRequest->method('entrySet')->willReturn($this->returnError);
        $spiralDataBase = new \App\Lib\SpiralDataBase($spiral,$spiralApiCommunicator,$spiralApiRequest);
        
        $regLot = new \App\Api\RegLot($spiralDataBase,$userInfo);

        $lotData = array(
                    array(
                        array(
                            'lotId' => 'test',
                            'lotNumber' => 'LotNumbertest',
                            'lotDate' => 'LotDatetest',
                            'inHospitalItemId' => 'inHospitalItemIdtest',
                            'receivingNumber' => 'receivingNumbertest',
                            'payoutId' => 'payoutIdtest',
                        )
                    )
                );
        $this->assertEquals($this->returnError,$regLot->regLot($lotData,'divisionId',null,'payoutHistoryId'));
        $this->assertEquals($this->returnError,$regLot->regLot($lotData,'divisionId','receivingHId','payoutHistoryId'));

    }
}