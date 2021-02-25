<?php
require_once ('src/NewJoyPla/lib/ApiSpiral.php');
require_once ('src/NewJoyPla/lib/Define.php');
require_once ('mock/Spiral.php');
require_once ('mock/SpiralApiRequest.php');
require_once ('src/NewJoyPla/lib/SpiralDataBase.php');


$spiral = new \Spiral();
$SpiralDataBase = new \App\Lib\SpiralDataBase($spiral);

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
var_dump($SpiralDataBase->arrayToNameArray($data,$columns));
var_dump($check );