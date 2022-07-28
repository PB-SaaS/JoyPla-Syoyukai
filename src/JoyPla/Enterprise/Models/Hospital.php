<?php

namespace JoyPla\Enterprise\Models;

use Collection;
class Hospital 
{
    private HospitalId $hospitalId;
    private HospitalName $hospitalName;

    public function __construct(HospitalId $hospitalId , HospitalName $hospitalName)
    {
        $this->hospitalId = $hospitalId;
        $this->hospitalName = $hospitalName;
    }

    public static function create(Collection $i)
    {
        return new Hospital(
            ( new HospitalId($i->hospitalId) ),
            ( new HospitalName($i->hospitalName) ),
        );
    }

    public function getHospitalId()
    {
        return $this->hospitalId;
    }

    public function getHospitalName()
    {
        return $this->hospitalName;
    }
    
    public function toArray()
    {
        return [
            'hospitalId' => $this->hospitalId->value(),
            'hospitalName' => $this->hospitalName->value(),
        ];
    }
}