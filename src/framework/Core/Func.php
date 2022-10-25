<?php

use framework\Facades\Gate;
use framework\Http\Session\Session;

function view(string $template, array $param = array() , bool $filter = true): framework\Http\View
{
    return new framework\Http\View($template , $param , $filter);
}

function html($string = '') {
    return htmlspecialchars($string, ENT_QUOTES, 'UTF-8');
}

function gate(string $pass , ...$instances)
{
    return Gate::getGateInstance($pass , ...$instances);
}

function collect(array $ary){
    return new Collection($ary);
}

function collect_column($array, $key)
{
    $result = []; 
    foreach($array as $a )
    {
        $result[] = $a->{$key};
    }
    return $result;
}

function number_format_jp($num)
{
    if(empty($num)) { return 0; }
    return preg_replace("/\.?0+$/","",number_format($num,2));
}

function config($key , $default = '')
{
    return (new Config())->get($key , $default);
}

function config_path($path)
{
    Config::setPath($path);
}

function shiftjis_strlen($value)
{
    return strlen( mb_convert_encoding($value, 'SJIS', 'UTF-8') );
}