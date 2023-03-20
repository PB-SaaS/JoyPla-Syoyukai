<?php

namespace JoyPla\Enterprise\Models;

use Collection;
use Exception;

class Lot
{
    private LotNumber $lotNumber;
    private LotDate $lotDate;

    public function __construct(LotNumber $lotNumber, LotDate $lotDate)
    {
        if (
            (!$lotNumber->isEmpty() && $lotDate->isEmpty()) ||
            ($lotNumber->isEmpty() && !$lotDate->isEmpty())
        ) {
            throw new Exception('Two fields need to be filled.', 422);
        }
        $this->lotNumber = $lotNumber;
        $this->lotDate = $lotDate;
    }

    public static function create(Collection $i)
    {
        return new Lot(
            new LotNumber($i->lotNumber),
            new LotDate($i->lotDate)
            //( ($i->lotManagement == "1") )
        );
    }

    public function isEmpty()
    {
        return $this->lotNumber->isEmpty() || $this->lotDate->isEmpty();
    }

    public function getLotNumber()
    {
        return $this->lotNumber;
    }

    public function getLotDate()
    {
        return $this->lotDate;
    }

    public function toArray()
    {
        return [
            'lotNumber' => $this->lotNumber->value(),
            'lotDate' => $this->lotDate->value(),
        ];
    }
}
