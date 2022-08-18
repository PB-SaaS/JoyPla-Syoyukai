<?php

use framework\Facades\Gate;

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

namespace framework;
class Application
{
    public function __construct()
    {
    }

    public function boot()
    {
    }
}