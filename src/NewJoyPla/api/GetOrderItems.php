<?php

namespace App\Api;

class GetOrderItems{

    private $spiralDataBase;
    private $database = 'hacchuShouhin';
    public function __construct(\App\Lib\SpiralDataBase $spiralDataBase){
        $this->spiralDataBase = $spiralDataBase;
    }

    public function select(string $orderNumber , string ...$fields){
        $this->spiralDataBase->setDataBase($this->database);
        $this->spiralDataBase->addSelectFields(...$fields);
        $this->spiralDataBase->addSortField('id' , 'asc' );
        $this->spiralDataBase->addSearchCondition('orderNumber',$orderNumber);
        return $this->spiralDataBase->doSelectLoop();
    }
}