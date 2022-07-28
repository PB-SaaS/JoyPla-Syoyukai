<?php

namespace JoyPla\Enterprise\Models;

use Collection;
class Distributor 
{
    private HospitalId $hospitalId;
    private DistributorId $distributorId;
    private string $distributorName;

    public function __construct(HospitalId $hospitalId , DistributorId $distributorId , string $distributorName)
    {
        $this->hospitalId = $hospitalId;
        $this->distributorId = $distributorId;
        $this->distributorName = $distributorName;
    }

    public static function create(Collection $i)
    {
        return new Distributor(
            ( new HospitalId($i->hospitalId) ),
            ( new DistributorId($i->distributorId) ),
            ( $i->distributorName )
        );
    }

    public function getHospitalId()
    {
        return $this->hospitalId;
    }

    public function getDistributorId()
    {
        return $this->distributorId;
    }
    
    public function getDistributorName()
    {
        return $this->distributorName;
    }

    public function toArray()
    {
        return [
            'hospitalId' => $this->hospitalId->value(),
            'distributorId' => $this->distributorId->value(),
            'distributorName' => $this->distributorName,
        ];
    }
}