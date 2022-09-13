<?php

namespace framework\Library;

use framework\SpiralConnecter\Paginator;
use framework\SpiralConnecter\SpiralDB;
use framework\SpiralConnecter\SpiralManager;

interface SiRule {
    public function processable($value);
    public function message();
    public function name();
}

class SpiralDbUniqueRule implements SiRule{

    private string $name = 'spiralDbUnique';
    private string $uniqueKey = "";
    private SpiralManager $table;

    public function __construct(SpiralManager $table , $uniqueKey)
    {
        $this->table = $table;
        $this->uniqueKey = $uniqueKey;
    }

    public static function unique($tableName , $uniqueKey , ?callable $searchCallable = null)
    {
        $instance = SpiralDB::title($tableName);
        if( is_callable( $searchCallable ))
        {
            $instance = $searchCallable($instance);
        }
        $self = new self($instance , $uniqueKey);
        return $self; 
    }

    public function processable($value)
    {
        $result = $this->table->where($this->uniqueKey , $value )->paginate(1);
        if($result instanceof Paginator && $result->getTotal() == 0)
        { 
            return true; 
        }
        return false;
    }
   
    public function message()
    {
        return [
            'ja' => 
            [
                $this->name => '{field}は重複しています'
            ]
        ];
    }

    public function name()
    {
        return $this->name;
    }
}