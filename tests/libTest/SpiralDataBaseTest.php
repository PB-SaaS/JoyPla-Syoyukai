<?php

namespace phpUnit\Test;

use PHPUnit\Framework\TestCase;

require_once ('src/NewJoyPla/lib/ApiSpiral.php');
require_once ('src/NewJoyPla/lib/Define.php');
require_once ('mock/Spiral.php');
require_once ('mock/SpiralApiRequest.php');
require_once ('mock/PbSpiralApiCommunicator.php');
require_once ('src/NewJoyPla/lib/SpiralDataBase.php');


class SpiralDataBaseTest extends TestCase
{
    private $Spiral;

    private $returnSuccess =  array(
        'data' => array(),
        'code' => '0',
        'message' => 'message',
        'count' => '1000'
    );

    private $returnError =  array(
        'code' => '1',
        'message' => 'error!'
        );

    public function testDoSelect()
    {
        $spiral = new \Spiral();
		$spiralApiCommunicator = $spiral->getSpiralApiCommunicator();
        $spiralApiRequest = new \SpiralApiRequest();
        
        //Success
        $spiralApiRequest = $this->createMock(\SpiralApiRequest::class);
        $spiralApiRequest->method('entrySet')->willReturn($this->returnSuccess);
        
        $SpiralDataBase = new \App\Lib\SpiralDataBase($spiral,$spiralApiCommunicator,$spiralApiRequest);
        $this->assertEquals($this->returnSuccess, $SpiralDataBase->doSelect(true));

        //Error
        $spiralApiRequest = $this->createMock(\SpiralApiRequest::class);
        $spiralApiRequest->method('entrySet')->willReturn($this->returnError);
        $SpiralDataBase = new \App\Lib\SpiralDataBase($spiral,$spiralApiCommunicator,$spiralApiRequest);

        $this->assertEquals($this->returnError, $SpiralDataBase->doSelect(true));
    }
    public function testDoInsert()
    {
        $spiral = new \Spiral();
		$spiralApiCommunicator = $spiral->getSpiralApiCommunicator();
        //Success
        $spiralApiRequest = $this->createMock(\SpiralApiRequest::class);
        $spiralApiRequest->method('entrySet')->willReturn($this->returnSuccess);
        $SpiralDataBase = new \App\Lib\SpiralDataBase($spiral,$spiralApiCommunicator,$spiralApiRequest);

        $insertData = array(array('name' => 'name','value' => 'value'));
        $this->assertEquals($this->returnSuccess, $SpiralDataBase->doInsert($insertData));

        //Error
        $spiralApiRequest = $this->createMock(\SpiralApiRequest::class);
        $spiralApiRequest->method('entrySet')->willReturn($this->returnError);
        $SpiralDataBase = new \App\Lib\SpiralDataBase($spiral,$spiralApiCommunicator,$spiralApiRequest);

        $insertData = array(array('name' => 'name','value' => 'value'));
        $this->assertEquals($this->returnError, $SpiralDataBase->doInsert($insertData));

    }
    
    public function testDoBulkInsert()
    {
        $columns = array('registrationTime','updateTime');
        $insertData = array(array('now','now'),array('now','now'));

        $spiral = new \Spiral();
        $spiralApiCommunicator = $spiral->getSpiralApiCommunicator();
        
        //Success
        $spiralApiRequest = $this->createMock(\SpiralApiRequest::class);
        $spiralApiRequest->method('entrySet')->willReturn($this->returnSuccess);
        $SpiralDataBase = new \App\Lib\SpiralDataBase($spiral,$spiralApiCommunicator,$spiralApiRequest);

        $this->assertEquals($this->returnSuccess, $SpiralDataBase->doInsert($columns,$insertData));

        
        ///Error
        $spiralApiRequest = $this->createMock(\SpiralApiRequest::class);
        $spiralApiRequest->method('entrySet')->willReturn($this->returnError);
        $SpiralDataBase = new \App\Lib\SpiralDataBase($spiral,$spiralApiCommunicator,$spiralApiRequest);

        $this->assertEquals($this->returnError, $SpiralDataBase->doInsert($columns,$insertData));
    }

