<?php

namespace JoyPla\Enterprise\Models;

use Collection;

class Quantity {

    private int $quantityNum;
    private string $quantityUnit;
    private string $itemUnit;
    
    public function __construct(
        int $quantityNum,
        string $quantityUnit,
        string $itemUnit
        )
    {
        $this->quantityNum = $quantityNum;
        $this->quantityUnit = $quantityUnit;
        $this->itemUnit = $itemUnit;
    }

    public static function create( Collection $input )
    {
        return new Quantity( 
            ( $input->quantity ),
            ( $input->quantityUnit ),
            ( $input->itemUnit )
        );
    }
    
    public function getQuantityNum()
    {
        return $this->quantityNum;
    }
    public function getQuantityUnit()
    {
        return $this->quantityUnit;
    }
    public function getItemUnit()
    {
        return $this->itemUnit;
    }

    public function toArray() 
    {
        return [
            'quantityNum' => $this->quantityNum,
            'quantityUnit' => $this->quantityUnit,
            'itemUnit' => $this->itemUnit,
        ];
    }
}