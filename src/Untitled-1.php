<?php
//<!-- SMP_DYNAMIC_PAGE DISPLAY_ERRORS=ON NAME=SAMPLE_PAGE -->
require_once "Component.php";
include_once 'NewJoyPla/lib/ApiSpiral.php';
include_once "NewJoyPla/lib/Define.php";
include_once 'NewJoyPla/lib/SpiralDataBase.php';
$spiralApiCommunicator = $SPIRAL->getSpiralApiCommunicator();
$spiralApiRequest = new SpiralApiRequest();
$spiralDataBase = new App\Lib\SpiralDataBase($SPIRAL,$spiralApiCommunicator,$spiralApiRequest);

$setting = array(
            'database' => '',
            'selectField' => array('f0001','aaa'),
            'linkFiled' => array('f0001'),
            'search' => array(array('name'=>'fieldtitle','value'=>'value','mode'=> '=','type'=>'and')),//可変 一覧表条件
            'sort' => array('f0001','asc'),//可変
        );
$test = new Component\ListComponent($setting ,$spiralDataBase);
$result = $test->setting(array('page_id' => 'page_00002','page' => 1 ,'sort' => array('test','asc'),'limit' => 10 , 'search' => array()));
if($result->getCode() == 0){
    echo 'complete';
}