    public function testDoDelete()
    {
        $spiral = new \Spiral();
		$spiralApiCommunicator = $spiral->getSpiralApiCommunicator();
        //Success
        $spiralApiRequest = $this->createMock(\SpiralApiRequest::class);
        $spiralApiRequest->method('entrySet')->willReturn($this->returnSuccess);
        $SpiralDataBase = new \App\Lib\SpiralDataBase($spiral,$spiralApiCommunicator,$spiralApiRequest);
     
        $this->assertEquals($this->returnSuccess, $SpiralDataBase->doDelete());

        ///Error
        $spiralApiRequest = $this->createMock(\SpiralApiRequest::class);
        $spiralApiRequest->method('entrySet')->willReturn($this->returnError);
        $SpiralDataBase = new \App\Lib\SpiralDataBase($spiral,$spiralApiCommunicator,$spiralApiRequest);

        $this->assertEquals($this->returnError, $SpiralDataBase->doDelete());
    }
    public function testDoBulkUpdate()
    {
        $columns = array('keyTitle','registrationTime','updateTime');
        $updateData = array(array('test','now','now'),array('test','now','now'));

        $spiral = new \Spiral();
		$spiralApiCommunicator = $spiral->getSpiralApiCommunicator();
        //Success
        $spiralApiRequest = $this->createMock(\SpiralApiRequest::class);
        $spiralApiRequest->method('entrySet')->willReturn($this->returnSuccess);
        $SpiralDataBase = new \App\Lib\SpiralDataBase($spiral,$spiralApiCommunicator,$spiralApiRequest);
     
        $this->assertEquals($this->returnSuccess, $SpiralDataBase->doBulkUpdate('keyTitle',$columns,$updateData));

        ///Error
        $spiralApiRequest = $this->createMock(\SpiralApiRequest::class);
        $spiralApiRequest->method('entrySet')->willReturn($this->returnError);
        $SpiralDataBase = new \App\Lib\SpiralDataBase($spiral,$spiralApiCommunicator,$spiralApiRequest);

        $this->assertEquals($this->returnError, $SpiralDataBase->doBulkUpdate('keyTitle',$columns,$updateData));
    }
    public function testDoUpsert()
    {
        $upsertData = array(array('name' => 'keyTitle','value' => '1'),array('name' => 'registrationTime','value' => 'now'));
        $spiral = new \Spiral();
		$spiralApiCommunicator = $spiral->getSpiralApiCommunicator();
        //Success
        $spiralApiRequest = $this->createMock(\SpiralApiRequest::class);
        $spiralApiRequest->method('entrySet')->willReturn($this->returnSuccess);
        $SpiralDataBase = new \App\Lib\SpiralDataBase($spiral,$spiralApiCommunicator,$spiralApiRequest);
     
        $this->assertEquals($this->returnSuccess, $SpiralDataBase->doUpsert('keyTitle',$upsertData));

        ///Error
        $spiralApiRequest = $this->createMock(\SpiralApiRequest::class);
        $spiralApiRequest->method('entrySet')->willReturn($this->returnError);
        $SpiralDataBase = new \App\Lib\SpiralDataBase($spiral,$spiralApiCommunicator,$spiralApiRequest);

        $this->assertEquals($this->returnError, $SpiralDataBase->doUpsert('keyTitle',$upsertData));
        
    }
    public function testDoBulkUpsert()
    {
        $columns = array('keyTitle','registrationTime','updateTime');
        $updateData = array(array('test','now','now'),array('test','now','now'));
        $spiral = new \Spiral();
		$spiralApiCommunicator = $spiral->getSpiralApiCommunicator();
        //Success
        $spiralApiRequest = $this->createMock(\SpiralApiRequest::class);
        $spiralApiRequest->method('entrySet')->willReturn($this->returnSuccess);
        $SpiralDataBase = new \App\Lib\SpiralDataBase($spiral,$spiralApiCommunicator,$spiralApiRequest);
     
        $this->assertEquals($this->returnSuccess, $SpiralDataBase->doBulkUpdate('keyTitle',$columns,$updateData));

        ///Error
        $spiralApiRequest = $this->createMock(\SpiralApiRequest::class);
        $spiralApiRequest->method('entrySet')->willReturn($this->returnError);
        $SpiralDataBase = new \App\Lib\SpiralDataBase($spiral,$spiralApiCommunicator,$spiralApiRequest);

        $this->assertEquals($this->returnError, $SpiralDataBase->doBulkUpdate('keyTitle',$columns,$updateData));
    }
    public function testDoSelectLoop()
    {
        $spiral = new \Spiral();
		$spiralApiCommunicator = $spiral->getSpiralApiCommunicator();
        //Success
        $spiralApiRequest = $this->createMock(\SpiralApiRequest::class);
        $spiralApiRequest->method('entrySet')->willReturn($this->returnSuccess);
        $SpiralDataBase = new \App\Lib\SpiralDataBase($spiral,$spiralApiCommunicator,$spiralApiRequest);
     
        $this->assertEquals($this->returnSuccess, $SpiralDataBase->doSelectLoop());

        ///Error
        $spiralApiRequest = $this->createMock(\SpiralApiRequest::class);
        $spiralApiRequest->method('entrySet')->willReturn($this->returnError);
        $SpiralDataBase = new \App\Lib\SpiralDataBase($spiral,$spiralApiCommunicator,$spiralApiRequest);

        $this->assertEquals($this->returnError, $SpiralDataBase->doSelectLoop());
    }
    
    public function testArrayToNameArray()
    {

        $spiral = new \Spiral();
		$spiralApiCommunicator = $spiral->getSpiralApiCommunicator();
		$spiralApiRequest = new \SpiralApiRequest();
        $SpiralDataBase = new \App\Lib\SpiralDataBase($spiral,$spiralApiCommunicator,$spiralApiRequest);

        $columns = array('keyTitle','registrationTime','updateTime');
        $data = array(array('test1','now','now'),array('test2','now','now'));

        $check = array(
            array(
                'keyTitle' => 'test1',
                'registrationTime' => 'now',
                'updateTime' => 'now'),
            array(
                'keyTitle' => 'test2',
                'registrationTime' => 'now',
                'updateTime' => 'now')
        );
        
        $this->assertEquals($check,$SpiralDataBase->arrayToNameArray($data,$columns));
    }
}