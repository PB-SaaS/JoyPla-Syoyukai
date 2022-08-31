<?php

namespace framework\Library;

use SpiralORM;

interface SiRule {
    public function processable($value);
    public function message();
    public function name();
}

class SpiralDbRule extends SpiralORM implements SiRule{

    private string $name = 'spiralDbUnique';
    private string $uniqueKey = "";

    public static function unique($tableName , $uniqueKey)
    {
        $instance = new SpiralDbRule();
        $instance->uniqueKey = $uniqueKey;
        $instance::title($tableName);
        return $instance;
    }

    public function processable($value)
    {
        $result = $this->where($this->uniqueKey , $value )->paginate(1);
        if($result->count == 0)
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