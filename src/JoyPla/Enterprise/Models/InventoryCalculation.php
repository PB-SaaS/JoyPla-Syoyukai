<?php

namespace JoyPla\Enterprise\Models;
class InventoryCalculation 
{
    
    public static $calculationPattern = [
        1 =>	'消費',//-
        2 =>	'発注', //+
        3 =>	'入庫', //-
        4 =>	'払出元', //-
        5 =>	'払出先', //+
        6 =>	'返品', //+
        7 =>	'棚卸', //+
        10 =>	'在庫調整', //+-
        11 =>   'ロット調整', //+-
    ];

    public function __construct(
        HospitalId $hospitalId,
        DivisionId $divisionId,
        InHospitalItemId $inHospitalItemId,
        int $orderedQuantity = 0,
        int $pattern,
        Lot $lot,
        int $calculationQuantity = 0
    )
    {
        $this->hospitalId = $hospitalId;
        $this->divisionId = $divisionId;
        $this->inHospitalItemId = $inHospitalItemId;
        $this->pattern = $pattern;
        $this->lot = $lot;
        $this->orderedQuantity = $orderedQuantity;
        $this->calculationQuantity = $calculationQuantity;
    }

    public function createUniqKey()
    {
        if($this->lot->getLotDate()->value() == "" && $this->lot->getLotNumber()->value() == "")
        {
            return "";
        }
        return $this->hospitalId->value() . $this->divisionId->value() . $this->inHospitalItemId->value() . $this->lot->getLotNumber()->value() . $this->lot->getLotDate()->value();
    }

    public function toArray()
    {
        return [
            'hospitalId' =>  $this->hospitalId->value(),
            'divisionId' =>  $this->divisionId->value(),
            'inHospitalItemId' =>  $this->inHospitalItemId->value(),
            'pattern' =>  $this->pattern,
            'lot' =>  $this->lot->toArray(),
            'uniqKey' =>  $this->createUniqKey(),
            'orderedQuantity' =>  $this->orderedQuantity,
            'calculationQuantity' =>  $this->calculationQuantity,
        ];
    }
}