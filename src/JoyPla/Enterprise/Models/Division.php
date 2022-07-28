<?php

namespace JoyPla\Enterprise\Models;

use Collection;
class Division 
{
    private HospitalId $hospitalId;
    private DivisionId $divisionId;
    private DivisionName $divisionName;

    public function __construct(HospitalId $hospitalId , DivisionId $divisionId , DivisionName $divisionName)
    {
        $this->hospitalId = $hospitalId;
        $this->divisionId = $divisionId;
        $this->divisionName = $divisionName;
    }

    public static function create(Collection $i)
    {
        return new Division(
            ( new HospitalId($i->hospitalId) ),
            ( new DivisionId($i->divisionId) ),
            ( new DivisionName($i->divisionName) )
        );
    }

    public function getHospitalId()
    {
        return $this->hospitalId;
    }

    public function getDivisionId()
    {
        return $this->divisionId;
    }
    
    public function getDivisionName()
    {
        return $this->divisionName;
    }

    public function toArray()
    {
        return [
            'hospitalId' => $this->hospitalId->value(),
            'divisionId' => $this->divisionId->value(),
            'divisionName' => $this->divisionName->value(),
        ];
    }
}