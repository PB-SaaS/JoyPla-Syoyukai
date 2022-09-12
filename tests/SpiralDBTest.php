<?php

require_once "framework/Bootstrap/autoload.php";

use framework\SpiralConnecter\DbFilterField;
use framework\SpiralConnecter\SpiralConnecter;
use framework\SpiralConnecter\SpiralDB;

SpiralDB::setToken('00011BfiY3b78290dd5fb1d4239f583e9f4506bc811ed9238b80','691019e4cf839065209ef1a548a1d3dac95126c3');

$field = new DbFilterField( 'mailAddress' , 'test' , '' ,'' ,'is_deliverable');

$test = SpiralDB::filter('UserDB')->selectName('test')->addField($field)->create();
var_dump($test);
/*
$test = SpiralDB::mail('UserDB')
    ->bodyText('test test')
    ->bodyHtml('<h1>Test</h1>')
    ->formAddress('ito.shun@pi-pe.co.jp')
    ->formName('itoh shun')
    ->subject('test')
    ->reserveDate('now')
    ->mailField('mailAddress')
    ->standby(false)
    ->send();

SpiralDB::mail('UserDB')->ruleId($test)->sampling($ids);
*/