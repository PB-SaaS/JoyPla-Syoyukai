<?php

namespace JoyPla\Enterprise\Models;

use Collection;
class Redemption 
{
    private bool $redemptionFlag;
    private Price $redemptionPrice;

    public function __construct(bool $redemptionFlag , Price $redemptionPrice)
    {
        $this->redemptionFlag = $redemptionFlag;
        $this->redemptionPrice = $redemptionPrice;
    }

    public static function create(Collection $i)
    {
        return new Redemption(
            ( $i->officialFlag === '1' ),
            ( new Price($i->officialprice) ),
        );
    }

    public function getRedemptionFlag()
    {
        return $this->redemptionFlag;
    }

    public function getRedemptionPrice()
    {
        return $this->redemptionPrice;
    }
    
    public function toArray()
    {
        return [
            'redemptionFlag' => $this->redemptionFlag,
            'officialprice' => $this->redemptionPrice->value(),
        ];
    }
}