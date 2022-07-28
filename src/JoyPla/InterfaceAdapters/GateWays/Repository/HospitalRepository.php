<?php

namespace JoyPla\InterfaceAdapters\GateWays\Repository;

use App\SpiralDb\Hospital as SpiralDbHospital;
use JoyPla\Enterprise\Models\Hospital;
use JoyPla\Enterprise\Models\HospitalId;

class HospitalRepository implements HospitalRepositoryInterface{

    public function find( HospitalId $hospitalId )
    {
        $hospital = (SpiralDbHospital::where('hospitalId',$hospitalId->value())->get())->data->get(0);

        $hospital = Hospital::create($hospital);

        return $hospital;
    }
}

interface HospitalRepositoryInterface 
{
    public function find( HospitalId $hospitalId );
    
}